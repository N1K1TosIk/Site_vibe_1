<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/ContractGenerator.php';

// Проверка авторизации
$user = new User();
if (!$user->checkSession()) {
    header('Location: ../auth/login.php');
    exit;
}

Security::configureSession();

$userData = $user->getUserData($_SESSION['user_id']);
if (!$userData) {
    header('Location: ../auth/login.php');
    exit;
}

$success = '';
$error = '';

// Инициализация генератора договоров
$contractGenerator = new ContractGenerator();

// Получение доступных шаблонов
$availableTemplates = $contractGenerator->getActiveTemplates();

// Обработка генерации договора
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_contract'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        $templateId = $_POST['template_id'] ?? '';
        $variables = $_POST['variables'] ?? [];
        
        // Генерация договора с помощью нового класса
        $result = $contractGenerator->generateContract($templateId, $variables, $_SESSION['user_id']);
        
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генератор договоров - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .contract-card {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }
        .contract-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .contract-card.selected {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
        }
        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 1rem;
            width: 95%;
            max-width: 1200px;
            max-height: 95vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .field-with-example {
            position: relative;
        }
        .example-text {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.875rem;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .field-with-example:hover .example-text {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 pt-12">
    <?php include '_navbar.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Генератор договоров
            </h1>
            <p class="mt-3 text-lg text-gray-600">Создайте профессиональные юридические документы за несколько минут</p>
            <div class="mt-4 flex justify-center space-x-6 text-sm text-gray-500">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>26 профессиональных шаблонов</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                    <span>Соответствие законодательству РФ</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-download text-purple-500 mr-2"></i>
                                            <span>Экспорт в Word</span>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 rounded-r-lg p-4 shadow-sm">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-red-400 mr-3 mt-0.5"></i>
                    <p class="text-sm text-red-700"><?= Security::sanitizeOutput($error) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 rounded-r-lg p-4 shadow-sm">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                    <p class="text-sm text-green-700"><?= Security::sanitizeOutput($success) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Список шаблонов по категориям -->
        <div class="space-y-8">
            <?php 
                $categories = [
                    'financial' => ['name' => 'Финансовые договоры', 'icon' => 'fa-coins', 'color' => 'yellow'],
                    'commercial' => ['name' => 'Коммерческие договоры', 'icon' => 'fa-briefcase', 'color' => 'orange'],
                    'rental' => ['name' => 'Аренда и найм', 'icon' => 'fa-building', 'color' => 'blue'],
                    'service' => ['name' => 'Услуги', 'icon' => 'fa-handshake', 'color' => 'indigo'],
                    'real_estate' => ['name' => 'Недвижимость', 'icon' => 'fa-home', 'color' => 'teal'],
                    'supply' => ['name' => 'Поставка и продажа', 'icon' => 'fa-truck', 'color' => 'green'],
                    'family' => ['name' => 'Семейные договоры', 'icon' => 'fa-heart', 'color' => 'rose'],
                    'employment' => ['name' => 'Трудовые отношения', 'icon' => 'fa-user-tie', 'color' => 'purple'],
                    'construction' => ['name' => 'Строительство', 'icon' => 'fa-hammer', 'color' => 'amber'],
                    'intellectual' => ['name' => 'Интеллектуальная собственность', 'icon' => 'fa-lightbulb', 'color' => 'cyan'],
                    'transport' => ['name' => 'Транспорт и перевозки', 'icon' => 'fa-truck-moving', 'color' => 'emerald'],
                    'trust' => ['name' => 'Доверительное управление', 'icon' => 'fa-university', 'color' => 'slate'],
                    'civil' => ['name' => 'Гражданские договоры', 'icon' => 'fa-gift', 'color' => 'pink'],
                    'nda' => ['name' => 'Конфиденциальность', 'icon' => 'fa-shield-alt', 'color' => 'red'],
                    'other' => ['name' => 'Подрядные работы', 'icon' => 'fa-tools', 'color' => 'gray']
                ];
                
                foreach ($categories as $categoryKey => $categoryInfo):
                    $categoryTemplates = array_filter($availableTemplates, function($template) use ($categoryKey) {
                        return $template['category'] === $categoryKey;
                    });
                    
                    if (!empty($categoryTemplates)):
                ?>
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="category-header px-6 py-4">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas <?= $categoryInfo['icon'] ?> text-white"></i>
                                </div>
                                <?= $categoryInfo['name'] ?>
                                <span class="ml-3 bg-white bg-opacity-20 text-white text-sm px-3 py-1 rounded-full">
                                    <?= count($categoryTemplates) ?> шт.
                                </span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($categoryTemplates as $template): ?>
                                    <div class="template-container">
                                        <button 
                                            type="button" 
                                            onclick="openModal(<?= $template['id'] ?>, '<?= $categoryInfo['color'] ?>')"
                                            class="contract-card template-btn w-full p-5 border-2 border-gray-200 rounded-xl hover:border-<?= $categoryInfo['color'] ?>-300 transition-all duration-300 text-left group"
                                            data-template-id="<?= $template['id'] ?>"
                                        >
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-<?= $categoryInfo['color'] ?>-100 to-<?= $categoryInfo['color'] ?>-200 rounded-xl flex items-center justify-center group-hover:from-<?= $categoryInfo['color'] ?>-200 group-hover:to-<?= $categoryInfo['color'] ?>-300 transition-all">
                                                    <i class="fas fa-file-contract text-<?= $categoryInfo['color'] ?>-600 text-lg"></i>
                                                </div>
                                                <div class="flex-shrink-0 ml-3">
                                                    <i class="fas fa-external-link-alt text-gray-400 transition-transform duration-300"></i>
                        </div>
                        </div>
                        <div>
                                                <h4 class="font-semibold text-gray-900 group-hover:text-<?= $categoryInfo['color'] ?>-900 mb-2 leading-tight">
                                                    <?= Security::sanitizeOutput($template['name']) ?>
                                                </h4>
                                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                    <?= Security::sanitizeOutput($template['description']) ?>
                                                </p>
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        <span>Соответствует РФ</span>
                        </div>
                                                    <div class="text-xs text-gray-500">
                                                        <i class="fas fa-download mr-1"></i>
                                                        Word
                        </div>
                        </div>
                        </div>
                                        </button>
                        </div>
                                <?php endforeach; ?>
                        </div>
                        </div>
                    </div>
                <?php 
                    endif;
                endforeach; 
                ?>
                        </div>
                    </div>
                    
    <!-- Модальное окно для формы -->
    <div id="contractModal" class="modal">
        <div class="modal-content">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                <div class="flex items-center">
                    <div id="modal-icon" class="w-12 h-12 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-file-contract text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 id="modal-title" class="text-xl font-bold text-gray-900"></h2>
                        <p id="modal-description" class="text-sm text-gray-600"></p>
                    </div>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                        <span class="font-semibold text-blue-900">Советы по заполнению</span>
                    </div>
                    <p class="text-sm text-blue-800">Наведите курсор на поля для просмотра примеров заполнения. Все поля с красной звездочкой (*) обязательны для заполнения.</p>
                </div>

                <form id="modal-form" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="generate_contract" value="1">
                    <input type="hidden" name="template_id" id="modal-template-id" value="">
                    
                    <div id="modal-form-fields" class="space-y-6">
                        <!-- Поля будут добавлены динамически -->
                    </div>
                    
                    <div class="mt-8 flex justify-center space-x-4 border-t border-gray-200 pt-6">
                        <button 
                            type="button" 
                            onclick="closeModal()"
                            class="px-8 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200 font-medium"
                        >
                            <i class="fas fa-times mr-2"></i>
                            Отмена
                        </button>
                        <button 
                            type="submit"
                            id="submit-btn"
                            class="px-12 py-3 text-white rounded-lg transition duration-200 font-medium shadow-lg"
                        >
                            <i class="fas fa-magic mr-2"></i>
                            Сгенерировать договор
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let templateData = <?= json_encode($availableTemplates) ?>;
        let currentColor = 'blue';

        // Примеры для заполнения полей
        const fieldExamples = {
            // Общие поля
            'contract_number': '001/2024',
            'city': 'Москва',
            'contract_date': '15.01.2024',
            
            // Договор лизинга
            'lessor_name': 'ООО "Лизинг Плюс"',
            'lessor_representative': 'Лизингов Леонид Леонидович',
            'lessor_authority': 'Устава',
            'lessee_name': 'ИП Петров Петр Петрович',
            'lessee_representative': 'Петров Петр Петрович',
            'lessee_authority': 'свидетельства о регистрации ИП',
            'leased_property': 'Автомобиль Toyota Camry, 2023 г.в., VIN: JT2BF28K9X0123456',
            'lease_payment': '45000',
            'lease_payment_words': 'сорок пять тысяч',
            'lease_term': '36 месяцев',
            'lease_start_date': '01.02.2024',
            'lease_end_date': '31.01.2027',
            'total_cost': '1620000',
            'total_cost_words': 'один миллион шестьсот двадцать тысяч',
            'payment_date': '10',
            'penalty_rate': '0.1',
            'lessor_inn': '7707083910',
            'lessor_address': 'г. Москва, ул. Лизинговая, д. 15',
            'lessee_inn': '770708391001',
            'lessee_address': 'г. Москва, ул. Предпринимательская, д. 7',
            
            // Трудовой договор
            'employer_name': 'ООО "Рога и Копыта"',
            'employer_representative': 'Петров Петр Петрович',
            'employer_authority': 'Устава',
            'employee_name': 'Работников Роман Романович',
            'position': 'Менеджер по продажам',
            'department': 'Отдел продаж',
            'workplace': 'г. Москва, ул. Тверская, д. 1, офис 101',
            'job_duties': 'Поиск клиентов, заключение договоров, ведение переговоров',
            'work_schedule': '5-дневная',
            'daily_hours': '8',
            'start_time': '09:00',
            'end_time': '18:00',
            'break_duration': '1 час',
            'break_start': '13:00',
            'break_end': '14:00',
            'vacation_days': '28',
            'salary': '80000',
            'salary_words': 'восемьдесят тысяч',
            'payment_frequency': 'два раза в месяц',
            'payment_dates': '15 и 30',
            'contract_type': 'на неопределенный срок',
            'start_date': '2024-02-01',
            'employer_inn': '7707083893',
            'employer_address': 'г. Москва, ул. Тверская, д. 1',
            'employee_passport': '4510 123456',
            'employee_address': 'г. Москва, ул. Ленина, д. 10, кв. 5',
            'employee_phone': '+7 (999) 123-45-67',
            
            // Аренда нежилого помещения
            'landlord_name': 'ООО "Арендодатель"',
            'landlord_representative': 'Арендодателев Александр Александрович',
            'landlord_authority': 'Устава',
            'tenant_name': 'ООО "Арендатор"',
            'tenant_representative': 'Арендаторов Борис Борисович',
            'tenant_authority': 'Устава',
            'property_address': 'г. Москва, ул. Садовая, д. 25, пом. 1',
            'total_area': '150',
            'useful_area': '140',
            'floor': '1',
            'floors_total': '5',
            'purpose': 'офисные помещения',
            'technical_condition': 'удовлетворительное',
            'lease_term': '1 год',
            'start_date': '2024-02-01',
            'end_date': '2025-01-31',
            'rent_amount': '120000',
            'rent_amount_words': 'сто двадцать тысяч',
            'payment_schedule': 'ежемесячно',
            'payment_date': '10',
            'penalty_rate': '0.1',
            'landlord_inn': '7707083894',
            'tenant_inn': '7707083895',
            
            // Купля-продажа товара
            'seller_name': 'ООО "Поставщик"',
            'seller_representative': 'Продавцов Павел Павлович',
            'seller_authority': 'Устава',
            'buyer_name': 'ООО "Покупатель"',
            'buyer_representative': 'Покупателев Константин Константинович',
            'buyer_authority': 'Устава',
            'goods_description': 'Офисная мебель согласно спецификации',
            'quantity': '10',
            'unit': 'комплектов',
            'quality_description': 'новая, в заводской упаковке',
            'completeness': 'полная комплектация согласно техническому паспорту',
            'total_price': '500000',
            'total_price_words': 'пятьсот тысяч',
            'vat_included': 'включая НДС 20%',
            'payment_method': 'безналичным расчетом',
            'payment_terms': '100% предоплата',
            'payment_period': '5',
            'payment_trigger': 'подписания договора',
            'delivery_address': 'г. Москва, ул. Офисная, д. 1',
            'delivery_date': '2024-02-15',
            'quality_standards': 'ГОСТ Р 52870-2018',
            'warranty_period': '12 месяцев',
            'delay_penalty': '0.1',
            'payment_penalty': '0.1',
            'seller_inn': '7707083896',
            'seller_address': 'г. Москва, ул. Складская, д. 5',
            'buyer_inn': '7707083897',
            'buyer_address': 'г. Москва, ул. Офисная, д. 1',
            
            // Оказание услуг
            'provider_name': 'ООО "БухУчет"',
            'provider_representative': 'Услугин Устин Устинович',
            'provider_authority': 'Устава',
            'client_name': 'ООО "Заказчик"',
            'client_representative': 'Заказчиков Захар Захарович',
            'client_authority': 'Устава',
            'services_description': 'Бухгалтерское сопровождение деятельности',
            'services_scope': 'ведение бухгалтерского учета, подготовка отчетности',
            'service_location': 'офис Заказчика',
            'milestones': 'ежемесячная подготовка отчетности',
            'total_cost': '50000',
            'total_cost_words': 'пятьдесят тысяч',
            'vat_status': 'включая НДС 20%',
            'payment_method': 'безналичным расчетом',
            'payment_schedule': 'ежемесячно до 10 числа',
            'quality_requirements': 'соответствие требованиям законодательства РФ',
            'provider_inn': '7707083898',
            'provider_address': 'г. Москва, ул. Бухгалтерская, д. 3',
            'client_inn': '7707083899',
            'client_address': 'г. Москва, ул. Клиентская, д. 7',
            
            // Подрядные работы
            'contractor_name': 'ООО "Подрядчик"',
            'work_description': 'Ремонт офисного помещения',
            'work_scope': 'косметический ремонт площадью 100 кв.м.',
            'work_location': 'г. Москва, ул. Ремонтная, д. 1',
            'completion_date': '2024-03-01',
            'materials_provider': 'Заказчик',
            'equipment_provider': 'Подрядчик',
            'warranty_period': '12 месяцев',
            'contractor_inn': '7707083900',
            'contractor_address': 'г. Москва, ул. Строительная, д. 9',
            
            // Соглашение о неразглашении
            'disclosing_party': 'ООО "Инноватор"',
            'disclosing_representative': 'Секретов Сергей Сергеевич',
            'disclosing_authority': 'Устава',
            'receiving_party': 'ООО "Партнер"',
            'receiving_representative': 'Партнеров Павел Павлович',
            'receiving_authority': 'Устава',
            'purpose': 'ведением переговоров о сотрудничестве',
            'confidential_info': 'техническая документация, бизнес-процессы, финансовая информация',
            'usage_purpose': 'оценки возможности сотрудничества',
            'access_purpose': 'выполнения своих трудовых обязанностей',
            'confidentiality_period': '5 лет',
            'return_period': '30',
            'penalty_amount': '1000000',
            'penalty_amount_words': 'один миллион',
            'disclosing_inn': '7707083901',
            'disclosing_address': 'г. Москва, ул. Секретная, д. 11',
            'receiving_inn': '7707083902',
            'receiving_address': 'г. Москва, ул. Партнерская, д. 13',
            
            // Договор авторского заказа
            'client_name': 'ООО "Издательский дом"',
            'client_representative': 'Издателев Иван Иванович',
            'client_authority': 'Устава',
            'author_name': 'Писателев Петр Петрович',
            'work_description': 'Художественное произведение "Повесть о времени"',
            'work_type': 'Художественная литература',
            'work_volume': '200 страниц',
            'work_requirements': 'Жанр - историческая повесть, объем 200 страниц формата А4',
            'completion_date': '31.12.2024',
            'delivery_method': 'в электронном виде на email заказчика',
            'fee_amount': '150000',
            'fee_amount_words': 'сто пятьдесят тысяч',
            'payment_schedule': 'в два этапа: 50% - аванс, 50% - после сдачи работы',
            'payment_period': '10',
            'payment_trigger': 'подписания акта приема-передачи',
            'usage_rights': 'право на издание тиражом до 5000 экземпляров',
            'rights_period': '5 лет',
            'delay_penalty': '0.1',
            'payment_penalty': '0.1',
            'client_inn': '7707083903',
            'client_address': 'г. Москва, ул. Литературная, д. 25',
            'author_passport': '4510 654321',
            'author_address': 'г. Москва, ул. Писательская, д. 12, кв. 45',
            'author_phone': '+7 (999) 876-54-32'
        };

        function openModal(templateId, color) {
            currentColor = color;
            document.getElementById('modal-template-id').value = templateId;
            
            // Обновляем цвет иконки и кнопки
            const modalIcon = document.getElementById('modal-icon');
            const submitBtn = document.getElementById('submit-btn');
            
            modalIcon.className = `w-12 h-12 bg-gradient-to-br from-${color}-500 to-${color}-600 rounded-xl flex items-center justify-center mr-4`;
            submitBtn.className = `px-12 py-3 bg-gradient-to-r from-${color}-500 to-${color}-600 hover:from-${color}-600 hover:to-${color}-700 text-white rounded-lg transition duration-200 font-medium shadow-lg`;
            
            // Загружаем поля формы
            loadTemplateFields(templateId);
            
            // Показываем модальное окно
            document.getElementById('contractModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('contractModal').classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        async function loadTemplateFields(templateId) {
            try {
                const response = await fetch('get_template_fields.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ template_id: templateId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('modal-title').textContent = data.template.name;
                    document.getElementById('modal-description').textContent = data.template.description;
                    renderFormFields(data.variables);
                } else {
                    alert('Ошибка загрузки полей шаблона: ' + data.message);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при загрузке шаблона');
            }
        }

        function renderFormFields(variables) {
            const container = document.getElementById('modal-form-fields');
            container.innerHTML = '';
            
            // Группировка полей по категориям
            const fieldGroups = {
                'basic': { name: 'Основная информация', icon: 'fa-info-circle' },
                'parties': { name: 'Стороны договора', icon: 'fa-users' },
                'financial': { name: 'Денежные условия', icon: 'fa-ruble-sign' },
                'terms': { name: 'Сроки и условия', icon: 'fa-calendar-alt' },
                'additional': { name: 'Дополнительные условия', icon: 'fa-plus-circle' }
            };
            
            Object.entries(variables).forEach(([key, description]) => {
                const fieldGroup = getFieldGroup(key);
                
                let groupContainer = container.querySelector(`[data-group="${fieldGroup}"]`);
                if (!groupContainer) {
                    groupContainer = document.createElement('div');
                    groupContainer.setAttribute('data-group', fieldGroup);
                    groupContainer.innerHTML = `
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-6 shadow-sm border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas ${fieldGroups[fieldGroup]?.icon || 'fa-folder-open'} text-${currentColor}-500 mr-3"></i>
                                ${fieldGroups[fieldGroup]?.name || 'Прочие поля'}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 fields-container"></div>
                        </div>
                    `;
                    container.appendChild(groupContainer);
                }
                
                const fieldsContainer = groupContainer.querySelector('.fields-container');
                const fieldElement = createFieldElement(key, description);
                fieldsContainer.appendChild(fieldElement);
            });
        }

        function getFieldGroup(fieldName) {
            if (fieldName.includes('city') || fieldName.includes('date') || fieldName.includes('contract_number')) {
                return 'basic';
            }
            if (fieldName.includes('name') || fieldName.includes('inn') || fieldName.includes('address') || fieldName.includes('signature')) {
                return 'parties';
            }
            if (fieldName.includes('cost') || fieldName.includes('price') || fieldName.includes('salary') || fieldName.includes('rent') || fieldName.includes('payment') || fieldName.includes('amount')) {
                return 'financial';
            }
            if (fieldName.includes('date') || fieldName.includes('period') || fieldName.includes('term') || fieldName.includes('schedule')) {
                return 'terms';
            }
            return 'additional';
        }

        function createFieldElement(name, description) {
            const div = document.createElement('div');
            const inputType = getInputType(name);
            const isTextarea = inputType === 'textarea';
            const colSpan = isTextarea ? 'md:col-span-2' : 'md:col-span-1';
            const example = fieldExamples[name] || '';
            
            div.className = `${colSpan} field-with-example`;
            div.innerHTML = `
                                 <label for="${name}" class="block text-sm font-bold text-gray-700 mb-2">
                     ${description}
                     ${isRequiredField(name) ? '<span class="text-red-500 ml-1">*</span>' : ''}
                 </label>
                <div class="relative">
                    ${isTextarea ? 
                        `<textarea name="variables[${name}]" id="${name}" rows="3" class="w-full px-4 py-3 pr-24 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-${currentColor}-500 focus:border-${currentColor}-500 transition-colors" placeholder="${getPlaceholder(name)}"></textarea>` :
                        `<input type="${inputType}" name="variables[${name}]" id="${name}" class="w-full px-4 py-3 pr-24 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-${currentColor}-500 focus:border-${currentColor}-500 transition-colors" placeholder="${getPlaceholder(name)}">` 
                    }
                    ${example ? `<span class="example-text">Пример: ${example}</span>` : ''}
                </div>
            `;
            
            return div;
        }

        function getInputType(fieldName) {
            if (fieldName.includes('date')) return 'date';
            if (fieldName.includes('email')) return 'email';
            if (fieldName.includes('phone')) return 'tel';
            if (fieldName.includes('description') || fieldName.includes('duties') || fieldName.includes('conditions') || fieldName.includes('requirements')) return 'textarea';
            return 'text';
        }

        function isRequiredField(fieldName) {
            const requiredFields = ['city', 'contract_number', 'name', 'inn'];
            return requiredFields.some(required => fieldName.includes(required));
        }

        function getPlaceholder(fieldName) {
            const placeholders = {
                'city': 'Введите город',
                'contract_number': 'Введите номер договора',
                'inn': 'Введите ИНН',
                'date': 'Выберите дату',
                'phone': 'Введите телефон',
                'email': 'Введите email'
            };
            
            for (const [key, placeholder] of Object.entries(placeholders)) {
                if (fieldName.includes(key)) return placeholder;
            }
            
            return 'Введите значение';
        }

        // Закрытие модального окна при клике вне его
        document.getElementById('contractModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Закрытие модального окна при нажатии Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html> 