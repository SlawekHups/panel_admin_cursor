<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shipment Information')
                    ->schema([
                        Select::make('order_id')
                            ->relationship('order', 'number')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('carrier')
                            ->options([
                                'inpost' => 'InPost',
                            ])
                            ->default('inpost')
                            ->required(),
                        Select::make('service_type')
                            ->options([
                                'parcel_locker' => 'Parcel Locker',
                                'courier' => 'Courier',
                            ])
                            ->default('parcel_locker')
                            ->required(),
                        TextInput::make('tracking_number')
                            ->maxLength(255),
                        Select::make('status')
                            ->options([
                                'created' => 'Created',
                                'in_transit' => 'In Transit',
                                'delivered' => 'Delivered',
                                'returned' => 'Returned',
                                'lost' => 'Lost',
                            ])
                            ->default('created')
                            ->required(),
                        FileUpload::make('label_path')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('shipments'),
                    ])
                    ->columns(2),
            ]);
    }
}
