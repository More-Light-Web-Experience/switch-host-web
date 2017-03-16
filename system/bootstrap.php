<?php
/**
 * Created by PhpStorm.
 * User: saive
 * Date: 17-1-3
 * Time: 下午9:59
 * php bootstrap
 */
header("Content-type:text/html;charset=utf-8");
define('APP_PATH', dirname(dirname(__FILE__)));
define('APP_HOST',$_SERVER['HTTP_HOST']);
define('IS_DEBUG',false);
/**
 * get computer system .
 */
if(strtolower(substr(PHP_OS, 0, 3)) == 'win') {
    defined('SYSTEM_ENV') or define('SYSTEM_ENV','WIN');
    defined('DEFAULT_HOST_FILE') or define('DEFAULT_HOST_FILE', 'C:/Windows/System32/drivers/etc/hosts');
} else {
    defined('SYSTEM_ENV') or define('SYSTEM_ENV','LINUX');
    defined('DEFAULT_HOST_FILE') or define('DEFAULT_HOST_FILE', '/etc/hosts');
}

/**
 * check host file is exists
 */
if(!file_exists(DEFAULT_HOST_FILE)) {
    echo 'The default host file is not found ,This file is ' . DEFAULT_HOST_FILE;
    exit;
}

/**
 * check host file is writabled
 */
if(!is_writable(DEFAULT_HOST_FILE)) {
    echo 'This host file is not write !';
    exit;
}




