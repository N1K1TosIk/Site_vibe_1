USE ai_lawyer_db;

-- Массовое обновление категорий
UPDATE contract_templates SET 
    category = CASE 
        WHEN name LIKE '%Агентский%' THEN 'commercial'
        WHEN name LIKE '%Брачный%' THEN 'family'
        WHEN name LIKE '%дарения%' THEN 'civil'
        WHEN name LIKE '%доверительного управления%' THEN 'trust'
        WHEN name LIKE '%займа%' THEN 'financial'
        WHEN name LIKE '%комиссии%' THEN 'commercial'
        WHEN name LIKE '%перевозки%' THEN 'transport'
        WHEN name LIKE '%страхования%' THEN 'financial'
        WHEN name LIKE '%строительного%' THEN 'construction'
        WHEN name LIKE '%франчайзинга%' THEN 'commercial'
        WHEN name LIKE '%Лицензионный%' THEN 'intellectual'
        WHEN name LIKE '%разделе имущества%' THEN 'family'
        WHEN name LIKE '%автомобиля%' THEN 'transport'
        WHEN name LIKE '%недвижимости%' THEN 'real_estate'
        WHEN name LIKE '%Трудовой%' THEN 'employment'
        WHEN name LIKE '%неразглашении%' THEN 'nda'
        WHEN name LIKE '%аренды нежилого%' THEN 'rental'
        WHEN name LIKE '%купли-продажи товара%' THEN 'supply'
        WHEN name LIKE '%оказания услуг%' THEN 'service'
        WHEN name LIKE '%подряда%' THEN 'other'
        ELSE category
    END
WHERE category IS NULL OR category = '';

-- Показать результат
SELECT category, COUNT(*) as count FROM contract_templates GROUP BY category ORDER BY category; 