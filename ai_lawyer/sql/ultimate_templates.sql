USE ai_lawyer_db;

-- Ультимативная коллекция дополнительных шаблонов

-- 16. ДОГОВОР КУПЛИ-ПРОДАЖИ АВТОМОБИЛЯ
INSERT INTO contract_templates (name, category, description, template_content, variables) VALUES 
('Договор купли-продажи автомобиля', 'transport', 'Договор купли-продажи транспортного средства', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР КУПЛИ-ПРОДАЖИ ТРАНСПОРТНОГО СРЕДСТВА № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{seller_full_name}}, именуемый(-ая) в дальнейшем "Продавец", с одной стороны, и {{buyer_full_name}}, именуемый(-ая) в дальнейшем "Покупатель", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Продавец передает в собственность Покупателю транспортное средство:
- Марка, модель: {{car_brand_model}}
- Год выпуска: {{manufacture_year}}
- VIN: {{vin_number}}
- Гос. номер: {{license_plate}}
- Номер двигателя: {{engine_number}}
- Цвет: {{car_color}}
- Пробег: {{mileage}} км
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ЦЕНА И ПОРЯДОК РАСЧЕТОВ
</div>

<p style="text-align: justify;">
2.1. Цена транспортного средства составляет {{sale_price}} ({{sale_price_words}}) рублей.
</p>

<p style="text-align: justify;">
2.2. Расчет производится {{payment_method}} в момент подписания договора.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. СОСТОЯНИЕ ТРАНСПОРТНОГО СРЕДСТВА
</div>

<p style="text-align: justify;">
3.1. На момент продажи транспортное средство находится в {{vehicle_condition}}.
</p>

