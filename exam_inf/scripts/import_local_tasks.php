<?php
/**
 * Импортирует скрины из public/content/{cat}/images/*.png и ответы из answers/*.txt.
 * Каждому файлу создаётся запись в таблице tasks (если нет).
 *
 * Файл ответа должен иметь то же имя, что и картинка (например 001.png + 001.txt)
 * Категория определяется по номеру папки (01..27) => CategoryController::$categories
 */

require_once __DIR__ . '/../app/Models/Task.php';
require_once __DIR__ . '/../app/Controllers/CategoryController.php';

$baseDir = __DIR__ . '/../public/content';
$added = 0;
for ($i=1; $i<=27; $i++) {
    $catDir = sprintf('%s/%02d', $baseDir, $i);
    $imgDir = $catDir . '/images';
    $ansDir = $catDir . '/answers';
    if (!is_dir($imgDir)) continue;

    $categoryName = CategoryController::$categories[$i] ?? 'Категория '.$i;
    $files = glob($imgDir.'/*.png');
    natsort($files);
    foreach ($files as $filePath) {
        $fileName = basename($filePath, '.png');
        $imgUrl   = str_replace(__DIR__.'/../public/', '', $filePath); // relative url
        // check if exists
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id FROM tasks WHERE image_url = ? LIMIT 1');
        $stmt->execute([$imgUrl]);
        if ($stmt->fetch()) continue;

        $answerFile = $ansDir . '/' . $fileName . '.txt';
        $answer = is_file($answerFile) ? trim(file_get_contents($answerFile)) : '';

        $data=[
            'source_id'     => 200000 + $i*1000 + (int)$fileName,
            'task_number'   => sprintf('Local-%02d-%s',$i,$fileName),
            'category'      => $categoryName,
            'theme'         => '',
            'text'          => '<img src="/'.str_replace('\\','/',$imgUrl).'" class="img-fluid"/>',
            'image_url'     => $imgUrl,
            'answer'        => $answer,
            'solution_html' => '',
            'difficulty'    => '',
            'year'          => null,
        ];
        Task::create($data);
        $added++;
        echo "[+] {$data['task_number']}\n";
    }
}

echo "Done. Added {$added} tasks.\n";
