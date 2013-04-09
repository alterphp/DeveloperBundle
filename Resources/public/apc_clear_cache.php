<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it, or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    header('HTTP/1.0 403 Forbidden');
    die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}


//Empty file cache
apc_clear_cache();

//If is set param "user", empty user cache
if (isset($_GET['user']))
{
   echo 'USER'.PHP_EOL.PHP_EOL;
   apc_clear_cache('user');
}

//Display user cache
var_dump(apc_cache_info('user'));
//Display file cache
var_dump(apc_cache_info());
