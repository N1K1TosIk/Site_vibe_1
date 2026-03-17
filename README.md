# Site Vibe 1

**Веб-платформа на базе XAMPP** — система AI-юриста для создания и анализа юридических документов.

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
- [Конфигурация](#конфигурация)
- [Разработка](#разработка)
- [Лицензия](#лицензия)

---

## О проекте

**Site Vibe 1** — веб-приложение на стеке **Apache + MariaDB + PHP** (XAMPP). Включает:

| Модуль | Описание |
|--------|----------|
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

### 3. Установка ai_lawyer

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
| AI Юрист | `http://localhost/Site_vibe_1/ai_lawyer/` |

---

## Модули

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

### ai_lawyer

- **База данных:** `config/database.php`
- **Безопасность:** `config/security.php` (pepper, настройки сессий)
- **API ключи:** `config/security.local.php` (см. `security.local.php.example`)

---

## Разработка

### Расширение ai_lawyer

- **Внешние AI API:** интеграция GPT-4, Claude (см. `classes/OpenAIAnalyzer.php`)
- **Электронная подпись**
- **Мобильное приложение**

### Резервное копирование

```powershell
& "C:\xampp\mysql\bin\mysqldump.exe" -uroot --password= ai_lawyer_db > ai_lawyer_backup.sql
```

---

## Устранение неполадок

| Проблема | Решение |
|----------|---------|
| Ошибка подключения к БД | Запустите MySQL в XAMPP |
| Class not found (ai_lawyer) | `composer dump-autoload` |
| Permission denied | Права на `temp/`, `uploads/documents/`, `logs/` |

---

## Лицензия

Проект создан для образовательных и демонстрационных целей.

- **ai_lawyer:** Используйте на свой страх и риск.
- **XAMPP/Dashboard:** Apache Friends, Bitnami — соответствующие лицензии.

---

## Ссылки

- [Репозиторий](https://github.com/N1K1TosIk/Site_vibe_1)
- [XAMPP](https://www.apachefriends.org/)

---

**Site Vibe 1** — AI-автоматизация юридических процессов 🚀
