<?php
$url='https://bank-ege.ru/ege/informatika/tasks/task1/1-simmetricnye-grafy';
$html=@file_get_contents($url);
if(!$html){die("no html\n");}
echo strlen($html)." bytes\n";
file_put_contents(__DIR__.'/tmp.html',$html);
