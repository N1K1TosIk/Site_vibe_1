USE ai_lawyer_db;

-- ===========================
-- ТРУДОВОЙ ДОГОВОР (ID: 23)
-- ===========================
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ТРУДОВОЙ ДОГОВОР № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{employer_name}}, именуемое в дальнейшем "Работодатель", в лице {{employer_representative}}, действующего на основании {{employer_authority}}, с одной стороны, и {{employee_name}}, именуемый в дальнейшем "Работник", с другой стороны, заключили настоящий трудовой договор о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ОБЩИЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">1.1. По настоящему договору Работник обязуется лично выполнять трудовую функцию по должности {{position}} (профессии, специальности с указанием квалификации) в {{department}}, соблюдать правила внутреннего трудового распорядка, а Работодатель обязуется предоставить Работнику работу по обусловленной трудовой функции, обеспечить условия труда, предусмотренные трудовым законодательством и иными нормативными правовыми актами, содержащими нормы трудового права, коллективным договором, соглашениями, локальными нормативными актами и данным трудовым договором, своевременно и в полном размере выплачивать Работнику заработную плату.</p>

<p style="text-align: justify;">1.2. Трудовые отношения между Работником и Работодателем возникают на основании заключения настоящего трудового договора.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. ТРУДОВАЯ ФУНКЦИЯ</h3>

<p style="text-align: justify;">2.1. Работник принимается на работу в {{department}} на должность {{position}}.</p>

<p style="text-align: justify;">2.2. Место работы: {{workplace}}.</p>

<p style="text-align: justify;">2.3. Трудовые обязанности Работника:</p>
<p style="text-align: justify;">{{job_duties}}</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. ПРАВА И ОБЯЗАННОСТИ СТОРОН</h3>

<p style="text-align: justify;"><strong>3.1. Работник имеет право:</strong></p>
<p style="text-align: justify;">- на предоставление работы, обусловленной трудовым договором;</p>
<p style="text-align: justify;">- на рабочее место, соответствующее государственным нормативным требованиям охраны труда;</p>
<p style="text-align: justify;">- на своевременную и в полном объеме выплату заработной платы;</p>
<p style="text-align: justify;">- на отдых, обеспечиваемый установлением нормальной продолжительности рабочего времени, сокращенного рабочего времени для отдельных профессий и категорий работников, предоставлением еженедельных выходных дней, нерабочих праздничных дней, оплачиваемых ежегодных отпусков.</p>

<p style="text-align: justify;"><strong>3.2. Работник обязан:</strong></p>
<p style="text-align: justify;">- добросовестно исполнять свои трудовые обязанности;</p>
<p style="text-align: justify;">- соблюдать правила внутреннего трудового распорядка;</p>
<p style="text-align: justify;">- соблюдать трудовую дисциплину;</p>
<p style="text-align: justify;">- выполнять установленные нормы труда;</p>
<p style="text-align: justify;">- соблюдать требования по охране труда и обеспечению безопасности труда;</p>
<p style="text-align: justify;">- бережно относиться к имуществу работодателя и других работников.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. ВРЕМЯ РАБОТЫ И ВРЕМЯ ОТДЫХА</h3>

<p style="text-align: justify;">4.1. Работнику устанавливается {{work_schedule}} рабочая неделя.</p>

<p style="text-align: justify;">4.2. Продолжительность ежедневной работы (смены) составляет {{daily_hours}} часов.</p>

<p style="text-align: justify;">4.3. Время начала работы: {{start_time}}, время окончания работы: {{end_time}}.</p>

<p style="text-align: justify;">4.4. Работнику предоставляется перерыв для отдыха и питания продолжительностью {{break_duration}} с {{break_start}} до {{break_end}}.</p>

<p style="text-align: justify;">4.5. Работнику предоставляется ежегодный основной оплачиваемый отпуск продолжительностью {{vacation_days}} календарных дней.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. ОПЛАТА ТРУДА</h3>

<p style="text-align: justify;">5.1. За выполнение трудовых обязанностей, предусмотренных настоящим договором, Работнику устанавливается заработная плата в размере {{salary}} ({{salary_words}}) рублей в месяц.</p>

<p style="text-align: justify;">5.2. Выплата заработной платы производится {{payment_frequency}} {{payment_dates}} числа каждого месяца путем перечисления на банковскую карту Работника.</p>

<p style="text-align: justify;">5.3. На Работника распространяются льготы, гарантии и компенсации, установленные законодательством РФ, коллективным договором и локальными нормативными актами Работодателя.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. СОЦИАЛЬНОЕ СТРАХОВАНИЕ</h3>

<p style="text-align: justify;">6.1. Работник подлежит обязательному социальному страхованию в соответствии с действующим законодательством.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. СРОК ДЕЙСТВИЯ ДОГОВОРА</h3>

<p style="text-align: justify;">7.1. Настоящий трудовой договор заключается {{contract_type}}.</p>

