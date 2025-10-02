USE ai_lawyer_db;

-- ===========================
-- ИСПРАВЛЯЕМ ДОГОВОР ЛИЗИНГА (ID: 59)
-- ===========================
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ДОГОВОР ЛИЗИНГА № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{lessor_name}}, именуемое в дальнейшем "Лизингодатель", в лице {{lessor_representative}}, действующего на основании {{lessor_authority}}, с одной стороны, и {{lessee_name}}, именуемое в дальнейшем "Лизингополучатель", в лице {{lessee_representative}}, действующего на основании {{lessee_authority}}, с другой стороны, заключили договор лизинга о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ ЛИЗИНГА</h3>

<p style="text-align: justify;">1.1. Предмет лизинга: {{leased_property}}.</p>

<p style="text-align: justify;">1.2. Лизинговый платеж составляет {{lease_payment}} ({{lease_payment_words}}) рублей в месяц.</p>

<p style="text-align: justify;">1.3. Срок лизинга: {{lease_term}} с {{lease_start_date}} по {{lease_end_date}}.</p>

<p style="text-align: justify;">1.4. Общая стоимость предмета лизинга: {{total_cost}} ({{total_cost_words}}) рублей.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. ПРАВА И ОБЯЗАННОСТИ СТОРОН</h3>

<p style="text-align: justify;"><strong>2.1. Лизингодатель обязуется:</strong></p>
<p style="text-align: justify;">- приобрести в собственность указанное в п. 1.1 имущество и передать его во временное владение и пользование Лизингополучателю;</p>
<p style="text-align: justify;">- обеспечить соответствие предмета лизинга условиям договора;</p>
<p style="text-align: justify;">- не вмешиваться в хозяйственную деятельность Лизингополучателя, если она не противоречит договору и действующему законодательству.</p>

<p style="text-align: justify;"><strong>2.2. Лизингополучатель обязуется:</strong></p>
<p style="text-align: justify;">- принять предмет лизинга в порядке, предусмотренном договором;</p>
<p style="text-align: justify;">- выплачивать лизингодателю лизинговые платежи в порядке, в размерах и в сроки, предусмотренные договором;</p>
<p style="text-align: justify;">- содержать предмет лизинга в исправном состоянии, производить за свой счет его текущий и капитальный ремонт;</p>
<p style="text-align: justify;">- страховать предмет лизинга, если иное не предусмотрено договором;</p>
<p style="text-align: justify;">- использовать предмет лизинга в соответствии с его назначением.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. ПОРЯДОК РАСЧЕТОВ</h3>

<p style="text-align: justify;">3.1. Лизинговые платежи вносятся ежемесячно до {{payment_date}} числа каждого месяца путем перечисления денежных средств на расчетный счет Лизингодателя.</p>

<p style="text-align: justify;">3.2. Первый лизинговый платеж вносится в течение 10 банковских дней с момента подписания акта приема-передачи предмета лизинга.</p>

<p style="text-align: justify;">3.3. В случае просрочки платежа Лизингополучатель уплачивает пени в размере {{penalty_rate}}% от суммы просроченного платежа за каждый день просрочки.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. ПЕРЕХОД ПРАВА СОБСТВЕННОСТИ</h3>

<p style="text-align: justify;">4.1. По истечении срока договора лизинга и при условии выплаты всех лизинговых платежей предмет лизинга переходит в собственность Лизингополучателя без дополнительной оплаты.</p>

<p style="text-align: justify;">4.2. Переход права собственности оформляется соответствующим актом приема-передачи.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. ОТВЕТСТВЕННОСТЬ СТОРОН</h3>

<p style="text-align: justify;">5.1. За неисполнение или ненадлежащее исполнение обязательств по настоящему договору стороны несут ответственность в соответствии с действующим законодательством.</p>

<p style="text-align: justify;">5.2. Лизингополучатель несет риск случайной гибели или случайной порчи предмета лизинга с момента его фактической передачи, если иное не предусмотрено договором лизинга.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. ДОСРОЧНОЕ РАСТОРЖЕНИЕ ДОГОВОРА</h3>

<p style="text-align: justify;">6.1. Лизингодатель имеет право потребовать досрочного расторжения договора и возврата предмета лизинга в случаях:</p>
<p style="text-align: justify;">- просрочки внесения лизинговых платежей более чем на 30 дней;</p>
<p style="text-align: justify;">- использования предмета лизинга не по назначению;</p>
<p style="text-align: justify;">- нарушения обязанности по содержанию и ремонту предмета лизинга.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. РАЗРЕШЕНИЕ СПОРОВ</h3>

