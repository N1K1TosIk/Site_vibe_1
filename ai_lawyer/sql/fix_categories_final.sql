USE ai_lawyer_db;

-- Исправляем категории для всех шаблонов
UPDATE contract_templates SET category = 'financial' WHERE id = 43;  -- Договор банковского вклада
UPDATE contract_templates SET category = 'financial' WHERE id = 44;  -- Договор лизинга
UPDATE contract_templates SET category = 'commercial' WHERE id = 45; -- Договор поручения
UPDATE contract_templates SET category = 'real_estate' WHERE id = 46; -- Договор мены недвижимости
UPDATE contract_templates SET category = 'family' WHERE id = 47;     -- Соглашение об уплате алиментов
UPDATE contract_templates SET category = 'transport' WHERE id = 48;  -- Договор аренды транспортного средства
UPDATE contract_templates SET category = 'construction' WHERE id = 49; -- Договор на выполнение проектных работ
UPDATE contract_templates SET category = 'intellectual' WHERE id = 50; -- Договор авторского заказа
UPDATE contract_templates SET category = 'trust' WHERE id = 51;      -- Договор управления ценными бумагами
UPDATE contract_templates SET category = 'civil' WHERE id = 52;      -- Договор ренты
UPDATE contract_templates SET category = 'commercial' WHERE id = 53; -- Договор простого товарищества
UPDATE contract_templates SET category = 'financial' WHERE id = 54;  -- Договор факторинга
UPDATE contract_templates SET category = 'commercial' WHERE id = 55; -- Договор коммерческой концессии
UPDATE contract_templates SET category = 'real_estate' WHERE id = 56; -- Договор долевого участия в строительстве
UPDATE contract_templates SET category = 'family' WHERE id = 57;     -- Соглашение о порядке общения с ребенком

-- Проверяем результат
SELECT category, COUNT(*) as count FROM contract_templates GROUP BY category ORDER BY count DESC; 