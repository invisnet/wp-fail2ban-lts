<?php

namespace org\lecklider\charles\wordpress\wp_fail2ban
{
    require_once '../../../wp-includes/version.php';
    require_once 'wp-fail2ban.php';


    define('ARRAY_A', true);


    function add_action($a,$b,$c=false,$d=false)
    {
        // stub
    }

    function add_filter($a,$b,$c=false,$d=false)
    {
        // stub
    }

    function get_comment($id, $ary)
    {
        return [
            'comment_ID' => $id,
            'comment_post_ID' => 1,
            'comment_author' => 'phpunit',
            'comment_author_email' => 'phpunit@example.com',
            'comment_author_url' => 'http://example.com',
            'comment_author_IP' => '255.255.255.255',
            'comment_content' => 'meh'
        ];
    }

    function wp_die($msg, $title, $args)
    {

    }
}

namespace
{
    $_SERVER['HTTP_HOST'] = 'phpunit.local';
    $_SERVER['REMOTE_ADDR'] = '255.255.255.255';
}
