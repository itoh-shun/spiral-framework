<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Core\View;
use Command\Basis\Request\CommandArgv;
use Command\Basis\Request\CurlClient;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ApplicationDeploy extends Command
{
    public string $serialize = 'app:deploy';
    public string $currentPath = '';

    // Move hardcoded values to class constants
    const API_LOCATOR_URL = 'https://www.pi-pe.co.jp/api/locator';
    const MULTIPART_BOUNDARY = 'SPIRAL_API_MULTIPART_BOUNDARY';

    private $curlClient;
    public function __construct()
    {
        $this->curlClient = new CurlClient();
        parent::__construct();
        $this->currentPath = getcwd();
    }
    protected function defineOptions()
    {
        $this->addOption('s', 'skip', '指定するとGitの差分を反映します。', false);
    }

    public function execute(CommandArgv $commandArgv)
    {
        $this->line('Welcome Spiral Frame !!!!');

        $environments = $this->config();
        $environment = $this->askForEnvironment($environments);
        if (!$environment) return;

        $response = $this->createZip($environment);
        $this->deploy($response['filename'], $environments['deploy'][$environment]);
    }

    private function askForEnvironment($environments)
    {
        $environment = $this->ask(
            'Please select the environment you wish to reflect. [' .
            implode(',', array_keys($environments['deploy'])) .
            '] : ',
            false
        );

        if (!isset($environments['deploy'][$environment])) {
            $this->line('Not configured...');
            return null;
        }

        return $environment;
    }

    private function deploy($filename, $config)
    {
        // Use constants instead of hardcoded values
        $API_TOKEN = $config['token'];
        $API_SECRET = $config['secret'];

        $api_headers = [
            'X-SPIRAL-API: locator/apiserver/request',
            'Content-Type: application/json; charset=UTF-8',
        ];
        $parameters = [];
        $parameters['spiral_api_token'] = $API_TOKEN;
        $json = json_encode($parameters);

        try {
            $response = $this->curlClient->post(self::API_LOCATOR_URL, $json, $api_headers);
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }

        if ($response['code'] != 0) {
            var_dump($response);
            exit(1);
        }

        $API_URL = $response['location'];

        $filedata = file_get_contents($filename);
        $api_headers = [
            'X-SPIRAL-API: custom_module/upload/request',
            "Content-Type: multipart/form-data; boundary=\"" .
            $this::MULTIPART_BOUNDARY .
            "\"",
        ];
        $parameters = [];
        $parameters['spiral_api_token'] = $API_TOKEN;
        $parameters['passkey'] = time();
        $parameters['dir'] = '';
        $parameters['compress'] = 't';
        $key = $parameters['spiral_api_token'] . '&' . $parameters['passkey'];
        $parameters['signature'] = hash_hmac('sha1', $key, $API_SECRET, false);

        $postdata = '--' . $this::MULTIPART_BOUNDARY . "\r\n";
        $postdata .= "Content-Type: application/json; charset=\"UTF-8\";\r\n";
        $postdata .= "Content-Disposition: form-data; name=\"json\"\r\n\r\n";
        $postdata .= json_encode($parameters);
        $postdata .= "\r\n\r\n";

        $postdata .= '--' . $this::MULTIPART_BOUNDARY . "\r\n";
        $postdata .= "Content-Type: application/x-httpd-php;\r\n";
        $postdata .= "Content-Disposition: form-data; name=\"src\"; filename=\"$filename\"\r\n\r\n";
        $postdata .= $filedata;
        $postdata .= "\r\n\r\n";
        $postdata .= '--' . $this::MULTIPART_BOUNDARY . "--\r\n";
        $postdata .= "\r\n";
        

        try {
            $response = $this->curlClient->post($API_URL, $postdata, $api_headers);
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }

        if ($response['code'] != 0) {
            var_dump($response);
            return false;
        }

        return true;
    }
    private function createZip($environment)
    {
        $this->prepareDirectories($environment);
        $this->updateAutoload();
        $this->handleGitOperations($environment);
        $this->createZipArchive($environment);
    
        return ['filename' => ".tmp/$environment.zip"];
    }
    
    private function prepareDirectories($environment)
    {
        if (file_exists('.tmp')) {
            exec('rm -rf .tmp');
        }
    
        mkdir('.tmp');
    
        if (!file_exists(".tmp/$environment")) {
            mkdir(".tmp/$environment");
        }
        if (file_exists(".tmp/$environment.zip")) {
            exec("rm -rf .tmp/$environment.zip");
        }
        exec('composer dump-autoload');
    }
    
    private function updateAutoload()
    {
        $srcFileList = glob("src/Library/*");
        foreach ($srcFileList as $file) {
            if (file_exists("$file/makeAutoloadCustom.php")) {
                
                exec("cd $file && php makeAutoloadCustom.php {$this->currentPath} && cd -");
            }
            if (file_exists("$file/makeAutoload.php")) {
                $text = View::forge('spiral-framework/Command/Samples/makeAutoload');
                file_put_contents("$file/makeAutoload.php", $text->render());
                
                exec("cd $file && php makeAutoload.php {$this->currentPath} && cd -");
            }
        }
        
        $srcFileList = glob("src/*");
        foreach ($srcFileList as $file) {
            if (file_exists("$file/makeAutoloadCustom.php")) {
                
                exec("cd $file && php makeAutoloadCustom.php {$this->currentPath} && cd -");
            }
            if (file_exists("$file/makeAutoload.php")) {
                $text = View::forge('spiral-framework/Command/Samples/makeAutoload');
                file_put_contents("$file/makeAutoload.php", $text->render());
                
                exec("cd $file && php makeAutoload.php {$this->currentPath} && cd -");
            }
        }
        
        $spiralFileList = glob("spiral-framework/src/*");
        foreach ($spiralFileList as $file) {
            if (file_exists("$file/makeAutoload.php")) {
                
                exec("cd $file && php makeAutoload.php {$this->currentPath} && cd -");
            }
        }
    }
    
    private function handleGitOperations($environment)
    {
        $skip = $this->getOptionValue('skip');
        $isGit = 'yes';
        if (!$skip) {
            $isGit = $this->ask(
                'gitコマンドがインストールされている場合、差分更新が可能です。実行しますか？ [yes or no]: ',
                false
            );
        }
    
        if ($isGit === 'yes') {
            $commitId = '';
            if (!$skip) {
                $commitId = $this->ask(
                    '差分を取得するコミットIDがある場合は入力してください : ',
                    false
                );
                $output = null;
                exec(
                    "git add -N .; git diff --name-only --relative=src/ $commitId",
                    $output
                );
    
                $this->line($output);
    
                $isDeploy = $this->ask(
                    'これらのファイルがデプロイされます。よろしいですか？ [yes or no]: ',
                    false
                );
    
                if ($isDeploy !== 'yes') {
                    $this->line('中止します');
                    exit();
                }
            }
    
            exec(
                "git add -N .; git diff --name-only --relative=src/ $commitId | xargs -I % cp -r --parents ./src/% .tmp/$environment > /dev/null 2>&1"
            );
    
            if (file_exists(".tmp/$environment/src")) {
                exec("mv .tmp/$environment/src/* .tmp/$environment");
                $this->rmdir_recursively(".tmp/$environment/src");
            }
        } else {
            exec("cp -r spiral-framework/src/* .tmp/$environment");
            exec("cp -r src/* .tmp/$environment");
        }
        $this->unlinkSymlinks('.tmp');
        $this->removeDotStartingDirectories('.tmp');
    }
    
    private function createZipArchive($environment)
    {
        $this->processDirectory(".tmp/$environment");
        exec("cd .tmp/$environment ; zip -r ../$environment.zip * ; cd -");
    }
    
    private function processDirectory($dir)
    {
        $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::CHILD_FIRST);
    
        foreach ($items as $item) {
            if ($item->isDir() && basename($item) == '.git') {
                // .git ディレクトリを再帰的に削除
                $this->rmdir_recursively($item->getPathname());
            } elseif ($item->isFile()) {
                // 特定のファイルを削除
                if (pathinfo($item->getPathname(), PATHINFO_EXTENSION) !== 'php') {
                    unlink($item->getPathname());
                }
                $filename = basename($item);
                if (in_array($filename, ['make.php', 'makeAutoload.php', 'makeAutoloadCustom.php'])) {
                    unlink($item->getPathname());
                }
            }
        }
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

    private function removeDotStartingDirectories($dir)
    {
        $dh = opendir($dir);
        if ($dh === false) {
            return false;
        }


        while (($file = readdir($dh)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = rtrim($dir, '/') . '/' . $file;
            if (is_dir($path)) {
                // ディレクトリ名がドットで始まる場合に再帰的に処理
                if (strpos($file, '.') === 0) {
                    
                    $this->removeDirectoryRecursively($path);
                } else {
                    // ドットで始まらないディレクトリの場合、そのディレクトリ内をさらに検索
                    $this->removeDotStartingDirectories($path);
                }
            }
        }
        closedir($dh);
    }

    private function removeDirectoryRecursively($dir)
    {
        $dh = opendir($dir);
        if ($dh === false) {
            return false;
        }

        while (($file = readdir($dh)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = rtrim($dir, '/') . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectoryRecursively($path);
            } else {
                unlink($path);
            }
        }
        closedir($dh);

        return rmdir($dir);
    }

    private function unlinkSymlinks($dir) {
        // ディレクトリが存在しないか確認
        if (!file_exists($dir)) {
            return;
        }
    
        // ディレクトリ内のアイテムを取得
        $items = new DirectoryIterator($dir);
        foreach ($items as $item) {
            // 現在のアイテムがディレクトリかどうか確認
            if ($item->isLink()) {
                $realPath = realpath($item->getPathname());
                // シンボリックリンクなら削除
                unlink($item->getPathname());
                if (is_dir($realPath)) {
                    // 実体がディレクトリの場合、ディレクトリをコピー
                    $this->recurse_copy($realPath, $item->getPathname());
                } else {
                    // 実体がファイルの場合、ファイルをコピー
                    copy($realPath, $item->getPathname());
                }
            } else if ($item->isDir() && !$item->isDot()) {
                // ディレクトリなら再帰的に処理
                $this->unlinkSymlinks($item->getPathname());
            }
        }
    }

    
    private function recurse_copy($src, $dst) {
        $dir = opendir($src);
        mkdir($dst);
        while (($file = readdir($dir)) !== false) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

}
