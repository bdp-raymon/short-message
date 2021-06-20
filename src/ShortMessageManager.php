<?php

namespace Alish\ShortMessage;

use Alish\ShortMessage\Drivers\Ghasedak;
use Alish\ShortMessage\Drivers\LogDriver;
use Alish\ShortMessage\Drivers\MassSmsir;
use Alish\ShortMessage\Drivers\Rahyab;
use Alish\ShortMessage\Drivers\WhiteSmsir;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Log\LogManager;
use Illuminate\Support\Manager;
use Psr\Log\LoggerInterface;

class ShortMessageManager extends Manager
{

    public function getDefaultDriver(): string
    {
        return $this->config()['default'];
    }

    public function createGhasedakDriver()
    {
        return new Ghasedak($this->config()['ghasedak']);
    }

    public function createRahyabDriver()
    {
        return new Rahyab($this->config()['rahyab']);
    }

    public function createLogDriver()
    {
        $logger = $this->container->make(LoggerInterface::class);

        if ($logger instanceof LogManager) {
            $logger = $logger->channel(
                $this->config()['log']['channel']
            );
        }

        return new LogDriver($logger);
    }

    public function createSmsirDriver()
    {
        switch ($this->config()['smsir']['default']) {
            case 'mass':
                return $this->createMassSmsirDriver();
            default:
                return $this->createWhiteSmsirDriver();
        }
    }

    public function createWhiteSmsirDriver()
    {
        return new WhiteSmsir($this->config()['smsir']['white'], $this->container->make(Repository::class));
    }

    public function createMassSmsirDriver()
    {
        return new MassSmsir($this->config()['smsir']['mass'], $this->container->make(Repository::class));
    }

    protected function config()
    {
        return $this->config['short-message'];
    }
}
