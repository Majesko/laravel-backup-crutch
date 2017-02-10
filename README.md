## Костыль для настраиваемых уведомлений в spatie/laravel-backup v3	
### Шаги установки костыля:
1. подключить в composer.json:
```
"spatie/laravel-backup": "^3.0.0",
"league/flysystem-aws-s3-v3": "~1.0", // s3 storage
"irazasyed/telegram-bot-sdk": "~2.2"  // отправка telegram уведомлений
```
2. Подключить  laravel-backup: 
```php
'providers' => [
    // ...
    Spatie\Backup\BackupServiceProvider::class,
];
```
3. Опубликовать конфиг
```
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```
4. Добавить в .env секцию для s3, email и telegram:
```
# Backup configuration
TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=
NOTIFY_FROM_EMAIL=
NOTIFY_TO_EMAIL=
S3_BACKUP_KEY=
S3_BACKUP_SECRET=
S3_BACKUP_REGION=
S3_BACKUP_BUCKET=
```
5. В filesystem.php добавить хранилища:
```
'localBackup' => [
	'driver' => 'local',
	'root' => storage_path('backups'),
],
's3Backup' => [
	'driver' => 's3',
	'key' => env('S3_BACKUP_KEY'),
	'secret' => env('S3_BACKUP_SECRET'),
	'region' => env('S3_BACKUP_REGION'),
	'bucket' => env('S3_BACKUP_BUCKET'),
],
```
6. Скачать и развернуть в папке приложения.
7. Прописать биндинг в composer.json

#### В laravel-backup.php: 
1. прописать в секции email:
```php
'mail' => [
    'from' => env('NOTIFY_FROM_EMAIL'),
    'to'   => env('NOTIFY_TO_EMAIL'),
],
```
2. в telegram:
```
'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'chat_id'   => env('TELEGRAM_CHAT_ID'),
    'async_requests' => env('TELEGRAM_ASYNC_REQUESTS', false),
    'disable_web_page_preview' => env('TELEGRAM_DISABLE_WEB_PAGE_PREVIEW', true),
],
```
3. подключить хранилища:
```
'disks' => [
    'localBackup', 's3Backup'
],
```
4. прописать интересующие каналы уведомлений

5. Переопределить handler в секции notifications:
```
'handler' => \BackupNotifier\BackupNotifier::class, // класс указывается согласно биндингу в composer.json
```

#### В EventService Provider.php
Добавить слушатели событий:
```
$events->listen(
    BackupWasSuccessful::class,
    BackupNotifier::class.'@correctBackupWasSuccessful'
);

$events->listen(
    BackupHasFailed::class,
    BackupNotifier::class.'@correctBackupHasFailed'
);
```
Список команд доступен в artisan
