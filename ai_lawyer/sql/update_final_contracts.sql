USE ai_lawyer_db;

-- ===========================
-- ДОГОВОР ПОДРЯДА (ID: 27)
-- ===========================
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ДОГОВОР ПОДРЯДА № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{contractor_name}}, именуемое в дальнейшем "Подрядчик", в лице {{contractor_representative}}, действующего на основании {{contractor_authority}}, с одной стороны, и {{client_name}}, именуемое в дальнейшем "Заказчик", в лице {{client_representative}}, действующего на основании {{client_authority}}, с другой стороны, заключили настоящий договор о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ ДОГОВОРА</h3>

<p style="text-align: justify;">1.1. Подрядчик обязуется выполнить по заданию Заказчика работы: {{work_description}}, и сдать их результат Заказчику, а Заказчик обязуется принять результат работы и оплатить его.</p>

<p style="text-align: justify;">1.2. Объем работ: {{work_scope}}.</p>

<p style="text-align: justify;">1.3. Место выполнения работ: {{work_location}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. СРОКИ ВЫПОЛНЕНИЯ РАБОТ</h3>

<p style="text-align: justify;">2.1. Работы выполняются в период с {{start_date}} по {{completion_date}}.</p>

<p style="text-align: justify;">2.2. Этапы выполнения работ и промежуточные сроки: {{milestones}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. ЦЕНА РАБОТ И ПОРЯДОК РАСЧЕТОВ</h3>

<p style="text-align: justify;">3.1. Цена работ составляет {{total_cost}} ({{total_cost_words}}) рублей {{vat_status}}.</p>

<p style="text-align: justify;">3.2. Оплата производится {{payment_method}} в следующем порядке: {{payment_schedule}}.</p>

<p style="text-align: justify;">3.3. Заказчик производит оплату в течение {{payment_period}} дней с момента {{payment_trigger}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. МАТЕРИАЛЫ И ОБОРУДОВАНИЕ</h3>

<p style="text-align: justify;">4.1. Материалы для выполнения работ предоставляет {{materials_provider}}.</p>

<p style="text-align: justify;">4.2. Оборудование для выполнения работ предоставляет {{equipment_provider}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. ПРАВА И ОБЯЗАННОСТИ ПОДРЯДЧИКА</h3>

<p style="text-align: justify;"><strong>5.1. Подрядчик обязан:</strong></p>
<p style="text-align: justify;">- выполнить работы в соответствии с условиями договора;</p>
<p style="text-align: justify;">- выполнить работы лично или с привлечением третьих лиц;</p>
<p style="text-align: justify;">- использовать предоставленные материалы экономно и по назначению;</p>
<p style="text-align: justify;">- немедленно предупредить Заказчика о непригодности предоставленных материалов.</p>

<p style="text-align: justify;"><strong>5.2. Подрядчик имеет право:</strong></p>
<p style="text-align: justify;">- требовать от Заказчика содействия в выполнении работ;</p>
<p style="text-align: justify;">- требовать оплаты выполненных работ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. ПРАВА И ОБЯЗАННОСТИ ЗАКАЗЧИКА</h3>

<p style="text-align: justify;"><strong>6.1. Заказчик обязан:</strong></p>
<p style="text-align: justify;">- оплатить работы в размере и в сроки, предусмотренные договором;</p>
<p style="text-align: justify;">- принять результат работы при условии его соответствия договору;</p>
<p style="text-align: justify;">- предоставить Подрядчику необходимые для выполнения работ материалы и информацию.</p>

<p style="text-align: justify;"><strong>6.2. Заказчик имеет право:</strong></p>
<p style="text-align: justify;">- во всякое время проверять ход и качество выполняемых работ;</p>
<p style="text-align: justify;">- требовать устранения недостатков за счет Подрядчика.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. КАЧЕСТВО РАБОТ</h3>

<p style="text-align: justify;">7.1. Качество выполняемых работ должно соответствовать {{quality_standards}}.</p>

<p style="text-align: justify;">7.2. Приемка работ осуществляется путем подписания акта выполненных работ.</p>

<p style="text-align: justify;">7.3. Гарантийный срок на выполненные работы составляет {{warranty_period}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. ОТВЕТСТВЕННОСТЬ СТОРОН</h3>

<p style="text-align: justify;">8.1. За просрочку выполнения работ Подрядчик уплачивает неустойку в размере {{delay_penalty}}% от стоимости работ за каждый день просрочки.</p>

<p style="text-align: justify;">8.2. За просрочку оплаты Заказчик уплачивает пени в размере {{payment_penalty}}% от суммы просроченного платежа за каждый день просрочки.</p>

<p style="text-align: justify;">8.3. Подрядчик несет ответственность за ненадлежащее качество выполненных работ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">9. РАЗРЕШЕНИЕ СПОРОВ</h3>

<p style="text-align: justify;">9.1. Все споры разрешаются путем переговоров, а при невозможности достижения соглашения - в суде в соответствии с действующим законодательством РФ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">10. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">10.1. Договор вступает в силу с момента подписания и действует до полного исполнения обязательств сторонами.</p>

<p style="text-align: justify;">10.2. Договор составлен в двух экземплярах, имеющих одинаковую юридическую силу.</p>

<table width="100%" style="margin-top: 30px;">
<tr>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>ПОДРЯДЧИК</strong><br/>
{{contractor_name}}<br/>
ИНН: {{contractor_inn}}<br/>
Адрес: {{contractor_address}}<br/>
{{contractor_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{contractor_signature}}
</td>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>ЗАКАЗЧИК</strong><br/>
{{client_name}}<br/>
ИНН: {{client_inn}}<br/>
Адрес: {{client_address}}<br/>
{{client_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{client_signature}}
</td>
</tr>
</table>',

variables = JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'contractor_name', 'Наименование подрядчика',
    'contractor_representative', 'Представитель подрядчика',
    'contractor_authority', 'Основание полномочий подрядчика',
    'client_name', 'Наименование заказчика',
    'client_representative', 'Представитель заказчика',
    'client_authority', 'Основание полномочий заказчика',
    'work_description', 'Описание работ',
    'work_scope', 'Объем работ',
    'work_location', 'Место выполнения работ',
    'start_date', 'Дата начала работ',
    'completion_date', 'Дата завершения работ',
    'milestones', 'Этапы выполнения работ',
    'total_cost', 'Стоимость работ (руб.)',
    'total_cost_words', 'Стоимость работ прописью',
    'vat_status', 'НДС (включая/не включая)',
    'payment_method', 'Способ оплаты',
    'payment_schedule', 'График оплаты',
    'payment_period', 'Срок оплаты (дней)',
    'payment_trigger', 'Момент начала отсчета срока оплаты',
    'materials_provider', 'Кто предоставляет материалы',
    'equipment_provider', 'Кто предоставляет оборудование',
    'quality_standards', 'Стандарты качества работ',
    'warranty_period', 'Гарантийный срок',
    'delay_penalty', 'Неустойка за просрочку (%)',
    'payment_penalty', 'Пени за просрочку оплаты (%)',
    'contractor_inn', 'ИНН подрядчика',
    'contractor_address', 'Адрес подрядчика',
    'contractor_signature', 'Подпись подрядчика',
    'client_inn', 'ИНН заказчика',
    'client_address', 'Адрес заказчика',
    'client_signature', 'Подпись заказчика'
)
WHERE id = 27;

-- ===========================
-- СОГЛАШЕНИЕ О НЕРАЗГЛАШЕНИИ ИНФОРМАЦИИ (ID: 24)
-- ===========================
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">СОГЛАШЕНИЕ О НЕРАЗГЛАШЕНИИ ИНФОРМАЦИИ № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{disclosing_party}}, именуемое в дальнейшем "Сторона, раскрывающая информацию", в лице {{disclosing_representative}}, действующего на основании {{disclosing_authority}}, с одной стороны, и {{receiving_party}}, именуемое в дальнейшем "Сторона, получающая информацию", в лице {{receiving_representative}}, действующего на основании {{receiving_authority}}, с другой стороны, заключили настоящее соглашение о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ СОГЛАШЕНИЯ</h3>

<p style="text-align: justify;">1.1. Настоящее соглашение регулирует отношения сторон по защите конфиденциальной информации, которая может быть передана одной стороной другой стороне в связи с {{purpose}}.</p>

<p style="text-align: justify;">1.2. Конфиденциальной информацией признается любая информация, переданная одной стороной другой стороне, включая, но не ограничиваясь: {{confidential_info}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. ОБЯЗАТЕЛЬСТВА ПО НЕРАЗГЛАШЕНИЮ</h3>

<p style="text-align: justify;">2.1. Сторона, получающая информацию, обязуется:</p>
<p style="text-align: justify;">- сохранять в строгой тайне всю конфиденциальную информацию;</p>
<p style="text-align: justify;">- не разглашать конфиденциальную информацию третьим лицам без письменного согласия стороны, раскрывающей информацию;</p>
<p style="text-align: justify;">- использовать конфиденциальную информацию исключительно для {{usage_purpose}};</p>
<p style="text-align: justify;">- принимать все разумные меры для защиты конфиденциальной информации.</p>

<p style="text-align: justify;">2.2. Сторона, получающая информацию, имеет право ознакомить с конфиденциальной информацией своих сотрудников только в объеме, необходимом для {{access_purpose}}, при условии принятия ими аналогичных обязательств по неразглашению.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. ИСКЛЮЧЕНИЯ</h3>

<p style="text-align: justify;">3.1. Обязательства по неразглашению не распространяются на информацию, которая:</p>
<p style="text-align: justify;">- была известна стороне, получающей информацию, до ее получения от стороны, раскрывающей информацию;</p>
<p style="text-align: justify;">- является общедоступной или стала таковой не по вине стороны, получающей информацию;</p>
<p style="text-align: justify;">- была получена от третьих лиц без нарушения ими обязательств по конфиденциальности;</p>
<p style="text-align: justify;">- подлежит раскрытию в соответствии с требованиями закона.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. СРОК ДЕЙСТВИЯ</h3>

<p style="text-align: justify;">4.1. Настоящее соглашение вступает в силу с момента подписания и действует в течение {{confidentiality_period}}.</p>

<p style="text-align: justify;">4.2. Обязательства по неразглашению сохраняются и после прекращения действия настоящего соглашения.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. ВОЗВРАТ ИНФОРМАЦИИ</h3>

<p style="text-align: justify;">5.1. По требованию стороны, раскрывающей информацию, сторона, получающая информацию, обязана вернуть или уничтожить все материальные носители конфиденциальной информации в течение {{return_period}} дней.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. ОТВЕТСТВЕННОСТЬ</h3>

<p style="text-align: justify;">6.1. За нарушение обязательств по неразглашению виновная сторона уплачивает штраф в размере {{penalty_amount}} ({{penalty_amount_words}}) рублей.</p>

<p style="text-align: justify;">6.2. Уплата штрафа не освобождает виновную сторону от возмещения причиненных убытков.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. РАЗРЕШЕНИЕ СПОРОВ</h3>

<p style="text-align: justify;">7.1. Все споры разрешаются путем переговоров, а при невозможности достижения соглашения - в суде в соответствии с действующим законодательством РФ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">8.1. Соглашение составлено в двух экземплярах, имеющих одинаковую юридическую силу.</p>

<p style="text-align: justify;">8.2. Изменения и дополнения к соглашению оформляются в письменном виде.</p>

<table width="100%" style="margin-top: 30px;">
<tr>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>СТОРОНА, РАСКРЫВАЮЩАЯ ИНФОРМАЦИЮ</strong><br/>
{{disclosing_party}}<br/>
ИНН: {{disclosing_inn}}<br/>
Адрес: {{disclosing_address}}<br/>
{{disclosing_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{disclosing_signature}}
</td>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>СТОРОНА, ПОЛУЧАЮЩАЯ ИНФОРМАЦИЮ</strong><br/>
{{receiving_party}}<br/>
ИНН: {{receiving_inn}}<br/>
Адрес: {{receiving_address}}<br/>
{{receiving_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{receiving_signature}}
</td>
</tr>
</table>',

variables = JSON_OBJECT(
    'contract_number', 'Номер соглашения',
    'city', 'Город заключения соглашения',
    'contract_date', 'Дата заключения соглашения',
    'disclosing_party', 'Сторона, раскрывающая информацию',
    'disclosing_representative', 'Представитель раскрывающей стороны',
    'disclosing_authority', 'Основание полномочий раскрывающей стороны',
    'receiving_party', 'Сторона, получающая информацию',
    'receiving_representative', 'Представитель получающей стороны',
    'receiving_authority', 'Основание полномочий получающей стороны',
    'purpose', 'Цель передачи информации',
    'confidential_info', 'Описание конфиденциальной информации',
    'usage_purpose', 'Цель использования информации',
    'access_purpose', 'Цель ознакомления сотрудников с информацией',
    'confidentiality_period', 'Срок действия обязательств по конфиденциальности',
    'return_period', 'Срок возврата информации (дней)',
    'penalty_amount', 'Размер штрафа (руб.)',
    'penalty_amount_words', 'Размер штрафа прописью',
    'disclosing_inn', 'ИНН раскрывающей стороны',
    'disclosing_address', 'Адрес раскрывающей стороны',
    'disclosing_signature', 'Подпись раскрывающей стороны',
    'receiving_inn', 'ИНН получающей стороны',
    'receiving_address', 'Адрес получающей стороны',
    'receiving_signature', 'Подпись получающей стороны'
)
WHERE id = 24; 