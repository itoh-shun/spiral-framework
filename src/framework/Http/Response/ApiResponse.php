<?php

// use App\Lib\LoggingSpiralv2;

class ApiResponse
{
    public $data = null;
    public $count = 0;
    public $code = 0;
    public $message = null;
    public $header = [];
    public $result = false;

    public static ?Logger $logger = null;

    public function __construct(
        $data = null,
        $count = 0,
        $code = 0,
        $message = null,
        $header = []
    ) {
        $this->data = $data;
        $this->count = $count;
        $this->code = $code;
        $this->message = $message;
        $this->header = $header;
    }

    public function toJson(): string
    {
        return json_encode(
            [
                'data' => $this->data,
                'count' => $this->count,
                'code' => $this->code,
                'message' => $this->message,
                'header' => $this->header,
            ],
            JSON_UNESCAPED_SLASHES
        );
    }
}
