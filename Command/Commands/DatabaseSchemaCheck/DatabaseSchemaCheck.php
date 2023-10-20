<?php

namespace Command\Commands;

use Command\Basis\Command;
use Command\Basis\Request\CommandArgv;
use Exception;
use ZipArchive;

class DatabaseSchemaCheck extends Command
{
    public string $serialize = 'database:schema';
    
    protected function defineOptions()
    {
        $this->addOption('n', 'name', 'Specify the database name' , true);
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
        $db_title = $this->getOptionValue('name');
        if (!$db_title) {
            $db_title = $this->ask("Please specify database name: ");
        }

        $this->schemacheck(
            $environments['deploy'][$environment],
            $db_title
        );
    }

    private function schemacheck($config, string $db_title)
    {
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

        $api_headers = [
            "X-SPIRAL-API: database/get/request",
            "Content-Type: application/json; charset=UTF-8",
        ];
        $parameters = [];
        $parameters['spiral_api_token'] = $API_TOKEN;
        $parameters['passkey'] = time();
        $key = $parameters['spiral_api_token'] . '&' . $parameters['passkey'];
        $parameters['signature'] = hash_hmac('sha1', $key, $API_SECRET, false);
        $parameters['db_title'] = $db_title;
        $json = json_encode($parameters);
        $curl = curl_init($API_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);

        curl_exec($curl);
        $response = curl_multi_getcontent($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        if ($response['code'] != 0) {
            return false;
        }
        foreach($response['schema']['fieldList'] as $field)
        {
            echo "'".$field['title']."'," . PHP_EOL;
        }
        return true;
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
