<?php

namespace Alish\ShortMessage\Messages;

class GhasedakOtp
{
    public string $template;

    public array $params;

    public ?string $checkId;

    public int $type = 1;

    public function __construct(string $template, array $params, string $checkId = null)
    {
        $this->template = $template;
        $this->params = $params;
        $this->checkId = $checkId;
    }

    public static function template(string $template): self
    {
        return new self($template, []);
    }

    public function params(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function type(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function addParam($param): self
    {
        $this->params = array_merge($this->params, (array) $param);

        return $this;
    }

    public function checkId(string $checkId): self
    {
        $this->checkId = $checkId;

        return $this;
    }
}
