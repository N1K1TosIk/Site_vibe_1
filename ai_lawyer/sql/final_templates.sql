USE ai_lawyer_db;

-- Финальная коллекция дополнительных шаблонов

-- 11. ДОГОВОР ДОВЕРИТЕЛЬНОГО УПРАВЛЕНИЯ
INSERT INTO contract_templates (name, category, description, template_content, variables) VALUES 
('Договор доверительного управления', 'trust', 'Договор доверительного управления имуществом', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ДОВЕРИТЕЛЬНОГО УПРАВЛЕНИЯ ИМУЩЕСТВОМ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{trustor_full_name}}, действующий(-ая) на основании {{trustor_legal_basis}}, ИНН {{trustor_inn}}, именуемый(-ая) в дальнейшем "Учредитель управления", с одной стороны, и {{trustee_full_name}}, действующий(-ая) на основании {{trustee_legal_basis}}, ИНН {{trustee_inn}}, именуемый(-ая) в дальнейшем "Доверительный управляющий", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Учредитель управления передает Доверительному управляющему на определенный срок имущество: {{property_description}} в доверительное управление, а Доверительный управляющий обязуется осуществлять управление этим имуществом в интересах {{beneficiary_info}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ЦЕЛИ УПРАВЛЕНИЯ
</div>

<p style="text-align: justify;">
2.1. Целями доверительного управления являются: {{management_goals}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ВОЗНАГРАЖДЕНИЕ
</div>

<p style="text-align: justify;">
3.1. Размер вознаграждения Доверительного управляющего составляет {{management_fee}}% от {{fee_base}} {{fee_period}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. СРОК УПРАВЛЕНИЯ
</div>

<p style="text-align: justify;">
4.1. Срок доверительного управления: с {{start_date}} по {{end_date}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Учредитель управления:</strong><br/>
{{trustor_full_name}}<br/>
ИНН: {{trustor_inn}}<br/>
Адрес: {{trustor_address}}<br/>
Тел.: {{trustor_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{trustor_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Доверительный управляющий:</strong><br/>
{{trustee_full_name}}<br/>
ИНН: {{trustee_inn}}<br/>
Адрес: {{trustee_address}}<br/>
Тел.: {{trustee_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{trustee_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'trustor_full_name', 'Полное наименование учредителя управления',
    'trustor_legal_basis', 'Основание полномочий учредителя',
    'trustor_inn', 'ИНН учредителя управления',
    'trustee_full_name', 'Полное наименование доверительного управляющего',
    'trustee_legal_basis', 'Основание полномочий управляющего',
    'trustee_inn', 'ИНН доверительного управляющего',
    'property_description', 'Описание передаваемого в управление имущества',
    'beneficiary_info', 'Информация о выгодоприобретателе',
    'management_goals', 'Цели доверительного управления',
    'management_fee', 'Размер вознаграждения (%)',
    'fee_base', 'База для расчета вознаграждения',
    'fee_period', 'Период выплаты вознаграждения',
    'start_date', 'Дата начала управления',
    'end_date', 'Дата окончания управления',
    'trustor_address', 'Адрес учредителя управления',
    'trustor_phone', 'Телефон учредителя управления',
    'trustee_address', 'Адрес доверительного управляющего',
    'trustee_phone', 'Телефон доверительного управляющего',
    'trustor_signature', 'ФИО подписанта учредителя',
    'trustee_signature', 'ФИО подписанта управляющего'
)),

-- 12. ДОГОВОР КОМИССИИ
('Договор комиссии', 'commercial', 'Договор комиссии на совершение сделок', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР КОМИССИИ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{principal_full_name}}, действующий(-ая) на основании {{principal_legal_basis}}, ИНН {{principal_inn}}, именуемый(-ая) в дальнейшем "Комитент", с одной стороны, и {{commissioner_full_name}}, действующий(-ая) на основании {{commissioner_legal_basis}}, ИНН {{commissioner_inn}}, именуемый(-ая) в дальнейшем "Комиссионер", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Комитент поручает, а Комиссионер обязуется за вознаграждение совершить одну или несколько сделок от своего имени, но за счет Комитента: {{commission_scope}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ОБЯЗАННОСТИ КОМИССИОНЕРА
</div>

<p style="text-align: justify;">
2.1. Комиссионер обязуется:
- совершать сделки на наиболее выгодных для Комитента условиях;
- представлять Комитенту отчеты о выполненных поручениях;
- передавать Комитенту все полученное по заключенным сделкам.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. КОМИССИОННОЕ ВОЗНАГРАЖДЕНИЕ
</div>

<p style="text-align: justify;">
3.1. Размер комиссионного вознаграждения составляет {{commission_rate}}% от суммы заключенной сделки.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Комитент:</strong><br/>
{{principal_full_name}}<br/>
ИНН: {{principal_inn}}<br/>
Адрес: {{principal_address}}<br/>
Тел.: {{principal_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{principal_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Комиссионер:</strong><br/>
{{commissioner_full_name}}<br/>
ИНН: {{commissioner_inn}}<br/>
Адрес: {{commissioner_address}}<br/>
Тел.: {{commissioner_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{commissioner_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'principal_full_name', 'Полное наименование комитента',
    'principal_legal_basis', 'Основание полномочий комитента',
    'principal_inn', 'ИНН комитента',
    'commissioner_full_name', 'Полное наименование комиссионера',
    'commissioner_legal_basis', 'Основание полномочий комиссионера',
    'commissioner_inn', 'ИНН комиссионера',
    'commission_scope', 'Предмет комиссии',
    'commission_rate', 'Размер комиссионного вознаграждения (%)',
    'principal_address', 'Адрес комитента',
    'principal_phone', 'Телефон комитента',
    'commissioner_address', 'Адрес комиссионера',
    'commissioner_phone', 'Телефон комиссионера',
    'principal_signature', 'ФИО подписанта комитента',
    'commissioner_signature', 'ФИО подписанта комиссионера'
)),

-- 13. ДОГОВОР ПОРУЧЕНИЯ
('Договор поручения', 'service', 'Договор поручения на совершение юридических действий', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ПОРУЧЕНИЯ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{principal_full_name}}, действующий(-ая) на основании {{principal_legal_basis}}, ИНН {{principal_inn}}, именуемый(-ая) в дальнейшем "Доверитель", с одной стороны, и {{attorney_full_name}}, действующий(-ая) на основании {{attorney_legal_basis}}, ИНН {{attorney_inn}}, именуемый(-ая) в дальнейшем "Поверенный", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Доверитель поручает, а Поверенный обязуется совершить от имени и за счет Доверителя следующие юридические действия: {{actions_description}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ПРАВА И ОБЯЗАННОСТИ ПОВЕРЕННОГО
</div>

<p style="text-align: justify;">
2.1. Поверенный обязуется исполнить принятое поручение в соответствии с указаниями Доверителя.
</p>

<p style="text-align: justify;">
2.2. Поверенный должен без промедления уведомлять Доверителя о ходе исполнения поручения.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ВОЗНАГРАЖДЕНИЕ
</div>

<p style="text-align: justify;">
3.1. {{fee_clause}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Доверитель:</strong><br/>
{{principal_full_name}}<br/>
ИНН: {{principal_inn}}<br/>
Адрес: {{principal_address}}<br/>
Тел.: {{principal_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{principal_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Поверенный:</strong><br/>
{{attorney_full_name}}<br/>
ИНН: {{attorney_inn}}<br/>
Адрес: {{attorney_address}}<br/>
Тел.: {{attorney_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{attorney_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'principal_full_name', 'Полное наименование доверителя',
    'principal_legal_basis', 'Основание полномочий доверителя',
    'principal_inn', 'ИНН доверителя',
    'attorney_full_name', 'Полное наименование поверенного',
    'attorney_legal_basis', 'Основание полномочий поверенного',
    'attorney_inn', 'ИНН поверенного',
    'actions_description', 'Описание поручаемых действий',
    'fee_clause', 'Условия вознаграждения',
    'principal_address', 'Адрес доверителя',
    'principal_phone', 'Телефон доверителя',
    'attorney_address', 'Адрес поверенного',
    'attorney_phone', 'Телефон поверенного',
    'principal_signature', 'ФИО подписанта доверителя',
    'attorney_signature', 'ФИО подписанта поверенного'
)),

-- 14. СОГЛАШЕНИЕ О РАЗДЕЛЕ ИМУЩЕСТВА
('Соглашение о разделе имущества', 'family', 'Соглашение супругов о разделе общего имущества', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
СОГЛАШЕНИЕ О РАЗДЕЛЕ ОБЩЕГО ИМУЩЕСТВА СУПРУГОВ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{spouse1_full_name}}, {{spouse1_birth_date}} года рождения, паспорт {{spouse1_passport}}, именуемый(-ая) в дальнейшем "Первый супруг", с одной стороны, и {{spouse2_full_name}}, {{spouse2_birth_date}} года рождения, паспорт {{spouse2_passport}}, именуемый(-ая) в дальнейшем "Второй супруг", с другой стороны, {{marriage_status}}, заключили настоящее соглашение о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ СОГЛАШЕНИЯ
</div>

<p style="text-align: justify;">
1.1. Стороны договариваются о разделе следующего общего имущества: {{shared_property}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ПОРЯДОК РАЗДЕЛА
</div>

<p style="text-align: justify;">
2.1. Первому супругу передается: {{spouse1_property}}.
</p>

<p style="text-align: justify;">
2.2. Второму супругу передается: {{spouse2_property}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ОБЩИЕ ОБЯЗАТЕЛЬСТВА
</div>

<p style="text-align: justify;">
3.1. Общие долги супругов: {{shared_debts}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ
</div>

<p style="text-align: justify;">
4.1. Соглашение подлежит нотариальному удостоверению.
</p>

<p style="text-align: justify;">
4.2. После раздела имущества стороны не имеют друг к другу имущественных претензий.
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
    'marriage_status', 'Статус брака (состоящие в браке/бывшие супруги)',
    'shared_property', 'Описание совместного имущества',
    'spouse1_property', 'Имущество, передаваемое первому супругу',
    'spouse2_property', 'Имущество, передаваемое второму супругу',
    'shared_debts', 'Распределение общих долгов',
    'spouse1_address', 'Адрес первого супруга',
    'spouse1_phone', 'Телефон первого супруга',
    'spouse2_address', 'Адрес второго супруга',
    'spouse2_phone', 'Телефон второго супруга',
    'spouse1_signature', 'Подпись первого супруга',
    'spouse2_signature', 'Подпись второго супруга'
)),

-- 15. ДОГОВОР СОЦИАЛЬНОГО НАЙМА
('Договор социального найма', 'rental', 'Договор социального найма жилого помещения', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР СОЦИАЛЬНОГО НАЙМА ЖИЛОГО ПОМЕЩЕНИЯ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{landlord_full_name}}, действующий на основании {{landlord_legal_basis}}, именуемый в дальнейшем "Наймодатель", с одной стороны, и {{tenant_full_name}}, именуемый(-ая) в дальнейшем "Наниматель", с другой стороны, на основании {{allocation_document}}, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Наймодатель предоставляет Нанимателю и членам его семьи жилое помещение в {{dwelling_type}} общей площадью {{total_area}} кв.м, жилой площадью {{living_area}} кв.м, расположенное по адресу: {{property_address}}.
</p>

<p style="text-align: justify;">
1.2. Жилое помещение предоставляется для постоянного проживания.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ПРАВА И ОБЯЗАННОСТИ НАНИМАТЕЛЯ
</div>

<p style="text-align: justify;">
2.1. Наниматель имеет право:
- пользоваться жилым помещением и общим имуществом;
- требовать своевременного проведения капитального ремонта;
- с согласия Наймодателя производить переустройство и перепланировку.
</p>

<p style="text-align: justify;">
2.2. Наниматель обязан:
- использовать помещение по назначению;
- своевременно вносить плату за жилое помещение;
- поддерживать помещение в надлежащем состоянии.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ПЛАТА ЗА ЖИЛОЕ ПОМЕЩЕНИЕ
</div>

<p style="text-align: justify;">
3.1. Размер платы за жилое помещение составляет {{monthly_rent}} рублей в месяц.
</p>

<p style="text-align: justify;">
3.2. Плата вносится ежемесячно до {{payment_day}} числа.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. СРОК ДОГОВОРА
</div>

<p style="text-align: justify;">
4.1. Договор заключается на неопределенный срок.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Наймодатель:</strong><br/>
{{landlord_full_name}}<br/>
Адрес: {{landlord_address}}<br/>
Тел.: {{landlord_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{landlord_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Наниматель:</strong><br/>
{{tenant_full_name}}<br/>
Паспорт: {{tenant_passport}}<br/>
Адрес: {{tenant_address}}<br/>
Тел.: {{tenant_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{tenant_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'landlord_full_name', 'Полное наименование наймодателя',
    'landlord_legal_basis', 'Основание полномочий наймодателя',
    'tenant_full_name', 'ФИО нанимателя',
    'allocation_document', 'Документ-основание предоставления жилья',
    'dwelling_type', 'Тип жилья (квартира, комната, дом)',
    'total_area', 'Общая площадь',
    'living_area', 'Жилая площадь',
    'property_address', 'Адрес жилого помещения',
    'monthly_rent', 'Размер ежемесячной платы',
    'payment_day', 'День внесения платы',
    'landlord_address', 'Адрес наймодателя',
    'landlord_phone', 'Телефон наймодателя',
    'tenant_passport', 'Паспортные данные нанимателя',
    'tenant_address', 'Адрес нанимателя',
    'tenant_phone', 'Телефон нанимателя',
    'landlord_signature', 'ФИО подписанта наймодателя',
    'tenant_signature', 'ФИО подписанта нанимателя'
)); 