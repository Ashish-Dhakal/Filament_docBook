<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Service\AppointmentService;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AppointmentResource;
use App\Models\AppointmentSlot;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads;


class CreateAppointment extends CreateRecord
{
    // use WithFileUploads;

    protected static string $resource = AppointmentResource::class;

    public $doctorInfo = null;

    // Listen for the `doctorSelected` event
    protected $listeners = ['doctorSelected' => 'onDoctorSelected'];


    public function onDoctorSelected($data)
    {
        if ($data['context'] === 'appointment_form') {
            $doctorId = $data['doctor_id']; // Doctor ID from input

            // Fetch appointment slots for the selected doctor with 'booked' or 'unavailable' status
            $appointmentSlots = AppointmentSlot::where('doctor_id', $doctorId)
                ->whereIn('status', ['booked', 'unavailable'])
                ->get();

            // Initialize the doctor info
            $this->doctorInfo = [];

            // Check if there are appointment slots found
            if ($appointmentSlots->isNotEmpty()) {
                // If there are appointment slots, you can process them
                foreach ($appointmentSlots as $slot) {
                    // Example of extracting details from each slot
                    $this->doctorInfo[] = [
                        'status' => $slot->status,  // Slot status (booked/unavailable)
                        'date' => $slot->date,      // Appointment date
                        'start_time' => $slot->start_time, // Start time
                        'end_time' => $slot->end_time,     // End time
                    ];
                }
            } else {
                // If no appointment slots are found, handle this case
                $this->doctorInfo[] = [
                    'message' => 'No booked or unavailable slots for this doctor.'
                ];
            }
        }
    }




    protected function handleRecordCreation(array $data): Model
    {
        $service = new AppointmentService();

        try {
            $appointment = $service->createAppointment($data);
            // Success notification
            Notification::make()
                ->title('Appointment Created')
                ->success()
                ->body('The appointment has been successfully created.')
                ->send();

            return $appointment;
        } catch (ValidationException $e) {
            // Error notification
            Notification::make()
                ->title('Error Creating Appointment')
                ->danger()
                ->body($e->getMessage())
                ->send();

            throw $e; // Propagate the exception
        }
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    public function mount(): void
    {
        parent::mount();
        $this->doctorInfo = null;  // Initialize with null
    }

    public function getListeners(): array
    {
        return [
            'doctorSelected' => 'onDoctorSelected',
        ];
    }
}
