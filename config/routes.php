<?php

$arr = array();

if(file_exists(__DIR__.'/routes/api.php'))
{
    $arr = include __DIR__.'/routes/api.php';
}

return $arr;
