USE ai_lawyer_db;

-- ===========================
-- ДОГОВОР АВТОРСКОГО ЗАКАЗА (ID: 70)
-- ===========================
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ДОГОВОР АВТОРСКОГО ЗАКАЗА № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{client_name}}, именуемое в дальнейшем "Заказчик", в лице {{client_representative}}, действующего на основании {{client_authority}}, с одной стороны, и {{author_name}}, именуемый в дальнейшем "Автор", с другой стороны, заключили настоящий договор о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ ДОГОВОРА</h3>

<p style="text-align: justify;">1.1. Заказчик поручает, а Автор принимает на себя обязательство создать по заданию Заказчика произведение: {{work_description}}.</p>

<p style="text-align: justify;">1.2. Вид произведения: {{work_type}}.</p>

<p style="text-align: justify;">1.3. Объем произведения: {{work_volume}}.</p>

<p style="text-align: justify;">1.4. Требования к произведению: {{work_requirements}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. СРОКИ ВЫПОЛНЕНИЯ РАБОТЫ</h3>

<p style="text-align: justify;">2.1. Автор обязуется создать и передать Заказчику произведение в срок до {{completion_date}}.</p>

<p style="text-align: justify;">2.2. Передача произведения осуществляется {{delivery_method}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. АВТОРСКОЕ ВОЗНАГРАЖДЕНИЕ</h3>

<p style="text-align: justify;">3.1. За создание произведения Заказчик выплачивает Автору авторское вознаграждение в размере {{fee_amount}} ({{fee_amount_words}}) рублей.</p>

<p style="text-align: justify;">3.2. Вознаграждение выплачивается {{payment_schedule}}.</p>

<p style="text-align: justify;">3.3. Оплата производится путем перечисления денежных средств на банковский счет Автора в течение {{payment_period}} дней с момента {{payment_trigger}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. ПРАВА И ОБЯЗАННОСТИ СТОРОН</h3>

<p style="text-align: justify;"><strong>4.1. Автор обязуется:</strong></p>
<p style="text-align: justify;">- создать произведение лично;</p>
<p style="text-align: justify;">- соблюдать установленные сроки;</p>
<p style="text-align: justify;">- обеспечить соответствие произведения техническому заданию;</p>
<p style="text-align: justify;">- не нарушать авторские права третьих лиц.</p>

<p style="text-align: justify;"><strong>4.2. Заказчик обязуется:</strong></p>
<p style="text-align: justify;">- принять созданное произведение;</p>
<p style="text-align: justify;">- выплатить авторское вознаграждение;</p>
<p style="text-align: justify;">- обеспечить указание имени автора при использовании произведения.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. АВТОРСКИЕ ПРАВА</h3>

<p style="text-align: justify;">5.1. Автор сохраняет за собой исключительные авторские права на созданное произведение.</p>

<p style="text-align: justify;">5.2. Заказчику передаются права на использование произведения в следующих пределах: {{usage_rights}}.</p>

<p style="text-align: justify;">5.3. Срок передачи прав составляет {{rights_period}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. ОТВЕТСТВЕННОСТЬ СТОРОН</h3>

<p style="text-align: justify;">6.1. За просрочку передачи произведения Автор уплачивает неустойку в размере {{delay_penalty}}% от суммы вознаграждения за каждый день просрочки.</p>

<p style="text-align: justify;">6.2. За просрочку оплаты Заказчик уплачивает пени в размере {{payment_penalty}}% от суммы просроченного платежа за каждый день просрочки.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. РАЗРЕШЕНИЕ СПОРОВ</h3>

<p style="text-align: justify;">7.1. Все споры и разногласия разрешаются путем переговоров, а при невозможности достижения соглашения - в суде в соответствии с действующим законодательством РФ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">8.1. Договор вступает в силу с момента подписания и действует до полного исполнения обязательств сторонами.</p>

<p style="text-align: justify;">8.2. Договор составлен в двух экземплярах, имеющих одинаковую юридическую силу.</p>

<div style="margin-top: 50px;">
<table width="100%">
<tr>
<td width="50%" style="text-align: left; vertical-align: top;">
<strong>ЗАКАЗЧИК:</strong><br/>
{{client_name}}<br/>
ИНН: {{client_inn}}<br/>
Адрес: {{client_address}}<br/>
<br/>
{{client_representative}}<br/>
<br/>
<div style="margin-top: 30px;">
____________________
</div>
</td>
<td width="50%" style="text-align: left; vertical-align: top;">
<strong>АВТОР:</strong><br/>
{{author_name}}<br/>
Паспорт: {{author_passport}}<br/>
Адрес: {{author_address}}<br/>
Телефон: {{author_phone}}<br/>
<br/>
<div style="margin-top: 30px;">
____________________
</div>
</td>
</tr>
</table>
</div>',

variables = JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'client_name', 'Наименование заказчика',
    'client_representative', 'Представитель заказчика',
    'client_authority', 'Основание полномочий заказчика',
    'author_name', 'ФИО автора',
    'work_description', 'Описание произведения',
    'work_type', 'Вид произведения',
    'work_volume', 'Объем произведения',
    'work_requirements', 'Требования к произведению',
    'completion_date', 'Срок выполнения работы',
    'delivery_method', 'Способ передачи произведения',
    'fee_amount', 'Размер авторского вознаграждения (руб.)',
    'fee_amount_words', 'Размер вознаграждения прописью',
    'payment_schedule', 'График выплаты вознаграждения',
    'payment_period', 'Срок оплаты (дней)',
    'payment_trigger', 'Момент начала отсчета срока оплаты',
    'usage_rights', 'Передаваемые права использования',
    'rights_period', 'Срок передачи прав',
    'delay_penalty', 'Неустойка за просрочку (%)',
    'payment_penalty', 'Пени за просрочку оплаты (%)',
    'client_inn', 'ИНН заказчика',
    'client_address', 'Адрес заказчика',
    'author_passport', 'Паспортные данные автора',
    'author_address', 'Адрес автора',
    'author_phone', 'Телефон автора'
)
WHERE id = 70; 