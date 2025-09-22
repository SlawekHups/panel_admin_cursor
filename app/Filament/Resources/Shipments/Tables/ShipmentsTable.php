<?php

namespace App\Filament\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.customer.email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('carrier')
                    ->badge()
                    ->colors([
                        'info' => 'inpost',
                    ]),
                TextColumn::make('service_type')
                    ->badge()
                    ->colors([
                        'success' => 'parcel_locker',
                        'warning' => 'courier',
                    ]),
                TextColumn::make('tracking_number')
                    ->searchable()
                    ->copyable(),
                BadgeColumn::make('status')
                    ->colors([
                        'info' => 'created',
                        'warning' => 'in_transit',
                        'success' => 'delivered',
                        'danger' => 'returned',
                        'purple' => 'lost',
                    ]),
                BadgeColumn::make('has_label')
                    ->getStateUsing(fn ($record) => $record->label_path ? 'Yes' : 'No')
                    ->colors([
                        'success' => 'Yes',
                        'danger' => 'No',
                    ]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'created' => 'Created',
                        'in_transit' => 'In Transit',
                        'delivered' => 'Delivered',
                        'returned' => 'Returned',
                        'lost' => 'Lost',
                    ]),
                SelectFilter::make('carrier')
                    ->options([
                        'inpost' => 'InPost',
                    ]),
                SelectFilter::make('service_type')
                    ->options([
                        'parcel_locker' => 'Parcel Locker',
                        'courier' => 'Courier',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
