<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterDeviceRequest;
use App\Http\Requests\Api\RegisterBiometricTokenRequest;
use App\Http\Resources\DriverResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Driver;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $driver = Driver::where('code', $request->driver_code)->first();

        if (!$driver) {
            return $this->errorResponse(__('api.invalid_credentials'), 401);
        }

        // Biometric authentication
        if ($request->has('biometric_token')) {
            if (!$driver->validateBiometricToken($request->biometric_token)) {
                return $this->errorResponse(__('api.invalid_biometric_token'), 401);
            }
        } 
        // Password authentication
        else {
            $passwordValid = false;
            
            // First, check driver's own password if set
            if ($driver->password) {
                $passwordValid = Hash::check($request->password, $driver->password);
            }
            // Fall back to user password for backward compatibility
            elseif ($driver->user && $driver->user->password) {
                $passwordValid = Hash::check($request->password, $driver->user->password);
            }
            
            if (!$passwordValid) {
                return $this->errorResponse(__('api.invalid_credentials'), 401);
            }
        }

        // Create token
        $token = $driver->createToken('driver-api-token')->plainTextToken;

        return $this->successResponse([
            'id' => (string) $driver->id,
            'username' => $driver->code,
            'token' => $token,
            'biometricEnabled' => $driver->hasBiometricEnabled(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var Driver $driver */
        $driver = $request->user();
        $driver->currentAccessToken()->delete();

        return $this->successResponse(null);
    }

    public function profile(Request $request): JsonResponse
    {
        /** @var Driver $driver */
        $driver = $request->user();

        return $this->successResponse(new DriverResource($driver));
    }

    public function registerDevice(RegisterDeviceRequest $request): JsonResponse
    {
        $driver = $request->user();

        $device = $this->notificationService->registerDevice(
            $driver,
            $request->device_token,
            $request->device_type
        );

        return $this->successResponse([
            'message' => __('api.device_registered'),
            'device' => $device,
        ]);
    }

    public function biometricEnroll(Request $request): JsonResponse
    {
        /** @var Driver $driver */
        $driver = $request->user();

        $request->validate([
            'userId' => ['required', 'string'],
        ]);

        // Verify userId matches current driver
        if ($request->userId !== (string) $driver->id) {
            return $this->errorResponse(__('api.invalid_user_id'), 400);
        }

        // Generate a biometric token
        $biometricToken = 'bio_token_' . Str::random(32) . '_' . time();

        // Store the token (will be encrypted by model mutator)
        $driver->biometric_token = $biometricToken;
        $driver->save();

        return $this->successResponse([
            'token' => $biometricToken,
        ]);
    }

    public function biometricVerify(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'min:32'],
        ]);

        // Find driver by biometric token
        $drivers = Driver::all();
        $matchedDriver = null;

        foreach ($drivers as $driver) {
            if ($driver->hasBiometricEnabled() && $driver->validateBiometricToken($request->token)) {
                $matchedDriver = $driver;
                break;
            }
        }

        if (!$matchedDriver) {
            return $this->errorResponse(__('api.invalid_biometric_token'), 401);
        }

        // Create new token for session
        $token = $matchedDriver->createToken('driver-api-token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => [
                'id' => (string) $matchedDriver->id,
                'username' => $matchedDriver->code,
            ],
        ]);
    }

    public function biometricRevoke(Request $request): JsonResponse
    {
        /** @var Driver $driver */
        $driver = $request->user();

        $request->validate([
            'token' => ['required', 'string'],
        ]);

        // Verify the token matches
        if (!$driver->hasBiometricEnabled() || !$driver->validateBiometricToken($request->token)) {
            return $this->errorResponse(__('api.invalid_biometric_token'), 400);
        }

        // Revoke the token
        $driver->biometric_token = null;
        $driver->save();

        return $this->successResponse(null);
    }

    public function registerBiometricToken(RegisterBiometricTokenRequest $request): JsonResponse
    {
        /** @var Driver $driver */
        $driver = $request->user();

        // Verify password again for security
        $passwordValid = false;
        
        // First, check driver's own password if set
        if ($driver->password) {
            $passwordValid = Hash::check($request->password, $driver->password);
        }
        // Fall back to user password for backward compatibility
        elseif ($driver->user && $driver->user->password) {
            $passwordValid = Hash::check($request->password, $driver->user->password);
        }
        
        if (!$passwordValid) {
            return $this->errorResponse(__('api.invalid_password'), 401);
        }

        // Store the token (will be encrypted by model mutator)
        $driver->biometric_token = $request->biometric_token;
        $driver->save();

        return $this->successResponse([
            'message' => __('api.biometric_registered'),
            'biometric_enabled' => true,
        ]);
    }

    public function removeBiometricToken(Request $request): JsonResponse
    {
        /** @var Driver $driver */
        $driver = $request->user();

        $driver->biometric_token = null;
        $driver->save();

        return $this->successResponse([
            'message' => __('api.biometric_disabled'),
            'biometric_enabled' => false,
        ]);
    }
}
