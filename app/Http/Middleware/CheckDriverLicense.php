<?php

namespace App\Http\Middleware;

use App\Services\LicenseValidationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDriverLicense
{
    public function __construct(
        private LicenseValidationService $licenseService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Driver $driver */
        $driver = $request->user();

        if (!$this->licenseService->canPerformAction($driver)) {
            $status = $this->licenseService->checkLicenseStatus($driver);
            return response()->json([
                'message' => $status['message'],
                'blocked' => true,
            ], 403);
        }

        return $next($request);
    }
}
