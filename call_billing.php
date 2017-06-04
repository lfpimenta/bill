#! /usr/bin/env php

<?php

use Symfony\Component\Console\Application;
use \Talkdesk\Commands\ChargeCommand;
use \Talkdesk\Commands\ListCommand;

defined('APP_PATH') or define('APP_PATH', dirname(__FILE__));
require 'vendor/autoload.php';

$app = new Application('Call Billing', '1.0');

$app->add(new ChargeCommand());
$app->add(new ListCommand());


$app->run();
