<?php
/**
 * Скрипт загружает 24 задания «Симметричные графы» с bank-ege.ru и заносит как категорию №1.
 *
 * Страница: https://bank-ege.ru/ege/informatika/tasks/task1/1-simmetricnye-grafy
 */

require_once __DIR__ . '/../app/Models/Task.php';

$url = 'https://bank-ege.ru/ege/informatika/tasks/task1/1-simmetricnye-grafy';
$html = @file_get_contents($url);
if (!$html) {
    exit("Не могу скачать страницу {$url}\n");
}
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
libxml_clear_errors();
$xpath = new DOMXPath($dom);

// Каждый блок задания <div class="task"> или <div class="task-block">
$taskNodes = $xpath->query('//div[contains(@class, "task")]');
$counter = 0;
foreach ($taskNodes as $taskNode) {
    $counter++;
    // Номер задачи внутри серии
    $taskNumber = 'BankSymGraph-' . $counter;

    // Текст задания (HTML)
    $taskHTML = trim($dom->saveHTML($taskNode));

    // Попытка найти ответ: ищем текст «Ответ:»
    $answer = '';
    $answerNode = $xpath->query('.//*[contains(text(), "Ответ:")]', $taskNode)->item(0);
    if ($answerNode) {
        $answerText = strip_tags($answerNode->textContent);
        if (preg_match('/Ответ:\s*([A-Za-zА-Яа-я0-9]+)/u', $answerText, $m)) {
            $answer = $m[1];
        }
    }

    // Если ответ в отдельном <div class="answer">
    if ($answer === '') {
        $aNode = $xpath->query('.//div[contains(@class, "answer")]', $taskNode)->item(0);
        if ($aNode) {
            $answer = trim($aNode->textContent);
        }
    }

    if ($answer === '') {
        echo "[!] Не найден ответ для задания {$counter}, пропуск\n";
        continue;
    }

    $data = [
        'source_id'     => 100000 + $counter,           // уникальный ID вне зоны inf-ege
        'task_number'   => $taskNumber,
        'category'      => 'Анализ информационных моделей',
        'theme'         => 'Симметричные графы',
        'text'          => $taskHTML,
        'image_url'     => null,
        'answer'        => $answer,
        'solution_html' => '',
        'difficulty'    => '',
        'year'          => null,
    ];

    try {
        Task::create($data);
        echo "[+] Добавлено {$taskNumber}\n";
    } catch (Throwable $e) {
        echo "[!] Ошибка при добавлении {$taskNumber}: {$e->getMessage()}\n";
    }
}

echo "Завершено. Добавлено {$counter} заданий.\n";
