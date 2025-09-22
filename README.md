# Panel Administracyjny Laravel

Profesjonalny panel administracyjny zbudowany w Laravel 11.x z wykorzystaniem Filament v4, podobny do BaseLinker.

## 🚀 Funkcjonalności

### 👥 Zarządzanie użytkownikami
- **CRUD użytkowników** - Pełne zarządzanie użytkownikami
- **System zaproszeń** - Zapraszanie użytkowników przez email/SMS
- **Role i uprawnienia** - System ról (SuperAdmin, Admin, Operator, Viewer)
- **Statusy użytkowników** - Aktywny, Zaproszony, Oczekujący

### 📦 Zarządzanie zamówieniami
- **CRUD zamówień** - Pełne zarządzanie zamówieniami
- **Statusy zamówień** - Pending, Processing, Shipped, Delivered, Cancelled, Refunded
- **Integracja z PrestaShop** - Automatyczna synchronizacja zamówień
- **Akcje na zamówieniach** - Tworzenie przesyłek, generowanie faktur

### 🛍️ Zarządzanie produktami
- **CRUD produktów** - Pełne zarządzanie produktami
- **Stany magazynowe** - Śledzenie stanów magazynowych
- **Statusy produktów** - Aktywny, Nieaktywny, Brak w magazynie
- **Integracja z PrestaShop** - Synchronizacja produktów

### 👤 Zarządzanie klientami
- **CRUD klientów** - Pełne zarządzanie klientami
- **Dane kontaktowe** - Email, telefon, imię, nazwisko
- **Integracja z PrestaShop** - Synchronizacja klientów

### 🧾 Zarządzanie fakturami
- **CRUD faktur** - Pełne zarządzanie fakturami
- **Generowanie PDF** - Automatyczne generowanie faktur PDF
- **Integracja z zamówieniami** - Faktury powiązane z zamówieniami

### 🚚 Zarządzanie przesyłkami
- **CRUD przesyłek** - Pełne zarządzanie przesyłkami
- **Integracja InPost** - Tworzenie przesyłek InPost
- **Śledzenie statusów** - Statusy przesyłek w czasie rzeczywistym
- **Etykiety** - Pobieranie etykiet przesyłek

### 📊 Dashboard
- **Widgety KPI** - Statystyki zamówień, przychodów, produktów
- **Wykresy** - Wykresy zamówień w czasie, statusów
- **Tabela ostatnich zamówień** - Szybki podgląd ostatnich zamówień

### ⚙️ Ustawienia systemu
- **Konfiguracja integracji** - PrestaShop, InPost, SMSAPI
- **Testowanie połączeń** - Sprawdzanie statusu integracji
- **Ustawienia systemowe** - Powiadomienia, synchronizacja

### 🔔 System powiadomień
- **Email** - Powiadomienia email
- **SMS** - Powiadomienia SMS przez SMSAPI
- **Database** - Powiadomienia w bazie danych
- **Statusy** - Powiadomienia o zmianach statusów

## 🛠️ Technologie

- **Laravel 11.x** - Framework PHP
- **Filament v4** - Panel administracyjny
- **MySQL 8** - Baza danych
- **Redis** - Kolejki i cache
- **Laravel Horizon** - Monitorowanie kolejek
- **Spatie Permission** - System ról i uprawnień
- **Guzzle HTTP** - Klient HTTP
- **Pest** - Testy

## 📋 Wymagania

- PHP 8.3+
- MySQL 8.0+
- Redis
- Composer
- Node.js & NPM

## 🚀 Instalacja

### 1. Klonowanie repozytorium
```bash
git clone https://github.com/your-username/panel-admin-cursor.git
cd panel-admin-cursor
```

### 2. Instalacja zależności
```bash
composer install
npm install && npm run build
```

### 3. Konfiguracja środowiska
```bash
cp .env.example .env
```

Edytuj plik `.env` i ustaw:
```env
APP_NAME="Panel Administracyjny"
APP_LOCALE=pl
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=panel_admin
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Integracje
PRESTASHOP_BASE_URL=https://your-shop.com
PRESTASHOP_API_KEY=your-api-key
INPOST_API_TOKEN=your-inpost-token
SMSAPI_TOKEN=your-smsapi-token
```

