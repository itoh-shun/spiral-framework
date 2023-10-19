<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;
use Exception;
use ZipArchive;

class ApplicationDeploy extends Command
{
    private string $serialize = 'app:deploy';

    public function getSerialize()
    {
        return $this->serialize;
    }

    public function execute(CommandArgv $commandArgv)
    {
        //      ApplicationInitalizeInputData();
        $this->line('Welcome Spiral Frame !!!!');
        /*
        $bool = $this->ask("Reflects everything under the src directory. [yes , no] : " , false);
        if($bool !== "yes" && $bool !== 'y')
        {
            $this->line('Cancelled.');
            return null;
        }
        */
        $environments = $this->config();

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

        $response = $this->createZip($environment, $commandArgv);
        $this->deploy(
            $response['filename'],
            $environments['deploy'][$environment],
            $commandArgv
        );
    }

    private function deploy($filename, $config, CommandArgv $commandArgv)
    {
        $MULTIPART_BOUNDARY = 'SPIRAL_API_MULTIPART_BOUNDARY';
        $API_TOKEN = $config['token'];
        $API_SECRET = $config['secret'];

        $locator = 'https://www.pi-pe.co.jp/api/locator';
        $api_headers = [
            'X-SPIRAL-API: locator/apiserver/request',
            'Content-Type: application/json; charset=UTF-8',
        ];
        $parameters = [];
        $parameters['spiral_api_token'] = $API_TOKEN;
        $json = json_encode($parameters);
        $curl = curl_init($locator);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);

        curl_exec($curl);
        if (curl_errno($curl)) {
            echo curl_error($curl);
            exit(1);
        }
        $response = curl_multi_getcontent($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        if ($response['code'] != 0) {
            var_dump($response);
            exit(1);
        }

        $API_URL = $response['location'];

        $filedata = file_get_contents($filename);
        $api_headers = [
            'X-SPIRAL-API: custom_module/upload/request',
            "Content-Type: multipart/form-data; boundary=\"" .
            $MULTIPART_BOUNDARY .
            "\"",
        ];
        $parameters = [];
        $parameters['spiral_api_token'] = $API_TOKEN;
        $parameters['passkey'] = time();
        $parameters['dir'] = '';
        $parameters['compress'] = 't';
        $key = $parameters['spiral_api_token'] . '&' . $parameters['passkey'];
        $parameters['signature'] = hash_hmac('sha1', $key, $API_SECRET, false);

        $postdata = '--' . $MULTIPART_BOUNDARY . "\r\n";
        $postdata .= "Content-Type: application/json; charset=\"UTF-8\";\r\n";
        $postdata .= "Content-Disposition: form-data; name=\"json\"\r\n\r\n";
        $postdata .= json_encode($parameters);
        $postdata .= "\r\n\r\n";

        $postdata .= '--' . $MULTIPART_BOUNDARY . "\r\n";
        $postdata .= "Content-Type: application/x-httpd-php;\r\n";
        $postdata .= "Content-Disposition: form-data; name=\"src\"; filename=\"$filename\"\r\n\r\n";
        $postdata .= $filedata;
        $postdata .= "\r\n\r\n";
        $postdata .= '--' . $MULTIPART_BOUNDARY . "--\r\n";
        $postdata .= "\r\n";
        // curl�饤�֥���Ȥä��������ޤ���
        $curl = curl_init($API_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);

        curl_exec($curl);
        $response = curl_multi_getcontent($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        if ($response['code'] != 0) {
            var_dump($response);
            return false;
        }

        return true;
    }

    private function createZip($environment, CommandArgv $commandArgv)
    {
        $skip = false;
        if (
            !empty($commandArgv->__get('options')) &&
            $commandArgv->__get('options')[0] === '--skip'
        ) {
            $skip = true;
        }

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

        $isGit = 'yes';
        if (!$skip) {
            $isGit = $this->ask(
                'gitコマンドがインストールされている場合、差分更新が可能です。実行しますか？ [yes or no]: ',
                false
            );
        }
        
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
            
            exec("cp -r spiral-framework/src/* .tmp/$environment");
            exec("mv .tmp/$environment/src/* .tmp/$environment");
            rmdir(".tmp/$environment/src");

        } else {
            exec("cp -r spiral-framework/src/* .tmp/$environment");
            exec("cp -r src/* .tmp/$environment");
        }

        $filelist = glob(".tmp/$environment/*");
        foreach ($filelist as $file) {
            if (file_exists("$file/makeAutoload.php")) {
                unlink("$file/makeAutoload.php");
            }
            if (file_exists("$file/.git")) {
                $this->rmdir_recursively("$file/.git");
            }
        }

        exec("cd .tmp/$environment ; zip -r ../$environment.zip * ; cd -");

        return ['filename' => ".tmp/$environment.zip"];
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