<p style="text-align: justify;">7.2. Договор вступает в силу с {{start_date}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. ПРЕКРАЩЕНИЕ ДОГОВОРА</h3>

<p style="text-align: justify;">8.1. Настоящий трудовой договор может быть прекращен по основаниям, предусмотренным Трудовым кодексом Российской Федерации.</p>

<p style="text-align: justify;">8.2. При прекращении трудового договора выплата всех сумм, причитающихся Работнику от Работодателя, производится в день увольнения работника.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">9. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">9.1. Трудовой договор составлен в двух экземплярах, каждый из которых имеет одинаковую юридическую силу.</p>

<p style="text-align: justify;">9.2. Изменения и дополнения к настоящему договору могут вноситься только по соглашению сторон в письменной форме.</p>

<table width="100%" style="margin-top: 30px;">
<tr>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>РАБОТОДАТЕЛЬ</strong><br/>
{{employer_name}}<br/>
ИНН: {{employer_inn}}<br/>
Адрес: {{employer_address}}<br/>
<br/>
{{employer_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{employer_signature}}
</td>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>РАБОТНИК</strong><br/>
{{employee_name}}<br/>
Паспорт: {{employee_passport}}<br/>
Адрес: {{employee_address}}<br/>
Телефон: {{employee_phone}}<br/>
<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{employee_signature}}
</td>
</tr>
</table>',

variables = JSON_OBJECT(
    'contract_number', 'Номер трудового договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'employer_name', 'Наименование работодателя',
    'employer_representative', 'Представитель работодателя (ФИО, должность)',
    'employer_authority', 'Основание полномочий представителя',
    'employee_name', 'ФИО работника',
    'position', 'Должность работника',
    'department', 'Структурное подразделение',
    'workplace', 'Место работы (адрес)',
    'job_duties', 'Трудовые обязанности работника',
    'work_schedule', 'График работы (5-дневная, 6-дневная)',
    'daily_hours', 'Продолжительность рабочего дня (часов)',
    'start_time', 'Время начала работы',
    'end_time', 'Время окончания работы',
    'break_duration', 'Продолжительность обеденного перерыва',
    'break_start', 'Начало обеденного перерыва',
    'break_end', 'Окончание обеденного перерыва',
    'vacation_days', 'Продолжительность отпуска (дней)',
    'salary', 'Размер заработной платы (руб.)',
    'salary_words', 'Размер заработной платы прописью',
    'payment_frequency', 'Периодичность выплаты (два раза в месяц)',
    'payment_dates', 'Даты выплаты зарплаты',
    'contract_type', 'Тип договора (на неопределенный срок/срочный)',
    'start_date', 'Дата начала работы',
    'employer_inn', 'ИНН работодателя',
    'employer_address', 'Адрес работодателя',
    'employer_signature', 'Подпись работодателя',
    'employee_passport', 'Паспортные данные работника',
    'employee_address', 'Адрес работника',
    'employee_phone', 'Телефон работника',
    'employee_signature', 'Подпись работника'
)
WHERE id = 23;

-- ===========================
-- ДОГОВОР АРЕНДЫ НЕЖИЛОГО ПОМЕЩЕНИЯ (ID: 25)
-- ===========================
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ДОГОВОР АРЕНДЫ НЕЖИЛОГО ПОМЕЩЕНИЯ № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{landlord_name}}, именуемое в дальнейшем "Арендодатель", в лице {{landlord_representative}}, действующего на основании {{landlord_authority}}, с одной стороны, и {{tenant_name}}, именуемое в дальнейшем "Арендатор", в лице {{tenant_representative}}, действующего на основании {{tenant_authority}}, с другой стороны, заключили настоящий договор о нижеследующем:</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ ДОГОВОРА</h3>

<p style="text-align: justify;">1.1. Арендодатель предоставляет, а Арендатор принимает во временное владение и пользование нежилое помещение, расположенное по адресу: {{property_address}}.</p>

<p style="text-align: justify;">1.2. Общая площадь помещения составляет {{total_area}} кв.м., полезная площадь - {{useful_area}} кв.м.</p>

<p style="text-align: justify;">1.3. Помещение находится на {{floor}} этаже {{floors_total}}-этажного здания.</p>

<p style="text-align: justify;">1.4. Назначение помещения: {{purpose}}.</p>

<p style="text-align: justify;">1.5. Техническое состояние помещения: {{technical_condition}}.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. СРОК АРЕНДЫ</h3>

<p style="text-align: justify;">2.1. Помещение предоставляется в аренду сроком на {{lease_term}} с {{start_date}} по {{end_date}}.</p>

<p style="text-align: justify;">2.2. Договор может быть продлен по соглашению сторон.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. РАЗМЕР И ПОРЯДОК ВНЕСЕНИЯ АРЕНДНОЙ ПЛАТЫ</h3>

<p style="text-align: justify;">3.1. Размер арендной платы составляет {{rent_amount}} ({{rent_amount_words}}) рублей в месяц.</p>

<p style="text-align: justify;">3.2. Арендная плата вносится {{payment_schedule}} до {{payment_date}} числа каждого месяца путем перечисления на расчетный счет Арендодателя.</p>

