<?php
/**
 * Plugin Name: WP fail2ban
 * Plugin URI: https://charles.lecklider.org/wordpress/wp-fail2ban/
 * Description: Write all login attempts to syslog for integration with fail2ban.
 * Text Domain: wp-fail2ban
 * Version: 3.0.3
 * Author: Charles Lecklider
 * Author URI: https://charles.lecklider.org/
 * License: GPL2
 * SPDX-License-Identifier: GPL-2.0
 */

/**
 *  Copyright 2012-16  Charles Lecklider  (email : wordpress@charles.lecklider.org)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as
 *  published by the Free Software Foundation.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace org\lecklider\charles\wordpress\wp_fail2ban;


if (!defined('WP_FAIL2BAN')) {
	define('WP_FAIL2BAN', true);

	function admin_menu()
	{
		require_once('wp-fail2ban-admin.php');

	  add_options_page('WP fail2ban','WP fail2ban','manage_options','wp-f2b',__NAMESPACE__.'\admin_settings');
	}
	add_action('admin_menu',__NAMESPACE__.'\admin_menu');

	function load_plugin_textdomain()
	{
		load_textdomain('wp-f2b',dirname(__FILE__).'/lang/wp-f2b-'.get_locale().'.mo');
		// TODO: find out why this gives a WSoD
		// load_plugin_textdomain('wp-f2b', false, dirname(plugin_basename(__FILE__)).'/lang');
	}
	add_action('plugins_loaded',__NAMESPACE__.'\load_plugin_textdomain');

	function openlog($log = LOG_AUTH, $custom_log = 'WP_FAIL2BAN_AUTH_LOG')
	{
		$tag	= (defined('WP_FAIL2BAN_SYSLOG_SHORT_TAG') && true === WP_FAIL2BAN_SYSLOG_SHORT_TAG)
					? 'wp'
					: 'wordpress';
		$host	= (array_key_exists('WP_FAIL2BAN_HTTP_HOST',$_ENV))
					? $_ENV['WP_FAIL2BAN_HTTP_HOST']
					: $_SERVER['HTTP_HOST'];
		\openlog("$tag($host)",
				 LOG_NDELAY|LOG_PID,
				 defined($custom_log) ? constant($custom_log) : $log);
	}

	function bail()
	{
		ob_end_clean();
		header('HTTP/1.0 403 Forbidden');
		header('Content-Type: text/plain');
		exit('Forbidden');
	}

	function remote_addr()
	{
		if (defined('WP_FAIL2BAN_PROXIES')) {
			if (array_key_exists('HTTP_X_FORWARDED_FOR',$_SERVER)) {
				$ip = ip2long($_SERVER['REMOTE_ADDR']);
				foreach(explode(',',WP_FAIL2BAN_PROXIES) as $proxy) {
					if (2 == count($cidr = explode('/',$proxy))) {
						$net = ip2long($cidr[0]);
						$mask = ~ ( pow(2, (32 - $cidr[1])) - 1 );
					} else {
						$net = ip2long($proxy);
						$mask = -1;
					}
					if ($net == ($ip & $mask)) {
						return (false===($len = strpos($_SERVER['HTTP_X_FORWARDED_FOR'],',')))
								? $_SERVER['HTTP_X_FORWARDED_FOR']
								: substr($_SERVER['HTTP_X_FORWARDED_FOR'],0,$len);
					}
				}
			}
		}

		return $_SERVER['REMOTE_ADDR'];
	}
	/*
	 * @since 2.0.0
	 */
	if (defined('WP_FAIL2BAN_BLOCKED_USERS')) {
		add_filter( 'authenticate',
					function($user, $username, $password)
					{
						if (!empty($username)) {
							if (is_array(WP_FAIL2BAN_BLOCKED_USERS)) {
								$matched = in_array($username, WP_FAIL2BAN_BLOCKED_USERS);
							} else {
								$matched = preg_match('/'.WP_FAIL2BAN_BLOCKED_USERS.'/i', $username);
							}
							if ($matched) {
								openlog();
								\syslog(LOG_NOTICE,"Blocked authentication attempt for $username from ".remote_addr());
								bail();
							}
						}

						return $user;
					},1,3);
	}
	/*
	 * @since 2.1.0
	 */
	if (defined('WP_FAIL2BAN_BLOCK_USER_ENUMERATION') && true === WP_FAIL2BAN_BLOCK_USER_ENUMERATION) {
		add_filter( 'redirect_canonical',
					function($redirect_url, $requested_url)
					{
						if (intval(@$_GET['author'])) {
							openlog();
							\syslog(LOG_NOTICE,'Blocked user enumeration attempt from '.remote_addr());
							bail();
						}

						return $redirect_url;
					},10,2);
	}
	/*
	 * @since 2.2.0
	 */
	if (defined('WP_FAIL2BAN_LOG_PINGBACKS') && true === WP_FAIL2BAN_LOG_PINGBACKS) {
		add_action( 'xmlrpc_call',
					function($call)
					{
						if ('pingback.ping' == $call) {
							openlog(LOG_USER,'WP_FAIL2BAN_PINGBACK_LOG');
							\syslog(LOG_INFO,'Pingback requested from '.remote_addr());
						}
					});
	}
	/*
	 * @since 3.1.0
	 */
	if (defined('WP_FAIL2BAN_LOG_SPAM') && true === WP_FAIL2BAN_LOG_SPAM) {
		function log_spam($comment_id, $comment_status)
		{
			if ('spam' === $comment_status) {
				if (is_null($comment = get_comment($comment_id,ARRAY_A))) {
					// something went wrong
					// TODO: decide where to log this
				} else {
					$remote_addr = (empty($comment['comment_author_IP']))
						? 'unknown'
						: $comment['comment_author_IP'];
					openlog(LOG_USER,'WP_FAIL2BAN_COMMENT_LOG');
					\syslog(LOG_INFO,"Comment {$comment_id} from {$remote_addr} marked as spam");
				}
			}
		}
		add_action( 'comment_post', __NAMESPACE__.'\log_spam', 10, 2 );
		add_action( 'wp_set_comment_status', __NAMESPACE__.'\log_spam', 10, 2 );
	}
	/*
	 * @since 1.0.0
	 */
	add_action( 'wp_login',
				function($user_login, $user)
				{
					openlog();
					\syslog(LOG_INFO,"Accepted password for $user_login from ".remote_addr());
				},10,2);
	/*
	 * @since 1.0.0
	 */
	add_action( 'wp_login_failed',
				function($username)
				{
					$msg = (wp_cache_get($username, 'userlogins'))
							? "Authentication failure for $username from "
							: "Authentication attempt for unknown user $username from ";
					$msg .= remote_addr();
					if (class_exists('wp_xmlrpc_server',false)) {
						$msg .= ' via XML-RPC';
					}
					openlog();
					\syslog(LOG_NOTICE,$msg);
				});
	/*
	 * @since 3.0.0
	 */
	$v = explode('.',$wp_version);
	if (4 == $v[0] && 5 > $v[1]) {
		// prevent double logging
		// will be removed for WP4.7
		add_action( 'xmlrpc_login_error',
					function($error, $user)
					{
						openlog();
						\syslog(LOG_NOTICE,'XML-RPC authentication failure from '.remote_addr());
						bail();
					},10,2);
	}
	/*
	 * @since 3.0.0
	 */
	add_filter( 'xmlrpc_pingback_error',
				function($ixr_error)
				{
					if (48 === $ixr_error->code)
						return $ixr_error;
					openlog();
					\syslog(LOG_NOTICE,'Pingback error '.$ixr_error->code.' generated from '.remote_addr());
				},5);
}
