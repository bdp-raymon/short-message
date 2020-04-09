<?php

namespace Alish\ShortMessage\Drivers;

use Alish\ShortMessage\Contracts\ShortMessage;
use Alish\ShortMessage\Messages\GhasedakOtp;
use Alish\ShortMessage\SentFail;
use Alish\ShortMessage\SentSuccessful;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Ghasedak implements ShortMessage
{
    protected $config;

    protected $baseUrl = 'https://api.ghasedak.io/v2';

    protected $lineNumber;

    protected $sendDate;

    protected $checkId;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function lineNumber(string $lineNumber): self
    {
        $this->lineNumber = $lineNumber;

        return $this;
    }

    public function sendDate(int $sendDate): self
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    public function checkId(array $checkId): self
    {
        $this->checkId = $checkId;

        return $this;
    }

    public function send(array $recipients, string $message)
    {
        $payload = array_filter([
            'message' => $message,
            'receptor' => implode(',', $recipients),
            'linenumber' => $this->getLineNumber(),
            'senddate' => $this->sendDate,
            'checkid' => $this->getCheckId(),
        ]);

        $response = Http::withHeaders([
            'apikey' => $this->apiKey(),
        ])
            ->asForm()
            ->post($this->endpoint('sms/send/pair'), $payload);

        if ($response->successful() && $response['result']['code'] === 200) {
            return new SentSuccessful($response['items']);
        }

        return new SentFail($response->json());
    }

    public function otp(array $recipients, GhasedakOtp $otp)
    {
        $payload = [
            'receptor' => implode(',', $recipients),
            'type' => $otp->type,
            'template' => $otp->template,
        ];

        foreach ($otp->params as $index => $param) {
            $payload['param'.($index + 1)] = $param;
        }

        if (! is_null($otp->checkId)) {
            $payload['checkid'] = $otp->checkId;
        }

        $response = Http::withHeaders([
            'apikey' => $this->apiKey(),
        ])
            ->asForm()
            ->post($this->endpoint('verification/send/simple'), $payload);

        if ($response->successful() && $response['result']['code'] === 200) {
            return new SentSuccessful($response['items']);
        }

        return new SentFail($response->json());
    }

    protected function apiKey()
    {
        return $this->config['api-key'];
    }

    protected function getCheckId(): ?string
    {
        return is_null($this->checkId) ? null : implode($this->checkId);
    }

    protected function getLineNumber(): ?string
    {
        return $this->lineNumber ?? $this->config['line-number'];
    }

    protected function endpoint($url): string
    {
        return $this->baseUrl.Str::start($url, '/');
    }
}
