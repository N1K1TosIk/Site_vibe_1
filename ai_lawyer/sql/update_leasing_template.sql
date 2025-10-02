USE ai_lawyer_db;

-- Обновляем шаблон договора лизинга с полным содержанием и форматированием
UPDATE contract_templates SET 
template_content = '<div style="text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 30px;">ДОГОВОР ЛИЗИНГА № {{contract_number}}</div>

<div style="text-align: right; margin-bottom: 20px;">г. {{city}}, {{contract_date}}</div>

<p style="text-align: justify;">{{lessor_name}}, именуемый "Лизингодатель", и {{lessee_name}}, именуемый "Лизингополучатель", заключили договор лизинга.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">1. ПРЕДМЕТ ЛИЗИНГА</h3>

<p style="text-align: justify;">1.1. Предмет лизинга: {{leased_property}}.</p>

<p style="text-align: justify;">1.2. Лизинговый платеж: {{lease_payment}} ({{lease_payment}}) рублей в месяц.</p>

<p style="text-align: justify;">1.3. Срок лизинга: {{lease_term}} месяцев с момента передачи предмета лизинга.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">2. ПРАВА И ОБЯЗАННОСТИ СТОРОН</h3>

<p style="text-align: justify;"><strong>2.1. Лизингодатель обязуется:</strong></p>
<p style="text-align: justify;">- приобрести в собственность указанное в п. 1.1 имущество и передать его во временное владение и пользование Лизингополучателю;</p>
<p style="text-align: justify;">- обеспечить соответствие предмета лизинга условиям договора;</p>
<p style="text-align: justify;">- не вмешиваться в хозяйственную деятельность Лизингополучателя, если она не противоречит договору и действующему законодательству.</p>

<p style="text-align: justify;"><strong>2.2. Лизингополучатель обязуется:</strong></p>
<p style="text-align: justify;">- принять предмет лизинга в порядке, предусмотренном договором;</p>
<p style="text-align: justify;">- выплачивать лизингодателю лизинговые платежи в порядке, в размерах и в сроки, предусмотренные договором;</p>
<p style="text-align: justify;">- содержать предмет лизинга в исправном состоянии, производить за свой счет его текущий и капитальный ремонт;</p>
<p style="text-align: justify;">- осуществлять капитальный и текущий ремонт предмета лизинга, если иное не предусмотрено договором;</p>
<p style="text-align: justify;">- страховать предмет лизинга, если иное не предусмотрено договором.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">3. ПОРЯДОК РАСЧЕТОВ</h3>

<p style="text-align: justify;">3.1. Лизинговые платежи вносятся ежемесячно до {{payment_date}} числа каждого месяца путем перечисления денежных средств на расчетный счет Лизингодателя.</p>

<p style="text-align: justify;">3.2. Первый лизинговый платеж вносится в течение 10 банковских дней с момента подписания акта приема-передачи предмета лизинга.</p>

<p style="text-align: justify;">3.3. В случае просрочки платежа Лизингополучатель уплачивает пени в размере {{penalty_rate}}% от суммы просроченного платежа за каждый день просрочки.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">4. ПЕРЕХОД ПРАВА СОБСТВЕННОСТИ</h3>

<p style="text-align: justify;">4.1. По истечении срока договора лизинга и при условии выплаты всех лизинговых платежей предмет лизинга переходит в собственность Лизингополучателя без дополнительной оплаты.</p>

<p style="text-align: justify;">4.2. Переход права собственности оформляется соответствующим актом приема-передачи.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">5. ОТВЕТСТВЕННОСТЬ СТОРОН</h3>

<p style="text-align: justify;">5.1. За неисполнение или ненадлежащее исполнение обязательств по настоящему договору стороны несут ответственность в соответствии с действующим законодательством.</p>

<p style="text-align: justify;">5.2. Лизингополучатель несет риск случайной гибели или случайной порчи предмета лизинга с момента его фактической передачи, если иное не предусмотрено договором лизинга.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">6. ДОСРОЧНОЕ РАСТОРЖЕНИЕ ДОГОВОРА</h3>

<p style="text-align: justify;">6.1. Лизингодатель имеет право потребовать досрочного расторжения договора и возврата предмета лизинга в случаях:</p>
<p style="text-align: justify;">- просрочки внесения лизинговых платежей более чем на 30 дней;</p>
<p style="text-align: justify;">- использования предмета лизинга не по назначению;</p>
<p style="text-align: justify;">- нарушения обязанности по содержанию и ремонту предмета лизинга.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">7. РАЗРЕШЕНИЕ СПОРОВ</h3>

<p style="text-align: justify;">7.1. Все споры и разногласия, которые могут возникнуть между сторонами, разрешаются путем переговоров. В случае невозможности достижения соглашения споры разрешаются в судебном порядке в соответствии с действующим законодательством Российской Федерации.</p>

<h3 style="text-align: center; font-weight: bold; margin: 20px 0;">8. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h3>

<p style="text-align: justify;">8.1. Настоящий договор составлен в двух экземплярах, имеющих одинаковую юридическую силу, по одному для каждой из сторон.</p>

<p style="text-align: justify;">8.2. Договор вступает в силу с момента его подписания сторонами и действует до полного исполнения сторонами своих обязательств.</p>

<p style="text-align: justify;">8.3. Изменения и дополнения к настоящему договору действительны только при условии, если они совершены в письменной форме и подписаны сторонами.</p>

<table width="100%" style="margin-top: 30px;">
<tr>
<td width="50%" style="text-align: center;"><strong>Лизингодатель:</strong><br/>{{lessor_name}}<br/><div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>{{lessor_signature}}</td>
<td width="50%" style="text-align: center;"><strong>Лизингополучатель:</strong><br/>{{lessee_name}}<br/><div style="border-bottom: 1px solid black; width: 150px; margin: 20px auto;"></div>{{lessee_signature}}</td>
</tr>
</table>',

variables = JSON_OBJECT(
    'contract_number', 'Номер договора',
    'city', 'Город заключения договора',
    'contract_date', 'Дата заключения договора',
    'lessor_name', 'Наименование лизингодателя',
    'lessee_name', 'Наименование лизингополучателя',
    'leased_property', 'Предмет лизинга',
    'lease_payment', 'Размер лизингового платежа',
    'lease_term', 'Срок лизинга (в месяцах)',
    'payment_date', 'Число месяца для внесения платежа',
    'penalty_rate', 'Размер пени (%)',
    'lessor_signature', 'Подпись лизингодателя',
    'lessee_signature', 'Подпись лизингополучателя'
)

WHERE id = 59; 