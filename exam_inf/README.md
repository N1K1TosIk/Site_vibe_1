# exam_inf — локальный тренажёр ЕГЭ по информатике

Этот мини-сайт развёрнут на XAMPP и копирует функционал ресурса [inf-ege.sdamgia.ru](https://inf-ege.sdamgia.ru). 
Позволяет:
1. Скачивать задания с официального сайта и хранить у себя в MySQL
2. Просматривать список задач с фильтрами (категория / год / сложность)
3. Отвечать на задание, сразу видеть «Верно / Неверно» и подробное решение
4. Отслеживать личный прогресс в сессии

## 1. Требования
* XAMPP (Apache + MariaDB + PHP ≥ 8.0) уже установлен
* Виртуальный хост `exam_inf.local` настроен (см. `httpd-vhosts.conf` и hosts файл)

## 2. Установка
```powershell
# 1. Импортируем структуру БД
& "C:\xampp\mysql\bin\mysql.exe" -uroot --password= -e "SOURCE C:/xampp/htdocs/exam_inf/sql/schema.sql;"

# 2. Загружаем первые задачи (пример id 1–100)
cd C:\xampp\htdocs\exam_inf\scripts
& "C:\xampp\php\php.exe" scrape_tasks.php 1 100
```
Скрипт `scrape_tasks.php` можно вызывать много раз, указывая диапазон ID.

## 3. Запуск
1. Запустите Apache и MySQL в XAMPP Control Panel
2. Перейдите в браузере по адресу `http://exam_inf.local/public/`

## 4. Структура проекта
```
exam_inf/
├── app/              # MVC-код (Controllers, Models, Views)
├── public/           # Корень сайта (index.php)
├── scripts/          # Консольные утилиты (скраппер)
├── sql/              # SQL-миграции (schema.sql)
└── config.php        # Настройки подключения к БД / BASE_URL
```

## 5. Расширение функционала
* **Добавление фильтров**: в `app/Models/Task.php` добавить условие и отобразить в `app/Views/tasks.php`
* **Аутентификация**: подключить библиотеку (например, PHP-Auth) и хранить прогресс в таблице `user_progress`
* **Кэширование изображений**: при парсинге скачивать картинки локально, а не грузить с оригинального сайта.

## 6. Экспорт / резервное копирование
Обычным дампом:
```powershell
& "C:\xampp\mysql\bin\mysqldump.exe" -uroot --password= exam_inf_db > exam_inf_backup.sql
```

## 7. Лицензия
Только для личного учебного использования. Контент задач принадлежит их авторам.
