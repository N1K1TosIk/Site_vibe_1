# Site Vibe 1

**Многофункциональная веб-платформа на базе XAMPP** — объединяет образовательный тренажёр ЕГЭ по информатике и систему AI-юриста для создания и анализа юридических документов.

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![XAMPP](https://img.shields.io/badge/XAMPP-8.2-FB7A24?logo=apache&logoColor=white)](https://www.apachefriends.org/)

---

## 📋 Содержание

- [О проекте](#о-проекте)
- [Структура проекта](#структура-проекта)
- [Требования](#требования)
- [Установка](#установка)
- [Запуск](#запуск)
- [Модули](#модули)
- [Конфигурация](#конфигурация)
- [Разработка](#разработка)
- [Лицензия](#лицензия)

---

## О проекте

**Site Vibe 1** — это комплексное веб-приложение, развёрнутое на стеке **Apache + MariaDB + PHP** (XAMPP). Проект включает:

| Модуль | Описание |
|--------|----------|
| **exam_inf** | Локальный тренажёр ЕГЭ по информатике с заданиями с [inf-ege.sdamgia.ru](https://inf-ege.sdamgia.ru) |
| **ai_lawyer** | Система генерации договоров и AI-анализа юридических документов |
| **dashboard** | Стандартная панель XAMPP с мультиязычной поддержкой |

---

## Структура проекта

```
Site_vibe_1/
├── index.php              # Точка входа — редирект на /dashboard/
├── applications.html      # Страница приложений Bitnami
├── bitnami.css            # Стили Bitnami
├── my_simple.ini          # Конфигурация MySQL (упрощённая)
├── favicon.ico
│
├── exam_inf/              # 🎓 Тренажёр ЕГЭ по информатике
│   ├── app/
│   │   ├── Controllers/   # TaskController, ProgressController, CategoryController
│   │   ├── Models/        # Task, Database
│   │   ├── Views/         # Шаблоны страниц
│   │   └── bootstrap.php  # Роутинг и инициализация
│   ├── public/            # Публичная точка входа
│   ├── scripts/           # Скрапперы и утилиты
│   ├── sql/               # Схема БД
│   └── config.php
│
├── ai_lawyer/             # ⚖️ AI Юрист
│   ├── auth/              # Регистрация, вход, выход
│   ├── classes/           # DocumentProcessor, ContractGenerator, User и др.
│   ├── config/            # database.php, security.php
│   ├── dashboard/         # Генератор, анализатор, документы
│   ├── sql/               # Миграции и setup
│   ├── composer.json      # Зависимости PHP
│   └── setup.php          # Установщик БД
│
├── dashboard/             # Панель XAMPP (en, ru, de, fr, ...)
├── img/                   # Изображения
└── xampp/                 # Конфигурация XAMPP
```

---

## Требования

| Компонент | Версия |
|-----------|--------|
| **XAMPP** | 8.2+ (Apache + MariaDB + PHP) |
| **PHP** | 7.4+ (рекомендуется 8.0+) |
| **MySQL/MariaDB** | 10.x |
| **Расширения PHP** | mbstring, json, zip, gd, pdo_mysql |

Для **ai_lawyer** дополнительно:
- **Composer**
- Расширения: `imagick` (опционально), `gd`

---

## Установка

### 1. Клонирование репозитория

```bash
git clone https://github.com/N1K1TosIk/Site_vibe_1.git
cd Site_vibe_1
```

### 2. Размещение в XAMPP

Скопируйте содержимое проекта в корень веб-сервера XAMPP:

- **Windows:** `C:\xampp\htdocs\`
- **Linux:** `/opt/lampp/htdocs/`
- **macOS:** `/Applications/XAMPP/htdocs/`

Либо настройте виртуальный хост (см. [Конфигурация](#конфигурация)).

### 3. Установка exam_inf

```powershell
# Импорт схемы БД
& "C:\xampp\mysql\bin\mysql.exe" -uroot --password= -e "SOURCE C:/xampp/htdocs/Site_vibe_1/exam_inf/sql/schema.sql;"

# Загрузка задач (диапазон ID 1–100)
cd C:\xampp\htdocs\Site_vibe_1\exam_inf\scripts
& "C:\xampp\php\php.exe" scrape_tasks.php 1 100
```

### 4. Установка ai_lawyer

```bash
cd ai_lawyer
composer install
```

Затем откройте в браузере:

```
http://localhost/Site_vibe_1/ai_lawyer/setup.php
```

Нажмите **«Установить базу данных»** — будут созданы таблицы и тестовые шаблоны.

---

## Запуск

1. Запустите **Apache** и **MySQL** в XAMPP Control Panel.
2. Откройте в браузере:

| Приложение | URL |
|------------|-----|
| XAMPP Dashboard | `http://localhost/Site_vibe_1/dashboard/` |
| Тренажёр ЕГЭ | `http://localhost/Site_vibe_1/exam_inf/public/` |
| AI Юрист | `http://localhost/Site_vibe_1/ai_lawyer/` |

> **Примечание:** Для exam_inf рекомендуется настроить виртуальный хост `exam_inf.local` (см. `exam_inf/README.md`).

---

## Модули

### 🎓 exam_inf — Тренажёр ЕГЭ по информатике

Локальная копия функционала [inf-ege.sdamgia.ru](https://inf-ege.sdamgia.ru).

**Возможности:**
- Скачивание заданий с официального сайта и хранение в MySQL
- Фильтрация по категории, году, сложности
- Решение задач с мгновенной проверкой (Верно/Неверно)
- Просмотр подробных решений
- Отслеживание прогресса в сессии

**Скрапперы:**
- `scrape_tasks.php` — загрузка задач по диапазону ID
- `scrape_bank_task1.php` — загрузка заданий из банка
- `scrape_all.php` — массовая загрузка

### ⚖️ ai_lawyer — AI Юрист

Система для создания и анализа юридических документов.

**Возможности:**
- Регистрация и авторизация (Argon2ID, CSRF, rate limiting)
- Генератор договоров из шаблонов (аренда, услуги, поставка, NDA и др.)
- Анализатор документов (риски, соответствие ГК РФ/ТК РФ)
- Обработка DOCX, DOC, TXT, RTF
- Экспорт в DOCX

**Безопасность:**
- Хеширование паролей Argon2ID
- Защита от CSRF
- Rate limiting (5 попыток / 15 мин)
- Логирование действий

---

## Конфигурация

### exam_inf

Файл `exam_inf/config.php`:

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'exam_inf_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://exam_inf.local');  // или http://localhost/...
```

### ai_lawyer

- **База данных:** `config/database.php`
- **Безопасность:** `config/security.php` (pepper, настройки сессий)

### Виртуальный хост для exam_inf

**httpd-vhosts.conf:**
```apache
<VirtualHost *:80>
    ServerName exam_inf.local
    DocumentRoot "C:/xampp/htdocs/Site_vibe_1/exam_inf/public"
    <Directory "C:/xampp/htdocs/Site_vibe_1/exam_inf/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**hosts** (C:\Windows\System32\drivers\etc\hosts):
```
127.0.0.1 exam_inf.local
```

---

## Разработка

### Расширение exam_inf

- **Новые фильтры:** `app/Models/Task.php` + `app/Views/tasks.php`
- **Аутентификация:** интеграция PHP-Auth, таблица `user_progress`
- **Кэширование изображений:** скачивание картинок при парсинге

### Расширение ai_lawyer

- **Внешние AI API:** интеграция GPT-4, Claude (см. `classes/OpenAIAnalyzer.php`)
- **Электронная подпись**
- **Мобильное приложение**

### Резервное копирование

```powershell
# exam_inf
& "C:\xampp\mysql\bin\mysqldump.exe" -uroot --password= exam_inf_db > exam_inf_backup.sql

# ai_lawyer
& "C:\xampp\mysql\bin\mysqldump.exe" -uroot --password= ai_lawyer_db > ai_lawyer_backup.sql
```

---

## Устранение неполадок

| Проблема | Решение |
|----------|---------|
| Ошибка подключения к БД | Запустите MySQL в XAMPP |
| Class not found (ai_lawyer) | `composer dump-autoload` |
| Permission denied | Права на `temp/`, `uploads/documents/`, `logs/` |
| Задачи не загружаются | Проверьте доступ к inf-ege.sdamgia.ru, задержка 1 сек между запросами |

---

## Лицензия

Проект создан для образовательных и демонстрационных целей.

- **exam_inf:** Контент заданий принадлежит авторам [inf-ege.sdamgia.ru](https://inf-ege.sdamgia.ru). Только для личного учебного использования.
- **ai_lawyer:** Используйте на свой страх и риск.
- **XAMPP/Dashboard:** Apache Friends, Bitnami — соответствующие лицензии.

---

## Ссылки

- [Репозиторий](https://github.com/N1K1TosIk/Site_vibe_1)
- [XAMPP](https://www.apachefriends.org/)
- [inf-ege.sdamgia.ru](https://inf-ege.sdamgia.ru)

---

**Site Vibe 1** — Образование и автоматизация в одном проекте 🚀
