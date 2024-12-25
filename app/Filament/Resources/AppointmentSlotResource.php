<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Doctor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AppointmentSlot;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AppointmentSlotResource\Pages;
use App\Filament\Resources\AppointmentSlotResource\RelationManagers;

class AppointmentSlotResource extends Resource
{
    protected static ?string $model = AppointmentSlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Appointments Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('doctor_id')
                    ->default(function () {
                        // Check if the authenticated user is a doctor
                        $user = Auth::user();
                        if ($user && $user->doctor) {
                            return $user->doctor->id; // Pre-select the doctor's ID for the authenticated user
                        }
                        return null; // Return null if no doctor is found
                    }),
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->minDate(now()->toDateString()),
                    // ->rules('after:today'),
                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->rules('date_format:H:i:s'),
                Forms\Components\TimePicker::make('end_time')
                    ->required()
                    ->rules('date_format:H:i:s')
                    ->after('start_time'),
                Forms\Components\Select::make('status')
                    ->options([
                        'unavailable' => 'Unavailable',
                    ])
                    ->required()
                    ->rules('in:unavailable'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('status'),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointmentSlots::route('/'),
            'create' => Pages\CreateAppointmentSlot::route('/create'),
            'view' => Pages\ViewAppointmentSlot::route('/{record}'),
            'edit' => Pages\EditAppointmentSlot::route('/{record}/edit'),
        ];
    }
}
