<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'email_verified_at',
        'company_name',
        'company_address',
        'company_license',
        'contact_person',
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
            'is_active' => 'boolean',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }

    /**
     * Get the bookings for this user.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the buses owned by this operator.
     */
    public function buses()
    {
        return $this->hasMany(Bus::class, 'operator_id');
    }

    /**
     * Get the schedules created by this operator.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'operator_id');
    }

    /**
     * Get bookings for this operator's buses.
     */
    public function operatorBookings()
    {
        return $this->hasManyThrough(Booking::class, Schedule::class, 'operator_id', 'schedule_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is operator.
     */
    public function isOperator()
    {
        return $this->hasRole('operator');
    }

    /**
     * Check if user is regular user.
     */
    public function isUser()
    {
        return $this->hasRole('user');
    }
}
