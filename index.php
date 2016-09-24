<?php

/* 
@file
 */

require_once __DIR__."/vendor/autoload.php";

use ControlAltKaboom\Debug\Debug;

echo "Testing the Debug Module";

$test = ["test1", "test2", "foobar"];

define("TEST_MODE", FALSE);

function foobar() {
  return (constant("TEST_MODE") === TRUE)
    ? TRUE
    : FALSE;
}


Debug::setDebugMode(TRUE);

Debug::debug($test);