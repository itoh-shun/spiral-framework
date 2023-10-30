<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Core\View;
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
        $this->prepareBuildDirectory();
        $this->updateAutoload();
        $this->copyMockFiles();
        $this->createBuildFile();
        $this->startLocalServer();

        return true;
    }

    private function prepareBuildDirectory()
    {
        if (file_exists('.build')) {
            exec('rm -rf .build');
        }

        mkdir('.build');
    }

    private function updateAutoload()
    {
        exec('composer dump-autoload');
        $this->updateAutoloadForDirectory("src");
        $this->updateAutoloadForDirectory("spiral-framework/src", false);
    }

    private function updateAutoloadForDirectory($directory, $forgeTemplate = true)
    {
        $filelist = glob("$directory/*");
        foreach ($filelist as $file) {
            if (file_exists("$file/makeAutoload.php")) {
                if ($forgeTemplate) {
                    $text = View::forge('spiral-framework/Command/Samples/makeAutoload');
                    file_put_contents("$file/makeAutoload.php", $text->render());
                }
                exec("cd $file && php makeAutoload.php && cd -");
            }
        }
    }

    private function copyMockFiles()
    {
        exec("cp -r spiral-framework/.mock .build");
    }

    private function createBuildFile()
    {
        $routeFile = $this->getOptionValue('file');
        $fileContent = $this->buildFileContent($routeFile);
        $filePath = '.build/build.php';

        file_put_contents($filePath, $fileContent);
    }

    private function buildFileContent($file)
    {
        return <<<EOF
<?php
require_once '.mock/Spiral.php';
require_once '.mock/PbSpiralApiCommunicator.php';
chdir('src');
define('BASE_PATH','../spiral-framework/src/');
define('IS_LOCAL',true);
\$SPIRAL = new Spiral();
require_once "{$file}";
EOF;
    }

    private function startLocalServer()
    {
        $port = $this->getOptionValue('port');
        exec("php -S localhost:{$port} .build/build.php");
    }
}