# Безопасность

## ⚠️ Если репозиторий был публичным

**OpenAI API ключ** ранее мог быть в истории коммитов. Если репозиторий публичный или был таковым:

1. **Немедленно отзовите ключ** в [OpenAI API Keys](https://platform.openai.com/api-keys)
2. Создайте новый ключ
3. Укажите его в `ai_lawyer/config/security.local.php` (скопируйте из `security.local.php.example`)

## Хранение секретов

- **Никогда** не коммитьте API ключи, пароли, токены в код
- Используйте `security.local.php` (в .gitignore) или переменные окружения
- Для production: меняйте `$pepper` в `ai_lawyer/config/security.php`
