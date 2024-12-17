<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class ReviewPolicy
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
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        if ($user->roles === 'admin') {
            return true;
        } elseif ($user->roles === 'doctor' && $review->appointment->doctor_id === $this->userId()) {
            return true;
        } elseif ($user->roles === 'patient' && $review->appointment->patient_id === $this->userId()) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->roles === 'doctor';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Review $review): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return false;
    }
}
