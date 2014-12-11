<?php

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

ApplicationTest\Integration\Util\Bootstrap::setConfig(include 'config/application.config.php');