<p style="text-align: justify;">3.3. Арендная плата за первый месяц вносится в течение 3 банковских дней с момента подписания договора.</p>

<p style="text-align: justify;">3.4. Размер арендной платы может быть изменен Арендодателем в одностороннем порядке, но не чаще одного раза в год, с уведомлением Арендатора за 30 дней.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. ПРАВА И ОБЯЗАННОСТИ АРЕНДОДАТЕЛЯ</h3>

<p style="text-align: justify;"><strong>4.1. Арендодатель имеет право:</strong></p>
<p style="text-align: justify;">- требовать своевременного внесения арендной платы;</p>
<p style="text-align: justify;">- осуществлять контроль за использованием помещения;</p>
<p style="text-align: justify;">- требовать возмещения убытков, причиненных ухудшением состояния помещения.</p>

<p style="text-align: justify;"><strong>4.2. Арендодатель обязан:</strong></p>
<p style="text-align: justify;">- передать помещение в состоянии, пригодном для использования;</p>
<p style="text-align: justify;">- обеспечивать надлежащее содержание общего имущества здания;</p>
<p style="text-align: justify;">- не вмешиваться в хозяйственную деятельность Арендатора.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. ПРАВА И ОБЯЗАННОСТИ АРЕНДАТОРА</h3>

<p style="text-align: justify;"><strong>5.1. Арендатор имеет право:</strong></p>
<p style="text-align: justify;">- использовать помещение в соответствии с его назначением;</p>
<p style="text-align: justify;">- с согласия Арендодателя производить улучшения помещения.</p>

<p style="text-align: justify;"><strong>5.2. Арендатор обязан:</strong></p>
<p style="text-align: justify;">- использовать помещение только по назначению;</p>
<p style="text-align: justify;">- своевременно вносить арендную плату;</p>
<p style="text-align: justify;">- содержать помещение в исправном состоянии;</p>
<p style="text-align: justify;">- производить текущий ремонт помещения;</p>
<p style="text-align: justify;">- не нарушать права других арендаторов и собственников.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. ОТВЕТСТВЕННОСТЬ СТОРОН</h3>

<p style="text-align: justify;">6.1. За просрочку внесения арендной платы Арендатор уплачивает пеню в размере {{penalty_rate}}% от суммы просроченного платежа за каждый день просрочки.</p>

<p style="text-align: justify;">6.2. Сторона, нарушившая договор, возмещает другой стороне причиненные убытки.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. ПРЕКРАЩЕНИЕ ДОГОВОРА</h3>

<p style="text-align: justify;">7.1. Договор прекращается по истечении срока его действия.</p>

<p style="text-align: justify;">7.2. Договор может быть расторгнут досрочно по соглашению сторон или по основаниям, предусмотренным законом.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">8.1. Споры разрешаются путем переговоров, а при недостижении соглашения - в суде.</p>

<p style="text-align: justify;">8.2. Договор составлен в двух экземплярах, имеющих одинаковую юридическую силу.</p>

<table width="100%" style="margin-top: 30px;">
<tr>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>АРЕНДОДАТЕЛЬ</strong><br/>
{{landlord_name}}<br/>
ИНН: {{landlord_inn}}<br/>
{{landlord_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{landlord_signature}}
</td>
<td width="50%" style="text-align: center; vertical-align: top;">
<strong>АРЕНДАТОР</strong><br/>
{{tenant_name}}<br/>
ИНН: {{tenant_inn}}<br/>
{{tenant_representative}}<br/>
<div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>
{{tenant_signature}}
</td>
</tr>
</table>',

variables = JSON_OBJECT(
    'contract_number', 'Номер договора аренды',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'landlord_name', 'Наименование арендодателя',
    'landlord_representative', 'Представитель арендодателя',
    'landlord_authority', 'Основание полномочий арендодателя',
    'tenant_name', 'Наименование арендатора',
    'tenant_representative', 'Представитель арендатора',
    'tenant_authority', 'Основание полномочий арендатора',
    'property_address', 'Адрес арендуемого помещения',
    'total_area', 'Общая площадь (кв.м.)',
    'useful_area', 'Полезная площадь (кв.м.)',
    'floor', 'Этаж расположения',
    'floors_total', 'Всего этажей в здании',
    'purpose', 'Назначение помещения',
    'technical_condition', 'Техническое состояние',
    'lease_term', 'Срок аренды',
    'start_date', 'Дата начала аренды',
    'end_date', 'Дата окончания аренды',
    'rent_amount', 'Размер арендной платы (руб.)',
    'rent_amount_words', 'Размер арендной платы прописью',
    'payment_schedule', 'График внесения платы',
    'payment_date', 'Число месяца для внесения платы',
    'penalty_rate', 'Размер пени (%)',
    'landlord_inn', 'ИНН арендодателя',
    'landlord_signature', 'Подпись арендодателя',
    'tenant_inn', 'ИНН арендатора',
    'tenant_signature', 'Подпись арендатора'
)
WHERE id = 25; 