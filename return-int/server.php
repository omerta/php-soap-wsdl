<?php
require 'return_string_function.php';

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer("returnString.wsdl");
$server->addFunction("getItemCount");
$server->handle();
?>