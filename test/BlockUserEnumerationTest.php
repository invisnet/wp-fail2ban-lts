<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\redirect_canonical;

define('WP_FAIL2BAN_BLOCK_USER_ENUMERATION', true);


class BlockUserEnumerationTest extends TestCase
{
    function testBlock()
    {
        $_GET['author'] = 1;

        $this->expectOutputRegex('/\d+|Blocked user enumeration attempt from 255.255.255.255/');
        redirect_canonical('redirect', 'requested');
    }

    function testAllow()
    {
        $rv = redirect_canonical('redirect', 'requested');
        $this->assertEquals($rv, 'redirect');
    }
}
