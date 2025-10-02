USE ai_lawyer_db;

-- Исправление категорий для всех шаблонов

UPDATE contract_templates SET category = 'commercial' WHERE name = 'Агентский договор';
UPDATE contract_templates SET category = 'family' WHERE name = 'Брачный договор';
UPDATE contract_templates SET category = 'civil' WHERE name = 'Договор дарения';
UPDATE contract_templates SET category = 'trust' WHERE name = 'Договор доверительного управления';
UPDATE contract_templates SET category = 'financial' WHERE name = 'Договор займа';
UPDATE contract_templates SET category = 'commercial' WHERE name = 'Договор комиссии';
UPDATE contract_templates SET category = 'transport' WHERE name = 'Договор перевозки грузов';
UPDATE contract_templates SET category = 'financial' WHERE name = 'Договор страхования';
UPDATE contract_templates SET category = 'construction' WHERE name = 'Договор строительного подряда';
UPDATE contract_templates SET category = 'commercial' WHERE name = 'Договор франчайзинга';
UPDATE contract_templates SET category = 'intellectual' WHERE name = 'Лицензионный договор';
UPDATE contract_templates SET category = 'family' WHERE name = 'Соглашение о разделе имущества';
UPDATE contract_templates SET category = 'transport' WHERE name = 'Договор купли-продажи автомобиля';
UPDATE contract_templates SET category = 'real_estate' WHERE name = 'Договор купли-продажи недвижимости';

-- Проверяем результат
SELECT name, category FROM contract_templates ORDER BY category, name; 