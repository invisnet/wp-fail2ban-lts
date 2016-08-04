<?php
use PHPUnit\Framework\TestCase;
use org\lecklider\charles\wordpress\wp_fail2ban;
use org\lecklider\charles\wordpress\wp_fail2ban\phpunit;


class WpLoginFailedTest extends TestCase
{
    public function testUnknownUser()
    {
        $this->expectOutputRegex('/\d+|Authentication attempt for unknown user phpunit from 255.255.255.255/');
        wp_fail2ban\wp_login_failed('phpunit', 'userlogins');
    }

    public function testUnknownUserLive()
    {
        $this->expectOutputRegex('/Authentication attempt for unknown user phpunit from \d+\.\d+\.\d+\.\d+/');
        phpunit\request('/wp-login.php', ['log' => 'phpunit', 'pwd' => 'password']);
    }

    public function testKnownUser()
    {
        wp_fail2ban\wp_cache_set('phpunit', true);
        $this->expectOutputRegex('/\d+|Authentication failure for phpunit from 255.255.255.255/');
        wp_fail2ban\wp_login_failed('phpunit');
    }

    public function testKnownUserLive()
    {
        $this->expectOutputRegex('/Authentication failure for admin from \d+\.\d+\.\d+\.\d+/');
        phpunit\request('/wp-login.php', ['log' => 'admin', 'pwd' => 'password']);
    }
}
