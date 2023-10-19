<?php

namespace framework\Library;

use DateTime;
use Exception;
use stdClass;

class SiValidator
{
    private static string $language = 'ja';
    private static array $defineRules = [];
    private static array $errorMessages = [];
    private static array $values = [];
    private static array $labels = [];
    private array $result = [];

    public function __construct($result = [])
    {
        $this->result = array_map(function (SiValidateResult $r) {
            return $r;
        }, $result);
    }

    public function isError()
    {
        foreach ($this->result as $r) {
            if (!$r->isValid()) {
                return true;
            }
        }

        return false;
    }

    public function getResults()
    {
        return array_map(function ($r) {
            return $r->toArray();
        }, $this->result);
    }

    public static function make($values, $rules, $labels = [], $messages = [])
    {
        $result = [];

        self::errorMessages($messages);

        self::$values = $values;
        self::$labels = $labels;

        foreach ($values as $key => $value) {
            $label = $labels[$key] ?? $key;
            $result[$key] = self::validate($value, $label, $rules[$key]);
        }

        return new SiValidator($result);
    }

    public static function language($lang)
    {
        self::$language = $lang;
    }

    private static function of($value, array $rules)
    {
        $ruleName = self::isValid($value, $rules);
        return $ruleName === '';
    }

    private static function isValid($value, $rule)
    {
        if (self::processable($rule)) {
            if (!self::exec($value, $rule)) {
                return false;
            }
        }
        return true;
    }

    public static function validate(
        $value,
        $label,
        array $rules
    ): SiValidateResult {
        $result = new SiValidateResult(true, '', $value);

        foreach ($rules as $rule) {
            if ($rule instanceof SiRule) {
                self::errorMessages($rule->message());
                $result = $rule->processable($value);
                $message = !$result
                    ? $rule->message()[self::$language][$rule->name()]
                    : '';
                $message = self::messageReplace($message, $label);
                $result = new SiValidateResult($result, $message, $value);
            } elseif (!is_string($rule) && is_callable($rule)) {
                $message = $rule($value, $label);
                $result = new SiValidateResult(
                    $message == '',
                    $message,
                    $value
                );
            } else {
                //return ($rule !== '')? self::errorMessage( $rule , $field) : "";
                $isValid = self::isValid($value, $rule);
                $message = !$isValid ? self::errorMessage($rule, $label) : '';
                $result = new SiValidateResult($isValid, $message, $value);
            }

            if (!$result->isValid()) {
                return $result;
            }
        }
        return $result;
    }

    private static function exec($value, $validateRule)
    {
        return self::$defineRules[self::getRuleName($validateRule)](
            $value,
            self::param($validateRule),
            self::$values
        );
    }

    private static function getRuleName(string $validateRule)
    {
        foreach (self::$defineRules as $ruleName => $defineRule) {
            $rule = $ruleName;
            if (self::startsWith($rule, ':')) {
                $rule = explode(':', $rule, 2)[0];
                $validateRule = explode(':', $validateRule, 2)[0];
            }
            if ($rule === $validateRule) {
                return $ruleName;
            }
        }
        return null;
    }

    private static function processable(string $validateRule)
    {
        $ruleName = self::getRuleName($validateRule);
        if ($ruleName !== null) {
            return true;
        }
        return false;
    }

    private static function param(string $validateRule)
    {
        $ruleName = self::getRuleName($validateRule);
        if ($ruleName !== null) {
            $rules = explode(':', $ruleName, 2);
            $validateRule = explode(':', $validateRule, 2);

            if (isset($rules[1]) && isset($validateRule[1])) {
                $paramKeys = explode(',', $rules[1]);
                $params = explode(',', $validateRule[1]);
                if (count($paramKeys) !== count($params)) {
                    throw new Exception(
                        'The number of parameters does not match'
                    );
                }
                return array_combine($paramKeys, $params);
            }
        }
        return [];
    }

    public static function defineRule($ruleName, callable $func)
    {
        self::$defineRules[$ruleName] = $func;
    }

    public static function errorMessages(array $errorMessages)
    {
        self::$errorMessages = array_replace_recursive(
            self::$errorMessages,
            $errorMessages
        );
    }

    public static function startsWith($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }

    private static function errorMessage($validateRule, $field)
    {
        $ruleName = self::getRuleName($validateRule);
        $param = self::param($validateRule);
        $message = self::getErrorMessage($ruleName);
        return self::messageReplace($message, $field, $param);
    }

    private static function getErrorMessage($ruleName)
    {
        return self::$errorMessages[self::$language][$ruleName];
    }

    private static function messageReplace($message, $field, $param = [])
    {
        $message = str_replace('{field}', $field, $message);

        if (isset($param['other']) && isset(self::$labels[$param['other']])) {
            $message = str_replace(
                '{other}',
                self::$labels[$param['other']],
                $message
            );
        }

        foreach ($param as $key => $v) {
            $message = str_replace("{{$key}}", $v, $message);
        }

        return $message;
    }

    public static function help()
    {
        $help = [];
        foreach (self::$defineRules as $ruleName => $func) {
            $errorMessage = [];

            foreach (self::$errorMessages as $lang => $message) {
                $errorMessage[$lang] = $message[$ruleName];
            }

            $help[] = [
                'rule_name' => $ruleName,
                'errorMessage' => $errorMessage,
            ];
        }

        print_r($help);
    }

    public function __get($name)
    {
        return $this->result[$name];
    }
}

class SiValidateResult
{
    private bool $result = true;
    private ?string $message = '';
    private ?string $value = '';

    public function __construct(
        bool $result,
        ?string $message = '',
        ?string $value = ''
    ) {
        $this->result = $result;
        $this->message = $message;
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function message()
    {
        return $this->message;
    }

    public function isValid()
    {
        return $this->result;
    }

    public function toArray()
    {
        return [
            'value' => $this->value,
            'result' => $this->result,
            'message' => $this->message,
        ];
    }
}
