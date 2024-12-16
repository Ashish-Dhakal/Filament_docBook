<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable

{
    use  HasFactory;
    protected $fillable = ['name', 'email', 'password', 'roles', 'gender', 'age', 'blood_group', 'phone', 'address'];

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

    // public function hasRole($roles)
    // {
    //     if (is_array($roles)) {
    //         return in_array($this->role, $roles);
    //     }

    //     return $this->role === $roles;
    // }

    public function hasRole($roles): bool
    {
        return $this->roles === $roles;
    }



    public function patient()
    {
        return $this->hasOne(Patient::class,'user_id');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
