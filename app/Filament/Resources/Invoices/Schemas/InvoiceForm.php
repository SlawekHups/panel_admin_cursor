<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Information')
                    ->schema([
                        Select::make('order_id')
                            ->relationship('order', 'number')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('number')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('total_gross')
                            ->numeric()
                            ->required(),
                        TextInput::make('total_net')
                            ->numeric()
                            ->required(),
                        DatePicker::make('issued_at')
                            ->required(),
                        FileUpload::make('pdf_path')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('invoices'),
                    ])
                    ->columns(2),
            ]);
    }
}
