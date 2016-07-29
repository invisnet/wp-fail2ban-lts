<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\retrieve_password;

define('WP_FAIL2BAN_LOG_PASSWORD_REQUEST', true);


class LogPasswordRequestTest extends TestCase
{
    function testRequest()
    {
        $this->expectOutputString('5|Password reset requested for user_login from 255.255.255.255');
        retrieve_password('user_login');
    }
}
