<?php

namespace App\Filament\Pages\Auth;

use App\Models\Patient;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;


class Register extends BaseRegister
{

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getAddressFormComponent(),
                        $this->getGenderFormComponent(),
                        $this->getAgeFormComponent(),
                        $this->getBloodGroupFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getAddressFormComponent(): Component
    {
        return TextInput::make('address')
            ->required();
    }

    protected function getGenderFormComponent(): Component
    {
        return Select::make('gender')
            ->options([
                'male' => 'Male',
                'female' => 'Female',
            ])
            ->label('Gender')
            ->required();
    }

    protected function getAgeFormComponent(): Component
    {
        return TextInput::make('age')
            ->numeric()
            ->required();
    }

    protected function getBloodGroupFormComponent(): Component
    {
        return Select::make('blood_group')
            ->options([
                'A+' => 'A+',
                'A-' => 'A-',
                'B+' => 'B+',
                'B-' => 'B-',
                'AB+' => 'AB+',
                'AB-' => 'AB-',
                'O+' => 'O+',
                'O-' => 'O-',
            ])
            ->required();
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->tel()
            ->required();
    }

    protected function afterRegister()
    {
        // dd($this->data);
        $user = $this->form->model;
        $data = $this->data;
        $patient = Patient::create([
            'user_id' => $user->id,
        ]);
    }
}
