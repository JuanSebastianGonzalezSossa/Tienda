<?php
require("common.php");

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "El carrito está vacío.";
} else {
    // Aquí puedes agregar la lógica para finalizar la compra, como guardar en una base de datos, enviar un correo, etc.
    // Luego, puedes limpiar el carrito de compras:
    unset($_SESSION['carrito']);

    $success_message = "COMPRA FINALIZADA CON EXITO";
    header("Location: carrito.php?success=" . urlencode($success_message));
    die("COMPRA FINALIZADA CON EXITO");
         
}
?>