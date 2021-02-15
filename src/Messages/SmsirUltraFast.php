<?php

namespace Alish\ShortMessage\Messages;

use Illuminate\Support\Collection;

class SmsirUltraFast
{
    public string $template;

    public array $parameters;

    public function __construct(string $template, array $parameters)
    {
        $this->template = $template;
        $this->parameters = $parameters;
    }

    public static function template(string $template): self
    {
        return new self($template, []);
    }

    public function parameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function addParameter(string $key, string $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function parameterArray(): array
    {
        return (new Collection($this->parameters))->map(function ($value, $key) {
            return [
                'Parameter' => $key,
                'ParameterValue' => $value,
            ];
        })->toArray();
    }
}
