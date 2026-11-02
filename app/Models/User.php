<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'unit',
        'phone',
        'password',
    ];

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

    /**
     * Helper wewenang peran (Role Access)
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['ADMIN', 'MASTER'], true);
    }

    public function isUnit(): bool
    {
        return $this->role === 'UNIT';
    }

    public function isLegal(): bool
    {
        return $this->role === 'LEGAL';
    }

    public function isLeader(): bool
    {
        return $this->role === 'LEADER';
    }
}
