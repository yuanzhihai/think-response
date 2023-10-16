<?php

namespace yuanzhihai\response\think;


use yuanzhihai\response\think\contracts\Format;
use yuanzhihai\response\think\support\trait\JsonResponseTrait;

class Response
{
    use JsonResponseTrait;

    protected $formatter;

    public function __construct(Format $format)
    {
        $this->formatter = $format;
    }
}