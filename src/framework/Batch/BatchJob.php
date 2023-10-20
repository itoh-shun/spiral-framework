<?php

namespace framework\Batch;

use DateTime;

abstract class BatchJob implements BatchJobInterface
{
    protected bool $run = false;

    public function __construct()
    {
    }

    protected static function now()
    {
        return new DateTime();
    }

    public function shouldRun()
    {
        return $this->run;
    }

    public function everyMinute()
    {
        $this->run = true;
        return $this;
    }

    public function everyFifteenMinutes()
    {
        $now = self::now();
        $this->run = $now->format('i') % 15 === 0;
        return $this;
    }

    public function hourly()
    {
        $now = self::now();
        $this->run = $now->format('i') === '00';
        return $this;
    }

    public function everyTwoHours()
    {
        $now = self::now();
        $this->run = $now->format('i') === '00' && $now->format('G') % 2 === 0;
        return $this;
    }

    public function everyThreeHours()
    {
        $now = self::now();
        $this->run = $now->format('i') === '00' && $now->format('G') % 3 === 0;
        return $this;
    }

    public function everyFourHours()
    {
        $now = self::now();
        $this->run = $now->format('i') === '00' && $now->format('G') % 4 === 0;
        return $this;
    }

    public function everySixHours()
    {
        $now = self::now();
        $this->run = $now->format('i') === '00' && $now->format('G') % 6 === 0;
        return $this;
    }

    public function daily()
    {
        $now = self::now();
        $this->run = $now->format('H:i') === '00:00';
        return $this;
    }

    public function dailyAt($time)
    {
        $now = self::now();
        $this->run = $now->format('H:i') === $time;
        return $this;
    }

    public function twiceDaily($firstTime, $secondTime)
    {
        $now = self::now();
        $this->run =
            $now->format('H:i') === $firstTime ||
            $now->format('H:i') === $secondTime;
        return $this;
    }

    public function weekly()
    {
        $now = self::now();
        $this->run =
            $now->format('w') === '0' && $now->format('H:i') === '00:00';
        return $this;
    }

    // ... 他のスケジュールに関するメソッドもここに追加してください。
}

interface BatchJobInterface
{
    public function handle();
}
