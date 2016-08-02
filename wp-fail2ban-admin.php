<?php

namespace org\lecklider\charles\wordpress\wp_fail2ban;


function show_BLOCKED_USERS($constants)
{
    $value = ($constants['BLOCKED_USERS'])
                ? $constants['BLOCKED_USERS']
                : @WP_FAIL2BAN_BLOCKED_USERS;
    if (is_array($value)) {
        $value = join(',', $value);
    }
?>
  <input class="regular-text"
         name="BLOCKED_USERS"
         placeholder="<?=__('BLOCKED_USERS__PLACEHOLDER', 'wp-f2b')?>"
         type="text"
         value="<?=htmlspecialchars($value)?>">
  <p><?=__('BLOCKED_USERS__BLURB', 'wp-f2b')?></p>
  <button><?=__('BLOCKED_USERS__BUTTON_EXISTING', 'wp-f2b')?></button>
<?php
}

function show_checkbox($constants, $key)
{
    $value = ($constants[$key])
                ? $constants[$key]
                : @constant("WP_FAIL2BAN_{$key}");
?>
        <tr>
          <th scope="row"><?=__("{$key}__TH", 'wp-f2b')?></th>
          <td>
            <fieldset>
              <label for="<?=$key?>">
                <input name="<?=$key?>"
                       type="checkbox"
                       value="1"
                       <?=checked($value,true,false)?>>
                <?=__("{$key}__LABEL", 'wp-f2b')?>
              </label>
            </fieldset>
            <p><?=__("{$key}__BLURB", 'wp-f2b')?></p>
          </td>
        </tr>
<?php
}


function check_defined($name)
{
  switch($name) {
  case 'WP_FAIL2BAN_SYSLOG_SHORT_TAG':
    if (defined('WP_FAIL2BAN_SYSLOG_SHORT_TAG')) {
      return WP_FAIL2BAN_SYSLOG_SHORT_TAG ? 'Yes' : 'No';
    } else {
      return 'Not set';
    }
    break;
  case 'WP_FAIL2BAN_PROXIES':
    if (defined('WP_FAIL2BAN_PROXIES')) {
      return WP_FAIL2BAN_PROXIES ? 'Yes' : 'No';
    } else {
      return 'Not set';
    }
    break;
  case 'WP_FAIL2BAN_BLOCKED_USERS':
    if (defined('WP_FAIL2BAN_BLOCKED_USERS')) {
      if (is_array(WP_FAIL2BAN_BLOCKED_USERS)) {
        return join('<br>',WP_FAIL2BAN_BLOCKED_USERS);
      } else {
        return WP_FAIL2BAN_BLOCKED_USERS;
      }
    } else {
      return 'Not set';
    }
    break;
  case 'WP_FAIL2BAN_BLOCK_USER_ENUMERATION':
    if (defined('WP_FAIL2BAN_BLOCK_USER_ENUMERATION')) {
      return WP_FAIL2BAN_BLOCK_USER_ENUMERATION ? 'Yes' : 'No';
    } else {
      return 'Not set';
    }
    break;
  case 'WP_FAIL2BAN_LOG_PINGBACKS':
    if (defined('WP_FAIL2BAN_LOG_PINGBACKS')) {
      return WP_FAIL2BAN_LOG_PINGBACKS ? 'Yes' : 'No';
    } else {
      return 'Not set';
    }
    break;
  case 'WP_FAIL2BAN_LOG_SPAM':
    if (defined('WP_FAIL2BAN_LOG_SPAM')) {
      return WP_FAIL2BAN_LOG_SPAM ? 'Yes' : 'No';
    } else {
      return 'Not set';
    }
    break;
  case 'WP_FAIL2BAN_AUTH_LOG':
    if (defined('WP_FAIL2BAN_AUTH_LOG')) {
      return WP_FAIL2BAN_AUTH_LOG;
    } else {
      return 'Not set; using LOG_AUTH';
    }
    break;
  case 'WP_FAIL2BAN_PINGBACK_LOG':
    if (defined('WP_FAIL2BAN_PINGBACK_LOG')) {
      return WP_FAIL2BAN_PINGBACK_LOG;
    } else {
      return 'Not set; using LOG_AUTH';
    }
    break;
  case 'WP_FAIL2BAN_COMMENT_LOG':
    if (defined('WP_FAIL2BAN_COMMENT_LOG')) {
      return WP_FAIL2BAN_COMMENT_LOG;
    } else {
      return 'Not set; using LOG_AUTH';
    }
    break;
  }
}

