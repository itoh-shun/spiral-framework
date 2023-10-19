<?php

namespace SiValidator2;

class SiValidateResult
{
    private $value;
    private $isValid;
    private $message;
    private $field;

    public function __construct(string $field, $value, bool $isValid, ?string $message = null)
    {
        $this->field = $field;
        $this->value = $value;
        $this->isValid = $isValid;
        $this->message = $message;
    }

    public function value()
    {
        return $this->value;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            'field' => $this->getField(),
            'value' => $this->value(),
            'isValid' => $this->isValid(),
            'message' => $this->message()
        ];
    }
    public function getField(): string
    {
        return $this->field;
    }
}
