<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class PaymentPolicy
{

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
    
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user ): bool
    {
        if (( $user->roles === 'patient') || ($user->roles === 'admin')) {
            return true;
        } 
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->roles === 'admin') {
            return true;
        } elseif ($user->roles === 'patient' && $payment->patient_id === $this->userId()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payment $payment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payment $payment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payment $payment): bool
    {
        return false;
    }
}
