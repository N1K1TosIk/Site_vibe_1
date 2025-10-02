USE ai_lawyer_db;

-- Удаляем старые шаблоны чтобы заменить их улучшенными версиями
DELETE FROM contract_templates;

-- 1. ДОГОВОР ЗАЙМА
INSERT INTO contract_templates (name, category, description, template_content, variables) VALUES 
('Договор займа', 'financial', 'Договор займа денежных средств между физическими/юридическими лицами', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ЗАЙМА № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{lender_full_name}}, действующий(-ая) на основании {{lender_legal_basis}}, ИНН {{lender_inn}}, именуемый(-ая) в дальнейшем "Займодавец", с одной стороны, и {{borrower_full_name}}, действующий(-ая) на основании {{borrower_legal_basis}}, ИНН {{borrower_inn}}, именуемый(-ая) в дальнейшем "Заемщик", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Займодавец передает в собственность Заемщику денежные средства в размере {{loan_amount}} ({{loan_amount_words}}) рублей, а Заемщик обязуется возвратить Займодавцу указанную сумму в порядке и сроки, установленные настоящим договором.
</p>

<p style="text-align: justify;">
1.2. Заем является {{loan_type}}.
</p>

<p style="text-align: justify;">
1.3. Процентная ставка составляет {{interest_rate}}% годовых.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. СРОК ЗАЙМА
</div>

<p style="text-align: justify;">
2.1. Заем предоставляется на срок до {{repayment_date}}.
</p>

