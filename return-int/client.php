<?php
    ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
    $client = new SoapClient("http://interoperabilidad.io/return-int/returnInt.wsdl");
    $return = $client->getItemCount('12345');
    print_r($return);
?>