<?php

/**
 * User Directory
 *   Copyright (c) 2008, 2011, 2019 Theodore R. Smith <theodore@phpexperts.pro>
 *   GPG Fingerprint: 4BF8 2613 1C34 87AC D28F  2AD8 EB24 A91D D612 5690
 *
 *   https://www.phpexperts.pro/
 *   https://gitlab.com/phpexperts/user_directory
 *
 * The following code is licensed under a modified BSD License.
 * All of the terms and conditions of the BSD License apply with one
 * exception:
 *
 * 1. Every one who has not been a registered student of the "PHPExperts
 *    From Beginner To Pro" course (http://www.phpexperts.pro/) is forbidden
 *    from modifing this code or using in an another project, either as a
 *    deritvative work or stand-alone.
 *
 * BSD License: http://www.opensource.org/licenses/bsd-license.php
 */

namespace Tests\PHPExperts\MyDB;

use PDOStatement;
use PHPExperts\MyDB\MyDB;
use PHPExperts\MyDB\MyPDO;
use PHPUnit\Framework\TestCase;
use stdClass;

class MyPDOTest extends TestCase
{
    private static $userInfo;

    /** @var MyPDO */
    protected $MyPDO;

    protected function setUp(): void
    {
        parent::setUp();

        $config      = MyDatabaseTestSuite::getRealPDOConfig();
        $this->MyPDO = MyDB::loadDB($config);

        if (!self::$userInfo) {
            self::$userInfo = [
                'name' => uniqid(),
                'pass' => uniqid(),
            ];
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->MyPDO = null;
    }

    public function testCanInsertData()
    {
        self::$userInfo = [
            'name' => uniqid(),
            'pass' => uniqid(),
        ];

        $qs = 'INSERT INTO Users (username, password) VALUES (?, ?)';
        self::assertInstanceOf(PDOStatement::class, $this->MyPDO->query($qs, array(self::$userInfo['name'], self::$userInfo['pass'])));
    }

    public function testCanReadData()
    {
        $user = self::$userInfo;
        $qs   = 'SELECT * FROM Users WHERE username=?';
        $stmt = $this->MyPDO->query($qs, array($user['name']));
        self::assertInstanceOf(PDOStatement::class, $stmt);

        $userInfo = $stmt->fetchObject();
        self::assertInstanceOf(stdClass::class, $userInfo);
        self::assertObjectHasAttribute('username', $userInfo);
        self::assertEquals($user['name'], $userInfo->username);
    }

    public function testCanUpdateData()
    {
        $user             = self::$userInfo;
        $user['pass']     = uniqid();
        $GLOBALS['users'] = $user['pass'];

        $qs = 'UPDATE Users SET password=? WHERE username=?';
        self::assertInstanceOf(PDOStatement::class, $this->MyPDO->query($qs, array($user['name'], $user['pass'])));

        // Verify
        $this->testCanReadData();
    }

    public function testCanFetchAnArray()
    {
        $user = self::$userInfo;

        $qs = 'SELECT * FROM Users WHERE username=?';
        $this->MyPDO->query($qs, array($user['name']));

        $userInfo = $this->MyPDO->fetchArray();
        self::assertIsArray($userInfo);
        self::assertEquals($user['name'], $userInfo['username']);
    }

    public function testCanFetchAnObject()
    {
        $user = self::$userInfo;

        $qs = 'SELECT * FROM Users WHERE username=?';
        $this->MyPDO->query($qs, array($user['name']));

        $userInfo = $this->MyPDO->fetchObject();
        self::assertInstanceOf(stdClass::class, $userInfo);
        self::assertEquals($user['name'], $userInfo->username);
    }
}
