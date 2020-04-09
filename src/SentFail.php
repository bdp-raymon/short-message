<?php

namespace Alish\ShortMessage;

class SentFail
{
    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
