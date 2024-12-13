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
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->options([
                        'patient' => 'Patient',
                        'doctor' => 'Doctor',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->label('Gender')
                    ->required(),


                Forms\Components\Select::make('speciality_id')
                    ->label('Speciality')
                    ->options(fn() => Speciality::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->visible(fn(callable $get) => $get('roles') === 'doctor'),


                Forms\Components\TextInput::make('hourly_rate')
                    ->numeric()
                    ->required()
                    ->visible(fn(callable $get) => $get('roles') === 'doctor'),



                Forms\Components\TextInput::make('age')
                    ->numeric()
                    ->maxLength(2)
                    ->required(),
                Forms\Components\TextInput::make('blood_group')
                    ->maxLength(3)
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
