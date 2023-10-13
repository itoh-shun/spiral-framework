<?php

// 元のディレクトリのパスを保存
$originalDirectory = getcwd();

// 現在のディレクトリが 'spiral-framework' でない場合にのみディレクトリを変更
if (basename($originalDirectory) !== 'spiral-framework') {
    chdir('spiral-framework');
}

foreach(scanDirctory('Command/Basis') as $file){
    require_once($file); 
}

foreach(scanDirctory('Command/Commands') as $file){
    require_once($file); 
}
// 元のディレクトリに戻る
chdir($originalDirectory);
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