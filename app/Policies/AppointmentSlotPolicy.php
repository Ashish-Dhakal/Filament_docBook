<?php

namespace App\Policies;

use App\Models\AppointmentSlot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentSlotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       if($user->roles=== 'admin' || $user->roles=== 'doctor') {
           return true;
       }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AppointmentSlot $appointmentSlot): bool
    {
        if($user->roles=== 'admin' || $user->roles=== 'doctor') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if($user->roles=== 'doctor') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AppointmentSlot $appointmentSlot): bool
    {
        if($user->roles=== 'doctor') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AppointmentSlot $appointmentSlot): bool
    {
        if($user->roles=== 'admin' || $user->roles=== 'doctor') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AppointmentSlot $appointmentSlot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AppointmentSlot $appointmentSlot): bool
    {
        return false;
    }
}
