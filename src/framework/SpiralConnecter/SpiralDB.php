<?php

namespace framework\SpiralConnecter {
    use App\Lib\ApiSpiral;

    class SpiralDB
    {
        protected string $title = '';
        protected array $fields = [];

        private static string $token = '';
        private static string $secret = '';

        private static ?SpiralConnecterInterface $connecter = null;

        public static function setConnecter(SpiralConnecterInterface $connecter)
        {
            if(RateLimiter::getTotalRequestsInLastMinute())
            self::$connecter = $connecter;
        }

        public static function setToken(string $token, string $secret)
        {
            self::$token = $token;
            self::$secret = $secret;
        }

        public static function filter($title)
        {
            return (new SpiralFilterManager(self::$connecter))->setTitle(
                $title
            );
        }

        public static function mail($title)
        {
            return (new SpiralExpressManager(self::$connecter))->setTitle(
                $title
            );
        }

        public static function title($title)
        {
            return (new SpiralManager(self::$connecter))->setTitle($title);
        }

        public static function getConnection()
        {
            if (class_exists('Spiral') && ( self::$token == '' && self::$secret == '')) {
                global $SPIRAL;
                return new SpiralConnecter($SPIRAL);
            }

            return new SpiralApiConnecter(self::$token, self::$secret);
        }
    }

    abstract class SpiralModel
    {

        public $title;

        public $fields;
        public function init()
        {
            return (new SpiralManager(SpiralDB::getConnection()))
                ->setTitle($this->title)
                ->fields($this->fields);
        }
    }
}
