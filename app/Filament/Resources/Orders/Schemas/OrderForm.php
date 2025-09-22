<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->schema([
                        TextInput::make('external_id')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('number')
                            ->required()
                            ->maxLength(255),
                        Select::make('customer_id')
                            ->relationship('customer', 'email')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),
                        TextInput::make('currency')
                            ->default('PLN')
                            ->maxLength(3),
                        TextInput::make('total_gross')
                            ->numeric()
                            ->required(),
                        TextInput::make('total_net')
                            ->numeric()
                            ->required(),
                        TextInput::make('shipping_method')
                            ->maxLength(255),
                        DatePicker::make('paid_at'),
                    ])
                    ->columns(2),
            ]);
    }
}
