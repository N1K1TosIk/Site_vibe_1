USE ai_lawyer_db;

-- Финальное исправление всех категорий
UPDATE contract_templates SET category = 'financial' WHERE id = 3;     -- Договор займа
UPDATE contract_templates SET category = 'civil' WHERE id = 4;         -- Договор дарения
UPDATE contract_templates SET category = 'real_estate' WHERE id = 5;   -- Договор купли-продажи недвижимости
UPDATE contract_templates SET category = 'commercial' WHERE id = 6;    -- Агентский договор
UPDATE contract_templates SET category = 'construction' WHERE id = 8;  -- Договор строительного подряда
UPDATE contract_templates SET category = 'intellectual' WHERE id = 9;  -- Лицензионный договор
UPDATE contract_templates SET category = 'family' WHERE id = 10;       -- Брачный договор
UPDATE contract_templates SET category = 'transport' WHERE id = 12;    -- Договор перевозки грузов
UPDATE contract_templates SET category = 'trust' WHERE id = 13;        -- Договор доверительного управления
UPDATE contract_templates SET category = 'commercial' WHERE id = 14;   -- Договор комиссии
UPDATE contract_templates SET category = 'family' WHERE id = 16;       -- Соглашение о разделе имущества
UPDATE contract_templates SET category = 'transport' WHERE id = 18;    -- Договор купли-продажи автомобиля
UPDATE contract_templates SET category = 'commercial' WHERE id = 19;   -- Договор франчайзинга
UPDATE contract_templates SET category = 'financial' WHERE id = 20;    -- Договор страхования

-- Добавим недостающие шаблоны, если их нет
INSERT IGNORE INTO contract_templates (name, category, description, template_content, variables) VALUES 
('Трудовой договор', 'employment', 'Трудовой договор с работником', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ТРУДОВОЙ ДОГОВОР № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{employer_name}}, именуемый в дальнейшем "Работодатель", с одной стороны, и {{employee_name}}, именуемый(-ая) в дальнейшем "Работник", с другой стороны, заключили настоящий трудовой договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ОБЩИЕ ПОЛОЖЕНИЯ
</div>

<p style="text-align: justify;">
1.1. Работник принимается на работу в {{department}} на должность {{position}}.
</p>

