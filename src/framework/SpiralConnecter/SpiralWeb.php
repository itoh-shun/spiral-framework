<?php

namespace framework\SpiralConnecter {
    class SpiralWeb
    {
        protected string $title = '';
        protected array $fields = [];

        private static string $token = '';
        private static string $secret = '';

        private static ?SpiralConnecterInterface $connecter = null;

        public static function setConnecter(SpiralConnecterInterface $connecter)
        {
            self::$connecter = $connecter;
        }

        public static function setToken(string $token, string $secret)
        {
            self::$token = $token;
            self::$secret = $secret;
        }

        public static function area($my_area_title)
        {
            return (new SpiralWebManager(self::$connecter))->setMyAreaTitle($my_area_title);
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
}