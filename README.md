StartUp

# php install 

SPIRALに合わせたほうが吉。7.4 がいいと思います。

# Composer Install

composer -V

なければインストール。わからんのであればググれ。

# .env.php 

cp -f .env.sample.php .env.php

中身に、プロジェクトの展開先のトークンとシークレットトークンを設定すべし。

# プロジェクトの始め方

php spiralframe app:init <プロジェクト名>

# 静的解析

vendor/bin/phpstan analyse src