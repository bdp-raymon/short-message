<?php


namespace Alish\ShortMessage\Drivers;


use Alish\ShortMessage\Contracts\ShortMessage;
use Alish\ShortMessage\SentFail;
use Alish\ShortMessage\SentSuccessful;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Rahyab implements ShortMessage
{

    protected $config;

    protected $baseUrl = 'http://linepayamak.ir/url/post';

    protected $errors = [
        0 => 'WRONG_USERNAME_PASSWORD',
        2 => 'BALANCE_LIMIT',
        3 => 'LIMIT_SEND_DAILY',
        4 => 'LIMIT_SEND_COUNT',
        5 => 'WRONG_FROM',
        6 => 'WRONG_TO',
        7 => 'EMPTY_TEXT',
        8 => 'DISABLED_USER',
        9 => 'LIMIT_TO',
        100 => 'PRIVILEGE',
    ];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function send(array $recipients, string $message)
    {
        $payload = [
            'username' => $this->username(),
            'password' => $this->password(),
            'from' => $this->from(),
            'to' => implode(',', $recipients),
            'text' => $message,
            'isFlash' => false
        ];

        $response = Http::asForm()
            ->post($this->endpoint('SendSMS.ashx'), $payload);

        if ($response->successful() && $response->body() === 1) {
            return new SentSuccessful($response['items']);
        }

        if (isset($this->errors[$response->body()])) {
            return new SentFail($this->errors[$response->body()]);
        }

        return new SentFail($response->body());
    }

    protected function username(): string
    {
        return $this->config['username'];
    }

    protected function password(): string
    {
        return $this->config['password'];
    }

    protected function from(): string
    {
        return $this->config['from'];
    }

    protected function endpoint(string $path): string
    {
        return $this->baseUrl.Str::start($path, '/');
    }
}
