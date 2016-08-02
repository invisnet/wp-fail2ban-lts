<?php
use PHPUnit\Framework\TestCase;
use org\lecklider\charles\wordpress\wp_fail2ban;


class WpLoginFailedXmlRpcTest extends TestCase
{
    public function testXmlRpcUnknownUser()
    {
        global $wp_xmlrpc_server;

        $wp_xmlrpc_server = true;
        $this->expectOutputRegex('/\d+|XML-RPC authentication attempt for unknown user phpunit from 255.255.255.255/');
        wp_fail2ban\wp_login_failed('phpunit', 'userlogins');
    }

    public function testXmlRpcKnownUser()
    {
        global $wp_xmlrpc_server;

        $wp_xmlrpc_server = true;
        wp_fail2ban\wp_cache_set('phpunit', true);
        $this->expectOutputRegex('/\d+|XML-RPC authentication failure for phpunit from 255.255.255.255/');
        wp_fail2ban\wp_login_failed('phpunit');
    }
}
