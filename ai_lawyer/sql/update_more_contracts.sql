USE ai_lawyer_db;

-- ===========================
-- ДОГОВОР КУПЛИ-ПРОДАЖИ ТОВАРА (ID: 28)
-- ===========================
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ДОГОВОР КУПЛИ-ПРОДАЖИ № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{seller_name}}, именуемое в дальнейшем "Продавец", в лице {{seller_representative}}, действующего на основании {{seller_authority}}, с одной стороны, и {{buyer_name}}, именуемое в дальнейшем "Покупатель", в лице {{buyer_representative}}, действующего на основании {{buyer_authority}}, с другой стороны, заключили настоящий договор о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ ДОГОВОРА</h3>

<p style="text-align: justify;">1.1. Продавец обязуется передать в собственность Покупателю товар: {{goods_description}}, а Покупатель обязуется принять товар и уплатить за него цену.</p>

<p style="text-align: justify;">1.2. Количество товара: {{quantity}} {{unit}}.</p>

<p style="text-align: justify;">1.3. Качество товара: {{quality_description}}.</p>

<p style="text-align: justify;">1.4. Комплектность: {{completeness}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. ЦЕНА ТОВАРА И ПОРЯДОК РАСЧЕТОВ</h3>

<p style="text-align: justify;">2.1. Цена товара составляет {{total_price}} ({{total_price_words}}) рублей {{vat_included}}.</p>

<p style="text-align: justify;">2.2. Оплата производится {{payment_method}} в следующем порядке: {{payment_terms}}.</p>

<p style="text-align: justify;">2.3. Покупатель производит оплату в течение {{payment_period}} дней с момента {{payment_trigger}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. ПОРЯДОК ПЕРЕДАЧИ ТОВАРА</h3>

<p style="text-align: justify;">3.1. Передача товара осуществляется по адресу: {{delivery_address}}.</p>

<p style="text-align: justify;">3.2. Срок передачи товара: {{delivery_date}}.</p>

<p style="text-align: justify;">3.3. Передача товара оформляется актом приема-передачи, подписываемым обеими сторонами.</p>

<p style="text-align: justify;">3.4. Риск случайной гибели или повреждения товара переходит к Покупателю с момента передачи товара.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. ПРАВА И ОБЯЗАННОСТИ ПРОДАВЦА</h3>

<p style="text-align: justify;"><strong>4.1. Продавец обязан:</strong></p>
<p style="text-align: justify;">- передать Покупателю товар, предусмотренный договором;</p>
<p style="text-align: justify;">- передать товар свободным от прав третьих лиц;</p>
<p style="text-align: justify;">- передать принадлежности и документы, относящиеся к товару.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. ПРАВА И ОБЯЗАННОСТИ ПОКУПАТЕЛЯ</h3>

<p style="text-align: justify;"><strong>5.1. Покупатель обязан:</strong></p>
<p style="text-align: justify;">- принять товар в соответствии с условиями договора;</p>
<p style="text-align: justify;">- оплатить товар в порядке и сроки, установленные договором;</p>
<p style="text-align: justify;">- известить Продавца о ненадлежащем исполнении договора.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. КАЧЕСТВО ТОВАРА</h3>

<p style="text-align: justify;">6.1. Качество товара должно соответствовать {{quality_standards}}.</p>

<p style="text-align: justify;">6.2. Гарантийный срок составляет {{warranty_period}} с момента передачи товара.</p>

<p style="text-align: justify;">6.3. Покупатель обязан осмотреть товар в разумный срок после его получения.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. ОТВЕТСТВЕННОСТЬ СТОРОН</h3>

<p style="text-align: justify;">7.1. За просрочку передачи товара Продавец уплачивает неустойку в размере {{delay_penalty}}% от стоимости товара за каждый день просрочки.</p>

<p style="text-align: justify;">7.2. За просрочку оплаты Покупатель уплачивает пени в размере {{payment_penalty}}% от суммы просроченного платежа за каждый день просрочки.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. РАЗРЕШЕНИЕ СПОРОВ</h3>

<p style="text-align: justify;">8.1. Все споры разрешаются путем переговоров, а при невозможности достижения соглашения - в суде в соответствии с действующим законодательством РФ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">9. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">9.1. Договор вступает в силу с момента подписания и действует до полного исполнения обязательств сторонами.</p>

<p style="text-align: justify;">9.2. Договор составлен в двух экземплярах, имеющих одинаковую юридическую силу.</p>

<table width="100%" style="margin-top: 30px;">
<tr>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>ПРОДАВЕЦ</strong><br/>
{{seller_name}}<br/>
ИНН: {{seller_inn}}<br/>
Адрес: {{seller_address}}<br/>
{{seller_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{seller_signature}}
</td>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>ПОКУПАТЕЛЬ</strong><br/>
{{buyer_name}}<br/>
ИНН: {{buyer_inn}}<br/>
Адрес: {{buyer_address}}<br/>
{{buyer_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{buyer_signature}}
</td>
</tr>
</table>',

variables = JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'seller_name', 'Наименование продавца',
    'seller_representative', 'Представитель продавца',
    'seller_authority', 'Основание полномочий продавца',
    'buyer_name', 'Наименование покупателя',
    'buyer_representative', 'Представитель покупателя',
    'buyer_authority', 'Основание полномочий покупателя',
    'goods_description', 'Описание товара',
    'quantity', 'Количество товара',
    'unit', 'Единица измерения',
    'quality_description', 'Описание качества товара',
    'completeness', 'Комплектность товара',
    'total_price', 'Общая цена товара (руб.)',
    'total_price_words', 'Цена товара прописью',
    'vat_included', 'Включая/не включая НДС',
    'payment_method', 'Способ оплаты',
    'payment_terms', 'Условия оплаты',
    'payment_period', 'Срок оплаты (дней)',
    'payment_trigger', 'Момент начала отсчета срока оплаты',
    'delivery_address', 'Адрес доставки товара',
    'delivery_date', 'Срок передачи товара',
    'quality_standards', 'Стандарты качества',
    'warranty_period', 'Гарантийный срок',
    'delay_penalty', 'Неустойка за просрочку передачи (%)',
    'payment_penalty', 'Пени за просрочку оплаты (%)',
    'seller_inn', 'ИНН продавца',
    'seller_address', 'Адрес продавца',
    'seller_signature', 'Подпись продавца',
    'buyer_inn', 'ИНН покупателя',
    'buyer_address', 'Адрес покупателя',
    'buyer_signature', 'Подпись покупателя'
)
WHERE id = 28;

-- ===========================
-- ДОГОВОР ОКАЗАНИЯ УСЛУГ (ID: 26)
-- ===========================
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ДОГОВОР ОКАЗАНИЯ УСЛУГ № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{provider_name}}, именуемое в дальнейшем "Исполнитель", в лице {{provider_representative}}, действующего на основании {{provider_authority}}, с одной стороны, и {{client_name}}, именуемое в дальнейшем "Заказчик", в лице {{client_representative}}, действующего на основании {{client_authority}}, с другой стороны, заключили настоящий договор о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ ДОГОВОРА</h3>

<p style="text-align: justify;">1.1. Исполнитель обязуется по заданию Заказчика оказать услуги: {{services_description}}, а Заказчик обязуется оплатить эти услуги.</p>

<p style="text-align: justify;">1.2. Объем услуг: {{services_scope}}.</p>

<p style="text-align: justify;">1.3. Место оказания услуг: {{service_location}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. СРОКИ ОКАЗАНИЯ УСЛУГ</h3>

<p style="text-align: justify;">2.1. Услуги оказываются в период с {{start_date}} по {{end_date}}.</p>

<p style="text-align: justify;">2.2. Конкретные сроки выполнения отдельных этапов: {{milestones}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. СТОИМОСТЬ УСЛУГ И ПОРЯДОК РАСЧЕТОВ</h3>

<p style="text-align: justify;">3.1. Стоимость услуг составляет {{total_cost}} ({{total_cost_words}}) рублей {{vat_status}}.</p>

<p style="text-align: justify;">3.2. Оплата производится {{payment_method}} в следующем порядке: {{payment_schedule}}.</p>

<p style="text-align: justify;">3.3. Заказчик производит оплату в течение {{payment_period}} дней с момента {{payment_trigger}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. ПРАВА И ОБЯЗАННОСТИ ИСПОЛНИТЕЛЯ</h3>

<p style="text-align: justify;"><strong>4.1. Исполнитель обязан:</strong></p>
<p style="text-align: justify;">- оказать услуги лично или с привлечением третьих лиц;</p>
<p style="text-align: justify;">- оказать услуги в соответствии с условиями договора;</p>
<p style="text-align: justify;">- по требованию Заказчика предоставлять отчеты о ходе выполнения работ;</p>
<p style="text-align: justify;">- немедленно предупредить Заказчика о невозможности исполнения его указаний.</p>

<p style="text-align: justify;"><strong>4.2. Исполнитель имеет право:</strong></p>
<p style="text-align: justify;">- требовать от Заказчика содействия в оказании услуг;</p>
<p style="text-align: justify;">- требовать оплаты оказанных услуг.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. ПРАВА И ОБЯЗАННОСТИ ЗАКАЗЧИКА</h3>

<p style="text-align: justify;"><strong>5.1. Заказчик обязан:</strong></p>
<p style="text-align: justify;">- оплатить услуги в размере и в сроки, предусмотренные договором;</p>
<p style="text-align: justify;">- принять оказанные услуги;</p>
<p style="text-align: justify;">- предоставить Исполнителю необходимую информацию и документы.</p>

<p style="text-align: justify;"><strong>5.2. Заказчик имеет право:</strong></p>
<p style="text-align: justify;">- требовать оказания услуг надлежащего качества;</p>
<p style="text-align: justify;">- во всякое время проверять ход выполнения работ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. КАЧЕСТВО УСЛУГ</h3>

<p style="text-align: justify;">6.1. Качество оказываемых услуг должно соответствовать {{quality_requirements}}.</p>

<p style="text-align: justify;">6.2. Приемка услуг осуществляется путем подписания акта выполненных работ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. ОТВЕТСТВЕННОСТЬ СТОРОН</h3>

<p style="text-align: justify;">7.1. За просрочку оказания услуг Исполнитель уплачивает неустойку в размере {{delay_penalty}}% от стоимости услуг за каждый день просрочки.</p>

<p style="text-align: justify;">7.2. За просрочку оплаты Заказчик уплачивает пени в размере {{payment_penalty}}% от суммы просроченного платежа за каждый день просрочки.</p>

<p style="text-align: justify;">7.3. Исполнитель несет ответственность за ненадлежащее качество оказанных услуг.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. КОНФИДЕНЦИАЛЬНОСТЬ</h3>

<p style="text-align: justify;">8.1. Стороны обязуются сохранять конфиденциальность информации, полученной при исполнении договора.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">9. РАЗРЕШЕНИЕ СПОРОВ</h3>

<p style="text-align: justify;">9.1. Все споры разрешаются путем переговоров, а при невозможности достижения соглашения - в суде в соответствии с действующим законодательством РФ.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">10. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">10.1. Договор вступает в силу с момента подписания и действует до полного исполнения обязательств сторонами.</p>

<p style="text-align: justify;">10.2. Договор составлен в двух экземплярах, имеющих одинаковую юридическую силу.</p>

<table width="100%" style="margin-top: 30px;">
<tr>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>ИСПОЛНИТЕЛЬ</strong><br/>
{{provider_name}}<br/>
ИНН: {{provider_inn}}<br/>
Адрес: {{provider_address}}<br/>
{{provider_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{provider_signature}}
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
    'provider_name', 'Наименование исполнителя',
    'provider_representative', 'Представитель исполнителя',
    'provider_authority', 'Основание полномочий исполнителя',
    'client_name', 'Наименование заказчика',
    'client_representative', 'Представитель заказчика',
    'client_authority', 'Основание полномочий заказчика',
    'services_description', 'Описание услуг',
    'services_scope', 'Объем услуг',
    'service_location', 'Место оказания услуг',
    'start_date', 'Дата начала оказания услуг',
    'end_date', 'Дата окончания оказания услуг',
    'milestones', 'Этапы выполнения работ',
    'total_cost', 'Стоимость услуг (руб.)',
    'total_cost_words', 'Стоимость услуг прописью',
    'vat_status', 'НДС (включая/не включая)',
    'payment_method', 'Способ оплаты',
    'payment_schedule', 'График оплаты',
    'payment_period', 'Срок оплаты (дней)',
    'payment_trigger', 'Момент начала отсчета срока оплаты',
    'quality_requirements', 'Требования к качеству услуг',
    'delay_penalty', 'Неустойка за просрочку (%)',
    'payment_penalty', 'Пени за просрочку оплаты (%)',
    'provider_inn', 'ИНН исполнителя',
    'provider_address', 'Адрес исполнителя',
    'provider_signature', 'Подпись исполнителя',
    'client_inn', 'ИНН заказчика',
    'client_address', 'Адрес заказчика',
    'client_signature', 'Подпись заказчика'
)
WHERE id = 26; 