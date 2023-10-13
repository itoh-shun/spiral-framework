# Spiral-framework(SpiarlFrameβ)
# 最終的にこのような構成になります。
~~~
project_name/
    spiral-framework/　・・・サブモジュール
    src/
        <実際のプロジェクトルート>/
    .env.php ・・・デプロイ用トークン
    vendor/
    .gitignore
    .gitmodules
    composer.json
    composer.lock
    README.md
~~~
### gitignore はとりあえずこれを入れればいいと思うよ
~~~
.vscode
.tmp/
.vs/
.env.php
.tmp/
.vscode/
node_modules/
.node_modules/
node_modules
vendor/
~~~

## プロジェクトディレクトリの作成
~~~
mkdir project_name
cd project_name
~~~
## git init 
~~~
git init 
git add README.md
git commit -m "first commit"
git remote add origin <URL>
~~~
## Spiral-framework のサブモジュールをインストール
~~~
git submodule add git@github.com:itoh-shun/spiral-framework.git
~~~
## php install 
~~~
SPIRALに合わせたほうが吉。7.4 がいいと思います。ググれ。
~~~
## Composer Install
~~~
composer -V
~~~
なければインストール。わからんのであればググれ。

## Composer init
~~~
composer init
~~~
## プロジェクトの始め方
~~~
php spiral-framework/spiralframe app:init <プロジェクト名>
~~~
## 静的解析
~~~
composer require phpstan/phpstan --dev
~~~
~~~
vendor/bin/phpstan analyse src
~~~
## デプロイ
~~~
cp spiral-framework/.env.sample.php .env.php
~~~
中身に、プロジェクトの展開先のトークンとシークレットトークンを設定すべし。
~~~
php spiral-framework/spiralframe app:deploy
~~~
もしくは、Gitのバージョニングで変更があったものだけを即時に反映したい場合は、
~~~
php spiral-framework/spiralframe app:deploy --skip
~~~