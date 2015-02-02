# php-soap-wsdl

# Servidor SOAP con PHP5 - parte 1

DISCLAIMER: lo que se muestra a continuación es un traducción libre de un articulo de *Jimmy Zimmerman*, ver
bibliografía.

La creación de un servicio web SOAP usando php me parecía bastante intimidante, hasta que decidí agacharme
y construir mi propio WSDL. Ahora que he ido y regresado con éxito por el proceso de construcción de un servicio web
SOAP con php5, deseo confesar que realmente no es tan malo. En realidad estoy muy sorprendido de cuan
fácil fue terminarlo.

Para comenzar recomiendo leer [PHP Soap Extension article](http://devzone.zend.com/25/php-soap-extension)
que se encuentra en la pagina web del Zend Developer Zone. Antes de comenzar es necesario asegurarse 
que estamos ejecutando php5 con la extensión de soap instalada.
Si no estamos seguros de estar en la versión 5 y con la extensión SOAP instalada, podemos crear un script
con el código que se muestra a continuación:

```php
<?php phpinfo(); ?>
```

Este *script* debería mostrar toda la información de la configuración del servidor que nosotros necesitamos. 
Buscamos la página para "SOAP" si la encontramos nos encontraremos también con algunas variables de
configuración, con esto sabrás que esta todo bien y podremos continuar.

Para configurar el servidor SOAP que se muestra en este tutorial necesitamos tres archivos: el archivo *.wsdl*
(stockwuote.wsdl),  *server.php* y *client.php*. Coloque estos archivos en un directorio que sea accesible por http y modifique
el archivo stockquote.wsdl, reemplazando *http://[ruta real]/server.php con la ruta real hacia nuestro
archivo *server.php*.

Las pruebas sobre el servidor se hacen usando el script client.php. De esta manera podemos modificar el servidor
para que regrese otros tipos de datos, como datos *complex*, en este caso podremos desear cambiar la función
*print* por *print_r* para mostrar todo el *data schema* que fue regresado.

Una vez que tenemos esto trabajando, podemos comenzar a modificar la función y el WSDL para lo que necesitemos
hacer.

**Importante**: Antes de hacer los cambios al WSDL, agregamos la siguiente linea arriba del código del cliente
y del servidor:

```php
init_set("soap.wsdl_cache_enabled", "0"); // desactivar el cache WSDL
``` 

Además, en el archivo wsdl hay una parte que tiene *urn:xmethods-delayed-quotes* que no tiene nada que ver con
stockquotes y debemos dejarlo como esta.

En este ejemplo mostraremos como regresar un dato *int*, que en la lógica del negocio podríamos imaginar que
estamos consultando en nuestro inventario el numero de artículos/productos a partir de un identificador numérico único.

# Servidor SOAP con PHP5 - parte 2: divirtiéndonos con el archivo wsdl

La parte 1 de esta serie de posts debería ayudar a comenzar con la configuración de un
servidor soap simple, con una función y una operación de petición.

A continuación explicaremos algunas de las diferentes partes del wsdl, el wsdl no es tan intimidante.
Vamos a comenzar examinando una simplificación del wsdl que se encuentra en
 (este enlace)[http://www.webservicex.net/stockquote.asmx?WSDL].

Comenzaremos por el final del archivo wsdl para terminar con el encabezado.

## El bloque 'service'

```xml
<service name='StockQuoteService'>
 <port name='StockQuotePort' binding='StockQuoteBinding'>
  <soap:address location='http://[insertar la ruta correcta]/server.php />
</port>
</service>
```

El bloque de servicio de el wsdl muestra información básica de nuestro servidor soap. Debemos colocar el nombre 
del servicio y asegurar que sea consistente en todo el wsdl. Este ejemplo usa *StockQuoteService* para el nombre
del servicio pero podemos usar otro como 'Weather' o 'Inventory' cuidando que el nombre sea consitente, de manera que si se construye
un servicio de 'Inventario' que regresa un valor *int*, nuestro bloque de servicio en el wsdl podría ser como:

```xml
<service name='ReturnIntService'>
 <port name='ReturnIntPort' binding='ReturnIntBinding'>
  <soap:address location='http://[insertar la ruta correcta]/return-int/server.php' />
 </port>
</service>
```

El *port name* y el *binding* conecta este bloque con otras partes del wsdl, así que con solo mantener la convención
de nombres todo estará bien.

El *address location* apunta a la ubicación de lo que se llama *glue code* o el script de php que pega nuestra *función*
con la extensión soap y las bibliotecas del servidor. El *glue code* es el que maneja todo nuestros *xml messages* y pasa
los parámetros correctos a nuestras funciones.

## El bloque 'binding'

```xml
<binding name='StockQuoteBinding' type='tns:StockQuoetetype'>
 <soap:binding style='rpc' transport='http://schemas.xmlsoap.org/soap/http' />
 <operation name='getQuote'>
  <soap:operation soapAction='urn:xmethods-delayed-quotes#getQuote' />
  <input>
   <soap:body use='encoded' namespace='urn:xmethods-delayed-quotes' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/' />
  </input>
  <output>
   <soap:body use='encoded' namespace='urn:xmethods-delayed-quotes' encondingStyle='http://schemas.xmlsoap.org/soap/endoding/' />
  </output>
 </operation>
</binding>
```

El bloque de binding tiene muchas cosas en él, muchas de estas cosas definen como el cliente y el servidor
se comunican. Es muy poco el código que debemos modificar para adaptarlo a nuestros servicio.
Hay cuatro partes que necesitaremos modificar para hacer corresponder las funciones que deseamos publicar
en el servidor. Es necesario cambiar el *name* y el *type* en la etiqueta binding, el name de la etiqueta *binding* debe corresponder con el binding de la *port* en el bloque service. El *name* en la etiqueta *operation* y la *#functionName* (#getQuote -> #getItemCount) que se encuentra al final de la etiqueta soap:operation, coincidiendo con el nombre de la *función* en nuestro script de php.

Así, si nosotros estamos ajustando para adaptarlo al ejemplo del 'ReturnIntService' nosotros cambiaremos 
*getQuote* por *getItemCount* y la etiqueta binding como se muestra abajo:

```xml
<binding name='ReturnIntBinding' type='tns:ReturnIntPortType'>
 <soap:binding style='rpc' transport='http://schemas.xmlsoap.org/soap/http' />
 <operation name='getItemCount'>
  <soap:operation soapAction='urn:xmethods-delayed-quotes#getItemCount />
  <input>
   <soap:body use='encoded' namespace='urn:xmethods-delayed-quotes' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/' />
  </input>
  <output>
   <soap:body use='encoded' namespace='urn:xmethods-delayed-quotes' encondingStyle='http://schemas.xmlsoap.org/soap/endoding/' />
  </output>
 </operation>
</binding>
```

Si necesitáramos agregar otra función a nuestro servidor, solo debemos copiar la sección 'operaction' de este
bloque y pegarla una abajo de la otra cambiando los cuatro valores descritos.

## El bloque 'portType'

```xml
<portType name='StockQuotePortType'>
 <operation name='getQuote'>
  <input message='tns:getQuoteRequest'/>
  <output message='tns:getQuoteResponse'/>
 </operation>
</portType>
```

El bloque 'portType' connecta nuestro bloque **binding** con el bloque **message** que define las parámetros del método/función
y los tipos de datos que se regresan, es decir, los input y los output.

El *name* debe coincidir con el *type* dle bloque **binding**. Necesitamos también cambiar el 
*name* en la etiqueta *operation*, para que coincida con el *name* de la etiqueta operation del bloque *binding*.

Los atributos del *input message* y *output message*,
el atributo *message* de estas etiquetas puede ser llamado como se desee siempre que coincida con el name en el bloque *message*,
bloque del que hablaremos a continuación.

Para el ejemplo del 'ReturnIntService' debemos cambiar nuestro bloque **portType** de la siguiente manera:

```xml
<portType name='ReturnIntPortType'>
 <operation name='getItemCount'>
  <input message='tns:getItemCountRequest'/>
  <output message='tns:getItemcountResponse'/>
 </operation>
</portType>
```

Nuevamente, si tu deseas agregar otro método a nuestro servicio, podemos tener dos *operation block* dentro del *portType block*.

# El bloque 'message'

```xml
<message name='getQuoteRequest'>
 <part name='symbol' type='xsd:string' />
</message>
<message name='getQuoteResponse'>
 <part name='Result' type='xsd:float' />
</message>
```

Aquí hay dos **message blocks** que corresponden al atributo del *message* que se encuentran
en el *portType block* del archivo wsdl. Aquí es donde reside el núcleo de nuestro wsdl.

El ´name´ del **message block** debe conincidir con el ´message´ de la etiqueta *input*/*output* en el **portType block**.

Aquí se especifican los parámetros para los *request messages* y las estructuras de las variables (tipos de datos)
que es regresada por la *función* de nuestro script php. Debemos asegurar que el ´name´ corresponda con el parametro *name* y se especifique el tipo de dato, en el enlace siguiente se muestran los
*primitive data types*: [built-in-primitive-datatypes][http://www.w3.org/TR/xmlschema-2/#built-in-primitive-datatypes]

Para el ejemplo del 'ReturnIntService', podemos modificar la sección de esta manera:

```xml
<message name='getItemCountRequest'>
 <part name='upc' type='xsd:string' />
</message>
<message name='getItemcountresponse'>
 <part name='Result' type='zsd:integer'/>
</message>
```
# Opcional: El type block (ComplexType)

```xml
<types>
 <xsd:schema targetNamespace="http://interoperabilidad.io/return-array/server.php">
  <xsd:complexType name="ownType">
   <xsd:all>
    <xsd:element name="id" type="xsd:int" nillable="true"/>
    <xsd:element name="count" type="xsd:int" nillable="true"/>
   </xsd:all>
  <xsd:complexType>	
  <xsd:complexType name="ArrayOwnType">
   <xsd:complexContent>
    <xsd:restriction base="soap-enc:Array">
     <xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:ownType[]"/>
    </xsd:restriction>
   </xsd:complexContent>
  </xsd:complexType>
 </xsd:schema>
</types>
```

El *name* de la etiqueta ´complexContent´ debe conincidir con el *type* de la eqiqueta ´part´ en el **block message**.

#El header 'definitions'

La *definitions header* es el que define los *namespaces* de nuestro documento wsdl. Usted puede en la mayoría de los
casos dejarlo como esta en el ejemplo, pero es posible cambiar algunas partes. A continuación mostramos como se
modifica el header para el ejemplo del 'ReturnIntService':

```xml
<definitions name='Inventory'
 targetNamesscpace='urn:JimmyzInventyory'
 xmlns:tns='urn:JimmyzInventory'
 xmlns:soap='http:/schemas.xmlsoap.org/wsdl/soap/'
 xmlns:xsd='http://www.w3.org/2001/XMLSchema'
 xmlns:soapenc='http://schemas.xmlsoap.org/soap/encoding/'
 xmlns:wsdl='http://schemas.xmlsoap.org/wsdl/'
 xmlns='http://schemas.xmlsoap.org/wsdl/'>

... los otros bloques

</defnitions>
```
Esto es todo lo que debemos hacer. Crear nuestro propio wsdl no es tan malo como parece.

![Relación entre los bloques del wsdl (cc-by-sa)](https://github.com/omerta/php-soap-wsdl/blob/master/WSDL_11.png "Relación entre los bloques del wsdl")

# Servidor SOAP con PHP5  - parte 3: el *glue code*

Si antes nos concentramos en la creación del wsdl, ahora nos moveremos hacia el llamado *glue code*
y colocaremos todo junto. En la parte 2 nosotros creamos un *wsdl* que definió la *application programming
interface (API)* para un web service que publicaba un inventario, regresa un entero. Ahora definiremos una simple función
llamada getItemCount que toma un *upc (unique product code)* que tomara un *string* como parámetro de entrada
y regresará un numero, la cantidad de artículos que se encuentran en el inventario, en forma de un entero.

## El código de la función

El código de la función es el siguiente:

```php
<?php
 function getItemCount($upc){
 // en un escenario real los datos se obtienen desde la base de datos
 $items = array('123456'=>5,'19283'=>100,'23489'=>234);
 return $items[$upc];
 }
?>
```

Es importante probar todas las funciones fuera del servicio web para minimizar todos los errores,
de lo contrario se encontrara con un código de depuración algo desagradable. Es posible probar
con mayor facilidad si separamos el *código de las funciones o clases* del *glue code* (server.php
separado del return_int_function.php).

## El *glue code*

El *glue code* es el responsable de manejar los peticiones entrantes del servidor soap y regresar
un *soap xml* valido. Tomando el *wsdl service definition* y conectandolo con nuestras clases o funciones.
El código es como el siguiente:

```php
<?php
 require 'return_int_function.php';

 init_set("soap.wsdl_cache_enable", "0"); // desactivar el cache WSDL
 $server = new SoapServer("returnInt.wsdl");
 $server->addFunction("getItemCount");
 $server->handle();
?>
```
El script *return_int_function.php* contiene nuestra función *getItemCount*. Aquí es importante que la función
sea *included* o *required* antes de la declaración *$server->addFunction("functionName");* sea llamada en el
archivo *server.php*.

Una vez que sabemos que nuestro *wsdl* es correcto, y no necesitamos cambiarlo más. Podremos remover
la función *ini_set* del *glue code* (server.php).

## Probar el servicio

Ahora es el momento de la verdad, revisaremos si nuestro servicio funciona como lo pensamos, escribimos un
código como el siguiente:

```php
<?php
 ini_set("soap.wsdl_cache_enabled", "0"); // desactivar la cache WSDL
 $client = new SoapClient("http://[ruta al servicio]/returnString.wsdl");
 $return = $client->getItemCount('12345');
 print_r($return);
?>
```
En necesario sustituir *[ruta al servicio]* por nuestro url y ejecutar el script. Con suerte deberá ver los
resultados que esperaba.

### Bibliografía

* [SOAP Server with PHP5 – part 1](jimmyzimmerman.com/blog/2007/02/soap-server-with-php5-part3-the-glue-code.html)
* [SOAP Server with PHP5 – part 2: fun with wsdl](http://jimmyzimmerman.com/blog/2007/02/soap-server-with-php5-part-2-fun-with-wsdl.html)
* [SOAP Server with PHP5 – part3: the glue code](http://jimmyzimmerman.com/blog/2007/02/soap-server-with-php5-part3-the-glue-code.html)
