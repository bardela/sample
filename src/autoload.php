<?php

require('config.php');
require 'vendor/autoload.php';





function debug($var)
{
	highlight_string(var_export($var, true));
}