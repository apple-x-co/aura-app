<?php

//session_name('aura-php');
//ini_set('session.gc_maxlifetime', 3600);

require dirname(__DIR__) . '/vendor/autoload.php';
exit((new \MyVendor\MyPackage\Bootstrap())());
