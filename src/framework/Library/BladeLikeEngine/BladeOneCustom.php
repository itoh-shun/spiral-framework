<?php

namespace framework\Library\BladeLikeEngine;

use Exception;

class BladeOneCustom extends BladeOne
{
    protected $isCompiled = false;
    protected $isRunFast = true; // stored for historical purpose.
    protected $compileExtension = '';
    //protected $fileExtension = '.php';

    public function __construct(
        $templatePath = null,
        $compiledPath = null,
        $mode = 0
    ) {
        if ($templatePath === null) {
            $templatePath = '/views';
        }
        $this->templatePath = is_array($templatePath)
            ? $templatePath
            : [$templatePath];
        $this->setMode($mode);
        $this->authCallBack = function (
            $action = null,
            /** @noinspection PhpUnusedParameterInspection */
            $subject = null
        ) {
            return \in_array($action, $this->currentPermission, true);
        };

        $this->authAnyCallBack = function ($array = []) {
            foreach ($array as $permission) {
                if (
                    \in_array($permission, $this->currentPermission ?? [], true)
                ) {
                    return true;
                }
            }
            return false;
        };

        $this->errorCallBack = static function (
            /** @noinspection PhpUnusedParameterInspection */
            $key = null
        ) {
            return false;
        };

        // If the traits has "Constructors", then we call them.
        // Requisites.
        // 1- the method must be public or protected
        // 2- it must don't have arguments
        // 3- It must have the name of the trait. i.e. trait=MyTrait, method=MyTrait()
        $traits = get_declared_traits();
        foreach ($traits as $trait) {
            $r = explode('\\', $trait);
            $name = end($r);
            if (is_callable([$this, $name]) && method_exists($this, $name)) {
                $this->{$name}();
            }
        }
    }

    /**
     * It sets the template and compile path (without trailing slash).
     * <p>Example:setPath("somefolder","otherfolder");
     *
     * @param null|string|string[] $templatePath If null then it uses the current path /views folder
     * @param null|string          $compiledPath If null then it uses the current path /views folder
     */
    public function setPath($templatePath, $compiledPath): void
    {
        if ($templatePath === null) {
            $templatePath = \getcwd() . '/views';
        }
        $this->templatePath = is_array($templatePath)
            ? $templatePath
            : [$templatePath];
    }

    public function getFile($fullFileName): string
    {
        if (empty($fullFileName)) {
            return '';
        }
        ob_start(); //バッファ制御スタート

        if (preg_match('/\.(php)$/i', $fullFileName) === 1) {
            require $fullFileName;
        } else {
            require $fullFileName . '.blade.php';
        }
        return ob_get_clean(); //バッファ制御終了＆変数を取得
    }

    protected function evaluatePath($compiledFile, $variables): string
    {
        \ob_start();
        // note, the variables are extracted locally inside this method,
        // they are not global variables :-3
        \extract($variables);
        // We'll evaluate the contents of the view inside a try/catch block, so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            include $compiledFile;
        } catch (Exception $e) {
            $this->handleViewException($e);
        }
        return \ltrim(\ob_get_clean());
    }

    /**
     * Compile the view at the given path.
     *
     * @param string $templateName The name of the template. Example folder.template
     * @param bool   $forced       If the compilation will be forced (always compile) or not.
     * @return boolean|string True if the operation was correct, or false (if not exception)
     *                             if it fails. It returns a string (the content compiled) if isCompiled=false
     * @throws Exception
     */
    public function compile($templateName = null, $forced = false)
    {
        $template = $this->getTemplateFile($templateName);
        $contents = $this->compileString($this->getFile($template));
        $this->compileCallBacks($contents, $templateName);
        return $contents;
    }

    /**
     * run the blade engine. It returns the result of the code.
     *
     * @param string $view
     * @param array  $variables
     * @param bool   $forced  if true then it recompiles no matter if the compiled file exists or not.
     * @param bool   $isParent
     * @param bool   $runFast if true then the code is not compiled neither checked, and it runs directly the compiled
     *                        version.
     * @return string
     * @throws Exception
     * @noinspection PhpUnusedParameterInspection
     */
    protected function runInternal(
        $view,
        $variables = [],
        $forced = false,
        $isParent = true,
        $runFast = false
    ): string {
        $this->currentView = $view;
        if (@\count($this->composerStack)) {
            $this->evalComposer($view);
        }
        if (@\count($this->variablesGlobal) > 0) {
            $this->variables = \array_merge($variables, $this->variablesGlobal);
            //$this->variablesGlobal = []; // used so we delete it.
        } else {
            $this->variables = $variables;
        }
        // a) if the "compile" is forced then we compile the original file, then save the file.
        // b) if the "compile" is not forced then we read the datetime of both file, and we compared.
        // c) in both cases, if the compiled doesn't exist then we compile.
        if ($view) {
            $this->fileName = $view;
        }
        $result = $this->compile($view, $forced);
        if (!$this->isCompiled) {
            return $this->evaluateText($result, $this->variables);
        }

        return '';
    }
}
