<?php

namespace Alish\ShortMessage\Exception;

class CouldNotFindMobile extends \Exception
{
    public $notifiable;

    public function __construct($notifiable)
    {
        parent::__construct('could not find mobile for notifiable, notifiable should have mobile property or just define method routeForShortMessage for notifiable');

        $this->notifiable = $notifiable;
    }
}
