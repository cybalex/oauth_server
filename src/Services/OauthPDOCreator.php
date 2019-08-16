<?php

namespace App\Services;

use OAuth2\Storage\Pdo;

class OauthPDOCreator
{
    /** @var Pdo */
    private $pdoStorage;

    public function __construct(string $dbName, string $dbHost, string $dbUsername, string $dbUserPassword)
    {
        $dsn = sprintf("mysql:dbname=%s;host=%s", $dbName, $dbHost);
        $this->pdoStorage = new Pdo(['dsn' => $dsn, 'username' => $dbUsername, 'password' => $dbUserPassword]);
    }

    /**
     * @return Pdo
     */
    public function get(): Pdo
    {
        return $this->pdoStorage;
    }
}
