<?php
/**
 * Simple scraper to fetch tasks from inf-ege.sdamgia.ru and store them in the local database.
 * Usage (cmd):
 *   php scrape_tasks.php 1 100   # загрузит задачи с id 1..100
 */

require_once __DIR__ . '/../app/Models/Task.php';

/**
 * Парсит страницу задачи и возвращает массив данных или null, если парсинг не удался.
 */
if (!function_exists('fetchTask')) {
function fetchTask(int $sourceId): ?array
{
    $url = "https://inf-ege.sdamgia.ru/problem?id={$sourceId}";
    $html = @file_get_contents($url);
    if (!$html) {
        echo "Не удалось получить {$url}\n";
        return null;
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    // Номер задания
    $taskNumber = trim($xpath->evaluate('string(//span[@class="prob_nums"])'));
    if ($taskNumber === '') {
        echo "ID {$sourceId}: номер задания не найден, пропуск\n";
        return null;
    }

    // Крошки для категории/темы
    $category = trim($xpath->evaluate('string((//a[@class="breadcrumbs__level-link"])[2])'));
    $theme     = trim($xpath->evaluate('string((//a[@class="breadcrumbs__level-link"])[3])'));

    // Текст задания
    $textNode = $xpath->query('//div[contains(@class,"problem_text")]')->item(0);
    $textHTML = $textNode ? $dom->saveHTML($textNode) : '';

    // Картинка внутри текста (если есть)
    $imgNode = $xpath->query('//div[contains(@class,"problem_text")]//img')->item(0);
    $imgUrl  = $imgNode ? $imgNode->getAttribute('src') : null;

    // Ответ
    $answer = trim($xpath->evaluate('string(//div[@class="prob_answer"]//span)'));

    // Решение
    $solutionNode  = $xpath->query('//div[@class="solution"]')->item(0);
    $solutionHTML  = $solutionNode ? $dom->saveHTML($solutionNode) : '';

    // Сложность/уровень
    $difficulty = trim($xpath->evaluate('string(//span[contains(@class,"dificult")])'));

    // Год (берем из хлебных крошек, если есть)
    $year = (int)$xpath->evaluate('string(//a[contains(@href,"year=")])');
    if ($year === 0) $year = null;

    return [
        'source_id'     => $sourceId,
        'task_number'   => $taskNumber,
        'category'      => $category,
        'theme'         => $theme,
        'text'          => $textHTML,
        'image_url'     => $imgUrl,
        'answer'        => $answer,
        'solution_html' => $solutionHTML,
        'difficulty'    => $difficulty,
        'year'          => $year,
    ];
}
}

$startId = isset($argv[1]) ? (int)$argv[1] : 1;
$endId   = isset($argv[2]) ? (int)$argv[2] : $startId;

for ($id = $startId; $id <= $endId; $id++) {
    echo "==> {$id}\n";
    $data = fetchTask($id);
    if ($data) {
        try {
            Task::create($data);
            echo "   Сохранено.\n";
        } catch (Throwable $e) {
            echo "   Ошибка БД: {$e->getMessage()}\n";
        }
    }
    sleep(1); // вежливая задержка
}
