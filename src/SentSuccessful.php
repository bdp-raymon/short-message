<?php


namespace Alish\ShortMessage;


class SentSuccessful
{

    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

}