### 4. Konfiguracja bazy danych
```bash
php artisan migrate
php artisan db:seed
```

### 5. Uruchomienie
```bash
php artisan serve
php artisan horizon
```

## 🔑 Dane logowania

- **URL:** `http://localhost:8000/admin`
- **Email:** `admin@example.com`
- **Hasło:** Wygenerowane przez seeder

## 📁 Struktura projektu

```
app/
├── Actions/           # Akcje biznesowe
├── Console/Commands/  # Komendy Artisan
├── Filament/         # Zasoby Filament
│   ├── Resources/    # Zasoby (CRUD)
│   ├── Pages/        # Strony
│   └── Widgets/      # Widgety
├── Integrations/     # Klienci integracyjni
├── Jobs/            # Zadania w tle
├── Models/          # Modele Eloquent
└── Notifications/   # Powiadomienia

database/
├── migrations/      # Migracje bazy danych
└── seeders/        # Seeder danych

lang/
└── pl/             # Tłumaczenia polskie
```

## 🔧 Komendy Artisan

### Synchronizacja z PrestaShop
```bash
# Synchronizacja wszystkich danych
php artisan prestashop:sync

# Synchronizacja od określonej daty
php artisan prestashop:sync --since=2024-01-01
```

### Zarządzanie użytkownikami
```bash
# Zaproszenie użytkownika
php artisan users:invite user@example.com --role=Admin

# Zaproszenie przez SMS
php artisan users:invite --phone=+48123456789 --role=Operator
```

### Kolejki
```bash
# Uruchomienie worker'a kolejek
php artisan queue:work

# Uruchomienie Horizon
php artisan horizon
```

## 🧪 Testy

```bash
# Uruchomienie testów
php artisan test

# Testy z coverage
php artisan test --coverage
```

## 📚 Dokumentacja API

### Webhooks

#### PrestaShop
- **URL:** `POST /api/webhooks/prestashop`
- **Opis:** Odbiera webhooki z PrestaShop

#### InPost
- **URL:** `POST /api/webhooks/inpost`
- **Opis:** Odbiera webhooki z InPost

### Zaproszenia
- **URL:** `GET /invite/accept/{token}`
- **Opis:** Strona akceptacji zaproszenia

## 🔒 Bezpieczeństwo

- **Uwierzytelnianie** - Laravel Sanctum
- **Autoryzacja** - Spatie Permission
- **Walidacja** - Laravel Validation
- **CSRF** - Ochrona CSRF
- **Rate Limiting** - Ograniczenia żądań

## 🚀 Wdrożenie

### Produkcja
```bash
# Optymalizacja
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Uruchomienie
php artisan serve --host=0.0.0.0 --port=80
```

### Docker
```bash
# Uruchomienie z Docker
docker-compose up -d
```

## 🤝 Współpraca

1. Fork projektu
2. Utwórz branch (`git checkout -b feature/AmazingFeature`)
3. Commit zmian (`git commit -m 'Add some AmazingFeature'`)
4. Push do branch (`git push origin feature/AmazingFeature`)
5. Otwórz Pull Request

## 📄 Licencja

Ten projekt jest licencjonowany na licencji MIT - zobacz plik [LICENSE](LICENSE) dla szczegółów.

## 👥 Autorzy

- **Sławek** - *Główny deweloper* - [GitHub](https://github.com/slawek)

## 🙏 Podziękowania

- [Laravel](https://laravel.com/) - Framework PHP
- [Filament](https://filamentphp.com/) - Panel administracyjny
- [Spatie](https://spatie.be/) - Pakiety Laravel
- [InPost](https://inpost.pl/) - Usługi kurierskie
- [SMSAPI](https://smsapi.pl/) - Usługi SMS

## 📞 Kontakt

- **Email:** admin@example.com
- **GitHub:** [@slawek](https://github.com/slawek)
- **Projekt:** [Panel Administracyjny](https://github.com/your-username/panel-admin-cursor)

---

⭐ Jeśli projekt Ci się podoba, zostaw gwiazdkę!