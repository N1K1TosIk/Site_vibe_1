USE ai_lawyer_db;

-- Полный набор всех шаблонов с правильными категориями

-- 1. ФИНАНСОВЫЕ ДОГОВОРЫ
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
    'lender_address', 'Адрес займодавца',
    'lender_phone', 'Телефон займодавца',
    'borrower_address', 'Адрес заемщика',
    'borrower_phone', 'Телефон заемщика',
    'lender_signature', 'ФИО подписанта займодавца',
    'borrower_signature', 'ФИО подписанта заемщика'
)),

('Договор страхования', 'financial', 'Договор добровольного страхования', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР ДОБРОВОЛЬНОГО СТРАХОВАНИЯ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{insurer_full_name}}, действующее на основании {{insurer_legal_basis}}, лицензия {{insurance_license}}, именуемое в дальнейшем "Страховщик", с одной стороны, и {{insured_full_name}}, именуемый(-ая) в дальнейшем "Страхователь", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Страховщик обязуется при наступлении страхового случая выплатить страховое возмещение {{beneficiary}} в пределах страховой суммы.
</p>

<p style="text-align: justify;">
1.2. Объект страхования: {{insurance_object}}.
</p>

<p style="text-align: justify;">
1.3. Страховая сумма составляет {{insurance_amount}} рублей.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. СТРАХОВЫЕ СЛУЧАИ
</div>

<p style="text-align: justify;">
2.1. Страховыми случаями являются: {{insured_risks}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Страховщик:</strong><br/>
{{insurer_full_name}}<br/>
Лицензия: {{insurance_license}}<br/>
Адрес: {{insurer_address}}<br/>
Тел.: {{insurer_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{insurer_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Страхователь:</strong><br/>
{{insured_full_name}}<br/>
Адрес: {{insured_address}}<br/>
Тел.: {{insured_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{insured_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'insurer_full_name', 'Полное наименование страховщика',
    'insurer_legal_basis', 'Основание полномочий страховщика',
    'insurance_license', 'Номер лицензии страховщика',
    'insured_full_name', 'ФИО страхователя',
    'beneficiary', 'Выгодоприобретатель',
    'insurance_object', 'Объект страхования',
    'insurance_amount', 'Страховая сумма',
    'insured_risks', 'Застрахованные риски',
    'insurer_address', 'Адрес страховщика',
    'insurer_phone', 'Телефон страховщика',
    'insured_address', 'Адрес страхователя',
    'insured_phone', 'Телефон страхователя',
    'insurer_signature', 'ФИО подписанта страховщика',
    'insured_signature', 'ФИО подписанта страхователя'
)),

-- 2. КОММЕРЧЕСКИЕ ДОГОВОРЫ
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

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ВОЗНАГРАЖДЕНИЕ
</div>

<p style="text-align: justify;">
2.1. Размер агентского вознаграждения составляет {{commission_rate}}% от суммы заключенных сделок.
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
    'principal_address', 'Адрес принципала',
    'principal_phone', 'Телефон принципала',
    'agent_address', 'Адрес агента',
    'agent_phone', 'Телефон агента',
    'principal_signature', 'ФИО подписанта принципала',
    'agent_signature', 'ФИО подписанта агента'
)),

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
2. КОМИССИОННОЕ ВОЗНАГРАЖДЕНИЕ
</div>

<p style="text-align: justify;">
2.1. Размер комиссионного вознаграждения составляет {{commission_rate}}% от суммы заключенной сделки.
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

('Договор франчайзинга', 'commercial', 'Договор коммерческой концессии (франчайзинга)', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР КОММЕРЧЕСКОЙ КОНЦЕССИИ (ФРАНЧАЙЗИНГА) № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{franchisor_full_name}}, действующий(-ая) на основании {{franchisor_legal_basis}}, ИНН {{franchisor_inn}}, именуемый(-ая) в дальнейшем "Правообладатель", с одной стороны, и {{franchisee_full_name}}, действующий(-ая) на основании {{franchisee_legal_basis}}, ИНН {{franchisee_inn}}, именуемый(-ая) в дальнейшем "Пользователь", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Правообладатель предоставляет Пользователю за вознаграждение право использовать в предпринимательской деятельности комплекс исключительных прав: {{franchise_package}}.
</p>

<p style="text-align: justify;">
1.2. Сфера деятельности: {{business_sphere}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Правообладатель:</strong><br/>
{{franchisor_full_name}}<br/>
ИНН: {{franchisor_inn}}<br/>
Адрес: {{franchisor_address}}<br/>
Тел.: {{franchisor_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{franchisor_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Пользователь:</strong><br/>
{{franchisee_full_name}}<br/>
ИНН: {{franchisee_inn}}<br/>
Адрес: {{franchisee_address}}<br/>
Тел.: {{franchisee_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{franchisee_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'franchisor_full_name', 'Полное наименование правообладателя',
    'franchisor_legal_basis', 'Основание полномочий правообладателя',
    'franchisor_inn', 'ИНН правообладателя',
    'franchisee_full_name', 'Полное наименование пользователя',
    'franchisee_legal_basis', 'Основание полномочий пользователя',
    'franchisee_inn', 'ИНН пользователя',
    'franchise_package', 'Комплекс передаваемых прав',
    'business_sphere', 'Сфера деятельности',
    'franchisor_address', 'Адрес правообладателя',
    'franchisor_phone', 'Телефон правообладателя',
    'franchisee_address', 'Адрес пользователя',
    'franchisee_phone', 'Телефон пользователя',
    'franchisor_signature', 'ФИО подписанта правообладателя',
    'franchisee_signature', 'ФИО подписанта пользователя'
)); 