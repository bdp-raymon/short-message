<?php


namespace Alish\ShortMessage\Drivers;


use Alish\ShortMessage\Contracts\ShortMessage;
use Alish\ShortMessage\SentFail;
use Alish\ShortMessage\SentSuccessful;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WhiteSmsir implements ShortMessage
{

    protected $config;

    protected $cache;

    protected $tokenExpirationTime = 30;

    protected $baseUrl = 'https://api.sms.ir/users/v1';

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
            'CanContinueInCaseOfError' => true
        ];

        $response = Http::withHeaders([
            'x-sms-ir-secure-token' => $this->token()
        ])
            ->post($this->endpoint('Message/SendByMobileNumbers'), $body);

        if ($response->successful() && $response['IsSuccessful']) {
            return new SentSuccessful($response->json());
        }

        return new SentFail($response->json());
    }

    public function addContact(string $mobile, string $groupId = null): Response
    {
        $body = [
            "ContactsDetails" => [[
                "Mobile" => $mobile
            ]],
            "GroupId" => $groupId ?? $this->contactsGroupId()
        ];

        $response = Http::withHeaders([
            'x-sms-ir-secure-token' => $this->token()
        ])->post($this->endpoint('Contacts/AddContacts'), $body);

        return $response;
    }


    protected function token(): string
    {
        return $this->cache->remember('white-smsir-token', $this->tokenExpirationTime, function () {
            return $this->freshToken();
        });

    }

    protected function freshToken(): string
    {
        $body = [
            'UserApiKey' => $this->apiKey(),
            'SecretKey' => $this->secretKey()
        ];

        $response = Http::post($this->endpoint('Token/GetToken'), $body);

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

    protected function contactsGroupId(): string
    {
        return $this->config['contacts_group_id'];
    }

    protected function endpoint($url)
    {
        return $this->baseUrl.Str::start($url, '/');
    }


}
