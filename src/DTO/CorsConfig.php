<?php

namespace Cybalex\OauthServer\DTO;

class CorsConfig
{
    /**
     * @var string|null
     */
    private $allowOrigin;

    /**
     * @var string|null
     */
    private $allowMethods;

    /**
     * @var string|null
     */
    private $allowHeaders;

    /**
     * CorsConfig constructor.
     */
    public function __construct(
        ?string $allowOrigin,
        ?string $allowMethods,
        ?string $allowHeaders
    ) {
        $this->allowOrigin = $allowOrigin ?? '*';
        $this->allowMethods = $allowMethods ?? 'GET,POST,PUT,PATCH,OPTIONS';
        $this->allowHeaders = $allowHeaders ?? '*';
    }

    public function getAllowHeaders(): ?string
    {
        return $this->allowHeaders;
    }

    public function getAllowMethods(): ?string
    {
        return $this->allowMethods;
    }

    public function getAllowOrigin(): ?string
    {
        return $this->allowOrigin;
    }
}
