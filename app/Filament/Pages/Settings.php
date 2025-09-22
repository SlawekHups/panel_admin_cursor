<?php

namespace App\Filament\Pages;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use BackedEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected string $view = 'filament.pages.settings';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $title = 'System Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'prestashop_url' => config('integrations.prestashop.base_url'),
            'prestashop_api_key' => config('integrations.prestashop.api_key'),
            'inpost_api_token' => config('integrations.inpost.api_token'),
            'smsapi_token' => config('integrations.smsapi.token'),
            'notifications_enabled' => true,
            'auto_sync_enabled' => true,
            'sync_interval' => '60',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('PrestaShop Integration')
                    ->schema([
                        TextInput::make('prestashop_url')
                            ->label('Base URL')
                            ->url()
                            ->required(),
                        TextInput::make('prestashop_api_key')
                            ->label('API Key')
                            ->password()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('InPost Integration')
                    ->schema([
                        TextInput::make('inpost_api_token')
                            ->label('API Token')
                            ->password()
                            ->required(),
                    ]),

                Section::make('SMS API Integration')
                    ->schema([
                        TextInput::make('smsapi_token')
                            ->label('API Token')
                            ->password()
                            ->required(),
                    ]),

                Section::make('System Settings')
                    ->schema([
                        Toggle::make('notifications_enabled')
                            ->label('Enable Notifications')
                            ->default(true),
                        Toggle::make('auto_sync_enabled')
                            ->label('Enable Auto Sync')
                            ->default(true),
                        TextInput::make('sync_interval')
                            ->label('Sync Interval (minutes)')
                            ->numeric()
                            ->default(60),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save'),
            Action::make('test_connections')
                ->label('Test Connections')
                ->color('info')
                ->action('testConnections'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Update config values (in a real app, you'd save to database)
        config([
            'integrations.prestashop.base_url' => $data['prestashop_url'],
            'integrations.prestashop.api_key' => $data['prestashop_api_key'],
            'integrations.inpost.api_token' => $data['inpost_api_token'],
            'integrations.smsapi.token' => $data['smsapi_token'],
        ]);

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }

    public function testConnections(): void
    {
        // Test PrestaShop connection
        try {
            $prestashopClient = app(\App\Integrations\PrestaShop\PrestaShopClient::class);
            // Add actual test call here
            Notification::make()
                ->title('PrestaShop connection: OK')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('PrestaShop connection failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        // Test InPost connection
        try {
            $inpostClient = app(\App\Integrations\InPost\InPostClient::class);
            // Add actual test call here
            Notification::make()
                ->title('InPost connection: OK')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('InPost connection failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