<p style="text-align: justify;">
1.2. Место работы: {{workplace}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ТРУДОВАЯ ФУНКЦИЯ
</div>

<p style="text-align: justify;">
2.1. Работник обязуется выполнять трудовую функцию: {{job_duties}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ОПЛАТА ТРУДА
</div>

<p style="text-align: justify;">
3.1. Размер заработной платы составляет {{salary}} рублей в месяц.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Работодатель:</strong><br/>
{{employer_name}}<br/>
{{employer_address}}<br/>
{{employer_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{employer_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Работник:</strong><br/>
{{employee_name}}<br/>
{{employee_address}}<br/>
{{employee_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{employee_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения',
    'contract_date', 'Дата заключения',
    'employer_name', 'Наименование работодателя',
    'employee_name', 'ФИО работника',
    'department', 'Подразделение',
    'position', 'Должность',
    'workplace', 'Место работы',
    'job_duties', 'Трудовые обязанности',
    'salary', 'Размер заработной платы',
    'employer_address', 'Адрес работодателя',
    'employer_phone', 'Телефон работодателя',
    'employee_address', 'Адрес работника',
    'employee_phone', 'Телефон работника',
    'employer_signature', 'Подпись работодателя',
    'employee_signature', 'Подпись работника'
)),

('Соглашение о неразглашении информации', 'nda', 'Соглашение о неразглашении конфиденциальной информации', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
СОГЛАШЕНИЕ О НЕРАЗГЛАШЕНИИ ИНФОРМАЦИИ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{disclosing_party}}, именуемая в дальнейшем "Сторона, раскрывающая информацию", с одной стороны, и {{receiving_party}}, именуемая в дальнейшем "Сторона, получающая информацию", с другой стороны, заключили настоящее соглашение о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ СОГЛАШЕНИЯ
</div>

<p style="text-align: justify;">
1.1. Конфиденциальной информацией является: {{confidential_info}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ОБЯЗАТЕЛЬСТВА СТОРОН
</div>

<p style="text-align: justify;">
2.1. Получающая сторона обязуется не разглашать полученную информацию третьим лицам.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. СРОК ДЕЙСТВИЯ
</div>

<p style="text-align: justify;">
3.1. Обязательство по неразглашению действует в течение {{confidentiality_period}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Раскрывающая сторона:</strong><br/>
{{disclosing_party}}<br/>
{{disclosing_address}}<br/>
{{disclosing_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{disclosing_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Получающая сторона:</strong><br/>
{{receiving_party}}<br/>
{{receiving_address}}<br/>
{{receiving_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{receiving_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения',
    'contract_date', 'Дата заключения',
    'disclosing_party', 'Раскрывающая сторона',
    'receiving_party', 'Получающая сторона',
    'confidential_info', 'Описание конфиденциальной информации',
    'confidentiality_period', 'Срок конфиденциальности',
    'disclosing_address', 'Адрес раскрывающей стороны',
    'disclosing_phone', 'Телефон раскрывающей стороны',
    'receiving_address', 'Адрес получающей стороны',
    'receiving_phone', 'Телефон получающей стороны',
    'disclosing_signature', 'Подпись раскрывающей стороны',
    'receiving_signature', 'Подпись получающей стороны'
)),

('Договор аренды нежилого помещения', 'rental', 'Договор аренды коммерческой недвижимости', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР АРЕНДЫ НЕЖИЛОГО ПОМЕЩЕНИЯ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{landlord_name}}, именуемый в дальнейшем "Арендодатель", с одной стороны, и {{tenant_name}}, именуемый в дальнейшем "Арендатор", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Арендодатель сдает, а Арендатор принимает в аренду нежилое помещение площадью {{area}} кв.м, расположенное по адресу: {{address}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. АРЕНДНАЯ ПЛАТА
</div>

<p style="text-align: justify;">
2.1. Размер арендной платы составляет {{rent_amount}} рублей в месяц.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Арендодатель:</strong><br/>
{{landlord_name}}<br/>
{{landlord_address}}<br/>
{{landlord_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{landlord_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Арендатор:</strong><br/>
{{tenant_name}}<br/>
{{tenant_address}}<br/>
{{tenant_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{tenant_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения',
    'contract_date', 'Дата заключения',
    'landlord_name', 'Наименование арендодателя',
    'tenant_name', 'Наименование арендатора',
    'area', 'Площадь помещения',
    'address', 'Адрес помещения',
    'rent_amount', 'Размер арендной платы',
    'landlord_address', 'Адрес арендодателя',
    'landlord_phone', 'Телефон арендодателя',
    'tenant_address', 'Адрес арендатора',
    'tenant_phone', 'Телефон арендатора',
    'landlord_signature', 'Подпись арендодателя',
    'tenant_signature', 'Подпись арендатора'
)),

('Договор оказания услуг', 'service', 'Договор на оказание различных услуг', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ОКАЗАНИЯ УСЛУГ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{customer_name}}, именуемый в дальнейшем "Заказчик", с одной стороны, и {{provider_name}}, именуемый в дальнейшем "Исполнитель", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Исполнитель обязуется оказать услуги: {{services_description}}, а Заказчик обязуется принять и оплатить эти услуги.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Заказчик:</strong><br/>
{{customer_name}}<br/>
{{customer_address}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{customer_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Исполнитель:</strong><br/>
{{provider_name}}<br/>
{{provider_address}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{provider_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения',
    'contract_date', 'Дата заключения',
    'customer_name', 'Наименование заказчика',
    'provider_name', 'Наименование исполнителя',
    'services_description', 'Описание услуг',
    'customer_address', 'Адрес заказчика',
    'provider_address', 'Адрес исполнителя',
    'customer_signature', 'Подпись заказчика',
    'provider_signature', 'Подпись исполнителя'
)),

('Договор подряда', 'other', 'Договор на выполнение подрядных работ', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ПОДРЯДА № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{customer_name}}, именуемый в дальнейшем "Заказчик", с одной стороны, и {{contractor_name}}, именуемый в дальнейшем "Подрядчик", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Подрядчик обязуется выполнить работы: {{work_description}}, а Заказчик обязуется принять и оплатить выполненные работы.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Заказчик:</strong><br/>
{{customer_name}}<br/>
{{customer_address}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{customer_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Подрядчик:</strong><br/>
{{contractor_name}}<br/>
{{contractor_address}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{contractor_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения',
    'contract_date', 'Дата заключения',
    'customer_name', 'Наименование заказчика',
    'contractor_name', 'Наименование подрядчика',
    'work_description', 'Описание работ',
    'customer_address', 'Адрес заказчика',
    'contractor_address', 'Адрес подрядчика',
    'customer_signature', 'Подпись заказчика',
    'contractor_signature', 'Подпись подрядчика'
)),

('Договор купли-продажи товара', 'supply', 'Договор купли-продажи товара между организациями', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР КУПЛИ-ПРОДАЖИ ТОВАРА № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{seller_name}}, именуемый в дальнейшем "Продавец", с одной стороны, и {{buyer_name}}, именуемый в дальнейшем "Покупатель", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Продавец обязуется передать товар: {{goods_description}}, а Покупатель обязуется принять товар и уплатить за него цену.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Продавец:</strong><br/>
{{seller_name}}<br/>
{{seller_address}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{seller_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Покупатель:</strong><br/>
{{buyer_name}}<br/>
{{buyer_address}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{buyer_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения',
    'contract_date', 'Дата заключения',
    'seller_name', 'Наименование продавца',
    'buyer_name', 'Наименование покупателя',
    'goods_description', 'Описание товара',
    'seller_address', 'Адрес продавца',
    'buyer_address', 'Адрес покупателя',
    'seller_signature', 'Подпись продавца',
    'buyer_signature', 'Подпись покупателя'
));

-- Проверяем результат
SELECT category, COUNT(*) as count FROM contract_templates GROUP BY category ORDER BY category; 