<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi massal.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',     // admin | customer
        'phone',
        'address',
        'gender',   // L | P
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: satu user punya banyak pesanan.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Helper otorisasi.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
