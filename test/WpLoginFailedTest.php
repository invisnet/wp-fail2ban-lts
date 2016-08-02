<?php
use PHPUnit\Framework\TestCase;
use org\lecklider\charles\wordpress\wp_fail2ban;


class WpLoginFailedTest extends TestCase
{
    public function testUnknownUser()
    {
        $this->expectOutputRegex('/\d+|Authentication attempt for unknown user phpunit from 255.255.255.255/');
        wp_fail2ban\wp_login_failed('phpunit', 'userlogins');
    }

    public function testKnownUser()
    {
        wp_fail2ban\wp_cache_set('phpunit', true);
        $this->expectOutputRegex('/\d+|Authentication failure for phpunit from 255.255.255.255/');
        wp_fail2ban\wp_login_failed('phpunit');
    }
}
