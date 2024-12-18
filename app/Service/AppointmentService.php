<?php

namespace App\Service;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Appointment;
use Illuminate\Http\Response;
use App\Models\PatientHistory;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function createAppointment(array $data)
    {
        $data['patient_id'] = $this->getAuthenticatedPatientId($data);
        // Extract relevant variables
        $doctorId = $data['doctor_id'];
        $patientId = $data['patient_id'] ?? $this->getAuthenticatedPatientId($data);
        $date = $data['date'];
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];

        // Step 1: Check Doctor's Appointment Slot Availability
        $doctorSlotError = $this->checkDoctorSlotStatus($doctorId, $date, $startTime, $endTime);
        if ($doctorSlotError) {
            throw ValidationException::withMessages(['doctor_slot' => $doctorSlotError]);
        }

        // Step 2: Check Conflicting Appointments (Doctor & Patient)
        $appointmentConflict = $this->checkAppointmentConflict($doctorId, $patientId, $date, $startTime, $endTime);
        if ($appointmentConflict) {
            throw ValidationException::withMessages(['appointment_conflict' => $appointmentConflict]);
        }

        // Step 3: Create Appointment and Appointment Slot
        return $this->storeAppointment($data, $patientId);
    }

    private function checkDoctorSlotStatus($doctorId, $date, $startTime, $endTime)
    {
        $conflict = AppointmentSlot::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                    });
            })
            ->whereIn('status', ['booked', 'unavailable'])
            ->exists();

        if ($conflict) {
            return 'The doctor is not available during the selected time slot.';
        }

        return null;
    }

    private function checkAppointmentConflict($doctorId, $patientId, $date, $startTime, $endTime)
    {
        $conflict = Appointment::where('doctor_id', $doctorId)
            ->where('patient_id', $patientId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                    });
            })
            ->whereIn('status', ['booked', 'pending'])
            ->exists();

        if ($conflict) {
            return 'The doctor or patient already has an appointment at the selected time.';
        }

        return null;
    }

    private function storeAppointment($data, $patientId)
    {
        // Add the patient ID to data
        $data['patient_id'] = $patientId;

        // Save the appointment
        $appointment = new Appointment();
        $appointment->fill($data);
        $appointment->status = $data['status'] ?? 'booked';
        $appointment->save();

        // Save the corresponding slot as booked
        $appointmentSlot = new AppointmentSlot();
        $appointmentSlot->fill([
            'doctor_id' => $data['doctor_id'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => 'booked',
        ]);
        $appointmentSlot->save();

        return $appointment;
    }

    private function getAuthenticatedPatientId($data)
    {
        if (Auth::user()->roles === 'patient') {
            // Get the patient ID for authenticated patients
            $patient = \App\Models\Patient::where('user_id', Auth::id())->first();
            return $patient?->id;
        }
    
        if (Auth::user()->roles === 'admin') {
            // Admin must provide a patient ID
            if (!isset($data['patient_id']) || empty($data['patient_id'])) {
                throw ValidationException::withMessages([
                    'patient_id' => 'The patient ID is required when creating an appointment as an admin.',
                ]);
            }
    
            return $data['patient_id'];
        }
    
        // Default behavior for unauthorized roles
        throw ValidationException::withMessages([
            'role' => 'You do not have permission to create an appointment.',
        ]);
    }

    public function updateStatus(string $status, Appointment $appointment)
    {
        $this->validateStatus($status, $appointment);

        switch ($status) {
            case 'pending':
                return $this->handlePending($appointment);
                break;
            case 'booked':
                return $this->handleBooked($appointment);
                break;
            case 'cancelled':
                return $this->handleCancelled($appointment);
                break;
            case 'completed':
                return $this->handleCompleted($appointment);
                break;
            default:
                return 'Invalid status';
        }
    }

    /**
     * Validates whether a status update is allowed.
     */
    private function validateStatus(string $status, Appointment $appointment)
    {
        if ($appointment->status === 'completed' && $status !== 'completed') {
            // abort(Response::HTTP_FORBIDDEN, );
            throw ValidationException::withMessages(['data_exist' => 'This appointment is already completed and cannot be modified.']);
            return redirect()->route('filament.admin.resources.appointments.index');
        }
    }

    /**
     * Handles pending appointments.
     */
    private function handlePending(Appointment $appointment)
    {
        $appointmentSlot = AppointmentSlot::where('doctor_id', $appointment->doctor_id)
            ->where('date', $appointment->date)
            ->where('start_time', $appointment->start_time)
            ->where('end_time', $appointment->end_time)
            ->where('status', 'booked')
            ->first();

        if ($appointmentSlot) {
            // return 'This time slot is already booked.';
            return redirect()->back()->with('error', 'This time slot is already booked.');
        }

        $appointment->status = 'pending';
        $appointment->save();

        return null;
    }

    /**
     * Handles booked appointments.
     */
    private function handleBooked(Appointment $appointment)
    {
        $appointment->status = 'booked';
        $appointment->save();
        return null;
    }

    /**
     * Handles cancelled appointments.
     */
    private function handleCancelled(Appointment $appointment)
    {
        $appointmentSlot = AppointmentSlot::where('doctor_id', $appointment->doctor_id)
            ->where('date', $appointment->date)
            ->where('start_time', $appointment->start_time)
            ->where('end_time', $appointment->end_time)
            ->first();

        if ($appointmentSlot) {
            $appointmentSlot->delete();
        }

        $appointment->delete();

        return 'Appointment cancelled successfully';
    }

    /**
     * Handles completed appointments.
     */
    private function handleCompleted(Appointment $appointment)
    {
        if ($appointment->status !== 'completed') {
            AppointmentSlot::where('doctor_id', $appointment->doctor_id)
                ->where('date', $appointment->date)
                ->where('start_time', $appointment->start_time)
                ->where('end_time', $appointment->end_time)
                ->delete();
        }

        if (!$appointment->reviews) {
            return 'Please add a review first';
        }

        $startTime = Carbon::parse($appointment->start_time);
        $endTime = Carbon::parse($appointment->end_time);
        $durationInHours = $startTime->diffInHours($endTime);
        $totalFee = $durationInHours * $appointment->doctor->hourly_rate;

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'amount' => $totalFee,
            'patient_id' => $appointment->patient_id,
        ]);

        $firstReview = $appointment->reviews->first();
        if (!$firstReview) {
            return 'Doctor is yet to add a review.';
        }

        PatientHistory::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'review_id' => $firstReview->id,
            'payment_id' => $payment->id,
        ]);

        $appointment->status = 'completed';
        $appointment->save();

        return null;
    }
    
}
