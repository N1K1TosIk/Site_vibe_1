# Руководство по установке Document Processing System v2.0

## 🚀 Быстрая установка

### Шаг 1: Проверка требований

Убедитесь, что у вас установлены:
- PHP 7.4 или выше
- Composer
- Расширения PHP: mbstring, json, zip

```bash
# Проверка версии PHP
php --version

# Проверка Composer
composer --version

# Проверка расширений PHP
php -m | grep -E "(mbstring|json|zip)"
```

### Шаг 2: Установка зависимостей

```bash
cd ai_lawyer
composer install
```

### Шаг 3: Проверка установки

```bash
# Запуск тестов
php tests/DocumentProcessorTest.php

# Запуск примера (если есть тестовые файлы)
php examples/document_processing_example.php
```

## 📋 Подробная установка

### 1. Подготовка системы

#### Windows (XAMPP)
1. Убедитесь, что XAMPP запущен
2. Откройте командную строку в директории проекта
3. Проверьте, что PHP доступен в PATH

#### Linux/Mac
```bash
# Установка Composer (если не установлен)
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Установка расширений PHP
sudo apt-get install php-mbstring php-json php-zip  # Ubuntu/Debian
brew install php@7.4  # Mac (с Homebrew)
```

### 2. Установка зависимостей

```bash
# Переход в директорию проекта
cd ai_lawyer

# Установка зависимостей
composer install --no-dev  # Для продакшена
composer install  # Для разработки (включает тесты)
```

### 3. Настройка прав доступа

```bash
# Создание директории для логов
mkdir -p logs
chmod 755 logs

# Создание директории для загрузок (если не существует)
mkdir -p uploads/documents
chmod 755 uploads/documents
```

### 4. Проверка установки

#### Запуск тестов
```bash
php tests/DocumentProcessorTest.php
```

Ожидаемый вывод:
```
=== Document Processing System Tests ===

Testing system initialization...
✓ Supported formats: docx, doc, txt, rtf
✓ Maximum file size: 50.0 MB

Testing format detection...
✓ document.docx: supported
✓ document.docx: supported
✓ document.doc: supported
✓ document.txt: supported
✓ document.rtf: supported
✓ document.jpg: not supported
✓ document.exe: not supported

Testing error handling...
✓ Correctly handled non-existent file: File does not exist: /non/existent/file.docx
✓ Correctly handled unsupported format: Unsupported file format: jpg

Testing DocumentResult functionality...
✓ Content setting and retrieval works
✓ Metadata setting and retrieval works
✓ Statistics setting and retrieval works
✓ Word count calculation works
✓ Success status works correctly
✓ Error handling works correctly

=== Test Results ===
Passed: 4/4
✓ All tests passed! System is working correctly.
```

## 🔧 Интеграция с существующим кодом

### Замена старого кода

#### Старый код (v1.0):
```php
require_once 'classes/TextExtractor.php';
$text = TextExtractor::extractText($filePath, $fileType);
```

#### Новый код (v2.0):
```php
require_once 'vendor/autoload.php';
use AILawyer\Classes\DocumentProcessor;

$processor = new DocumentProcessor();
$result = $processor->processDocument($filePath);
$text = $result->getContent();
```

### Обновление существующих файлов

1. **dashboard/analyzer.php** - обновить для использования нового API
2. **dashboard/documents.php** - обновить для использования нового API
3. **dashboard/get_document_content.php** - обновить для использования нового API

## 🧪 Тестирование

### Создание тестовых файлов

Создайте тестовые документы в `uploads/documents/`:

```bash
# Создание тестового текстового файла
echo "Это тестовый документ для проверки системы обработки." > uploads/documents/sample.txt

# Создание тестового DOCX (если у вас есть DOCX файл)
cp /path/to/your/document.docx uploads/documents/sample.docx

# Создание тестового Word документа (если у вас есть DOCX файл)
cp /path/to/your/document.docx uploads/documents/sample.docx
```

### Запуск примеров

```bash
# Запуск основного примера
php examples/document_processing_example.php

# Запуск тестов
php tests/DocumentProcessorTest.php
```

## 🚨 Устранение неполадок

### Ошибка: "Class not found"
```bash
# Переустановите автозагрузчик
composer dump-autoload
```

### Ошибка: "Extension not loaded"
```bash
# Проверьте, что расширения PHP установлены
php -m | grep mbstring
php -m | grep json
php -m | grep zip

# Если расширения отсутствуют, установите их
# Windows: раскомментируйте строки в php.ini
# Linux: sudo apt-get install php-mbstring php-json php-zip
```

### Ошибка: "Permission denied"
```bash
# Установите правильные права доступа
chmod 755 logs
chmod 755 uploads/documents
chmod 644 logs/*.log 2>/dev/null || true
```

### Ошибка: "Memory limit exceeded"
```bash
# Увеличьте лимит памяти в php.ini
memory_limit = 256M

# Или временно в коде
ini_set('memory_limit', '256M');
```

## 📊 Мониторинг

### Проверка логов

```bash
# Просмотр логов обработки документов
tail -f logs/document_processing.log

# Поиск ошибок
grep "ERROR" logs/document_processing.log
```

### Проверка производительности

```bash
# Запуск теста производительности
php examples/document_processing_example.php | grep "Processing time"
```

## 🔄 Обновление

### Обновление зависимостей

```bash
composer update
```

### Обновление системы

```bash
# Получение последних изменений
git pull origin main

# Обновление зависимостей
composer install

# Запуск тестов
php tests/DocumentProcessorTest.php
```

## 📞 Поддержка

### Полезные команды

```bash
# Проверка версии PHP
php --version

# Проверка установленных расширений
php -m

# Проверка конфигурации PHP
php --ini

# Проверка Composer
composer --version

# Проверка зависимостей
composer show
```

### Логи и отладка

```bash
# Включение отладки в PHP
php -d display_errors=1 -d error_reporting=E_ALL your_script.php

# Просмотр логов в реальном времени
tail -f logs/document_processing.log
```

## ✅ Чек-лист установки

- [ ] PHP 7.4+ установлен
- [ ] Composer установлен
- [ ] Расширения PHP (mbstring, json, zip) установлены
- [ ] Зависимости установлены (`composer install`)
- [ ] Директории созданы (logs, uploads/documents)
- [ ] Права доступа настроены
- [ ] Тесты проходят (`php tests/DocumentProcessorTest.php`)
- [ ] Примеры работают (`php examples/document_processing_example.php`)
- [ ] Логи создаются в `logs/document_processing.log`

## 🎉 Готово!

После успешного прохождения всех пунктов чек-листа система готова к использованию!

Теперь вы можете использовать новую систему обработки документов в вашем приложении AI Lawyer. 