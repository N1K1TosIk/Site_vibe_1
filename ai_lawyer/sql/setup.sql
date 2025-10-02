-- Создание базы данных AI-юриста
CREATE DATABASE IF NOT EXISTS ai_lawyer_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ai_lawyer_db;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    company VARCHAR(255) DEFAULT '',
    phone VARCHAR(50) DEFAULT '',
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    failed_login_attempts INT DEFAULT 0,
    last_failed_login TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    reset_token VARCHAR(64) NULL,
    reset_token_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица сессий пользователей
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(128) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id)
);

-- Таблица шаблонов договоров
CREATE TABLE IF NOT EXISTS contract_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category ENUM('rental', 'service', 'supply', 'employment', 'nda', 'other') NOT NULL,
    description TEXT,
    template_content LONGTEXT NOT NULL,
    variables JSON, -- Переменные для заполнения
    created_by INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Таблица документов пользователей
CREATE TABLE IF NOT EXISTS user_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    document_type ENUM('generated', 'uploaded', 'analyzed') NOT NULL,
    original_filename VARCHAR(255),
    file_path VARCHAR(500),
    file_size INT,
    mime_type VARCHAR(100),
    template_id INT,
    document_data JSON, -- Данные документа и анализа
    status ENUM('draft', 'completed', 'analyzed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES contract_templates(id) ON DELETE SET NULL
);

-- Таблица анализа документов
CREATE TABLE IF NOT EXISTS document_analysis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    risks JSON, -- Выявленные риски
    suggestions JSON, -- Предложения по исправлению
    legal_issues JSON, -- Юридические проблемы
    clarity_issues JSON, -- Проблемы ясности формулировок
    analysis_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    analyzed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_id) REFERENCES user_documents(id) ON DELETE CASCADE
);

-- Таблица логов действий
CREATE TABLE IF NOT EXISTS action_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Вставка базовых шаблонов договоров
INSERT INTO contract_templates (name, category, description, template_content, variables) VALUES 
('Договор аренды помещения', 'rental', 'Стандартный договор аренды нежилого помещения', '
ДОГОВОР АРЕНДЫ НЕЖИЛОГО ПОМЕЩЕНИЯ

г. {{city}}, {{date}}

{{lessor_name}}, именуемый в дальнейшем "Арендодатель", с одной стороны, и {{lessee_name}}, именуемый в дальнейшем "Арендатор", с другой стороны, заключили настоящий договор о нижеследующем:

1. ПРЕДМЕТ ДОГОВОРА
1.1. Арендодатель предоставляет, а Арендатор принимает в аренду помещение общей площадью {{area}} кв.м., расположенное по адресу: {{address}}.

2. СРОК ДОГОВОРА
2.1. Договор заключается на срок {{term}}.
2.2. Договор вступает в силу с {{start_date}}.

3. АРЕНДНАЯ ПЛАТА
3.1. Размер арендной платы составляет {{rent_amount}} рублей в месяц.
3.2. Арендная плата вносится до {{payment_day}} числа каждого месяца.

4. ПРАВА И ОБЯЗАННОСТИ СТОРОН
4.1. Арендодатель обязуется:
- Передать Арендатору помещение в состоянии, пригодном для использования;
- Обеспечивать надлежащее содержание общего имущества.

4.2. Арендатор обязуется:
- Использовать помещение по назначению;
- Своевременно вносить арендную плату;
- Содержать помещение в надлежащем состоянии.

5. ОТВЕТСТВЕННОСТЬ СТОРОН
5.1. За просрочку внесения арендной платы Арендатор уплачивает пеню в размере 0,1% от суммы задолженности за каждый день просрочки.

6. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ
6.1. Договор составлен в двух экземплярах, имеющих одинаковую юридическую силу.

Арендодатель: _________________ {{lessor_name}}
Арендатор: ___________________ {{lessee_name}}
', JSON_OBJECT(
    'city', 'Город заключения договора',
    'date', 'Дата заключения договора', 
    'lessor_name', 'ФИО/наименование арендодателя',
    'lessee_name', 'ФИО/наименование арендатора',
    'area', 'Площадь помещения в кв.м',
    'address', 'Адрес помещения',
    'term', 'Срок аренды',
    'start_date', 'Дата начала аренды',
    'rent_amount', 'Размер арендной платы',
    'payment_day', 'День внесения платежа'
)),

('Договор оказания услуг', 'service', 'Универсальный договор на оказание услуг', '
ДОГОВОР ОКАЗАНИЯ УСЛУГ

г. {{city}}, {{date}}

{{provider_name}}, именуемый в дальнейшем "Исполнитель", с одной стороны, и {{client_name}}, именуемый в дальнейшем "Заказчик", с другой стороны, заключили настоящий договор о нижеследующем:

1. ПРЕДМЕТ ДОГОВОРА
1.1. Исполнитель обязуется оказать следующие услуги: {{services_description}}.
1.2. Заказчик обязуется принять и оплатить оказанные услуги.

2. СТОИМОСТЬ УСЛУГ И ПОРЯДОК РАСЧЕТОВ
2.1. Стоимость услуг составляет {{total_amount}} рублей.
2.2. Оплата производится {{payment_terms}}.

3. СРОКИ ВЫПОЛНЕНИЯ
3.1. Услуги должны быть оказаны до {{deadline}}.

4. ПРАВА И ОБЯЗАННОСТИ СТОРОН
4.1. Исполнитель обязуется:
- Оказать услуги качественно и в срок;
- Соблюдать конфиденциальность полученной информации.

4.2. Заказчик обязуется:
- Предоставить необходимую информацию для оказания услуг;
- Своевременно произвести оплату.

5. ОТВЕТСТВЕННОСТЬ СТОРОН
5.1. За просрочку оплаты Заказчик уплачивает пеню в размере 0,1% от суммы задолженности за каждый день просрочки.

6. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ
6.1. Договор вступает в силу с момента подписания и действует до полного исполнения обязательств сторонами.

Исполнитель: _________________ {{provider_name}}
Заказчик: ___________________ {{client_name}}
', JSON_OBJECT(
    'city', 'Город заключения договора',
    'date', 'Дата заключения договора',
    'provider_name', 'ФИО/наименование исполнителя', 
    'client_name', 'ФИО/наименование заказчика',
    'services_description', 'Описание оказываемых услуг',
    'total_amount', 'Стоимость услуг',
    'payment_terms', 'Условия оплаты',
    'deadline', 'Срок выполнения услуг'
)); 