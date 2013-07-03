<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();

$pass = 123;
//echo crypt($pass).'<br>';

//User::encrypted(123);
$hash = '$2a$12$N2M9ZVYoSLFrZTM7B38FTe8iZJLfFFNk.LnfxGUWoAAe6cmhY2S8a';

if(User::validatePassword(123, $pass, $hash)){
    echo 'valid';
}else{
    echo 'not_valid';
}

