<?php

require("common.php");

if (empty($_SESSION['user'])) {
    // Si no han iniciado sesión, los redirigimos a la página de inicio de sesión.
    header("Location: login.php");

    echo "<script>alert('Error !DEBES INICIAR SESION PARA ESTA ACCION!...')</script>";

    // Recuerda que esta declaración die es absolutamente crítica. Sin ella,
    // las personas pueden ver tu contenido solo para miembros sin iniciar sesión.
    die("Redirigiendo a login.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'])) {
        $productoId = $_POST['product_id'];

        // Verifica si el producto está en el carrito
        if (isset($_SESSION['carrito']) && in_array($productoId, $_SESSION['carrito'])) {
            // Encuentra la clave (índice) del producto en el arreglo
            $key = array_search($productoId, $_SESSION['carrito']);

            // Elimina el producto del carrito utilizando la clave
            unset($_SESSION['carrito'][$key]);

            $success_message = "El producto se eliminó del carrito con éxito";
        } else {
            $error_message = "El producto no se encontraba en el carrito";
        }

        // Después de eliminar el producto, puedes redirigir al usuario de regreso a la página del carrito
        $success_message = "SE ELIMINO PRODUCTO";
        header("Location: carrito.php?success=" . urlencode($success_message));
        die("COMPRA FINALIZADA CON EXITO");
    }
}
