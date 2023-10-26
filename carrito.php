<?php

require("common.php");
include 'config.php';
include 'conexion.php';

if (empty($_SESSION['user'])) {
    // Si no han iniciado sesión, los redirigimos a la página de inicio de sesión.
    header("Location: login.php");

    echo "<script>alert('Error !DEBES INICIAR SESION PARA ESTA ACCION!...')</script>";

    // Recuerda que esta declaración die es absolutamente crítica. Sin ella,
    // las personas pueden ver tu contenido solo para miembros sin iniciar sesión.
    die("Redirigiendo a login.php");
}

if (isset($_GET['success'])) {
    $success_message = $_GET['success'];
    echo "<script>alert('$success_message')</script>";
}

// Verificar si el carrito existe y no está vacío
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {

    $carritoIds = implode(",", $_SESSION['carrito']);
    $query = "SELECT * FROM `productos` WHERE id IN ($carritoIds)";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $productosCarrito = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Aquí puedes mostrar los productos del carrito
} else {
    echo "El carrito está vacío.";
}

if (isset($_SESSION['carrito'])) {
    $carrito = $_SESSION['carrito'];
    $contadorProductos = array();

    foreach ($carrito as $productoId) {
        if (array_key_exists($productoId, $contadorProductos)) {
            $contadorProductos[$productoId]++;
        } else {
            $contadorProductos[$productoId] = 1;
        }
    }

    // Muestra la cantidad de cada producto
    $productosRepetidos = array(); // Arreglo para almacenar la información

    foreach ($contadorProductos as $productoId => $cantidad) {
        $productoRepetidos[$productoId] = $cantidad;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body class="bg-dark text-white">
    <br>
    <div class="container">
        <h2 class="my-4">Carrito de Compras</h2>
        <div id="resultados-carrito">
            <?php
            if (isset($productosCarrito) && !empty($productosCarrito)) {
                echo "<div class='row'>";
                $precioTotal = 0;
                foreach ($productosCarrito as $producto) {
                    $productoId = $producto['id'];

                    echo "<div class='col-md-2'>";
                    echo "<div class='card mb-4'>";
                    echo "<img src='{$producto['imagen']}' alt='{$producto['modelo']}' class='card-img-top'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>{$producto['modelo']}</h5>";
                    echo "<p class='card-text'>{$producto['descripcion']}</p>";
                    echo "<p class='card-text'>$ {$producto['precio']}</p>";
                    echo "<p class='card-text'>X{$productoRepetidos[$productoId]}</p>";
                    // Agregar un formulario con un botón para eliminar el producto
                    echo "<form method='POST' action='eliminar_carrito_producto.php'>";
                    echo "<input type='hidden' name='product_id' value='$productoId'>";
                    echo "<button type='submit' class='btn btn-danger'>Eliminar</button>";
                    echo "</form>";
                    
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";

                    $preciosArray = explode(",", $producto['precio']);
                    $precioProducto = array_sum($preciosArray) * $productoRepetidos[$productoId];
                    $precioTotal += $precioProducto;
                }

                echo "<div class='card-body'>";
                echo "<h5 class='card-title'> Total precio de la compra : </h5>";
                echo "<p class='card-text'>$ {$precioTotal}</p>";
                echo "</div>";
            }
            ?>
        </div>
        <a href="index.php" class="btn btn-primary my-2">Volver al Inicio</a>
        <a href="finalizar_compra.php" class="btn btn-success my-2">Finalizar Compra</a>
    </div>
</body>

</html>