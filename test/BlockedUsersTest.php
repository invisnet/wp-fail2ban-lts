<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\authenticate;


class BlockedUsersTest extends TestCase
{
    public function testEmptyUsername()
    {
        $rv = authenticate('user', false, 'password');
        $this->assertEquals($rv, 'user');
    }

    public function testRegexBlocked()
    {
        define('WP_FAIL2BAN_BLOCKED_USERS', '^blocked$');

        $this->expectOutputRegex('/\d+|Blocked authentication attempt for blocked from 255.255.255.255/');
        authenticate('user', 'blocked', 'password');
    }

    public function testRegexAllowed()
    {
        define('WP_FAIL2BAN_BLOCKED_USERS', '^blocked$');

        $rv = authenticate('user', 'notblocked', 'password');
        $this->assertEquals($rv, 'user');
    }

    public function testArrayBlocked()
    {
        define('WP_FAIL2BAN_BLOCKED_USERS', ['a','b','c']);

        $this->expectOutputRegex('/\d+|Blocked authentication attempt for b from 255.255.255.255/');
        authenticate('user', 'b', 'password');
    }

    public function testArrayAllowed()
    {
        define('WP_FAIL2BAN_BLOCKED_USERS', ['a','b','c']);

        $rv = authenticate('user', 'd', 'password');
        $this->assertEquals($rv, 'user');
    }
}
