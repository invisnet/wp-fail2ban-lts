<?php
use PHPUnit\Framework\TestCase;
use org\lecklider\charles\wordpress\wp_fail2ban;
use org\lecklider\charles\wordpress\wp_fail2ban\phpunit;


class WpLoginFailedXmlRpcTest extends TestCase
{
    public function testXmlRpcUnknownUser()
    {
        global $wp_xmlrpc_server;

        $wp_xmlrpc_server = true;
        $this->expectOutputRegex('/\d+\|XML-RPC authentication attempt for unknown user phpunit from 255.255.255.255/');
        wp_fail2ban\wp_login_failed('phpunit', 'userlogins');
    }

    public function testXmlRpcUnknownUserLive()
    {
        $this->expectOutputRegex('/XML-RPC authentication attempt for unknown user phpunit from \d+\.\d+\.\d+\.\d+/');
        phpunit\request('/xmlrpc.php', '<?xmlversion="1.0"?><methodCall><methodName>wp.getUsersBlogs</methodName><params><param><value><string>phpunit</string></value></param><param><value><string>password</string></value></param></params></methodCall>');
    }

    public function testXmlRpcKnownUser()
    {
        global $wp_xmlrpc_server;

        $wp_xmlrpc_server = true;
        wp_fail2ban\wp_cache_set('phpunit', true);
        $this->expectOutputRegex('/\d+\|XML-RPC authentication failure for phpunit from 255.255.255.255/');
        wp_fail2ban\wp_login_failed('phpunit');
    }

    public function testXmlRpcKnownUserLive()
    {
        $this->expectOutputRegex('/XML-RPC authentication failure for admin from \d+\.\d+\.\d+\.\d+/');
        phpunit\request('/xmlrpc.php', '<?xmlversion="1.0"?><methodCall><methodName>wp.getUsersBlogs</methodName><params><param><value><string>admin</string></value></param><param><value><string>password</string></value></param></params></methodCall>');
    }
}
