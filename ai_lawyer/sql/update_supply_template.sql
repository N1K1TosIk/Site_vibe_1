USE ai_lawyer_db;

-- Обновляем шаблон договора поставки с полным содержанием и форматированием
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ДОГОВОР ПОСТАВКИ № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{supplier_name}}, именуемое в дальнейшем "Поставщик", и {{buyer_name}}, именуемое в дальнейшем "Покупатель", заключили настоящий договор о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ ДОГОВОРА</h3>

<p style="text-align: justify;">1.1. Поставщик обязуется передать в собственность Покупателю товары: {{goods_description}}, а Покупатель обязуется принять товары и уплатить за них цену.</p>

<p style="text-align: justify;">1.2. Количество товара: {{goods_quantity}} {{goods_unit}}.</p>

<p style="text-align: justify;">1.3. Общая стоимость товара составляет: {{total_cost}} ({{total_cost_words}}) рублей, включая НДС {{vat_rate}}%.</p>

<p style="text-align: justify;">1.4. Качество товара должно соответствовать требованиям {{quality_standards}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. СРОКИ И ПОРЯДОК ПОСТАВКИ</h3>

<p style="text-align: justify;">2.1. Поставка товара осуществляется в срок до {{delivery_date}}.</p>

<p style="text-align: justify;">2.2. Место поставки: {{delivery_address}}.</p>

<p style="text-align: justify;">2.3. Поставка товара осуществляется {{delivery_terms}} (условия поставки согласно Инкотермс).</p>

<p style="text-align: justify;">2.4. Передача товара оформляется товарной накладной, подписываемой обеими сторонами.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. ЦЕНА И ПОРЯДОК РАСЧЕТОВ</h3>

<p style="text-align: justify;">3.1. Цена товара указана в п. 1.3 настоящего договора и является твердой.</p>

<p style="text-align: justify;">3.2. Расчеты производятся в следующем порядке: {{payment_terms}}.</p>

<p style="text-align: justify;">3.3. Покупатель производит оплату путем перечисления денежных средств на расчетный счет Поставщика в течение {{payment_period}} банковских дней с момента {{payment_trigger}}.</p>

<p style="text-align: justify;">3.4. Моментом исполнения обязательства по оплате считается поступление денежных средств на расчетный счет Поставщика.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. ПРАВА И ОБЯЗАННОСТИ СТОРОН</h3>

<p style="text-align: justify;"><strong>4.1. Поставщик обязуется:</strong></p>
<p style="text-align: justify;">- поставить товар в количестве, ассортименте и комплектности, предусмотренных договором;</p>
<p style="text-align: justify;">- обеспечить соответствие товара требованиям по качеству;</p>
<p style="text-align: justify;">- передать Покупателю вместе с товаром относящиеся к нему документы;</p>
<p style="text-align: justify;">- уведомить Покупателя о готовности товара к передаче.</p>

<p style="text-align: justify;"><strong>4.2. Покупатель обязуется:</strong></p>
<p style="text-align: justify;">- принять поставляемый товар;</p>
<p style="text-align: justify;">- произвести оплату товара в сроки и в порядке, установленные договором;</p>
<p style="text-align: justify;">- обеспечить разгрузку товара в месте поставки.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. КАЧЕСТВО ТОВАРА И ГАРАНТИИ</h3>

<p style="text-align: justify;">5.1. Качество поставляемого товара должно соответствовать {{quality_requirements}}.</p>

<p style="text-align: justify;">5.2. Гарантийный срок на товар составляет {{warranty_period}} месяцев с момента передачи товара Покупателю.</p>

<p style="text-align: justify;">5.3. Покупатель обязан осмотреть товар в течение {{inspection_period}} дней после получения.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. ОТВЕТСТВЕННОСТЬ СТОРОН</h3>

<p style="text-align: justify;">6.1. За просрочку поставки товара Поставщик уплачивает Покупателю неустойку в размере {{delay_penalty}}% от стоимости не поставленного в срок товара за каждый день просрочки.</p>

<p style="text-align: justify;">6.2. За просрочку платежа Покупатель уплачивает Поставщику пени в размере {{payment_penalty}}% от суммы просроченного платежа за каждый день просрочки.</p>

<p style="text-align: justify;">6.3. Поставка товара ненадлежащего качества влечет обязанность Поставщика по выбору Покупателя безвозмездно устранить недостатки, заменить товар или возместить расходы на устранение недостатков.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. ФОРС-МАЖОР</h3>

<p style="text-align: justify;">7.1. Стороны освобождаются от ответственности за частичное или полное неисполнение обязательств по настоящему договору, если это неисполнение явилось следствием обстоятельств непреодолимой силы.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. РАЗРЕШЕНИЕ СПОРОВ</h3>

<p style="text-align: justify;">8.1. Все споры и разногласия разрешаются путем переговоров. При невозможности достижения соглашения споры рассматриваются в Арбитражном суде по месту нахождения ответчика.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">9. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">9.1. Договор вступает в силу с момента подписания и действует до полного исполнения сторонами обязательств.</p>

<p style="text-align: justify;">9.2. Изменения и дополнения к договору оформляются в письменном виде и подписываются обеими сторонами.</p>

<p style="text-align: justify;">9.3. Договор составлен в двух экземплярах, имеющих одинаковую юридическую силу.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">10. РЕКВИЗИТЫ И ПОДПИСИ СТОРОН</h3>

<table width="100%" style="margin-top: 30px;">
<tr>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>ПОСТАВЩИК</strong><br/>
{{supplier_name}}<br/>
ИНН: {{supplier_inn}}<br/>
КПП: {{supplier_kpp}}<br/>
Адрес: {{supplier_address}}<br/>
Тел.: {{supplier_phone}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{supplier_signature}}
</td>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>ПОКУПАТЕЛЬ</strong><br/>
{{buyer_name}}<br/>
ИНН: {{buyer_inn}}<br/>
КПП: {{buyer_kpp}}<br/>
Адрес: {{buyer_address}}<br/>
Тел.: {{buyer_phone}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{buyer_signature}}
</td>
</tr>
</table>',

variables = JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'supplier_name', 'Наименование поставщика',
    'buyer_name', 'Наименование покупателя',
    'goods_description', 'Описание товара',
    'goods_quantity', 'Количество товара',
    'goods_unit', 'Единица измерения',
    'total_cost', 'Общая стоимость товара',
    'total_cost_words', 'Стоимость прописью',
    'vat_rate', 'Ставка НДС',
    'quality_standards', 'Стандарты качества',
    'delivery_date', 'Срок поставки',
    'delivery_address', 'Адрес поставки',
    'delivery_terms', 'Условия поставки',
    'payment_terms', 'Условия оплаты',
    'payment_period', 'Срок оплаты (дней)',
    'payment_trigger', 'Момент начала отсчета срока оплаты',
    'quality_requirements', 'Требования к качеству',
    'warranty_period', 'Гарантийный срок (месяцев)',
    'inspection_period', 'Срок осмотра товара (дней)',
    'delay_penalty', 'Неустойка за просрочку поставки (%)',
    'payment_penalty', 'Пени за просрочку платежа (%)',
    'supplier_inn', 'ИНН поставщика',
    'supplier_kpp', 'КПП поставщика',
    'supplier_address', 'Адрес поставщика',
    'supplier_phone', 'Телефон поставщика',
    'supplier_signature', 'Подпись поставщика',
    'buyer_inn', 'ИНН покупателя',
    'buyer_kpp', 'КПП покупателя',
    'buyer_address', 'Адрес покупателя',
    'buyer_phone', 'Телефон покупателя',
    'buyer_signature', 'Подпись покупателя'
)

WHERE id = 7; 