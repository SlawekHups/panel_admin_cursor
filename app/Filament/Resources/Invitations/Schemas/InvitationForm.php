<?php

namespace App\Filament\Resources\Invitations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invitation Information')
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('token')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('expires_at')
                            ->required(),
                        Select::make('inviter_id')
                            ->relationship('inviter', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        DatePicker::make('accepted_at'),
                        Textarea::make('metadata')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }
}
