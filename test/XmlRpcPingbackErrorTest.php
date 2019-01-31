<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\phpunit\request;
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

        $this->expectOutputRegex('/\d+\|Pingback error 42 generated from 255.255.255.255/');
        xmlrpc_pingback_error($ixr_error);
    }

    public function testPingbackErrorLive()
    {
        $xml = <<<__XML__
<?xmlversion="1.0"?>
<methodCall>
    <methodName>pingback.ping</methodName>
    <params>
        <param><value><string>http://www.example.com</string></value></param>
        <param><value><string>www.example.com/some/page</string></value></param>
    </params>
</methodCall>
__XML__;
        $this->expectOutputRegex('/Pingback error \d+ generated from \d+\.\d+\.\d+\.\d+/');
        request('/xmlrpc.php', $xml);
    }
}
