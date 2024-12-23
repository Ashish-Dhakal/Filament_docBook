<?php

namespace App\Filament\Resources\AppointmentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Review;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('appointment_id') // Hidden field for appointment_id
                ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id), // Automatically set parent ID
            
                Forms\Components\TextInput::make('comment')
                    ->required()
                    ->maxLength(100),
                //for pdf add
                Forms\Components\FileUpload::make('pdf')
                    ->required()
                    ->image()
                    ->maxFiles(1),
            ])
            ->columns([
                'appointment_id' => 'col-span-2',
                'comment' => 'col-span-2',
                'pdf' => 'col-span-2',
            ]);
        // ->default([
        //     'status' => 'pending',
        // ])
        // ->columns([
        //     'status' => 'col-span-2',
        // ])
        // ->default([        
        //     'status' => 'pending',           
        // ])


    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('appointment_id')
            ->columns([
                Tables\Columns\TextColumn::make('appointment.patient.user.name')
                    ->label('Patient Name'),
                Tables\Columns\TextColumn::make('appointment.doctor.user.name')
                    ->label('Doctor Name'),
                Tables\Columns\TextColumn::make('comment'),
                Tables\Columns\ImageColumn::make('pdf') // Use 'pdf' (attribute name)
                ->label('PDF') // Optional: Set column label
                ->url(fn(Review $record) => Storage::url($record->pdf))
                ->disk('public') // Optional: Set image storage disk
                ->size(100), // Optional: Adjust image size
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
