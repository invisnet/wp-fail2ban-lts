<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\log_spam_comment;
use function org\lecklider\charles\wordpress\wp_fail2ban\notify_post_author;

define('WP_FAIL2BAN_LOG_COMMENTS', true);
define('WP_FAIL2BAN_LOG_SPAM', true);


class LogSpamTest extends TestCase
{
    function testHam()
    {
        $this->expectOutputString('');
        log_spam_comment(1, 1);
    }

    function testSpam()
    {
        $this->expectOutputRegex('/\d+|Spam comment 1 from 255.255.255.255/');
        log_spam_comment(1, 'spam');
    }

    function testCommentLog()
    {
        $this->expectOutputRegex('/\d+|Comment \d+ from 255.255.255.255/');
        notify_post_author(false, 1);
    }
}
