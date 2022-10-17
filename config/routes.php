<?php

$arr = array();

if(file_exists(__DIR__.'/routes/api.php'))
{
    $arr = include __DIR__.'/routes/api.php';
}

if(file_exists(__DIR__.'/routes/web.php'))
{
    $arr = array_merge($arr, include __DIR__.'/routes/web.php');
}

return $arr;
