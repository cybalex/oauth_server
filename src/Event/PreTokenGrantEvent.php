<?php

namespace Cybalex\OauthServer\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class PreTokenGrantEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * PreTokenGrantAccessEvent constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
