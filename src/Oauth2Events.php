<?php

namespace Cybalex\OauthServer;

final class Oauth2Events
{
    /**
     * Event is fired before oauth grant of access token.
     *
     * @Event("Cybalex\OauthServer\Event\PreTokenGrantAccessEvent")
     */
    const PRE_TOKEN_GRANT_ACCESS = 'oauth_server.pre_token_grant_access';
}
