<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;

class LocalBuild extends Command
{
    public string $serialize = 'app:local-build';

    public function execute(CommandArgv $commandArgv)
    {
        $this->line('Welcome Spiral Frame !!!!');
        $this->create();
    }
    
    protected function defineOptions()
    {
        $this->addOption('p', 'port', 'port を指定してください' , true);
        $this->addOption('d', 'dir', 'ディレクトリ を指定してください' , true);
        $this->addOption('f', 'file', 'ルートファイル を指定してください' , true);
    }

    private function create()
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
        exec("cp -r spiral-framework/.mock .build");
        $port = $this->getOptionValue('port');
        $dir = $this->getOptionValue('dir');
        $routeFile = $this->getOptionValue('file');
        $file = fopen('.build/build.php', 'w');  // ファイルを開くまたは新規作成
        if ($file) {
            fwrite($file, $this->buildFile($routeFile));  // ファイルにデータを書き込む
            fclose($file);                   // ファイルを閉じる
        } else {
            echo "ファイルを開くことができませんでした";
        }

        exec("php -S localhost:{$port} .build/build.php");
        return true;
    }

    private function buildFile($file){
        return <<<EOF
<?php
require_once '.mock/Spiral.php';
require_once '.mock/PbSpiralApiCommunicator.php';
require_once '.mock/SpiralApiRequest.php';
chdir('src');
define('BASE_PATH','../spiral-framework/src/');
define('IS_LOCAL',true);
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
