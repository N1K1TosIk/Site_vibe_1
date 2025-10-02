USE ai_lawyer_db;

-- Обновляем категории для всех шаблонов
UPDATE contract_templates SET category = 'financial' WHERE name = 'Договор банковского вклада';
UPDATE contract_templates SET category = 'financial' WHERE name = 'Договор лизинга';
UPDATE contract_templates SET category = 'financial' WHERE name = 'Договор факторинга';

UPDATE contract_templates SET category = 'commercial' WHERE name = 'Договор поручения';
UPDATE contract_templates SET category = 'commercial' WHERE name = 'Договор простого товарищества';
UPDATE contract_templates SET category = 'commercial' WHERE name = 'Договор коммерческой концессии';

UPDATE contract_templates SET category = 'real_estate' WHERE name = 'Договор мены недвижимости';
UPDATE contract_templates SET category = 'real_estate' WHERE name = 'Договор долевого участия в строительстве';

UPDATE contract_templates SET category = 'family' WHERE name = 'Соглашение об уплате алиментов';
UPDATE contract_templates SET category = 'family' WHERE name = 'Соглашение о порядке общения с ребенком';

UPDATE contract_templates SET category = 'transport' WHERE name = 'Договор аренды транспортного средства';
UPDATE contract_templates SET category = 'construction' WHERE name = 'Договор на выполнение проектных работ';
UPDATE contract_templates SET category = 'intellectual' WHERE name = 'Договор авторского заказа';
UPDATE contract_templates SET category = 'trust' WHERE name = 'Договор управления ценными бумагами';
UPDATE contract_templates SET category = 'civil' WHERE name = 'Договор ренты';

-- Проверяем результат
SELECT category, COUNT(*) as count FROM contract_templates GROUP BY category ORDER BY count DESC; 