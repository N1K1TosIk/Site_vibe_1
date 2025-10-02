<?php
/**
 * Bulk scraper: пытается пройти все ID задач подряд и сохранить найденные.
 *
 * Запуск:
 *   php scrape_all.php            # сканирует с 1 до MAX_ID, останавливается после серии промахов
 *   php scrape_all.php 1000 2000  # сканирует заданный диапазон
 */

require_once __DIR__ . '/../app/Models/Task.php';

require_once __DIR__ . '/scrape_tasks.php'; // содержит fetchTask + Task::create()

$start = isset($argv[1]) ? (int)$argv[1] : 1;
$end   = isset($argv[2]) ? (int)$argv[2] : 60000; // грубая верхняя граница

$missStreak = 0;
$maxMiss    = 1000; // остановка, если подряд столько промахов (нет задачи)

for ($id = $start; $id <= $end; $id++) {
    $data = fetchTask($id);
    if ($data) {
        try {
            Task::create($data);
            echo "[+] {$id} сохранено\n";
            $missStreak = 0;
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                // уже есть
                echo "[=] {$id} уже в базе\n";
            } else {
                echo "[!] Ошибка БД для {$id}: {$e->getMessage()}\n";
            }
            $missStreak = 0;
        }
    } else {
        $missStreak++;
        if ($missStreak >= $maxMiss) {
            echo "Последние {$maxMiss} id без задач. Останавливаемся.\n";
            break;
        }
    }
    if ($id % 100 == 0) {
        echo "-- достигнут id {$id} --\n";
    }
    usleep(300000); // 0.3s между запросами, чтобы не нагружать сайт
}
