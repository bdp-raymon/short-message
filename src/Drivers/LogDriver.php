<?php

namespace Alish\ShortMessage\Drivers;

use Alish\ShortMessage\Contracts\ShortMessage;
use Psr\Log\LoggerInterface;

class LogDriver implements ShortMessage
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function send(array $recipients, string $message)
    {
        $this->logger->debug($this->toString($recipients, $message));
    }

    public function toString(array $recipients, string $message)
    {
        $rec = implode($recipients);

        return $message."\n"."[Sent: $rec]";
    }
}
