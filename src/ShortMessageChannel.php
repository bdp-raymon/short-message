<?php


namespace Alish\ShortMessage;

use Alish\ShortMessage\Exception\CouldNotFindMobile;
use Alish\ShortMessage\Facade\ShortMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ShortMessageChannel
{

    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, $method = $this->getHandlerMethod())) {
            return $this->$method($notifiable, $notification);
        }

        return ShortMessage::send((array) $this->mobile($notifiable), $notification->toShortMessage($notifiable));
    }

    protected function getHandlerMethod(): string
    {
        return 'to'.Str::studly(ShortMessage::getDefaultDriver());
    }

    protected function mobile($notifiable): string
    {
        $mobile = $notifiable->mobile ? $notifiable->mobile : $notifiable->routeNotificationForShortMessage();

        if (is_null($mobile)) {
            throw new CouldNotFindMobile($notifiable);
        }

        return $mobile;
    }

    protected function toGhasedak($notifiable, Notification $notification)
    {
        $otp = $notification->toGhasedak($notifiable);

        return ShortMessage::driver('ghasedak')->otp((array) $this->mobile($notifiable), $otp);
    }

    protected function toSmsir($notifiable, Notification $notification)
    {
        $ultraFast = $notification->toSmsir($notifiable);

        return ShortMessage::driver('mass-smsir')->ultraFastSend($this->mobile($notifiable), $ultraFast);
    }

}
