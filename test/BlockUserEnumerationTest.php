<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\phpunit\request;
use function org\lecklider\charles\wordpress\wp_fail2ban\parse_request;

define('WP_FAIL2BAN_BLOCK_USER_ENUMERATION', true);


class query
{
    public $query_vars;
}

class BlockUserEnumerationTest extends TestCase
{
    function testBlock()
    {
        $query = new query();
        $query->query_vars['author'] = 1;
        $this->expectOutputRegex('/\d+|Blocked user enumeration attempt from 255.255.255.255/');
        parse_request($query);
    }

    public function testBlockLive()
    {
        $this->expectOutputRegex('/Blocked user enumeration attempt from \d+\.\d+\.\d+\.\d+/');
        request('/?author=1');
    }

    function testAllow()
    {
        $query = new query();
        $rv = parse_request($query);
        $this->assertEquals($rv, $query);
    }
}