<p style="text-align: justify;">7.1. Все споры и разногласия, которые могут возникнуть между сторонами, разрешаются путем переговоров. В случае невозможности достижения соглашения споры разрешаются в судебном порядке в соответствии с действующим законодательством Российской Федерации.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">8.1. Настоящий договор составлен в двух экземплярах, имеющих одинаковую юридическую силу, по одному для каждой из сторон.</p>

<p style="text-align: justify;">8.2. Договор вступает в силу с момента его подписания сторонами и действует до полного исполнения сторонами своих обязательств.</p>

<p style="text-align: justify;">8.3. Изменения и дополнения к настоящему договору действительны только при условии, если они совершены в письменной форме и подписаны сторонами.</p>

<div style="margin-top: 50px;">
<table width="100%">
<tr>
<td width="50%" style="text-align: left; vertical-align: top;">
<strong>ЛИЗИНГОДАТЕЛЬ:</strong><br/>
{{lessor_name}}<br/>
ИНН: {{lessor_inn}}<br/>
Адрес: {{lessor_address}}<br/>
<br/>
{{lessor_representative}}<br/>
<br/>
<div style="margin-top: 30px;">
____________________
</div>
</td>
<td width="50%" style="text-align: left; vertical-align: top;">
<strong>ЛИЗИНГОПОЛУЧАТЕЛЬ:</strong><br/>
{{lessee_name}}<br/>
ИНН: {{lessee_inn}}<br/>
Адрес: {{lessee_address}}<br/>
<br/>
{{lessee_representative}}<br/>
<br/>
<div style="margin-top: 30px;">
____________________
</div>
</td>
</tr>
</table>
</div>',

variables = JSON_OBJECT(
    'contract_number', 'Номер договора лизинга',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора (дд.мм.гггг)',
    'lessor_name', 'Наименование лизингодателя',
    'lessor_representative', 'Представитель лизингодателя',
    'lessor_authority', 'Основание полномочий лизингодателя',
    'lessee_name', 'Наименование лизингополучателя',
    'lessee_representative', 'Представитель лизингополучателя',
    'lessee_authority', 'Основание полномочий лизингополучателя',
    'leased_property', 'Предмет лизинга (подробное описание)',
    'lease_payment', 'Размер лизингового платежа (руб.)',
    'lease_payment_words', 'Размер лизингового платежа прописью',
    'lease_term', 'Срок лизинга',
    'lease_start_date', 'Дата начала лизинга',
    'lease_end_date', 'Дата окончания лизинга',
    'total_cost', 'Общая стоимость предмета лизинга (руб.)',
    'total_cost_words', 'Общая стоимость прописью',
    'payment_date', 'Число месяца для внесения платежа',
    'penalty_rate', 'Размер пени (%)',
    'lessor_inn', 'ИНН лизингодателя',
    'lessor_address', 'Адрес лизингодателя',
    'lessee_inn', 'ИНН лизингополучателя',
    'lessee_address', 'Адрес лизингополучателя'
)
WHERE id = 59;

-- ===========================
-- УБИРАЕМ ПОЛЯ ПОДПИСЕЙ ИЗ ВСЕХ ДОГОВОРОВ
-- ===========================

-- Трудовой договор (ID: 23)
UPDATE contract_templates SET 
template_content = REPLACE(
    REPLACE(template_content, '{{employer_signature}}', '____________________'),
    '{{employee_signature}}', '____________________'
)
WHERE id = 23;

-- Договор аренды (ID: 25)
UPDATE contract_templates SET 
template_content = REPLACE(
    REPLACE(template_content, '{{landlord_signature}}', '____________________'),
    '{{tenant_signature}}', '____________________'
)
WHERE id = 25;

-- Договор оказания услуг (ID: 26)
UPDATE contract_templates SET 
template_content = REPLACE(
    REPLACE(template_content, '{{provider_signature}}', '____________________'),
    '{{client_signature}}', '____________________'
)
WHERE id = 26;

-- Договор подряда (ID: 27)
UPDATE contract_templates SET 
template_content = REPLACE(
    REPLACE(template_content, '{{contractor_signature}}', '____________________'),
    '{{client_signature}}', '____________________'
)
WHERE id = 27;

-- Договор купли-продажи (ID: 28)
UPDATE contract_templates SET 
template_content = REPLACE(
    REPLACE(template_content, '{{seller_signature}}', '____________________'),
    '{{buyer_signature}}', '____________________'
)
WHERE id = 28;

-- Соглашение о неразглашении (ID: 24)
UPDATE contract_templates SET 
template_content = REPLACE(
    REPLACE(template_content, '{{disclosing_signature}}', '____________________'),
    '{{receiving_signature}}', '____________________'
)
WHERE id = 24;

-- Договор поставки (ID: 7)
UPDATE contract_templates SET 
template_content = REPLACE(
    REPLACE(template_content, '{{supplier_signature}}', '____________________'),
    '{{buyer_signature}}', '____________________'
)
WHERE id = 7; 