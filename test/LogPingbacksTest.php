<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\xmlrpc_call;

define('WP_FAIL2BAN_LOG_PINGBACKS', true);


class LogPingbacksTest extends TestCase
{
    function testPingback()
    {
        $this->expectOutputString('6|Pingback requested from 255.255.255.255');
        xmlrpc_call('pingback.ping');
    }

    function testNonPingback()
    {
        xmlrpc_call('something.else');
    }
}
