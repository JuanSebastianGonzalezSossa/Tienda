<?php
// Primero, ejecutamos nuestro código común para conectarnos a la base de datos y comenzar la sesión.
require("common.php");

// Esta declaración "if" verifica si el formulario de registro se ha enviado.
// Si se ha enviado, se ejecuta el código de registro; de lo contrario, se muestra el formulario.
if (!empty($_POST)) {
    // Aseguramos que el usuario haya ingresado un nombre de usuario no vacío.
    if (empty($_POST['username'])) {
        // Ten en cuenta que die() generalmente no es la mejor manera de manejar errores de usuario como este.
        // Es mucho mejor mostrar el error en el formulario y permitir al usuario corregir su error.
        // Sin embargo, eso es un ejercicio para que lo implementes tú mismo.
        die("Por favor, ingresa un nombre de usuario.");
    }

    // Aseguramos que el usuario haya ingresado una contraseña no vacía.
    if (empty($_POST['password'])) {
        die("Por favor, ingresa una contraseña.");
    }

    // Aseguramos que el usuario haya ingresado una dirección de correo electrónico válida.
    // filter_var es una función útil en PHP para validar la entrada del formulario.
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        die("Dirección de correo electrónico no válida");
    }

    // Utilizaremos esta consulta SQL para comprobar si el nombre de usuario ingresado por el usuario ya está en uso.
    // Una consulta SELECT se utiliza para recuperar datos de la base de datos.
    // :username es un token especial; lo sustituiremos por un valor real cuando ejecutemos la consulta.
    $query = "
            SELECT
                1
            FROM users
            WHERE
                username = :username
        ";

    // Aquí se definen los valores para los tokens especiales en nuestra consulta SQL.
    $query_params = array(
        ':username' => $_POST['username']
    );

    try {
        // Estas dos instrucciones ejecutan la consulta en la tabla de la base de datos.
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    } catch (PDOException $ex) {
        // Nota: En un sitio web de producción, no debes mostrar $ex->getMessage().
        // Puede proporcionar información útil sobre tu código a un atacante.
        die("Error al ejecutar la consulta: " . $ex->getMessage());
    }

    // El método fetch() devuelve una matriz que representa la "próxima" fila de los resultados seleccionados o false si no hay más filas para recuperar.
    $row = $stmt->fetch();

    // Si se devolvió una fila, sabemos que se encontró un nombre de usuario coincidente en la base de datos y no debemos permitir al usuario continuar.
    if ($row) {
        die("Este nombre de usuario ya está en uso");
    }

    // Ahora realizamos el mismo tipo de comprobación para la dirección de correo electrónico para asegurarnos de que sea única.
    $query = "
            SELECT
                1
            FROM users
            WHERE
                email = :email
        ";

    $query_params = array(
        ':email' => $_POST['email']
    );

    try {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    } catch (PDOException $ex) {
        die("Error al ejecutar la consulta: " . $ex->getMessage());
    }

    $row = $stmt->fetch();

    if ($row) {
        die("Esta dirección de correo electrónico ya está registrada");
    }

    $role = 'usuario';

    // Una consulta INSERT se utiliza para agregar nuevas filas a una tabla de la base de datos.
    // Nuevamente, usamos tokens especiales (parámetros) para protegernos contra ataques de inyección SQL.
    $query = "
            INSERT INTO users (
                username,
                password,
                salt,
                email,
                rol
            ) VALUES (
                :username,
                :password,
                :salt,
                :email,
                :rol
            )
        ";

    // Aquí generamos una sal de forma aleatoria para protegernos contra ataques de fuerza bruta y ataques de tabla arcoíris.
    // La siguiente declaración genera una representación hexadecimal de una sal de 8 bytes.
    $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));

    // Esto hashea la contraseña con la sal para que se almacene de manera segura en la base de datos.
    // La salida de esta declaración es una cadena hexadecimal de 64 bytes que representa el hash sha256 de 32 bytes de la contraseña. La contraseña original no se puede recuperar del hash.
    $password = hash('sha256', $_POST['password'] . $salt);

    // A continuación, hasheamos el valor del hash 65536 veces más para protegernos contra ataques de fuerza bruta.
    for ($round = 0; $round < 65536; $round++) {
        $password = hash('sha256', $password . $salt);
    }

    if (empty($_POST['token']) || $_POST['token'] != 'Esto123') {
        $rol = 'usuario';
    } else {
        $rol = 'admin';
    }


    // Aquí preparamos los tokens para su inserción en la consulta SQL.
    // No almacenamos la contraseña original, solo su versión hasheada. Almacenamos la sal en forma de texto sin formato.
    $query_params = array(
        ':username' => $_POST['username'],
        ':password' => $password,
        ':salt' => $salt,
        ':email' => $_POST['email'],
        ':rol' => $rol
    );

    try {
        // Ejecutamos la consulta para crear el usuario.
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    } catch (PDOException $ex) {
        // Nota: En un sitio web de producción, no debes mostrar $ex->getMessage().
        // Puede proporcionar información útil sobre tu código a un atacante.
        die("Error al ejecutar la consulta: " . $ex->getMessage());
    }

    // Esto redirige al usuario de nuevo a la página de inicio de sesión después de registrarse.
    header("Location: index.php");

    // Llamar a die o exit después de realizar una redirección utilizando la función header es crítico.
    // El resto de tu script PHP continuará ejecutándose y se enviará al usuario si no mueres o sales.
    die("Redirigiendo a index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body bg-dark">
                        <h1 class="card-title text-center text-white">Registro</h1>
                        <form action="register.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label text-white">Nombre de Usuario:</label>
                                <input type="text" class="form-control" id="username" name="username" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label text-white">Correo Electrónico:</label>
                                <input type="text" class="form-control" id="email" name="email" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label text-white">Contraseña:</label>
                                <input type="password" class="form-control" id="password" name="password" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="token" class="form-label text-white">Token:</label>
                                <input type="password" class="form-control" id="token" name="token" value="" required>
                            </div>
                            <div class="text-center text-white">
                                <input type="submit" value="Registrar" class="btn btn-primary">
                            </div>
                            <a href="login.php" class="text-white"> volver </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>