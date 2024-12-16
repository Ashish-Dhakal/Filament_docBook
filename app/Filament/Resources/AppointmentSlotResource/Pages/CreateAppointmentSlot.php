<?php

namespace App\Filament\Resources\AppointmentSlotResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Doctor;
use App\Models\AppointmentSlot;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AppointmentSlotResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateAppointmentSlot extends CreateRecord
{
    protected static string $resource = AppointmentSlotResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Check if the doctor exists
        $doctor = Doctor::find($data['doctor_id']);
        if (empty($doctor)) {
            // Throw validation exception with specific message
            throw ValidationException::withMessages([
                'doctor_id' => 'Invalid doctor ID.',
            ]);
        }

        // Parse start and end times
        $startDateTime = Carbon::parse($data['date'] . ' ' . $data['start_time']);
        $endDateTime = Carbon::parse($data['date'] . ' ' . $data['end_time']);

        // Check if the requested appointment time overlaps with an existing appointment for the doctor
        $existingAppointment = AppointmentSlot::where('doctor_id', $data['doctor_id'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('start_time', [$startDateTime, $endDateTime])
                    ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                    ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                        $query->where('start_time', '<', $endDateTime)
                            ->where('end_time', '>', $startDateTime);
                    });
            })
            ->exists();

        if ($existingAppointment) {
            // If the appointment slot already exists, throw a ValidationException
            throw ValidationException::withMessages([
                'time_slot' => 'The requested time slot is already booked.',
            ]);
        }

        // Convert start and end times to Carbon instances
        $startTimeCarbon = Carbon::createFromFormat('H:i:s', $data['start_time']);
        $endTimeCarbon = Carbon::createFromFormat('H:i:s', $data['end_time']);

        // Check if the end time is earlier than the start time
        if ($endTimeCarbon->lt($startTimeCarbon)) {
            // If end time is earlier than start time, throw validation exception
            throw ValidationException::withMessages([
                'end_time' => 'End time should be after start time.',
            ]);
        }

        $startTime = $data['start_time'];
        $endTime = $data['end_time'];

        // Check for conflicting schedules
        $conflictingSchedule = AppointmentSlot::where('doctor_id', $data['doctor_id'])
            ->whereDate('date', $data['date']) // Check for the same date
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])  // Existing start_time within the new time range
                    ->orWhereBetween('end_time', [$startTime, $endTime])    // Existing end_time within the new time range
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })
            ->exists();

        if ($conflictingSchedule) {
            // If there's a conflict, throw validation exception
            throw ValidationException::withMessages([
                'time_slot' => 'The requested time slot conflicts with an existing schedule for this doctor.',
            ]);
        }

        // Create and save the appointment slot if no errors
        $appointmentSlot = new AppointmentSlot();
        $appointmentSlot->doctor_id = $data['doctor_id'];
        $appointmentSlot->date = $data['date'];
        $appointmentSlot->start_time = $data['start_time'];
        $appointmentSlot->end_time = $data['end_time'];
        $appointmentSlot->status = $data['status'];
        $appointmentSlot->save();

        return $appointmentSlot;
    }
}
