<?php
/**
 * Scraper for 24 variants of symmetric graphs from bank-ege.ru
 */

require_once __DIR__ . '/../app/Models/Task.php';

$root = 'https://bank-ege.ru';
$listUrl = $root . '/ege/informatika/tasks/task1/1-simmetricnye-grafy';
$html = @file_get_contents($listUrl);
if (!$html) {
    exit("[err] cannot download list page\n");
}
// find variant links
preg_match_all('#/ege/informatika/tasks/variant/[^"\']+#i', $html, $m);
$links = array_unique($m[0]);
if (!$links) {
    exit("[err] no variant links found\n");
}
$counter = 0;
foreach ($links as $rel) {
    $url = $root . $rel;
    echo "-- $url\n";
    $vhtml = @file_get_contents($url);
    if (!$vhtml) {
        echo "   [skip] cannot load\n";
        continue;
    }
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($vhtml, LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    $xp = new DOMXPath($dom);

    // question block
    $qNode = $xp->query('//div[contains(@class, "question") or contains(@class,"task") or contains(@class,"problem")]')->item(0);
    if (!$qNode) {
        echo "   [skip] no question\n";
        continue;
    }
    $questionHtml = $dom->saveHTML($qNode);

    // answer extraction
    $answer = '';
    $aNode = $xp->query('//*[contains(text(),"Ответ") or contains(@class,"answer")]')->item(0);
    if ($aNode) {
        $txt = strip_tags($aNode->textContent);
        if (preg_match('/Ответ\s*[:\-]?\s*([A-Za-zА-Яа-я0-9]+)/u', $txt, $mm)) {
            $answer = trim($mm[1]);
        }
    }
    if ($answer === '') {
        echo "   [skip] answer not found\n";
        continue;
    }
    $counter++;
    $data = [
        'source_id'     => 110000 + $counter,
        'task_number'   => 'BankSymGraphVar-' . sprintf('%02d', $counter),
        'category'      => 'Анализ информационных моделей',
        'theme'         => 'Симметричные графы',
        'text'          => $questionHtml,
        'image_url'     => null,
        'answer'        => $answer,
        'solution_html' => '',
        'difficulty'    => '',
        'year'          => null,
    ];
    try {
        Task::create($data);
        echo "   [+] saved\n";
    } catch (Throwable $e) {
        echo "   [!] db error: {$e->getMessage()}\n";
    }
    sleep(1);
}

echo "Done. Added {$counter} tasks.\n";
