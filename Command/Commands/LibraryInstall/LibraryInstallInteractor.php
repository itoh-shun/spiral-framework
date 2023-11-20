<?php

namespace Command\Commands\Interactor;

use Command\Basis\Core\View;
use Command\Commands\UseCases\LibraryInstallDefaultFiles;
use Command\Commands\UseCases\LibraryInstallDirectory;
use Exception;

class LibraryInstallInteractor implements LibraryInstallInteractorInputPortInterface {

    public function execute(LibraryInstallInteractorInputData $inputdata)
    {
        if(!file_exists("src/Library")){
            mkdir("src/Library", 0755 , true);
            file_put_contents("src/Library/.gitignore", '.deploy');
        }

        if(file_exists('src/Library/'.$inputdata->name))
        {
            throw new Exception('There is already a directory.',1);
        }

        exec("cd src/Library && git submodule add {$inputdata->url} && cd -");
    }
}

class LibraryInstallInteractorInputData {
    public string $url = '';
    public string $name = '';

    public function __construct(array $inputData)
    {
        $this->url = $inputData['url'];
        // 最後のスラッシュの位置を見つける
        $lastSlashPos = strrpos($this->url, '/');
        // 最後のスラッシュ以降の文字列を取得
        $repositoryWithGit = substr($this->url, $lastSlashPos + 1);
        // ".git" を取り除く
        $this->name = str_replace(".git", "", $repositoryWithGit);
    }
}

interface LibraryInstallInteractorInputPortInterface {

    public function execute(LibraryInstallInteractorInputData $inputdata);
}
