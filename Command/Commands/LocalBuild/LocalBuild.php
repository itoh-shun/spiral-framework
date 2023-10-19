<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;

class LocalBuild extends Command
{
    private string $serialize = 'app:local-build';

    public function getSerialize()
    {
        return $this->serialize;
    }

    public function execute(CommandArgv $commandArgv)
    {
        $this->line('Welcome Spiral Frame !!!!');
        $this->create($commandArgv);
    }

    private function create(CommandArgv $commandArgv)
    {
        if (file_exists('.build')) {
            exec('rm -rf .build');
        }

        mkdir('.build');
        exec('composer dump-autoload');

        $filelist = glob("src/*");
        foreach ($filelist as $file) {
            if (file_exists("$file/makeAutoload.php")) {
                exec("cd $file && php makeAutoload.php && cd -");
            }
        }
        $filelist = glob("spiral-framework/src/*");
        foreach ($filelist as $file) {
            if (file_exists("$file/makeAutoload.php")) {
                exec("cd $file && php makeAutoload.php && cd -");
            }
        }

        exec("cp -r spiral-framework/src/* .build");
        exec("cp -r spiral-framework/.mock .build");
        exec("cp -r src/* .build");

        $port = $commandArgv->__get('options')[0];
        $dir = $commandArgv->__get('options')[1];
        $routeFile = $commandArgv->__get('options')[2];

        $file = fopen('.build/build.php', 'w');  // ファイルを開くまたは新規作成
        if ($file) {
            fwrite($file, $this->buildFile($routeFile));  // ファイルにデータを書き込む
            fclose($file);                   // ファイルを閉じる
        } else {
            echo "ファイルを開くことができませんでした";
        }

        $filelist = glob(".build/*");
        foreach ($filelist as $file) {
            if (file_exists("$file/makeAutoload.php")) {
                unlink("$file/makeAutoload.php");
            }
            if (file_exists("$file/.git")) {
                $this->rmdir_recursively("$file/.git");
            }
        }

        exec("cd .build && php -S localhost:{$port} -t {$dir} build.php");
        return true;
    }

    private function buildFile($file){
        return <<<EOF
<?php
require_once '.mock/Spiral.php';
require_once '.mock/PbSpiralApiCommunicator.php';
require_once '.mock/SpiralApiRequest.php';
\$SPIRAL = new Spiral();
require_once "{$file}";
EOF;
    }

    private function rmdir_recursively($dir)
    {
        $dh = opendir($dir);
        if ($dh === false) {
            return false;
        }

        while (true) {
            $file = readdir($dh);
            if ($file === false) {
                break;
            }
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = rtrim($dir, '/') . '/' . $file;
            if (is_dir($path)) {
                $this->rmdir_recursively($path);
            } else {
                unlink($path);
            }
        }
        closedir($dh);
        return rmdir($dir);
    }
}
