<?php
$ctx = stream_context_create([
    'http' => [
        'header' => [
            'Referer: https://bank-ege.ru',
            'X-Requested-With: XMLHttpRequest',
            'Accept: application/json',
            'User-Agent: Mozilla/5.0'
        ]
    ]
]);
$url='https://new-api.bank-ege.ru/api/ege/informatika/tasks/task1';
$json = @file_get_contents($url,false,$ctx);
var_dump(substr($json,0,200));
