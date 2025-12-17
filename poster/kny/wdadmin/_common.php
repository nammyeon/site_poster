<?php


$g5_path = dirname(dirname(dirname(dirname(__FILE__)))); 
if (!file_exists($g5_path . '/common.php')) {
    $g5_path = dirname($g5_path);
}
include_once($g5_path . '/common.php');

define('WD_URL', G5_URL . '/poster');
?>
