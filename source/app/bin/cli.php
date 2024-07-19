<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use MyVendor\MyPackage\Bootstrap;

exit((new Bootstrap())());
