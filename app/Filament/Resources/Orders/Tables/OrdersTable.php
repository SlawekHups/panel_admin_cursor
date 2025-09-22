<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Jobs\CreateInPostShipmentJob;
use App\Jobs\GenerateInvoicePdfJob;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.email')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'shipped',
                        'green' => 'delivered',
                        'danger' => 'cancelled',
                        'purple' => 'refunded',
                    ]),
                TextColumn::make('total_gross')
                    ->money('PLN')
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('create_shipment')
                    ->label('Create Shipment')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            CreateInPostShipmentJob::dispatch($record->id);
                            
                            Notification::make()
                                ->title('Shipment creation job dispatched!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to create shipment')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('generate_invoice')
                    ->label('Generate Invoice')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            // Create invoice first
                            $invoice = $record->invoice()->create([
                                'number' => 'INV-' . $record->number . '-' . now()->format('Ymd'),
                                'total_gross' => $record->total_gross,
                                'total_net' => $record->total_net,
                                'issued_at' => now(),
                            ]);
                            
                            // Dispatch job to generate PDF
                            GenerateInvoicePdfJob::dispatch($invoice->id);
                            
                            Notification::make()
                                ->title('Invoice created and PDF generation job dispatched!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to create invoice')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
