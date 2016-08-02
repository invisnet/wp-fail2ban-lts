<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\xmlrpc_login_error;


class XmlRpcLoginErrorTest extends TestCase
{
    function testXmlRpcTest()
    {
        xmlrpc_login_error('error', 'user');
        $this->expectOutputRegex('/\d+|XML-RPC multicall authentication failure from 255.255.255.255/');
        xmlrpc_login_error('error', 'user');
    }
}
