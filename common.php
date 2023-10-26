<?php

    // Estas variables definen la información de conexión para tu base de datos MySQL
    $username = "root";
    $password = "";
    $host = "localhost";
    $dbname = "tienda";

    // UTF-8 es un esquema de codificación de caracteres que te permite almacenar
    // una amplia variedad de caracteres especiales, como ¢ o €, en tu base de datos.
    // Al pasar la siguiente matriz $options al código de conexión de la base de datos,
    // estamos indicando al servidor MySQL que queremos comunicarnos con él utilizando UTF-8.
    // Consulta Wikipedia para obtener más información sobre UTF-8:
    // http://es.wikipedia.org/wiki/UTF-8
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

    // Un bloque try/catch es un método común para el manejo de errores en el código orientado a objetos.
    // Primero, PHP ejecuta el código dentro del bloque try. Si en cualquier momento se encuentra con un
    // error durante la ejecución de ese código, se detiene de inmediato y salta al bloque catch.
    // Para obtener información más detallada sobre excepciones y bloques try/catch:
    // http://php.net/manual/es/language.exceptions.php
    try
    {
        // Esta declaración abre una conexión con tu base de datos utilizando la librería PDO.
        // PDO está diseñado para proporcionar una interfaz flexible entre PHP y muchos
        // tipos diferentes de servidores de bases de datos. Para obtener más información sobre PDO:
        // http://php.net/manual/es/class.pdo.php
        $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
    }
    catch(PDOException $ex)
    {
        // Si ocurre un error al abrir una conexión con tu base de datos, se capturará aquí.
        // El script mostrará un error y dejará de ejecutarse.
        // Nota: En un sitio web de producción, no debes mostrar $ex->getMessage().
        // Puede proporcionar información útil sobre tu código (como tu nombre de usuario y contraseña de la base de datos) a un posible atacante.
        die("Error al conectar a la base de datos: " . $ex->getMessage());
    }

    // Esta declaración configura PDO para lanzar una excepción cuando se encuentra un error.
    // Esto nos permite utilizar bloques try/catch para capturar errores de la base de datos.
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Esta declaración configura PDO para devolver filas de la base de datos utilizando un array asociativo.
    // Esto significa que el array tendrá índices de cadena, donde el valor de la cadena
    // representa el nombre de la columna en tu base de datos.
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Este bloque de código se utiliza para deshacer las "magic quotes". Las "magic quotes" son una característica terrible
    // que se eliminó de PHP a partir de PHP 5.4. Sin embargo, las instalaciones antiguas
    // de PHP aún pueden tener habilitadas las "magic quotes" y este código es necesario para
    // evitar que causen problemas. Para obtener más información sobre las "magic quotes":
    // http://php.net/manual/es/security.magicquotes.php

    // Esto le dice al navegador web que tu contenido está codificado en UTF-8
    // y que debe enviar contenido de vuelta a ti utilizando UTF-8.
    header('Content-Type: text/html; charset=utf-8');

    // Esto inicializa una sesión. Las sesiones se utilizan para almacenar información sobre
    // un visitante desde una visita a otra en una página web. A diferencia de una cookie, la información se
    // almacena en el lado del servidor y no puede ser modificada por el visitante. Sin embargo,
    // ten en cuenta que en la mayoría de los casos, las sesiones aún utilizan cookies y requieren
    // que el visitante tenga las cookies habilitadas. Para obtener más información sobre las sesiones:
    // http://php.net/manual/es/book.session.php
    session_start();

    // Ten en cuenta que es una buena práctica NO finalizar tus archivos PHP con una etiqueta de cierre de PHP.
    // Esto evita que las nuevas líneas al final del archivo se incluyan en la salida,
    // lo que puede causar problemas al redirigir a los usuarios.
?>
