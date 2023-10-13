<?php

foreach(scanDirctory('Command/Basis') as $file){
    require_once($file); 
}

foreach(scanDirctory('Command/Commands') as $file){
    require_once($file); 
}

function scanDirctory($dir) {
    $files = glob(rtrim($dir, '/') . '/*');
    $list = array();
    foreach ($files as $file) {
        if (is_file($file)) {
            $list[] = $file;
        }
        if (is_dir($file)) {
            $list = array_merge($list, scanDirctory($file));
        }
    }
    return $list;
}