<?php

namespace App\Filament\Resources\Invitations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('token')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('inviter.name')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->getStateUsing(function ($record) {
                        if ($record->isAccepted()) return 'Accepted';
                        if ($record->isExpired()) return 'Expired';
                        return 'Pending';
                    })
                    ->colors([
                        'success' => 'Accepted',
                        'warning' => 'Pending',
                        'danger' => 'Expired',
                    ]),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('accepted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'expired' => 'Expired',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'pending') {
                            return $query->whereNull('accepted_at')->where('expires_at', '>', now());
                        }
                        if ($data['value'] === 'accepted') {
                            return $query->whereNotNull('accepted_at');
                        }
                        if ($data['value'] === 'expired') {
                            return $query->where('expires_at', '<=', now())->whereNull('accepted_at');
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
