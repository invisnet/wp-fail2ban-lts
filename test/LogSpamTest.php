<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\log_spam_comment;

define('WP_FAIL2BAN_LOG_SPAM', true);


class LogSpamTest extends TestCase
{
    function testHam()
    {
        log_spam_comment(1, 1);
    }

    function testSpam()
    {
        $this->expectOutputString('6|Comment 1 from 255.255.255.255 marked as spam');
        log_spam_comment(1, 'spam');
    }
}