<p style="text-align: justify;">
2.2. Возврат займа осуществляется {{repayment_method}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ПРАВА И ОБЯЗАННОСТИ СТОРОН
</div>

<p style="text-align: justify;">
3.1. Займодавец обязуется передать Заемщику сумму займа в течение {{transfer_days}} рабочих дней с момента подписания договора.
</p>

<p style="text-align: justify;">
3.2. Заемщик обязуется:
- своевременно возвращать заемные средства;
- уплачивать проценты в установленном порядке;
- уведомлять Займодавца об изменении своих реквизитов.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. ОТВЕТСТВЕННОСТЬ СТОРОН
</div>

<p style="text-align: justify;">
4.1. За нарушение сроков возврата займа Заемщик уплачивает пеню в размере {{penalty_rate}}% от просроченной суммы за каждый день просрочки.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
5. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ
</div>

<p style="text-align: justify;">
5.1. Договор вступает в силу с момента подписания и действует до полного исполнения обязательств сторонами.
</p>

<p style="text-align: justify;">
5.2. Споры разрешаются в соответствии с законодательством РФ.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Займодавец:</strong><br/>
{{lender_full_name}}<br/>
ИНН: {{lender_inn}}<br/>
Адрес: {{lender_address}}<br/>
Тел.: {{lender_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{lender_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Заемщик:</strong><br/>
{{borrower_full_name}}<br/>
ИНН: {{borrower_inn}}<br/>
Адрес: {{borrower_address}}<br/>
Тел.: {{borrower_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{borrower_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'lender_full_name', 'Полное наименование займодавца',
    'lender_legal_basis', 'Основание полномочий займодавца',
    'lender_inn', 'ИНН займодавца',
    'borrower_full_name', 'Полное наименование заемщика',
    'borrower_legal_basis', 'Основание полномочий заемщика',
    'borrower_inn', 'ИНН заемщика',
    'loan_amount', 'Сумма займа (цифрами)',
    'loan_amount_words', 'Сумма займа прописью',
    'loan_type', 'Тип займа (возмездный/безвозмездный)',
    'interest_rate', 'Процентная ставка',
    'repayment_date', 'Дата возврата займа',
    'repayment_method', 'Способ возврата займа',
    'transfer_days', 'Срок передачи займа (дней)',
    'penalty_rate', 'Размер пени (%)',
    'lender_address', 'Адрес займодавца',
    'lender_phone', 'Телефон займодавца',
    'borrower_address', 'Адрес заемщика',
    'borrower_phone', 'Телефон заемщика',
    'lender_signature', 'ФИО подписанта займодавца',
    'borrower_signature', 'ФИО подписанта заемщика'
)),

-- 2. ДОГОВОР ДАРЕНИЯ
('Договор дарения', 'civil', 'Договор безвозмездной передачи имущества', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ДАРЕНИЯ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{donor_full_name}}, действующий(-ая) на основании {{donor_legal_basis}}, ИНН {{donor_inn}}, именуемый(-ая) в дальнейшем "Даритель", с одной стороны, и {{recipient_full_name}}, действующий(-ая) на основании {{recipient_legal_basis}}, ИНН {{recipient_inn}}, именуемый(-ая) в дальнейшем "Одаряемый", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Даритель безвозмездно передает в собственность Одаряемому {{gift_description}}, именуемое в дальнейшем "Имущество".
</p>

<p style="text-align: justify;">
1.2. Характеристики передаваемого имущества: {{gift_characteristics}}.
</p>

<p style="text-align: justify;">
1.3. Оценочная стоимость передаваемого имущества составляет {{gift_value}} рублей.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ПРАВА И ОБЯЗАННОСТИ СТОРОН
</div>

<p style="text-align: justify;">
2.1. Даритель обязуется передать Имущество Одаряемому в срок до {{transfer_date}}.
</p>

<p style="text-align: justify;">
2.2. Одаряемый вправе принять дар либо отказаться от него.
</p>

<p style="text-align: justify;">
2.3. Передача Имущества оформляется актом приема-передачи.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ
</div>

<p style="text-align: justify;">
3.1. Договор составлен в соответствии с главой 32 Гражданского кодекса РФ.
</p>

<p style="text-align: justify;">
3.2. Договор вступает в силу с момента подписания.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Даритель:</strong><br/>
{{donor_full_name}}<br/>
ИНН: {{donor_inn}}<br/>
Адрес: {{donor_address}}<br/>
Тел.: {{donor_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{donor_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Одаряемый:</strong><br/>
{{recipient_full_name}}<br/>
ИНН: {{recipient_inn}}<br/>
Адрес: {{recipient_address}}<br/>
Тел.: {{recipient_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{recipient_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'donor_full_name', 'Полное наименование дарителя',
    'donor_legal_basis', 'Основание полномочий дарителя',
    'donor_inn', 'ИНН дарителя',
    'recipient_full_name', 'Полное наименование одаряемого',
    'recipient_legal_basis', 'Основание полномочий одаряемого',
    'recipient_inn', 'ИНН одаряемого',
    'gift_description', 'Описание передаваемого имущества',
    'gift_characteristics', 'Характеристики имущества',
    'gift_value', 'Оценочная стоимость имущества',
    'transfer_date', 'Дата передачи имущества',
    'donor_address', 'Адрес дарителя',
    'donor_phone', 'Телефон дарителя',
    'recipient_address', 'Адрес одаряемого',
    'recipient_phone', 'Телефон одаряемого',
    'donor_signature', 'ФИО подписанта дарителя',
    'recipient_signature', 'ФИО подписанта одаряемого'
)),

-- 3. ДОГОВОР КУПЛИ-ПРОДАЖИ НЕДВИЖИМОСТИ
('Договор купли-продажи недвижимости', 'real_estate', 'Договор купли-продажи недвижимого имущества', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР КУПЛИ-ПРОДАЖИ НЕДВИЖИМОГО ИМУЩЕСТВА № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{seller_full_name}}, действующий(-ая) на основании {{seller_legal_basis}}, ИНН {{seller_inn}}, именуемый(-ая) в дальнейшем "Продавец", с одной стороны, и {{buyer_full_name}}, действующий(-ая) на основании {{buyer_legal_basis}}, ИНН {{buyer_inn}}, именуемый(-ая) в дальнейшем "Покупатель", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Продавец обязуется передать в собственность Покупателю недвижимое имущество: {{property_type}} общей площадью {{total_area}} кв.м, жилой площадью {{living_area}} кв.м, расположенное по адресу: {{property_address}}.
</p>

<p style="text-align: justify;">
1.2. Кадастровый номер объекта: {{cadastral_number}}.
</p>

<p style="text-align: justify;">
1.3. Право собственности Продавца подтверждается {{ownership_document}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ЦЕНА И ПОРЯДОК РАСЧЕТОВ
</div>

<p style="text-align: justify;">
2.1. Цена недвижимого имущества составляет {{sale_price}} ({{sale_price_words}}) рублей.
</p>

<p style="text-align: justify;">
2.2. Расчеты производятся {{payment_method}} в срок до {{payment_deadline}}.
</p>

<p style="text-align: justify;">
2.3. Задаток в размере {{deposit_amount}} рублей внесен Покупателем {{deposit_date}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ОБРЕМЕНЕНИЯ И ОГРАНИЧЕНИЯ
</div>

<p style="text-align: justify;">
3.1. Недвижимое имущество {{encumbrances_status}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. ПЕРЕХОД ПРАВА СОБСТВЕННОСТИ
</div>

<p style="text-align: justify;">
4.1. Право собственности на недвижимое имущество переходит к Покупателю с момента государственной регистрации перехода права собственности.
</p>

<p style="text-align: justify;">
4.2. Передача недвижимого имущества оформляется актом приема-передачи.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
5. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ
</div>

<p style="text-align: justify;">
5.1. Договор составлен в соответствии с главой 30 Гражданского кодекса РФ и подлежит государственной регистрации.
</p>

<p style="text-align: justify;">
5.2. Расходы по государственной регистрации несет {{registration_expenses}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Продавец:</strong><br/>
{{seller_full_name}}<br/>
ИНН: {{seller_inn}}<br/>
Паспорт: {{seller_passport}}<br/>
Адрес: {{seller_address}}<br/>
Тел.: {{seller_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{seller_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Покупатель:</strong><br/>
{{buyer_full_name}}<br/>
ИНН: {{buyer_inn}}<br/>
Паспорт: {{buyer_passport}}<br/>
Адрес: {{buyer_address}}<br/>
Тел.: {{buyer_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{buyer_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'seller_full_name', 'ФИО продавца',
    'seller_legal_basis', 'Основание полномочий продавца',
    'seller_inn', 'ИНН продавца',
    'buyer_full_name', 'ФИО покупателя',
    'buyer_legal_basis', 'Основание полномочий покупателя',
    'buyer_inn', 'ИНН покупателя',
    'property_type', 'Тип недвижимости (квартира, дом, участок)',
    'total_area', 'Общая площадь',
    'living_area', 'Жилая площадь',
    'property_address', 'Адрес недвижимости',
    'cadastral_number', 'Кадастровый номер',
    'ownership_document', 'Документ о праве собственности',
    'sale_price', 'Цена продажи (цифрами)',
    'sale_price_words', 'Цена продажи прописью',
    'payment_method', 'Способ оплаты',
    'payment_deadline', 'Срок оплаты',
    'deposit_amount', 'Размер задатка',
    'deposit_date', 'Дата внесения задатка',
    'encumbrances_status', 'Статус обременений (свободно от обременений/имеются обременения)',
    'registration_expenses', 'Кто несет расходы по регистрации',
    'seller_passport', 'Паспортные данные продавца',
    'seller_address', 'Адрес продавца',
    'seller_phone', 'Телефон продавца',
    'buyer_passport', 'Паспортные данные покупателя',
    'buyer_address', 'Адрес покупателя',
    'buyer_phone', 'Телефон покупателя',
    'seller_signature', 'ФИО подписанта продавца',
    'buyer_signature', 'ФИО подписанта покупателя'
)),

-- 4. АГЕНТСКИЙ ДОГОВОР
('Агентский договор', 'commercial', 'Договор на совершение юридических и фактических действий', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
АГЕНТСКИЙ ДОГОВОР № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{principal_full_name}}, действующий(-ая) на основании {{principal_legal_basis}}, ИНН {{principal_inn}}, именуемый(-ая) в дальнейшем "Принципал", с одной стороны, и {{agent_full_name}}, действующий(-ая) на основании {{agent_legal_basis}}, ИНН {{agent_inn}}, именуемый(-ая) в дальнейшем "Агент", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Принципал поручает, а Агент обязуется за вознаграждение совершать от своего имени, но за счет Принципала следующие действия: {{agent_duties}}.
</p>

<p style="text-align: justify;">
1.2. Агент действует в интересах Принципала в пределах предоставленных полномочий.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ОБЯЗАННОСТИ СТОРОН
</div>

<p style="text-align: justify;">
2.1. Агент обязуется:
- действовать в интересах Принципала;
- представлять отчеты о выполненных действиях;
- передавать Принципалу все полученное по сделкам.
</p>

<p style="text-align: justify;">
2.2. Принципал обязуется:
- предоставить Агенту необходимые средства;
- принять и оплатить выполненные Агентом действия;
- возместить понесенные Агентом расходы.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ВОЗНАГРАЖДЕНИЕ
</div>

<p style="text-align: justify;">
3.1. Размер агентского вознаграждения составляет {{commission_rate}}% от суммы заключенных сделок, но не менее {{minimum_commission}} рублей.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. СРОК ДЕЙСТВИЯ ДОГОВОРА
</div>

<p style="text-align: justify;">
4.1. Договор действует с {{start_date}} по {{end_date}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Принципал:</strong><br/>
{{principal_full_name}}<br/>
ИНН: {{principal_inn}}<br/>
Адрес: {{principal_address}}<br/>
Тел.: {{principal_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{principal_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Агент:</strong><br/>
{{agent_full_name}}<br/>
ИНН: {{agent_inn}}<br/>
Адрес: {{agent_address}}<br/>
Тел.: {{agent_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{agent_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'principal_full_name', 'Полное наименование принципала',
    'principal_legal_basis', 'Основание полномочий принципала',
    'principal_inn', 'ИНН принципала',
    'agent_full_name', 'Полное наименование агента',
    'agent_legal_basis', 'Основание полномочий агента',
    'agent_inn', 'ИНН агента',
    'agent_duties', 'Обязанности агента',
    'commission_rate', 'Процент комиссии',
    'minimum_commission', 'Минимальная комиссия',
    'start_date', 'Дата начала действия договора',
    'end_date', 'Дата окончания действия договора',
    'principal_address', 'Адрес принципала',
    'principal_phone', 'Телефон принципала',
    'agent_address', 'Адрес агента',
    'agent_phone', 'Телефон агента',
    'principal_signature', 'ФИО подписанта принципала',
    'agent_signature', 'ФИО подписанта агента'
)),

-- 5. ДОГОВОР ПОСТАВКИ
('Договор поставки', 'supply', 'Договор поставки товаров для предпринимательской деятельности', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ПОСТАВКИ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{supplier_full_name}}, действующее на основании {{supplier_legal_basis}}, ИНН {{supplier_inn}}, именуемое в дальнейшем "Поставщик", с одной стороны, и {{buyer_full_name}}, действующее на основании {{buyer_legal_basis}}, ИНН {{buyer_inn}}, именуемое в дальнейшем "Покупатель", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Поставщик обязуется передать в установленные сроки производимые или закупаемые им товары Покупателю для использования в предпринимательской деятельности или в иных целях, не связанных с личным, семейным, домашним и иным подобным использованием.
</p>

<p style="text-align: justify;">
1.2. Наименование и характеристики товара: {{goods_description}}.
</p>

<p style="text-align: justify;">
1.3. Количество товара: {{goods_quantity}} {{measurement_unit}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ЦЕНА И ПОРЯДОК РАСЧЕТОВ
</div>

<p style="text-align: justify;">
2.1. Общая стоимость товара составляет {{total_price}} ({{total_price_words}}) рублей, включая НДС {{vat_rate}}%.
</p>

<p style="text-align: justify;">
2.2. Оплата производится {{payment_terms}} в течение {{payment_days}} банковских дней с момента {{payment_trigger}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. СРОКИ И ПОРЯДОК ПОСТАВКИ
</div>

<p style="text-align: justify;">
3.1. Поставка товара осуществляется {{delivery_terms}} в срок до {{delivery_date}}.
</p>

<p style="text-align: justify;">
3.2. Место поставки: {{delivery_address}}.
</p>

<p style="text-align: justify;">
3.3. Приемка товара оформляется товарной накладной и актом приема-передачи.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. КАЧЕСТВО ТОВАРА
</div>

<p style="text-align: justify;">
4.1. Качество товара должно соответствовать {{quality_standards}}.
</p>

<p style="text-align: justify;">
4.2. Гарантийный срок составляет {{warranty_period}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
5. ОТВЕТСТВЕННОСТЬ СТОРОН
</div>

<p style="text-align: justify;">
5.1. За просрочку поставки Поставщик уплачивает пеню в размере {{delay_penalty}}% от стоимости недопоставленного товара за каждый день просрочки.
</p>

<p style="text-align: justify;">
5.2. За просрочку оплаты Покупатель уплачивает пеню в размере {{payment_penalty}}% от неоплаченной суммы за каждый день просрочки.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Поставщик:</strong><br/>
{{supplier_full_name}}<br/>
ИНН: {{supplier_inn}}<br/>
КПП: {{supplier_kpp}}<br/>
Адрес: {{supplier_address}}<br/>
Тел.: {{supplier_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{supplier_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Покупатель:</strong><br/>
{{buyer_full_name}}<br/>
ИНН: {{buyer_inn}}<br/>
КПП: {{buyer_kpp}}<br/>
Адрес: {{buyer_address}}<br/>
Тел.: {{buyer_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{buyer_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'supplier_full_name', 'Полное наименование поставщика',
    'supplier_legal_basis', 'Основание полномочий поставщика',
    'supplier_inn', 'ИНН поставщика',
    'buyer_full_name', 'Полное наименование покупателя',
    'buyer_legal_basis', 'Основание полномочий покупателя',
    'buyer_inn', 'ИНН покупателя',
    'goods_description', 'Описание товара',
    'goods_quantity', 'Количество товара',
    'measurement_unit', 'Единица измерения',
    'total_price', 'Общая стоимость товара',
    'total_price_words', 'Общая стоимость прописью',
    'vat_rate', 'Ставка НДС',
    'payment_terms', 'Условия оплаты',
    'payment_days', 'Срок оплаты (дней)',
    'payment_trigger', 'Момент начала отсчета срока оплаты',
    'delivery_terms', 'Условия поставки',
    'delivery_date', 'Срок поставки',
    'delivery_address', 'Место поставки',
    'quality_standards', 'Стандарты качества',
    'warranty_period', 'Гарантийный срок',
    'delay_penalty', 'Пеня за просрочку поставки (%)',
    'payment_penalty', 'Пеня за просрочку оплаты (%)',
    'supplier_kpp', 'КПП поставщика',
    'supplier_address', 'Адрес поставщика',
    'supplier_phone', 'Телефон поставщика',
    'buyer_kpp', 'КПП покупателя',
    'buyer_address', 'Адрес покупателя',
    'buyer_phone', 'Телефон покупателя',
    'supplier_signature', 'ФИО подписанта поставщика',
    'buyer_signature', 'ФИО подписанта покупателя'
)); 