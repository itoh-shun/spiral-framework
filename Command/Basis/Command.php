<?php

namespace Command\Basis;

use Command\Basis\Request\CommandArgv;

abstract class Command
{
    abstract function getSerialize();
    abstract function execute(CommandArgv $commandArgv);

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
}
