<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'mobile',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'status' => 'boolean',
        ];
    }

    // Relationships for Orders
    public function ordersCreated()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function ordersAssigned()
    {
        return $this->hasMany(Order::class, 'assigned_delivery');
    }

    // Check role methods
    public function isAdmin()
    {
        return $this->role && $this->role === 'admin';
    }

    public function isSales()
    {
        return $this->role && $this->role === 'sales';
    }

    public function isDelivery()
    {
        return $this->role && $this->role === 'delivery';
    }

    // Scope for active users
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope for role
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
