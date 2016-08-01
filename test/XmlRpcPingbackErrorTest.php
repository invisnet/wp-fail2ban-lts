<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\xmlrpc_pingback_error;


class ixr_error
{
    public $code = 42;
}

class XmlRpcPingbackErrorTest extends TestCase
{
    public function testPingbackError()
    {
        $ixr_error = new ixr_error();

        $this->expectOutputRegex('/\d+|Pingback error 42 generated from 255.255.255.255/');
        xmlrpc_pingback_error($ixr_error);
    }
}
