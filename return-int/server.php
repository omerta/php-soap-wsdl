<?php
require 'return_int_function.php';

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer("returnInt.wsdl");
$server->addFunction("getItemCount");
$server->handle();
?>