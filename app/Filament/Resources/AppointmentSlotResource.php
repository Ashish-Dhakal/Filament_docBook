<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Doctor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AppointmentSlot;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AppointmentSlotResource\Pages;
use App\Filament\Resources\AppointmentSlotResource\RelationManagers;

class AppointmentSlotResource extends Resource
{
    protected static ?string $model = AppointmentSlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Assuming the relationship is defined as 'doctor' in AppointmentSlot
                Forms\Components\Select::make('doctor_id')
                    ->options(Doctor::with('user')->get()->pluck('user.name', 'id'))
                    ->label('Doctor')
                    // ->exists()
                    ->rules('exists:doctors,id'),
                Forms\Components\DatePicker::make('date')
                    ->required()
                    // ->minDate(now())
                    ->rules('date', 'after:today'),
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
