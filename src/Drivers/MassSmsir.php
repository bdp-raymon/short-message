<?php


namespace Alish\ShortMessage\Drivers;


use Alish\ShortMessage\Contracts\ShortMessage;
use Alish\ShortMessage\Messages\SmsirUltraFast;
use Alish\ShortMessage\SentFail;
use Alish\ShortMessage\SentSuccessful;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MassSmsir implements ShortMessage
{

    protected $config;

    protected $cache;

    protected $tokenExpirationTime = 30;

    protected $baseUrl = 'https://RestfulSms.com/api';

    public function __construct(array $config, Repository $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    public function send(array $recipients, string $message)
    {
        $body = [
            'Message' => $message,
            'MobileNumbers' => $recipients,
            "LineNumber" => $this->lineNumber(),
            'CanContinueInCaseOfError' => true
        ];

        $response = Http::withHeaders([
            'x-sms-ir-secure-token' => $this->token()
        ])
            ->post($this->endpoint('MessageSend'), $body);

        if ($response->successful() && is_array($response->json())) {
            return new SentSuccessful($response->json());
        }

        return new SentFail($response->json());
    }

    protected function lineNumber()
    {
        return $this->config['line-number'];
    }

    protected function token(): string
    {
        return $this->cache->remember('mass-smsir-token', $this->tokenExpirationTime, function () {
            return $this->freshToken();
        });

    }

    protected function freshToken(): string
    {
        $body = [
            'UserApiKey' => $this->apiKey(),
            'SecretKey' => $this->secretKey()
        ];

        $response = Http::post($this->endpoint('Token'), $body);

        if ($response['IsSuccessful']) {
            return $response['TokenKey'];
        }

    }

    protected function apiKey(): string
    {
        return $this->config['api-key'];
    }

    protected function secretKey(): string
    {
        return $this->config['secret-key'];
    }

    protected function endpoint($url)
    {
        return $this->baseUrl.Str::start($url, '/');
    }

    public function ultraFastSend(string $mobile, SmsirUltraFast $ultraFast): Response
    {
        $body = [
            'ParameterArray' => $ultraFast->parameterArray(),
            'Mobile' => $mobile,
            'TemplateId' => $ultraFast->template
        ];

        $response = Http::withHeaders([
            'x-sms-ir-secure-token' => $this->token()
        ])
            ->post($this->endpoint('UltraFastSend'), $body);

        return $response;
    }
}
