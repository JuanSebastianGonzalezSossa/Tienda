<?php

// Primero ejecutamos nuestro código común para conectarnos a la base de datos y comenzar la sesión
require("common.php");

// Esta variable se utilizará para volver a mostrar el nombre de usuario del usuario en el
// formulario de inicio de sesión si no ingresaron la contraseña correcta. Se inicializa aquí
// con un valor vacío, que se mostrará si el usuario no ha enviado el formulario.
$submitted_username = '';

// Esta declaración "if" verifica si el formulario de inicio de sesión se ha enviado.
// Si lo ha sido, se ejecuta el código de inicio de sesión, de lo contrario se muestra el formulario.
if (!empty($_POST)) {
    // Esta consulta recupera la información del usuario de la base de datos utilizando
    // su nombre de usuario.
    $query = " 
        SELECT 
            id, 
            username, 
            password, 
            salt, 
            email 
        FROM users 
        WHERE 
            username = :username 
    ";

    // Los valores de los parámetros
    $query_params = array(
        ':username' => $_POST['username']
    );

    try {
        // Ejecutar la consulta en la base de datos
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    } catch (PDOException $ex) {
        // Nota: En un sitio web de producción, no debes mostrar $ex->getMessage().
        // Puede proporcionar información útil sobre tu código a un atacante.
        die("Error al ejecutar la consulta: " . $ex->getMessage());
    }

    // Esta variable nos indica si el usuario ha iniciado sesión correctamente o no.
    // La inicializamos en "false", asumiendo que no lo han hecho.
    // Si determinamos que han ingresado los detalles correctos, la cambiamos a "true".
    $login_ok = false;

    // Recupera los datos del usuario de la base de datos. Si $row es "false", el nombre de usuario
    // que ingresaron no está registrado.
    $row = $stmt->fetch();
    if ($row) {
        // Utilizando la contraseña enviada por el usuario y la sal almacenada en la base de datos,
        // verificamos si las contraseñas coinciden, hasheando la contraseña enviada
        // y comparándola con la versión hasheada ya almacenada en la base de datos.
        $check_password = hash('sha256', $_POST['password'] . $row['salt']);
        for ($round = 0; $round < 65536; $round++) {
            $check_password = hash('sha256', $check_password . $row['salt']);
        }

        if ($check_password === $row['password']) {
            // Si coinciden, cambiamos esto a "true".
            $login_ok = true;
        }
    }

    // Si el usuario ha iniciado sesión correctamente, los redirigimos a la página privada solo para miembros.
    // De lo contrario, mostramos un mensaje de inicio de sesión fallido y volvemos a mostrar el formulario de inicio de sesión.
    if ($login_ok) {
        // Aquí me estoy preparando para almacenar el array $row en $_SESSION eliminando la sal y la contraseña de él.
        // Aunque $_SESSION se almacena en el servidor, no hay razón para almacenar valores sensibles en él a menos que sea necesario.
        // Por lo tanto, es una buena práctica eliminar estos valores sensibles primero.
        unset($row['salt']);
        unset($row['password']);

        // Agregamos un código SQL para obtener el rol del usuario
        $rol_query = "SELECT rol FROM users WHERE id = :user_id";
        $rol_query_params = array(
            ':user_id' => $row['id']
        );

        try {
            $rol_stmt = $db->prepare($rol_query);
            $rol_result = $rol_stmt->execute($rol_query_params);
        } catch (PDOException $ex) {
            die("Error al ejecutar la consulta de rol: " . $ex->getMessage());
        }

        // Recuperamos el rol y lo asignamos a la variable $rol
        $rol_row = $rol_stmt->fetch();
        $rol = $rol_row['rol'];

        // Almacenamos los datos del usuario en la sesión con el índice 'user',
        // incluyendo el rol
        $_SESSION['user'] = array(
            'username' => $row['username'], // Otras propiedades del usuario
            'rol' => $rol // Rol obtenido de la base de datos
        );

        // Redirigir al usuario a la página privada solo para miembros.
        header("Location: index.php");
        die("Redireccionando a: index.php");
    } else {
        // Decir al usuario que ha fallado el inicio de sesión.
        print("Inicio de Sesión Fallido.");

        // Mostrarles su nombre de usuario nuevamente para que solo tengan que ingresar una nueva contraseña.
        // El uso de htmlentities previene ataques XSS. Siempre debes usar htmlentities en valores enviados por usuarios
        // antes de mostrarlos a cualquier usuario (incluido el usuario que los envió). Para obtener más información:
        // http://en.wikipedia.org/wiki/XSS_attack
        $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body bg-dark"> <!-- Agregamos la clase "bg-dark" para el fondo negro -->
                        <h4 class="card-title text-center text-white">Iniciar Sesión</h4> <!-- Texto blanco -->
                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label text-white">Nombre de Usuario:</label> <!-- Texto blanco -->
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo $submitted_username; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="passwd" class="form-label text-white">Contraseña:</label> <!-- Texto blanco -->
                                <input type="password" class="form-control" id="password" name="password" value="" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" value="Iniciar Sesión" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="register.php" class="text-white">¿No tienes una cuenta? Regístrate aquí</a> <!-- Texto blanco -->
                        </div>
                        </br>
                        <a href="index.php" class="text-white">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>