<?php

namespace framework\Library;

use DateTime;
use DateTimeZone;
use LogicException;

class SiDateTime extends DateTime
{
    private static $holiday = null;

    public function __construct($date, $timezone = '')
    {
        $zone = null;
        if (!empty($timezone)) {
            $zone = new DateTimeZone($timezone);
        }
        if (self::hasFormat($date, 'Y年m月d日')) {
            $date = parent::createFromFormat('Y年m月d日', $date, $zone);
            $date = $date->format('Y-m-d H:i:s.u');
        } elseif (self::hasFormat($date, 'Y年m月d日 H時i分s秒')) {
            $date = parent::createFromFormat(
                'Y年m月d日 H時i分s秒',
                $date,
                $zone
            );
            $date = $date->format('Y-m-d H:i:s.u');
        }

        parent::__construct($date, $zone);
    }

    public static function setHolidayConfig(
        $file = 'framework/Library/SiDateTime/HolidayConfig.php'
    ) {
        self::$holiday = require $file;
    }

    public function isHoliday()
    {
        if (is_null(self::$holiday)) {
            throw new LogicException('need set HolidayConfig');
        }
        return array_key_exists($this->format('Y/n/j'), self::$holiday);
    }

    public static function hasFormat($date, $format)
    {
        return (bool) self::createFromFormat($format, $date);
    }

    public static function now($timezone = '')
    {
        return new self('now', $timezone);
    }

    public function toDateString()
    {
        return $this->format('Y-m-d');
    }

    public function toDateTimeString()
    {
        return $this->format('Y-m-d H:i:s');
    }

    public function toJapanDateString()
    {
        return $this->format('Y年m月d日');
    }

    public function toJapanDateTimeString()
    {
        return $this->format('Y年m月d日 H時i分s秒');
    }

    public function isLeapYear()
    {
        return $this->format('L') === 1;
    }

    public function isSameDay(SiDateTime $datetime)
    {
        return $this->format('Y-m-d') === $datetime->format('Y-m-d');
    }

    public function isSunday()
    {
        return $this->format('w') === '0';
    }

    public function isMonday()
    {
        return $this->format('w') === '1';
    }

    public function isTuesday()
    {
        return $this->format('w') === '2';
    }

    public function isWednesday()
    {
        return $this->format('w') === '3';
    }

    public function isThursday()
    {
        return $this->format('w') === '4';
    }

    public function isFriday()
    {
        return $this->format('w') === '5';
    }

    public function isSaturday()
    {
        return $this->format('w') === '6';
    }

    public function isBirthday(SiDateTime $birthday)
    {
        return $this->format('m-d') === $birthday->format('m-d');
    }

    public function eq(SiDateTime $latest)
    {
        return $this->getTimestamp() === $latest->getTimestamp();
    }

    public function ne(SiDateTime $latest)
    {
        return $this->getTimestamp() !== $latest->getTimestamp();
    }

    public function gt(SiDateTime $latest)
    {
        return $this->getTimestamp() > $latest->getTimestamp();
    }

    public function gte(SiDateTime $latest)
    {
        return $this->getTimestamp() >= $latest->getTimestamp();
    }

    public function lt(SiDateTime $latest)
    {
        return $this->getTimestamp() < $latest->getTimestamp();
    }

    public function lte(SiDateTime $latest)
    {
        return $this->getTimestamp() <= $latest->getTimestamp();
    }

    public function between(SiDateTime $old, SiDateTime $latest)
    {
        return $old->getTimestamp() <= $this->getTimestamp() &&
            $this->getTimestamp() <= $latest->getTimestamp();
    }

    public function copy()
    {
        return clone $this;
    }

    public function addYears($value)
    {
        return $this->modify((int) $value . ' year');
    }

    public function addYear($value = 1)
    {
        return $this->addYears($value);
    }

    public function addMonths($value)
    {
        return $this->modify((int) $value . ' month');
    }

    public function addMonth($value = 1)
    {
        return $this->addMonths($value);
    }

    public function addDays($value)
    {
        return $this->modify((int) $value . ' day');
    }

