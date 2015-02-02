<?php
ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$client = new SoapClient("http://interoperabilidad.io/return-array/returnArray.wsdl");

/*
 * Preguntar por los metodos/funciones dispoblibles.
 */
try {
    var_dump($client->__getFunctions());
} catch(Exception $e) {
    print_r($e);
}

/*
 * Ejecutar la función.
 */
$return = $client->getArray();
print_r($return);


?>