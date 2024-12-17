<?php
namespace App\Service;

use App\Models\Doctor;
use App\Models\AppointmentSlot;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AppointmentSlotService
{
    /**
     * Validate and create the appointment slot.
     *
     * @param array $data
     * @return AppointmentSlot
     * @throws ValidationException
     */
    public function createAppointmentSlot(array $data)
    {
        // Check if the doctor exists
        $doctor = Doctor::find($data['doctor_id']);
        if (!$doctor) {
            throw ValidationException::withMessages([
                'doctor_id' => 'Invalid doctor ID.',
            ]);
        }

        // Parse start and end times
        $startDateTime = Carbon::parse($data['date'] . ' ' . $data['start_time']);
        $endDateTime = Carbon::parse($data['date'] . ' ' . $data['end_time']);

        // Check if the requested appointment time overlaps with an existing appointment for the doctor
        $this->checkAppointmentConflict($data['doctor_id'], $startDateTime, $endDateTime);

        // Create the appointment slot
        $appointmentSlot = new AppointmentSlot();
        $appointmentSlot->doctor_id = $data['doctor_id'];
        $appointmentSlot->date = $data['date'];
        $appointmentSlot->start_time = $data['start_time'];
        $appointmentSlot->end_time = $data['end_time'];
        $appointmentSlot->status = $data['status'];
        $appointmentSlot->save();

        return $appointmentSlot;
    }

    /**
     * Check for appointment conflicts.
     *
     * @param int $doctorId
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @throws ValidationException
     */
    private function checkAppointmentConflict(int $doctorId, Carbon $startDateTime, Carbon $endDateTime)
    {
        // Check for conflicting schedules
        $conflictingSchedule = AppointmentSlot::where('doctor_id', $doctorId)
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('start_time', [$startDateTime, $endDateTime])
                    ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                    ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                        $query->where('start_time', '<', $endDateTime)
                            ->where('end_time', '>', $startDateTime);
                    });
            })
            ->exists();

        if ($conflictingSchedule) {
            throw ValidationException::withMessages([
                'time_slot' => 'The requested time slot conflicts with an existing schedule for this doctor.',
            ]);
        }

        // Check if end time is earlier than start time
        if ($endDateTime->lt($startDateTime)) {
            throw ValidationException::withMessages([
                'end_time' => 'End time should be after start time.',
            ]);
        }
    }
}
