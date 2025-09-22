<?php

namespace App\Filament\Resources\Users\Tables;

use App\Actions\Users\CreateInvitation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'invited',
                        'danger' => 'pending',
                    ]),
                TextColumn::make('last_login_at')
                    ->label('Ostatnie logowanie')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Data utworzenia')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktywny',
                        'invited' => 'Zaproszony',
                        'pending' => 'Oczekujący',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('invite')
                    ->label('Zaproś użytkownika')
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        \Filament\Forms\Components\Select::make('role')
                            ->label('Rola')
                            ->options([
                                'Admin' => 'Administrator',
                                'Operator' => 'Operator',
                                'Viewer' => 'Przeglądający',
                            ])
                            ->default('Viewer')
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        try {
                            $createInvitation = app(CreateInvitation::class);
                            $invitation = $createInvitation($data['email'], null, [$data['role']]);
                            
                            Notification::make()
                                ->title('Zaproszenie zostało wysłane!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Nie udało się wysłać zaproszenia')
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
