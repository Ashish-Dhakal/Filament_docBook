<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\ButtonColumn;
use App\Filament\Resources\PaymentResource;


class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.user.name')
                    ->label('Patient Name')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment.doctor.user.name')
                    ->label('Doctor Name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->prefix('$ ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_type')
                    ->label('Payment Type')
                    ->formatStateUsing(function ($state) {
                        // Return the state for display
                        return ucfirst($state);
                    })
                    ->searchable()
                    ->badge()
                    ->colors([
                        'secondary' => '-',          // Default no payment
                        'info' => 'cash',         // Offline payment
                        'success' => 'online',       // Online payment
                    ]),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->formatStateUsing(function ($state) {
                        // Return the state for display
                        return ucfirst($state);
                    })
                    ->searchable()
                    ->badge()
                    ->colors([
                        'success' => 'completed',    // Payment completed
                        'warning' => 'pending',      // Payment pending
                    ]),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // New "Quick Pay" Column
                Tables\Columns\TextColumn::make('quick_pay')
                    ->label('Quick Pay')
                    ->state(function ($record) {
                        // Return the text to display
                        if (!$record || !isset($record->payment_status)) {
                            return '-'; // Default fallback
                        }
                        return $record->payment_status === 'pending' ? 'Pay Via Stripe' : 'Paid';
                    })
                    ->url(function ($record) {
                        if ($record && $record->payment_status === 'pending') {
                            $url = url('/admin/payments/stripe-payment', ['id' => $record->id]);
                            return $url;
                        }
                        return null; 
                    })
                    ->color(function ($record) {
                        if (!$record || !isset($record->payment_status)) {
                            return 'secondary'; // Default color for missing data
                        }
                        return $record->payment_status === 'pending' ? 'primary' : 'success';
                    }),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