<p style="text-align: justify;">
3.2. Недостатки: {{defects}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. ПЕРЕХОД ПРАВА СОБСТВЕННОСТИ
</div>

<p style="text-align: justify;">
4.1. Право собственности на транспортное средство переходит к Покупателю с момента подписания договора.
</p>

<p style="text-align: justify;">
4.2. Покупатель обязуется зарегистрировать транспортное средство в ГИБДД в установленном порядке.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Продавец:</strong><br/>
{{seller_full_name}}<br/>
Паспорт: {{seller_passport}}<br/>
Адрес: {{seller_address}}<br/>
Тел.: {{seller_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{seller_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>Покупатель:</strong><br/>
{{buyer_full_name}}<br/>
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
    'buyer_full_name', 'ФИО покупателя',
    'car_brand_model', 'Марка и модель автомобиля',
    'manufacture_year', 'Год выпуска',
    'vin_number', 'VIN номер',
    'license_plate', 'Государственный номер',
    'engine_number', 'Номер двигателя',
    'car_color', 'Цвет автомобиля',
    'mileage', 'Пробег',
    'sale_price', 'Цена продажи',
    'sale_price_words', 'Цена прописью',
    'payment_method', 'Способ оплаты',
    'vehicle_condition', 'Состояние ТС',
    'defects', 'Недостатки ТС',
    'seller_passport', 'Паспорт продавца',
    'seller_address', 'Адрес продавца',
    'seller_phone', 'Телефон продавца',
    'buyer_passport', 'Паспорт покупателя',
    'buyer_address', 'Адрес покупателя',
    'buyer_phone', 'Телефон покупателя',
    'seller_signature', 'Подпись продавца',
    'buyer_signature', 'Подпись покупателя'
)),

-- 17. ДОГОВОР ФРАНЧАЙЗИНГА
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

<p style="text-align: justify;">
1.3. Территория действия: {{territory}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ОБЯЗАННОСТИ ПРАВООБЛАДАТЕЛЯ
</div>

<p style="text-align: justify;">
2.1. Правообладатель обязуется:
- передать Пользователю техническую и коммерческую документацию;
- обучить персонал Пользователя;
- обеспечить консультационную поддержку;
- контролировать качество товаров/услуг.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. ОБЯЗАННОСТИ ПОЛЬЗОВАТЕЛЯ
</div>

<p style="text-align: justify;">
3.1. Пользователь обязуется:
- соблюдать инструкции и стандарты Правообладателя;
- не разглашать коммерческую информацию;
- обеспечивать качество товаров/услуг;
- уплачивать вознаграждение в установленном порядке.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. РАЗМЕР И ПОРЯДОК ВЫПЛАТЫ ВОЗНАГРАЖДЕНИЯ
</div>

<p style="text-align: justify;">
4.1. Паушальный взнос составляет {{initial_fee}} рублей.
</p>

<p style="text-align: justify;">
4.2. Роялти составляет {{royalty_rate}}% от {{royalty_base}} и выплачивается {{payment_frequency}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
5. СРОК ДЕЙСТВИЯ ДОГОВОРА
</div>

<p style="text-align: justify;">
5.1. Договор действует с {{start_date}} по {{end_date}}.
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
    'territory', 'Территория действия',
    'initial_fee', 'Размер паушального взноса',
    'royalty_rate', 'Размер роялти (%)',
    'royalty_base', 'База для расчета роялти',
    'payment_frequency', 'Периодичность выплаты роялти',
    'start_date', 'Дата начала действия',
    'end_date', 'Дата окончания действия',
    'franchisor_address', 'Адрес правообладателя',
    'franchisor_phone', 'Телефон правообладателя',
    'franchisee_address', 'Адрес пользователя',
    'franchisee_phone', 'Телефон пользователя',
    'franchisor_signature', 'ФИО подписанта правообладателя',
    'franchisee_signature', 'ФИО подписанта пользователя'
)),

-- 18. ДОГОВОР СТРАХОВАНИЯ
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

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. СТРАХОВАЯ ПРЕМИЯ
</div>

<p style="text-align: justify;">
3.1. Размер страховой премии составляет {{premium_amount}} рублей.
</p>

<p style="text-align: justify;">
3.2. Страховая премия уплачивается {{premium_payment_terms}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. СРОК СТРАХОВАНИЯ
</div>

<p style="text-align: justify;">
4.1. Страхование действует с {{insurance_start}} по {{insurance_end}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
5. ПРАВА И ОБЯЗАННОСТИ СТОРОН
</div>

<p style="text-align: justify;">
5.1. Страхователь обязан немедленно уведомлять Страховщика о наступлении страхового случая.
</p>

<p style="text-align: justify;">
5.2. Страховщик обязан произвести страховую выплату в течение {{payment_period}} дней после получения всех необходимых документов.
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
Паспорт: {{insured_passport}}<br/>
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
    'premium_amount', 'Размер страховой премии',
    'premium_payment_terms', 'Условия уплаты премии',
    'insurance_start', 'Дата начала страхования',
    'insurance_end', 'Дата окончания страхования',
    'payment_period', 'Срок страховой выплаты (дней)',
    'insurer_address', 'Адрес страховщика',
    'insurer_phone', 'Телефон страховщика',
    'insured_passport', 'Паспорт страхователя',
    'insured_address', 'Адрес страхователя',
    'insured_phone', 'Телефон страхователя',
    'insurer_signature', 'ФИО подписанта страховщика',
    'insured_signature', 'ФИО подписанта страхователя'
)),

-- 19. ДОГОВОР УПРАВЛЕНИЯ МНОГОКВАРТИРНЫМ ДОМОМ
('Договор управления многоквартирным домом', 'service', 'Договор управления многоквартирным домом', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР УПРАВЛЕНИЯ МНОГОКВАРТИРНЫМ ДОМОМ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{management_company_name}}, действующая на основании {{mc_legal_basis}}, ИНН {{mc_inn}}, именуемая в дальнейшем "Управляющая организация", с одной стороны, и Собственники помещений в многоквартирном доме, расположенном по адресу: {{building_address}}, именуемые в дальнейшем "Собственники", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Управляющая организация обязуется по заданию Собственников оказывать услуги и выполнять работы по надлежащему содержанию и ремонту общего имущества в многоквартирном доме.
</p>

<p style="text-align: justify;">
1.2. Характеристики дома:
- Общая площадь дома: {{total_building_area}} кв.м
- Количество этажей: {{floors_count}}
- Количество квартир: {{apartments_count}}
- Год постройки: {{construction_year}}
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. ОБЯЗАННОСТИ УПРАВЛЯЮЩЕЙ ОРГАНИЗАЦИИ
</div>

<p style="text-align: justify;">
2.1. Управляющая организация обязуется:
- содержать в исправном состоянии общее имущество дома;
- обеспечивать предоставление коммунальных услуг;
- осуществлять текущий ремонт общего имущества;
- ведение технической документации;
- {{additional_services}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. РАЗМЕР ПЛАТЫ ЗА УСЛУГИ
</div>

<p style="text-align: justify;">
3.1. Размер платы за содержание и ремонт общего имущества составляет {{management_fee}} рублей с 1 кв.м общей площади помещения в месяц.
</p>

<p style="text-align: justify;">
3.2. Плата за коммунальные услуги устанавливается в соответствии с действующими тарифами.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. СРОК ДЕЙСТВИЯ ДОГОВОРА
</div>

<p style="text-align: justify;">
4.1. Договор заключается на срок {{contract_term}} и вступает в силу с {{contract_start_date}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Управляющая организация:</strong><br/>
{{management_company_name}}<br/>
ИНН: {{mc_inn}}<br/>
Адрес: {{mc_address}}<br/>
Тел.: {{mc_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{mc_signature}}/
</td>
<td width="50%" style="text-align: center;">
<strong>От имени Собственников:</strong><br/>
{{owners_representative}}<br/>
Протокол ОСС: {{oss_protocol}}<br/>
Адрес: {{representative_address}}<br/>
Тел.: {{representative_phone}}<br/><br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
/{{representative_signature}}/
</td>
</tr>
</table>
', JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'management_company_name', 'Наименование управляющей организации',
    'mc_legal_basis', 'Основание полномочий УО',
    'mc_inn', 'ИНН управляющей организации',
    'building_address', 'Адрес многоквартирного дома',
    'total_building_area', 'Общая площадь дома',
    'floors_count', 'Количество этажей',
    'apartments_count', 'Количество квартир',
    'construction_year', 'Год постройки',
    'additional_services', 'Дополнительные услуги',
    'management_fee', 'Размер платы за управление',
    'contract_term', 'Срок действия договора',
    'contract_start_date', 'Дата начала действия',
    'mc_address', 'Адрес управляющей организации',
    'mc_phone', 'Телефон управляющей организации',
    'owners_representative', 'Представитель собственников',
    'oss_protocol', 'Протокол общего собрания',
    'representative_address', 'Адрес представителя',
    'representative_phone', 'Телефон представителя',
    'mc_signature', 'ФИО подписанта УО',
    'representative_signature', 'ФИО подписанта представителя'
)),

-- 20. ДОГОВОР КОММЕРЧЕСКОГО НАЙМА ЖИЛОГО ПОМЕЩЕНИЯ  
('Договор коммерческого найма жилого помещения', 'rental', 'Договор аренды жилого помещения на коммерческих условиях', '
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">
ДОГОВОР НАЙМА ЖИЛОГО ПОМЕЩЕНИЯ № {{contract_number}}
</div>

<div style="text-align: right; margin-bottom: 20px;">
г. {{city}}, {{contract_date}}
</div>

<p style="text-align: justify; line-height: 1.6;">
{{landlord_full_name}}, именуемый(-ая) в дальнейшем "Наймодатель", с одной стороны, и {{tenant_full_name}}, именуемый(-ая) в дальнейшем "Наниматель", с другой стороны, заключили настоящий договор о нижеследующем:
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
1. ПРЕДМЕТ ДОГОВОРА
</div>

<p style="text-align: justify;">
1.1. Наймодатель предоставляет Нанимателю во временное владение и пользование жилое помещение: {{apartment_type}} общей площадью {{total_area}} кв.м, жилой площадью {{living_area}} кв.м, расположенное по адресу: {{property_address}}, {{floor}} этаж.
</p>

<p style="text-align: justify;">
1.2. Состав помещения: {{room_composition}}.
</p>

<p style="text-align: justify;">
1.3. Мебель и оборудование: {{furniture_equipment}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
2. СРОК НАЙМА
</div>

<p style="text-align: justify;">
2.1. Жилое помещение предоставляется в наем на срок с {{lease_start}} по {{lease_end}}.
</p>

<p style="text-align: justify;">
2.2. {{renewal_terms}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
3. РАЗМЕР И ПОРЯДОК ВНЕСЕНИЯ ПЛАТЫ
</div>

<p style="text-align: justify;">
3.1. Размер платы за наем жилого помещения составляет {{monthly_rent}} ({{monthly_rent_words}}) рублей в месяц.
</p>

<p style="text-align: justify;">
3.2. Плата вносится {{payment_schedule}} не позднее {{payment_deadline}} числа месяца.
</p>

<p style="text-align: justify;">
3.3. Задаток в размере {{security_deposit}} рублей внесен при заключении договора.
</p>

<p style="text-align: justify;">
3.4. Коммунальные платежи: {{utilities_payment_terms}}.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
4. ПРАВА И ОБЯЗАННОСТИ СТОРОН
</div>

<p style="text-align: justify;">
4.1. Наниматель имеет право пользоваться жилым помещением в соответствии с его назначением.
</p>

<p style="text-align: justify;">
4.2. Наниматель обязан:
- использовать помещение по назначению;
- содержать помещение в чистоте и порядке;
- не производить перепланировку без согласия Наймодателя;
- возместить ущерб при порче имущества.
</p>

<p style="text-align: justify;">
4.3. Наймодатель обязан предоставить помещение в состоянии, пригодном для проживания.
</p>

<div style="text-align: center; font-weight: bold; margin: 20px 0;">
5. ОТВЕТСТВЕННОСТЬ СТОРОН
</div>

<p style="text-align: justify;">
5.1. За просрочку внесения платы Наниматель уплачивает пеню {{late_payment_penalty}}% за каждый день просрочки.
</p>

<div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
РЕКВИЗИТЫ И ПОДПИСИ СТОРОН:
</div>

<table width="100%" style="margin-top: 30px;">
<tr valign="top">
<td width="50%" style="text-align: center;">
<strong>Наймодатель:</strong><br/>
{{landlord_full_name}}<br/>
Паспорт: {{landlord_passport}}<br/>
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
    'landlord_full_name', 'ФИО наймодателя',
    'tenant_full_name', 'ФИО нанимателя',
    'apartment_type', 'Тип жилого помещения',
    'total_area', 'Общая площадь',
    'living_area', 'Жилая площадь',
    'property_address', 'Адрес помещения',
    'floor', 'Этаж',
    'room_composition', 'Состав помещения',
    'furniture_equipment', 'Мебель и оборудование',
    'lease_start', 'Дата начала найма',
    'lease_end', 'Дата окончания найма',
    'renewal_terms', 'Условия продления',
    'monthly_rent', 'Ежемесячная плата',
    'monthly_rent_words', 'Ежемесячная плата прописью',
    'payment_schedule', 'График платежей',
    'payment_deadline', 'Срок внесения платы',
    'security_deposit', 'Размер задатка',
    'utilities_payment_terms', 'Условия оплаты коммунальных услуг',
    'late_payment_penalty', 'Пеня за просрочку (%)',
    'landlord_passport', 'Паспорт наймодателя',
    'landlord_address', 'Адрес наймодателя',
    'landlord_phone', 'Телефон наймодателя',
    'tenant_passport', 'Паспорт нанимателя',
    'tenant_address', 'Адрес нанимателя',
    'tenant_phone', 'Телефон нанимателя',
    'landlord_signature', 'Подпись наймодателя',
    'tenant_signature', 'Подпись нанимателя'
)); 