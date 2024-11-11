<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('Super Admin');
        } elseif ($panel->getId() === 'client') {
            return $this->hasAnyRole(['Client', 'Super Admin', 'Operations', 'Finance']);
        } elseif ($panel->getId() === 'finance') {
            return $this->hasRole('Finance');
        } elseif ($panel->getId() === 'operations') {
            return $this->hasRole('Operations');
        }

        return false;
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