    public function addDay($value = 1)
    {
        return $this->addDays($value);
    }

    public function addHours($value)
    {
        return $this->modify((int) $value . ' hour');
    }

    public function addHour($value = 1)
    {
        return $this->addHours($value);
    }

    public function addMinutes($value)
    {
        return $this->modify((int) $value . ' minute');
    }

    public function addMinute($value = 1)
    {
        return $this->addMinutes($value);
    }

    public function addSeconds($value)
    {
        return $this->modify((int) $value . ' second');
    }

    public function addSecond($value = 1)
    {
        return $this->addSeconds($value);
    }

    public function subYears($value)
    {
        return $this->modify('-' . (int) $value . ' year');
    }

    public function subYear($value = 1)
    {
        return $this->subYears($value);
    }

    public function subMonths($value)
    {
        return $this->modify('-' . (int) $value . ' month');
    }

    public function subMonth($value = 1)
    {
        return $this->subMonths($value);
    }

    public function subDays($value)
    {
        return $this->modify('-' . (int) $value . ' day');
    }

    public function subDay($value = 1)
    {
        return $this->subDays($value);
    }

    public function subHours($value)
    {
        return $this->modify('-' . (int) $value . ' hour');
    }

    public function subHour($value = 1)
    {
        return $this->subHours($value);
    }

    public function subMinutes($value)
    {
        return $this->modify('-' . (int) $value . ' minute');
    }

    public function subMinute($value = 1)
    {
        return $this->subMinutes($value);
    }

    public function subSeconds($value)
    {
        return $this->modify('-' . (int) $value . ' second');
    }

    public function subSecond($value = 1)
    {
        return $this->subSeconds($value);
    }

    public function addWeeks($value)
    {
        return $this->modify((int) $value . ' week');
    }

    public function addWeek($value = 1)
    {
        return $this->addWeeks($value);
    }

    public function isWeekend()
    {
        return $this->isSaturday() || $this->isSunday();
    }

    public function isWeekendOrisHoliday()
    {
        return $this->isWeekend() || $this->isHoliday();
    }

    public function isWeekday()
    {
        return !$this->isWeekend();
    }

    public function isWeekdayAndNotHoliday()
    {
        return !$this->isWeekend() && !$this->isHoliday();
    }

    public function addWeekday($value)
    {
        $this->addDays($value);
        while ($this->isWeekend()) {
            $this->addDay();
        }
        return $this;
    }

    public function addWeekdayAndNotHoliday($value)
    {
        $this->addDays($value);
        while ($this->isWeekendOrisHoliday()) {
            $this->addDay();
        }
        return $this;
    }

    public function age()
    {
        $now = new DateTime();
        $interval = $now->diff($this);
        return $interval->y;
    }

    public function weekNumber()
    {
        return 1 +
            date('W', $this->getTimestamp() + 86400) -
            date('W', strtotime(date('Y-m', $this->getTimestamp())) + 86400);
    }

    public function diffFormat($format = '%y歳%mヶ月%d日', $absolute = false)
    {
        if ($this->isFuture()) {
            return null;
        }
        $now = self::now();
        $diff = $this->diff($now, $absolute);
        return $diff->format($format);
    }

    public static function tomorrow()
    {
        return new self('tomorrow');
    }

    public static function today()
    {
        return new self('today');
    }

    public static function yesterday()
    {
        return new self('yesterday');
    }

    public static function parse($date)
    {
        return new self($date);
    }

    public function isToday()
    {
        return self::today()->format('Y-m-d') === $this->format('Y-m-d');
    }

    public function isTomorrow()
    {
        return self::tomorrow()->format('Y-m-d') === $this->format('Y-m-d');
    }

    public function isYesterday()
    {
        return self::yesterday()->format('Y-m-d') === $this->format('Y-m-d');
    }

    public function isFuture()
    {
        return self::now()->lt($this);
    }

    public function isPast()
    {
        return self::now()->gt($this);
    }

    public function diffInSeconds(SiDateTime $date, bool $absolute = false)
    {
        //$diff = $this->diff($date,$absolute );
        //return $diff->format('%s');
        return $date->getTimestamp() - $this->getTimestamp();
    }

