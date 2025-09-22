<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.customer.email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_gross')
                    ->money('PLN')
                    ->sortable(),
                TextColumn::make('total_net')
                    ->money('PLN')
                    ->sortable(),
                BadgeColumn::make('has_pdf')
                    ->getStateUsing(fn ($record) => $record->pdf_path ? 'Yes' : 'No')
                    ->colors([
                        'success' => 'Yes',
                        'danger' => 'No',
                    ]),
                TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('has_pdf')
                    ->options([
                        'yes' => 'Has PDF',
                        'no' => 'No PDF',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'yes') {
                            return $query->whereNotNull('pdf_path');
                        }
                        if ($data['value'] === 'no') {
                            return $query->whereNull('pdf_path');
                        }
                        return $query;
                    }),
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
