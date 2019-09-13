<?php

namespace Cybalex\OauthServer\Exception;

use InvalidArgumentException;

class UnsupportedUserScopeException extends InvalidArgumentException
{
    /**
     * @var array
     */
    private $unsupportedScopes = [];

    /**
     * @param string $unsupportedScoped
     *
     * @return UnsupportedUserScopeException
     */
    public function addUnsupportedScope(string $unsupportedScoped): UnsupportedUserScopeException
    {
        array_push($this->unsupportedScopes, $unsupportedScoped);

        $this->message = sprintf('The user scopes %s are not supported', implode(', ', $this->unsupportedScopes));

        return $this;
    }

    /**
     * @return array
     */
    public function getUnsupportedScopes(): array
    {
        return $this->unsupportedScopes;
    }
}