    public function diffInMinutes(SiDateTime $date, bool $absolute = false)
    {
        //$diff = $this->diff($date,$absolute );
        //return $diff->format('%i');
        return $this->diffInSeconds($date) / 60;
    }

    public function diffInHours(SiDateTime $date, bool $absolute = false)
    {
        //$diff = $this->diff($date,$absolute );
        //return $diff->format('%h');
        return $this->diffInMinutes($date) / 60;
    }

    public function diffInDays(SiDateTime $date, bool $absolute = false)
    {
        $diff = $this->diff($date, $absolute);
        return $diff->days;
    }

    public function diffInMonths(SiDateTime $date, bool $absolute = false)
    {
        $diff = $this->diff($date, $absolute);
        return $diff->m + $this->diffInYears($date) * 12;
    }

    public function diffInYears(SiDateTime $date, bool $absolute = false)
    {
        $diff = $this->diff($date, $absolute);
        return $diff->y;
    }

    public function diffInWeeks(SiDateTime $date, bool $absolute = false)
    {
        //$diff = $this->format('W') - $date->format('W');
        //if($diff < 0 && $absolute)
        //{
        //    $diff = $diff * -1;
        //}
        //return $diff;
        return (int) ($this->diffInDays($date) / 7);
    }

    public function __toString()
    {
        return $this->toDateTimeString();
    }

    public function __set($name, $value): void
    {
        switch ($name) {
            case 'year':
                $this->setDate($value, $this->month, $this->day);
                break;
            case 'month':
                $this->setDate($this->year, $value, $this->day);
                break;
            case 'day':
                $this->setDate($this->year, $this->month, $value);
                break;
            case 'hour':
                $this->setTime(
                    $value,
                    $this->minute,
                    $this->second,
                    $this->micro
                );
                break;
            case 'minute':
                $this->setTime(
                    $this->hour,
                    $value,
                    $this->second,
                    $this->micro
                );
                break;
            case 'second':
                $this->setTime(
                    $this->hour,
                    $this->minute,
                    $value,
                    $this->micro
                );
                break;
            case 'micro':
                $this->setTime(
                    $this->hour,
                    $this->minute,
                    $this->second,
                    $value
                );
                break;
            case 'timestamp':
                $this->setTimestamp($value);
                break;
            case 'timezone':
                $this->setTimezone($value);
                break;
            default:
                echo 'Error!!! Hint' . PHP_EOL;
                echo 'Use Key' . PHP_EOL;
                $setting = [
                    'year',
                    'month',
                    'day',
                    'hour',
                    'minute',
                    'second',
                    'micro',
                    'dayOfWeek',
                    'dayOfWeekIso',
                    'dayOfYear',
                    'weekNumberInMonth',
                    'daysInMonth',
                    'timestamp',
                    'quarter',
                    'timezone',
                ];
                foreach ($setting as $k => $v) {
                    echo $v . PHP_EOL;
                }
                break;
        }
    }

    public function __get($name)
    {
        $setting = [
            'year' => $this->format('Y'),
            'month' => $this->format('m'),
            'day' => $this->format('d'),
            'hour' => $this->format('H'),
            'minute' => $this->format('i'),
            'second' => $this->format('s'),
            'micro' => $this->format('u'),
            'dayOfWeek' => $this->format('w'),
            'dayOfWeekIso' => $this->format('N'),
            'dayOfYear' => $this->format('z'),
            'weekNumberInMonth' => $this->format('z'),
            'daysInMonth' => $this->format('t'),
            'timestamp' => $this->getTimestamp(),
            'quarter' => ceil($this->format('m') / 3),
            'timezone' => $this->getTimezone(),
            'age' => $this->age(),
        ];

        if (array_key_exists($name, $setting)) {
            return $setting[$name];
        }

        echo 'Error!!! Hint' . PHP_EOL;
        echo 'Use Key' . PHP_EOL;
        foreach ($setting as $k => $v) {
            echo $k . PHP_EOL;
        }
    }
}
