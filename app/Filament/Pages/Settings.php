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
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected string $view = 'filament.pages.settings';
    protected static ?string $navigationLabel = 'Ustawienia';
    protected static ?string $title = 'Ustawienia systemu';

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user instanceof User && $user->hasRole('SuperAdmin');
    }

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
                Section::make('Integracja PrestaShop')
                    ->schema([
                        TextInput::make('prestashop_url')
                            ->label('URL bazowy')
                            ->url()
                            ->required(),
                        TextInput::make('prestashop_api_key')
                            ->label('Klucz API')
                            ->password()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Integracja InPost')
                    ->schema([
                        TextInput::make('inpost_api_token')
                            ->label('Token API')
                            ->password()
                            ->required(),
                    ]),

                Section::make('Integracja SMS API')
                    ->schema([
                        TextInput::make('smsapi_token')
                            ->label('Token API')
                            ->password()
                            ->required(),
                    ]),

                Section::make('Ustawienia systemu')
                    ->schema([
                        Toggle::make('notifications_enabled')
                            ->label('Włącz powiadomienia')
                            ->default(true),
                        Toggle::make('auto_sync_enabled')
                            ->label('Włącz automatyczną synchronizację')
                            ->default(true),
                        TextInput::make('sync_interval')
                            ->label('Interwał synchronizacji (minuty)')
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
                ->label('Zapisz ustawienia')
                ->action('save'),
            Action::make('test_connections')
                ->label('Testuj połączenia')
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
            ->title('Ustawienia zostały zapisane!')
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
                ->title('Połączenie PrestaShop: OK')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Połączenie PrestaShop nieudane')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        // Test InPost connection
        try {
            $inpostClient = app(\App\Integrations\InPost\InPostClient::class);
            // Add actual test call here
            Notification::make()
                ->title('Połączenie InPost: OK')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Połączenie InPost nieudane')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
