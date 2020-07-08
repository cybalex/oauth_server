<?php

namespace Cybalex\OauthServer\Tests\DTO;

use Cybalex\OauthServer\DTO\CorsConfig;
use Cybalex\TestHelpers\DtoTestHelperTrait;
use PHPUnit\Framework\TestCase;

class CorsConfigTest extends TestCase
{
    use DtoTestHelperTrait;

    protected function setUp(): void
    {
        $this->DTOSetUp();
    }

    public function getConstructorConfig(): array
    {
          return [
               ['allowOrigin', '*'],
               ['allowMethods', 'GET,POST'],
               ['allowHeaders', 'Content-Type'],
          ];
    }

    protected function getEntityClass(): string
    {
        return CorsConfig::class;
    }
}
