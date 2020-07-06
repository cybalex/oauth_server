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
     * @param string|null $allowOrigin
     * @param string|null $allowMethods
     * @param string|null $allowHeaders
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

    /**
     * @return string|null
     */
    public function getAllowHeaders(): ?string
    {
        return $this->allowHeaders;
    }

    /**
     * @return string|null
     */
    public function getAllowMethods(): ?string
    {
        return $this->allowMethods;
    }

    /**
     * @return string|null
     */
    public function getAllowOrigin(): ?string
    {
        return $this->allowOrigin;
    }
}
