<?php
require 'return_array_function.php';

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer("returnArray.wsdl");
$server->addFunction("getArray");
$server->handle();
?>