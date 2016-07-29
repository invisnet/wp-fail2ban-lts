<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\xmlrpc_login_error;

define('WP_FAIL2BAN_LOG_SPAM', true);


class XmlRpcLoginErrorTest extends TestCase
{
    function testXmlRpcTest()
    {
        $this->expectOutputString('');
        xmlrpc_login_error('error', 'user');
        $this->expectOutputString('5|XML-RPC multicall authentication failure from 255.255.255.255');
        xmlrpc_login_error('error', 'user');
    }
}
