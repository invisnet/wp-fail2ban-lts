<?php

$file = file_get_contents('wp-fail2ban.php');
preg_match_all('/\@wp\-f2b\-(hard|soft) (.*)$/m',$file,$matches);

$rules = [
    'hard' => [],
    'soft' => []
];
array_map(function($k,$v) use (&$rules) {
    $rules[$k][] = trim($v);
}, $matches[1], $matches[2]);

foreach(array_keys($rules) as $key) {
    filter($rules, $key);
}


function filter($rules, $type)
{
    $header = 'Auto-generated: '.date(DATE_ATOM);

    $rules = $rules[$type];
    $failregex = "failregex = ^%(__prefix_line)s${rules[0]} from <HOST>$\n";
    for($i=1; $i<count($rules); $i++) {
        $failregex .= "            ^%(__prefix_line)s${rules[$i]} from <HOST>$\n";
    }

    $fp = fopen("filters.d/wordpress-$type.conf",'w+');
    fwrite($fp, <<<FILTER
# Fail2Ban filter for WordPress $type failures
# $header
#

[INCLUDES]

before = common.conf

[Definition]

_daemon = (?:wordpress|wp)

$failregex
ignoreregex =

# DEV Notes:
# Requires the 'WP fail2ban' plugin:
# https://wordpress.org/plugins/wp-fail2ban/
#
# Author: Charles Lecklider

FILTER
    );
    fclose($fp);
}
