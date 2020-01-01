<?php

namespace Cybalex\OauthServer\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PreTokenGrantAccessEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * PreTokenGrantAccessEvent constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