function admin_settings()
{
    $constants = array(
        'BLOCKED_USERS' => false,
        'BLOCK_USER_ENUMERATION' => false,
        'LOG_PINGBACKS' => false,
        'LOG_SPAM' => false,
        'AUTH_LOG' => false,
        'PINGBACK_LOG' => false,
        'COMMENT_LOG' => false,
        'SYSLOG_SHORT_TAG' => false,
        'PROXIES' => false
    );

    if ('POST' == $_SERVER['REQUEST_METHOD']) {
    // WP_FAIL2BAN_BLOCKED_USERS
        if (PHP_MAJOR_VERSION >= 7) {
            $constants['BLOCKED_USERS'] = explode(',', @$_POST['BLOCKED_USERS']);
            if (1 == count($constants['BLOCKED_USERS'])) {
                $constants['BLOCKED_USERS'] = $constants['BLOCKED_USERS'][0];
            }
        } else {
            $constants['BLOCKED_USERS'] = str_replace(',', '|', @$_POST['BLOCKED_USERS']);
            if ($constants['BLOCKED_USERS'][0] != '^') {
                $constants['BLOCKED_USERS'] = '^'.$constants['BLOCKED_USERS'];
            }
            if ($constants['BLOCKED_USERS'][strlen($constants['BLOCKED_USERS'])-1] != '$') {
                $constants['BLOCKED_USERS'] .= '$';
            }
        }

        // WP_FAIL2BAN_BLOCK_USER_ENUMERATION
        $constants['BLOCK_USER_ENUMERATION'] = (@$_POST['BLOCK_USER_ENUMERATION']) ? 'true' : false;

        // WP_FAIL2BAN_LOG_PINGBACKS

    }
?>
<div class="wrap">
  <h1>WP fail2ban</h1>
<?php if ('POST' == $_SERVER['REQUEST_METHOD']): ?>
  <textarea readonly="readonly" cols="100" rows="11"><?=
    join("\n", array_map(function($k, $v) {
      $line = false;

      if ($v) {
        $line = "define('WP_FAIL2BAN_{$k}', ".((is_array($v)) ? "['".join("','", $v)."']" : "'{$v}'").');';
      }

      return $line;
    }, array_keys($constants), $constants));
  ?></textarea>
<?php endif; ?>
  <form method="post">
    <h2><?=__('H2__THINGS_TO_BLOCK', 'wp-f2b')?></h2>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><?=__('BLOCKED_USERS__TH', 'wp-f2b')?></th>
          <td><?php show_BLOCKED_USERS($constants); ?></td>
        </tr>
<?php show_checkbox($constants, 'BLOCK_USER_ENUMERATION'); ?>
      </tbody>
    </table>
    <h2><?=__('H2__WHAT_TO_LOG', 'wp-f2b')?></h2>
    <table class="form-table">
      <tbody>
<?php show_checkbox($constants, 'LOG_PINGBACKS'); ?>
<?php show_checkbox($constants, 'LOG_SPAM'); ?>
<?php show_checkbox($constants, 'LOG_PASSWORD_REQUEST'); ?>
<?php show_checkbox($constants, 'LOG_PASSWORD_RESET'); ?>
      </tbody>
    </table>
    <h2>Where to Log</h2>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">WP_FAIL2BAN_AUTH_LOG</th>
          <td><?=check_defined('WP_FAIL2BAN_AUTH_LOG')?></td>
        </tr>
        <tr>
          <th scope="row">WP_FAIL2BAN_PINGBACK_LOG</th>
          <td><?=check_defined('WP_FAIL2BAN_PINGBACK_LOG')?></td>
        </tr>
        <tr>
          <th scope="row">WP_FAIL2BAN_COMMENT_LOG</th>
          <td><?=check_defined('WP_FAIL2BAN_COMMENT_LOG')?></td>
        </tr>
      </tbody>
    </table>
    <h2>How to Log</h2>
    <table class="form-table">
      <tbody>
<?php show_checkbox($constants,'SYSLOG_SHORT_TAG'); ?>
        <tr>
          <th scope="row">WP_FAIL2BAN_PROXIES</th>
          <td><?=check_defined('WP_FAIL2BAN_PROXIES')?></td>
        </tr>
      </tbody>
    </table>
    <input type="submit">
  </form>
</div>
<?php
}
