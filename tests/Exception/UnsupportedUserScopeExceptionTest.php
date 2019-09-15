<?php

namespace Cybalex\OauthServer\Tests\Exception;

use Cybalex\OauthServer\Exception\UnsupportedUserScopeException;
use PHPUnit\Framework\TestCase;

class UnsupportedUserScopeExceptionTest extends TestCase
{
    public function testAddUnsupportedScope()
    {
        $unsupportedUserScopeException = new UnsupportedUserScopeException();
        $this->assertSame($unsupportedUserScopeException, $unsupportedUserScopeException->addUnsupportedScope('admin'));
        $this->assertSame($unsupportedUserScopeException, $unsupportedUserScopeException->addUnsupportedScope('edit'));

        $this->expectExceptionMessage('The user scopes admin, edit are not supported');

        $this->assertEquals(['admin', 'edit'], $unsupportedUserScopeException->getUnsupportedScopes());

        throw $unsupportedUserScopeException;
    }
}
