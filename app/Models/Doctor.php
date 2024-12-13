<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointmentSlots()
    {
        return $this->hasMany(AppointmentSlot::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function patientHistories()
    {
        return $this->hasMany(PatientHistory::class);
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }
}
