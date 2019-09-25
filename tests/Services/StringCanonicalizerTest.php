<?php

namespace Cybalex\OauthServer\Tests\Services;

use Cybalex\OauthServer\Services\StringCanonicalizer;
use PHPUnit\Framework\TestCase;

class StringCanonicalizerTest extends TestCase
{
    public function testCanonizeNull()
    {
        $this->assertNull((new StringCanonicalizer())->canonicalize(null));
    }

    public function testCanonizeString()
    {
        $this->assertEquals('uppercase', (new StringCanonicalizer())->canonicalize('UPPERCASE'));
        $this->assertEquals('lowercase', (new StringCanonicalizer())->canonicalize('lowercase'));
    }
}
