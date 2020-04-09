<?php

namespace Alish\ShortMessage\Contracts;

interface ShortMessage
{
    public function send(array $recipients, string $message);
}
