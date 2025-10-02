<?php
$js=file_get_contents(__DIR__.'/bank_app.js');
if(!$js){exit('no js');}
if(preg_match_all('/https?:\\/\\/[^"\']+api[^"\']+/i',$js,$m)){
 foreach(array_unique($m[0]) as $u) echo $u."\n";
}
