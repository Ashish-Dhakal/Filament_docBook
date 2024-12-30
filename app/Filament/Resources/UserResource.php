<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Patient;
use Filament\Forms\Form;
use App\Models\Speciality;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Actions\RestoreAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static string $relationship = 'users';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn($context) => $context === 'create')
                    ->maxLength(255),

                // Forms\Components\TextInput::make('password')
                //     ->password()
                //     ->maxLength(255)
                //     ->required(fn(?Model $record) => $record === null) // Required only when creating
                //     ->visible(fn(?Model $record) => $record === null || $record !== null) // Always visible
                //     ->dehydrated(fn($state) => filled($state)) // Only include in form submission if filled
                //     ->label('Password'),


                Forms\Components\Select::make('roles')
                    ->options([
                        'patient' => 'Patient',
                        'doctor' => 'Doctor',
                    ])
                    ->required()
                    ->reactive()
                // ->disabled(fn(callable $get) => $get('id') !== null)  // Disable if the record is being edited
                ,
                Forms\Components\Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->label('Gender')
                    // ->extraInputAttributes(['readonly' => true])
                    ->required(),


                // Speciality field for doctors
                Forms\Components\Select::make('doctor.speciality_id') // Use the doctor relationship
                    ->label('Speciality')
                    ->options(fn() => Speciality::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->visible(fn(callable $get) => $get('roles') === 'doctor')
                    ->default(fn(?Model $record) => $record && $record->roles === 'doctor' ? $record->doctor->speciality_id : null),

                // Hourly rate for doctors
                Forms\Components\TextInput::make('doctor.hourly_rate') // Use the doctor relationship
                    ->numeric()
                    ->required()
                    ->visible(fn(callable $get) => $get('roles') === 'doctor')
                    ->default(fn(?Model $record) => $record && $record->roles === 'doctor' ? $record->doctor->hourly_rate : null),




                Forms\Components\TextInput::make('age')
                    ->numeric()
                    ->maxLength(2)
                    ->required(),
                Forms\Components\Select::make('blood_group')
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
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(10)
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->maxLength(20)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roles'),
                Tables\Columns\TextColumn::make('gender'),

                Tables\Columns\TextColumn::make('age')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('blood_group')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->disabled(function ($record) {
                        // If the record is a patient, check if they have appointments

                        if ($record->roles === 'patient' && $record->patient->appointments()->exists()) {
                            return true;
                        }
                        if ($record->roles === 'admin') {
                            return true;
                        }
                        // If the record is a doctor, check if they have appointments
                        if ($record->roles === 'doctor' && $record->doctor->appointments()->exists()) {
                            return true;
                        }

                        return false;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // public static function infolist(Infolist $infolist): Infolist

    // {
    //     return $infolist
    //         ->schema([
    //             Section::make('User Details')
    //                 ->schema([
    //                     TextEntry::make('name')->label('State Name'),
    //                     TextEntry::make('email')->label('City Name'),
    //                 ])->columns(2),


    //         ]);
    // }


    public static function infolist(Infolist $infolist): Infolist

    {
        return $infolist
            ->schema([
                TextEntry::make('name')->label('Name'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('roles')->label('Role'),
                TextEntry::make('roles')->label('hourly_rate')
                    ->getStateUsing(fn($record) => $record->doctor->hourly_rate)
                    ->visible(fn($record) => $record->roles === 'doctor'),
                TextEntry::make('address')->label('address'),
                TextEntry::make('phone')->label('phone'),
                TextEntry::make('gender')->label('gender'),
                TextEntry::make('age')->label('age'),
                TextEntry::make('blood_group')->label('blood_group'),

            ])->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
