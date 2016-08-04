<?php
use PHPUnit\Framework\TestCase;
use function org\lecklider\charles\wordpress\wp_fail2ban\phpunit\request;
use function org\lecklider\charles\wordpress\wp_fail2ban\xmlrpc_login_error;


class XmlRpcLoginErrorTest extends TestCase
{
    function testXmlRpcTest()
    {
        xmlrpc_login_error('error', 'user');
        $this->expectOutputRegex('/\d+|XML-RPC multicall authentication failure from 255.255.255.255/');
        xmlrpc_login_error('error', 'user');
    }

    public function testXmlRpcTestLive()
    {
        $this->expectOutputRegex('/XML-RPC multicall authentication failure from \d+\.\d+\.\d+\.\d+/');
        $xml = <<<__XML__
<?xml version="1.0"?>
<methodCall>
	<methodName>system.multicall</methodName>
	<params>
		<param>
			<value>
				<array>
					<data>
						<value>
							<struct><member><name>methodName</name><value><string>wp.getAuthors</string></value></member><member><name>params</name><value><array><data><value><string>1</string></value><value><string>admin</string></value><value><string>password1</string></value></data></array></value></member></struct>
							<struct><member><name>methodName</name><value><string>wp.getAuthors</string></value></member><member><name>params</name><value><array><data><value><string>1</string></value><value><string>phpunit</string></value><value><string>password1</string></value></data></array></value></member></struct>
                        </value>
                    </data>
                </array>
            </value>
        </param>
    </params>
</methodCall>
__XML__;
        request('/xmlrpc.php', $xml);
    }
}
