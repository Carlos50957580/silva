<?php
// app/Models/User.php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'superadmin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'superadmin' => 'boolean',
        ];
    }

    // Método para verificar si es superadmin
    public function isSuperAdmin(): bool
    {
        return $this->superadmin === true;
    }

    // Método para verificar si es superadmin (alias)
    public function getIsSuperAdminAttribute(): bool
    {
        return $this->superadmin === true;
    }

    // Scope para filtrar superadmins
    public function scopeSuperAdmin($query)
    {
        return $query->where('superadmin', true);
    }

    // Scope para filtrar usuarios normales
    public function scopeNormal($query)
    {
        return $query->where('superadmin', false);
    }
}