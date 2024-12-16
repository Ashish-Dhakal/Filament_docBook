<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * Helper function to get the user ID based on the role (doctor or patient).
     */
    public function userId()
    {
        if (Auth::user()->roles == 'doctor') {
            $userId =  Auth::user()->id;
            $doctorId = Doctor::where('user_id', $userId)->first();
            return $doctorId->id;
        } elseif (Auth::user()->roles == 'patient') {
            $userId =  Auth::user()->id;
            $patientId = Patient::where('user_id', $userId)->first();
            return $patientId->id;
        }
    }

    public function viewAny(User $user)
    {
        // return in_array($user->roles, ['admin', 'doctor', 'patient']);
        return true;
    }

    /**
     * Determine if the user can view a specific appointment.
     */
    public function view(User $user, Appointment $appointment)
    {
        if ($user->roles === 'admin') {
            return true;
        } elseif ($user->roles === 'doctor' && $appointment->doctor_id === $this->userId()) {
            return true;
        } elseif ($user->roles === 'patient' && $appointment->patient_id === $this->userId()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create an appointment.
     */
    public function create(User $user)
    {
        return $user->roles === 'admin' || $user->roles === 'patient';
    }

    public function edit(User $user, Appointment $appointment)
    {
        // Admins can edit any appointment
        if ($user->roles === 'admin') {
            return true;
        }

        // Patients can edit only their own appointments
        if ($user->roles === 'patient' && $appointment->patient_id === $this->userId()) {
            return true;
        }

        // Default: not authorized

        return false;
    }



    /**
     * Determine if the user can update an appointment.
     */
    public function update(User $user, Appointment $appointment)
    {
        return $user->roles === 'admin' ||
            ($user->roles === 'patient' && $appointment->patient_id === $this->userId());
    }

    /**
     * Determine if the user can delete an appointment.
     */
    public function delete(User $user, Appointment $appointment)
    {
        return $user->roles === 'admin';
    }
    /**
     * Determine whether the user can delete the appointment.
     *
     * Only admin can delete an appointment if its status is 'pending'.
     */
    // public function delete(User $user, Appointment $appointment): bool
    // {
    //     // Admin can delete an appointment only if its status is 'pending'
    //     return $user->roles === 'admin' && $appointment->status === 'pending';
    // }

    /**
     * Determine whether the user can restore the appointment.
     *
     * Restoring is not allowed in this case.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return false; // Cannot restore an appointment
    }

    /**
     * Determine whether the user can permanently delete the appointment.
     *
     * Permanently deleting is not allowed in this case.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return false; // Cannot permanently delete an appointment
    }
}
