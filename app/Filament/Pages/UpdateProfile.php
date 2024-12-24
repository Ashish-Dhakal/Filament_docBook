<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\EditProfile;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;

class UpdateProfile extends EditProfile
{
    protected function getForms(): array
    {
        $this->maxWidth = '5xl';
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Split::make([
                            Section::make([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                $this->getAddressFormComponent(),
                                $this->getGenderFormComponent(),
                            ]),
                            Section::make([
                                $this->getAgeFormComponent(),
                                $this->getBloodGroupFormComponent(),
                                $this->getPhoneFormComponent(),
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ])

                        ])


                    ])
                    ->statePath('data'),
            ),
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Split::make([
                            Section::make([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                $this->getAddressFormComponent(),
                                $this->getGenderFormComponent(),
                            ]),
                            Section::make([
                                $this->getAgeFormComponent(),
                                $this->getBloodGroupFormComponent(),
                                $this->getPhoneFormComponent(),
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ])

                        ])


                    ])
                    ->statePath('data'),
            ),
        ];

        return[

            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        // Split::make([
                        //     Section::make([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                $this->getAddressFormComponent(),
                                $this->getGenderFormComponent(),
                            // ]),
                            // Section::make([
                                $this->getAgeFormComponent(),
                                $this->getBloodGroupFormComponent(),
                                $this->getPhoneFormComponent(),
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            // ])

                        // ])


                    ])
                    ->statePath('data'),
            ),
        ];
    }


    public function afterSave()
    {
        return redirect()->route('filament.admin.pages.dashboard');
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
}
