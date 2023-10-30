<?php

namespace Command\Basis;

use Command\Basis\Request\CommandArgv;

abstract class Command
{
    public $options = [];
    protected $optionValues = [];
    public string $serialize;

    abstract function execute(CommandArgv $commandArgv);
    abstract protected function defineOptions();

    public function __construct()
    {
        $this->defineOptions();
    }
    
    public function getSerialize()
    {
        return $this->serialize;
    }


    /**
     * オプションを追加するためのメソッド
     */
    protected function addOption($short, $long, $description, $hasValue = false, $multipleValues = false)
    {
        $this->options[$long] = [
            'short' => $short,
            'long' => $long,
            'description' => $description,
            'hasValue' => $hasValue,
            'multipleValues' => $multipleValues,
            'value' => [],
        ];
    }

    /**
     * オプションの値を取得するためのメソッド
     */
    public function getOptionValue($name)
    {
        return $this->options[$name]['value'] ?? null;
    }

    /**
     * コマンドラインのオプションを解析し、オプションの値をセットするメソッド
     */
    public function parseOptions(CommandArgv $commandArgv)
    {
        for ($i = 0; $i < count($commandArgv->options); $i++) {
            $arg = $commandArgv->options[$i];

            foreach ($this->options as $key => $option) {
                if ($arg === '--' . $option['long'] || $arg === '-' . $option['short']) {
                    if ($option['hasValue']) {
                        if ($option['multipleValues']) {
                            while (isset($commandArgv->options[$i + 1]) && strpos($commandArgv->options[$i + 1], '-') !== 0) {
                                $this->options[$key]['value'][] = $commandArgv->options[++$i];
                            }
                        } else {
                            $this->options[$key]['value'] = $commandArgv->options[++$i];
                        }
                    } else {
                        $this->options[$key]['value'] = true; // 値を持たないオプションの場合、trueを設定
                    }
                    break;
                }
            }
        }
    }


    /**
     * 画面に文字を表示：末尾に改行なし
     *
     * @param string $message
     */
    protected function message(string $message, bool $escape = true)
    {
        // 危険な文字はエスケープする（HTMLエスケープのシェル版みたいなもの）
        echo $escape ? escapeshellcmd($message) : $message;
    }

    /**
     * 画面に文字を表示：末尾に改行あり
     *
     * @param string $message
     */
    protected function line($message, bool $escape = true)
    {
        if (is_array($message)) {
            foreach ($message as $m) {
                $this->line($m, $escape);
            }
        } else {
            echo $escape ? escapeshellcmd($message) . "\n" : $message . "\n";
        }
    }

    /**
     * 画面に文字を表示して、入力を受け付ける
     *
     * @param string $message
     * @return string
     */
    protected function ask(string $message, bool $escape = true)
    {
        // メッセージを表示し、「標準入力」の受け付け待ち。
        // 前後のスペースなど、入力ミスと思われるものを除外（trim）してあげるのは優しさ。
        $this->message($message, $escape);
        return trim(fgets(STDIN));
    }

    protected function config()
    {
        return require '.env.php';
    }

    public function displayHelp() {
        $this->line("=======================================");
        foreach ($this->options as $option) {
            $short = $option['short'] ? '-' . $option['short'] : '';
            $long = '--' . $option['long'];
            $description = $option['description'];
            $this->line("{$short}, {$long}: {$description}");
        }
        $this->line("=======================================");
    }

}
