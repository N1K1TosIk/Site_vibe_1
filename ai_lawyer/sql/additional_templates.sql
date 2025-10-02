USE ai_lawyer_db;

-- Дополнительные шаблоны договоров

-- 6. ДОГОВОР СТРОИТЕЛЬНОГО ПОДРЯДА
INSERT INTO contract_templates (name, category, description, template_content, variables) VALUES 
('Договор строительного подряда', 'construction', 'Договор на выполнение строительных работ', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР СТРОИТЕЛЬНОГО ПОДРЯДА № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{customer_full_name}}, действующий(-ая) на основании {{customer_legal_basis}}, ИНН {{customer_inn}}, именуемый(-ая) в дальнейшем "Заказчик", с одной стороны, и {{contractor_full_name}}, действующий(-ая) на основании {{contractor_legal_basis}}, ИНН {{contractor_inn}}, именуемый(-ая) в дальнейшем "Подрядчик", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Подрядчик обязуется выполнить следующие строительные работы: {{work_description}} по адресу: {{construction_address}}, а Заказчик обязуется принять выполненные работы и оплатить их.
</p>

<p style="text-align: justify;">
1.2. Работы выполняются в соответствии с технической документацией: {{technical_documentation}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ЦЕНА РАБОТ И ПОРЯДОК РАСЧЕТОВ
</div>

<p style="text-align: justify;">
2.1. Общая стоимость работ составляет {{total_cost}} ({{total_cost_words}}) рублей, включая материалы.
</p>

<p style="text-align: justify;">
2.2. Оплата производится поэтапно: {{payment_schedule}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. СРОКИ ВЫПОЛНЕНИЯ РАБОТ
</div>

<p style="text-align: justify;">
3.1. Начало работ: {{start_date}}.
</p>

<p style="text-align: justify;">
3.2. Окончание работ: {{completion_date}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. ГАРАНТИИ
</div>

<p style="text-align: justify;">
4.1. Подрядчик гарантирует качество выполненных работ в течение {{warranty_period}} с момента подписания акта приемки работ.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Заказчик:</strong><br/>
{{customer_full_name}}<br/>
ИНН: {{customer_inn}}<br/>
Адрес: {{customer_address}}<br/>
Тел.: {{customer_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{customer_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Подрядчик:</strong><br/>
{{contractor_full_name}}<br/>
ИНН: {{contractor_inn}}<br/>
Адрес: {{contractor_address}}<br/>
Тел.: {{contractor_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{contractor_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'customer_full_name', 'Полное наименование заказчика',
    'customer_legal_basis', 'Основание полномочий заказчика',
    'customer_inn', 'ИНН заказчика',
    'contractor_full_name', 'Полное наименование подрядчика',
    'contractor_legal_basis', 'Основание полномочий подрядчика',
    'contractor_inn', 'ИНН подрядчика',
    'work_description', 'Описание строительных работ',
    'construction_address', 'Адрес строительства',
    'technical_documentation', 'Техническая документация',
    'total_cost', 'Общая стоимость работ',
    'total_cost_words', 'Общая стоимость прописью',
    'payment_schedule', 'График платежей',
    'start_date', 'Дата начала работ',
    'completion_date', 'Дата окончания работ',
    'warranty_period', 'Гарантийный период',
    'customer_address', 'Адрес заказчика',
    'customer_phone', 'Телефон заказчика',
    'contractor_address', 'Адрес подрядчика',
    'contractor_phone', 'Телефон подрядчика',
    'customer_signature', 'ФИО подписанта заказчика',
    'contractor_signature', 'ФИО подписанта подрядчика'
)),

-- 7. ЛИЦЕНЗИОННЫЙ ДОГОВОР
('Лицензионный договор', 'intellectual', 'Договор на использование интеллектуальной собственности', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ЛИЦЕНЗИОННЫЙ ДОГОВОР № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{licensor_full_name}}, действующий(-ая) на основании {{licensor_legal_basis}}, ИНН {{licensor_inn}}, именуемый(-ая) в дальнейшем "Лицензиар", с одной стороны, и {{licensee_full_name}}, действующий(-ая) на основании {{licensee_legal_basis}}, ИНН {{licensee_inn}}, именуемый(-ая) в дальнейшем "Лицензиат", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Лицензиар предоставляет Лицензиату {{license_type}} лицензию на использование {{ip_object}}, охраняемого {{protection_document}}.
</p>

<p style="text-align: justify;">
1.2. Способы использования: {{usage_methods}}.
</p>

<p style="text-align: justify;">
1.3. Территория действия лицензии: {{territory}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ЛИЦЕНЗИОННОЕ ВОЗНАГРАЖДЕНИЕ
</div>

<p style="text-align: justify;">
2.1. Размер лицензионного вознаграждения составляет {{royalty_amount}} рублей.
</p>

<p style="text-align: justify;">
2.2. Порядок выплаты: {{payment_schedule}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. СРОК ДЕЙСТВИЯ ЛИЦЕНЗИИ
</div>

<p style="text-align: justify;">
3.1. Лицензия действует с {{start_date}} по {{end_date}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Лицензиар:</strong><br/>
{{licensor_full_name}}<br/>
ИНН: {{licensor_inn}}<br/>
Адрес: {{licensor_address}}<br/>
Тел.: {{licensor_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{licensor_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Лицензиат:</strong><br/>
{{licensee_full_name}}<br/>
ИНН: {{licensee_inn}}<br/>
Адрес: {{licensee_address}}<br/>
Тел.: {{licensee_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{licensee_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'licensor_full_name', 'Полное наименование лицензиара',
    'licensor_legal_basis', 'Основание полномочий лицензиара',
    'licensor_inn', 'ИНН лицензиара',
    'licensee_full_name', 'Полное наименование лицензиата',
    'licensee_legal_basis', 'Основание полномочий лицензиата',
    'licensee_inn', 'ИНН лицензиата',
    'license_type', 'Тип лицензии (исключительная/неисключительная)',
    'ip_object', 'Объект интеллектуальной собственности',
    'protection_document', 'Охранный документ',
    'usage_methods', 'Способы использования',
    'territory', 'Территория действия',
    'royalty_amount', 'Размер вознаграждения',
    'payment_schedule', 'График платежей',
    'start_date', 'Дата начала действия',
    'end_date', 'Дата окончания действия',
    'licensor_address', 'Адрес лицензиара',
    'licensor_phone', 'Телефон лицензиара',
    'licensee_address', 'Адрес лицензиата',
    'licensee_phone', 'Телефон лицензиата',
    'licensor_signature', 'ФИО подписанта лицензиара',
    'licensee_signature', 'ФИО подписанта лицензиата'
)),

-- 8. БРАЧНЫЙ ДОГОВОР
('Брачный договор', 'family', 'Соглашение о имущественных правах супругов', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
БРАЧНЫЙ ДОГОВОР № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{spouse1_full_name}}, {{spouse1_birth_date}} года рождения, паспорт {{spouse1_passport}}, именуемый(-ая) в дальнейшем "Первый супруг", с одной стороны, и {{spouse2_full_name}}, {{spouse2_birth_date}} года рождения, паспорт {{spouse2_passport}}, именуемый(-ая) в дальнейшем "Второй супруг", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ОБЩИЕ ПОЛОЖЕНИЯ
</div>

<p style="text-align: justify;">
1.1. Настоящий договор определяет имущественные права и обязанности супругов в браке и (или) в случае его расторжения.
</p>

<p style="text-align: justify;">
1.2. Договор составлен в соответствии с главой 8 Семейного кодекса РФ.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ПРАВОВОЙ РЕЖИМ ИМУЩЕСТВА
</div>

<p style="text-align: justify;">
2.1. В отношении {{property_regime_scope}} устанавливается режим {{property_regime}}.
</p>

<p style="text-align: justify;">
2.2. Имущество, принадлежавшее каждому из супругов до вступления в брак: {{premarital_property}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ПРАВА И ОБЯЗАННОСТИ ПО ВЗАИМНОМУ СОДЕРЖАНИЮ
</div>

<p style="text-align: justify;">
3.1. {{maintenance_obligations}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. УЧАСТИЕ В ДОХОДАХ ДРУГ ДРУГА
</div>

<p style="text-align: justify;">
4.1. {{income_participation}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
5. ПОРЯДОК НЕСЕНИЯ СЕМЕЙНЫХ РАСХОДОВ
</div>

<p style="text-align: justify;">
5.1. {{family_expenses}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
6. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ
</div>

<p style="text-align: justify;">
6.1. Договор подлежит нотариальному удостоверению.
</p>

<p style="text-align: justify;">
6.2. Договор может быть изменен или расторгнут в любое время по соглашению супругов.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Первый супруг:</strong><br/>
{{spouse1_full_name}}<br/>
Паспорт: {{spouse1_passport}}<br/>
Адрес: {{spouse1_address}}<br/>
Тел.: {{spouse1_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{spouse1_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Второй супруг:</strong><br/>
{{spouse2_full_name}}<br/>
Паспорт: {{spouse2_passport}}<br/>
Адрес: {{spouse2_address}}<br/>
Тел.: {{spouse2_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{spouse2_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'spouse1_full_name', 'ФИО первого супруга',
    'spouse1_birth_date', 'Дата рождения первого супруга',
    'spouse1_passport', 'Паспорт первого супруга',
    'spouse2_full_name', 'ФИО второго супруга',
    'spouse2_birth_date', 'Дата рождения второго супруга',
    'spouse2_passport', 'Паспорт второго супруга',
    'property_regime_scope', 'К какому имуществу применяется режим',
    'property_regime', 'Правовой режим (долевая/раздельная/совместная собственность)',
    'premarital_property', 'Режим добрачного имущества',
    'maintenance_obligations', 'Обязанности по взаимному содержанию',
    'income_participation', 'Участие в доходах друг друга',
    'family_expenses', 'Порядок несения семейных расходов',
    'spouse1_address', 'Адрес первого супруга',
    'spouse1_phone', 'Телефон первого супруга',
    'spouse2_address', 'Адрес второго супруга',
    'spouse2_phone', 'Телефон второго супруга',
    'spouse1_signature', 'Подпись первого супруга',
    'spouse2_signature', 'Подпись второго супруга'
)),

-- 9. ДОГОВОР ХРАНЕНИЯ
('Договор хранения', 'service', 'Договор на оказание услуг хранения имущества', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ХРАНЕНИЯ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{keeper_full_name}}, действующий(-ая) на основании {{keeper_legal_basis}}, ИНН {{keeper_inn}}, именуемый(-ая) в дальнейшем "Хранитель", с одной стороны, и {{depositor_full_name}}, действующий(-ая) на основании {{depositor_legal_basis}}, ИНН {{depositor_inn}}, именуемый(-ая) в дальнейшем "Поклажедатель", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Поклажедатель передает на хранение Хранителю {{item_description}}, а Хранитель обязуется хранить переданное имущество и возвратить его в сохранности.
</p>

<p style="text-align: justify;">
1.2. Количество и состояние имущества: {{item_condition}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. СРОК ХРАНЕНИЯ
</div>

<p style="text-align: justify;">
2.1. Срок хранения: с {{storage_start}} по {{storage_end}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ПЛАТА ЗА ХРАНЕНИЕ
</div>

<p style="text-align: justify;">
3.1. Плата за хранение составляет {{storage_fee}} рублей {{fee_period}}.
</p>

<p style="text-align: justify;">
3.2. Оплата производится {{payment_terms}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. ПРАВА И ОБЯЗАННОСТИ СТОРОН
</div>

<p style="text-align: justify;">
4.1. Хранитель обязуется обеспечить сохранность переданного имущества и не использовать его без согласия Поклажедателя.
</p>

<p style="text-align: justify;">
4.2. Поклажедатель обязуется своевременно оплачивать услуги хранения.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Хранитель:</strong><br/>
{{keeper_full_name}}<br/>
ИНН: {{keeper_inn}}<br/>
Адрес: {{keeper_address}}<br/>
Тел.: {{keeper_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{keeper_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Поклажедатель:</strong><br/>
{{depositor_full_name}}<br/>
ИНН: {{depositor_inn}}<br/>
Адрес: {{depositor_address}}<br/>
Тел.: {{depositor_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{depositor_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'keeper_full_name', 'Полное наименование хранителя',
    'keeper_legal_basis', 'Основание полномочий хранителя',
    'keeper_inn', 'ИНН хранителя',
    'depositor_full_name', 'Полное наименование поклажедателя',
    'depositor_legal_basis', 'Основание полномочий поклажедателя',
    'depositor_inn', 'ИНН поклажедателя',
    'item_description', 'Описание передаваемого на хранение имущества',
    'item_condition', 'Количество и состояние имущества',
    'storage_start', 'Дата начала хранения',
    'storage_end', 'Дата окончания хранения',
    'storage_fee', 'Плата за хранение',
    'fee_period', 'Период оплаты (в месяц/в день)',
    'payment_terms', 'Условия оплаты',
    'keeper_address', 'Адрес хранителя',
    'keeper_phone', 'Телефон хранителя',
    'depositor_address', 'Адрес поклажедателя',
    'depositor_phone', 'Телефон поклажедателя',
    'keeper_signature', 'ФИО подписанта хранителя',
    'depositor_signature', 'ФИО подписанта поклажедателя'
)),

-- 10. ДОГОВОР ПЕРЕВОЗКИ ГРУЗОВ
('Договор перевозки грузов', 'transport', 'Договор на перевозку грузов автомобильным транспортом', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ПЕРЕВОЗКИ ГРУЗОВ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{carrier_full_name}}, действующий(-ая) на основании {{carrier_legal_basis}}, ИНН {{carrier_inn}}, именуемый(-ая) в дальнейшем "Перевозчик", с одной стороны, и {{shipper_full_name}}, действующий(-ая) на основании {{shipper_legal_basis}}, ИНН {{shipper_inn}}, именуемый(-ая) в дальнейшем "Грузоотправитель", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Перевозчик обязуется перевезти вверенный ему Грузоотправителем груз {{cargo_description}} весом {{cargo_weight}} кг из пункта {{departure_point}} в пункт {{destination_point}} и выдать его управомоченному на получение груза лицу (получателю).
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. СРОКИ ПЕРЕВОЗКИ
</div>

<p style="text-align: justify;">
2.1. Срок доставки груза: {{delivery_time}}.
</p>

<p style="text-align: justify;">
2.2. Дата и время подачи транспорта под погрузку: {{loading_datetime}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ПРОВОЗНАЯ ПЛАТА
</div>

<p style="text-align: justify;">
3.1. Провозная плата составляет {{transportation_fee}} ({{transportation_fee_words}}) рублей.
</p>

<p style="text-align: justify;">
3.2. Оплата производится {{payment_method}} в срок {{payment_deadline}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. ОТВЕТСТВЕННОСТЬ СТОРОН
</div>

<p style="text-align: justify;">
4.1. За утрату, недостачу или повреждение груза Перевозчик несет ответственность в размере {{liability_amount}}.
</p>

<p style="text-align: justify;">
4.2. За просрочку доставки груза Перевозчик уплачивает штраф в размере {{delay_penalty}}% от провозной платы за каждые сутки просрочки.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Перевозчик:</strong><br/>
{{carrier_full_name}}<br/>
ИНН: {{carrier_inn}}<br/>
Лицензия: {{carrier_license}}<br/>
Адрес: {{carrier_address}}<br/>
Тел.: {{carrier_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{carrier_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Грузоотправитель:</strong><br/>
{{shipper_full_name}}<br/>
ИНН: {{shipper_inn}}<br/>
Адрес: {{shipper_address}}<br/>
Тел.: {{shipper_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{shipper_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'carrier_full_name', 'Полное наименование перевозчика',
    'carrier_legal_basis', 'Основание полномочий перевозчика',
    'carrier_inn', 'ИНН перевозчика',
    'shipper_full_name', 'Полное наименование грузоотправителя',
    'shipper_legal_basis', 'Основание полномочий грузоотправителя',
    'shipper_inn', 'ИНН грузоотправителя',
    'cargo_description', 'Описание груза',
    'cargo_weight', 'Вес груза',
    'departure_point', 'Пункт отправления',
    'destination_point', 'Пункт назначения',
    'delivery_time', 'Срок доставки',
    'loading_datetime', 'Дата и время подачи транспорта',
    'transportation_fee', 'Провозная плата',
    'transportation_fee_words', 'Провозная плата прописью',
    'payment_method', 'Способ оплаты',
    'payment_deadline', 'Срок оплаты',
    'liability_amount', 'Размер ответственности за утрату груза',
    'delay_penalty', 'Штраф за просрочку (%)',
    'carrier_license', 'Номер лицензии перевозчика',
    'carrier_address', 'Адрес перевозчика',
    'carrier_phone', 'Телефон перевозчика',
    'shipper_address', 'Адрес грузоотправителя',
    'shipper_phone', 'Телефон грузоотправителя',
    'carrier_signature', 'ФИО подписанта перевозчика',
    'shipper_signature', 'ФИО подписанта грузоотправителя'
)); 