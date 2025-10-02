USE ai_lawyer_db;

-- Удаляем поля подписей из переменных трудового договора
UPDATE contract_templates SET 
variables = JSON_REMOVE(
    JSON_REMOVE(variables, '$.employer_signature'),
    '$.employee_signature'
)
WHERE id = 23;

-- Удаляем поля подписей из переменных договора аренды
UPDATE contract_templates SET 
variables = JSON_REMOVE(
    JSON_REMOVE(variables, '$.landlord_signature'),
    '$.tenant_signature'
)
WHERE id = 25;

-- Удаляем поля подписей из переменных договора услуг
UPDATE contract_templates SET 
variables = JSON_REMOVE(
    JSON_REMOVE(variables, '$.provider_signature'),
    '$.client_signature'
)
WHERE id = 26;

-- Удаляем поля подписей из переменных договора подряда
UPDATE contract_templates SET 
variables = JSON_REMOVE(
    JSON_REMOVE(variables, '$.contractor_signature'),
    '$.client_signature'
)
WHERE id = 27;

-- Удаляем поля подписей из переменных договора купли-продажи
UPDATE contract_templates SET 
variables = JSON_REMOVE(
    JSON_REMOVE(variables, '$.seller_signature'),
    '$.buyer_signature'
)
WHERE id = 28;

-- Удаляем поля подписей из переменных соглашения о неразглашении
UPDATE contract_templates SET 
variables = JSON_REMOVE(
    JSON_REMOVE(variables, '$.disclosing_signature'),
    '$.receiving_signature'
)
WHERE id = 24;

-- Удаляем поля подписей из переменных договора поставки
UPDATE contract_templates SET 
variables = JSON_REMOVE(
    JSON_REMOVE(variables, '$.supplier_signature'),
    '$.buyer_signature'
)
WHERE id = 7; 