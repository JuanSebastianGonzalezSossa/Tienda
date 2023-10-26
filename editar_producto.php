<?php
require("common.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Comprobar si se ha enviado el formulario de edición
    $id = $_POST['id'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen = $_POST['imagen'];

    // Realizar una consulta para actualizar el producto en la base de datos
    $update_query = "UPDATE productos SET marca = :marca, modelo = :modelo, descripcion = :descripcion, precio = :precio, imagen = :imagen WHERE id = :id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindValue(':marca', $marca);
    $update_stmt->bindValue(':modelo', $modelo);
    $update_stmt->bindValue(':descripcion', $descripcion);
    $update_stmt->bindValue(':precio', $precio);
    $update_stmt->bindValue(':imagen', $imagen);
    $update_stmt->bindValue(':id', $id);

    if ($update_stmt->execute()) {
        $success_message = "Producto actualizado exitosamente";
        header("Location: admin_dashboard.php?success=" . urlencode($success_message));
        die("Producto no encontrado");
    } else {
        $error_message = "Error al actualizar el producto";
    }
} else {
    // Mostrar el formulario de edición
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Realizar una consulta para obtener los detalles del producto
        $query = "SELECT * FROM productos WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            header("Location: admin_dashboard.php");
            die("Producto no encontrado");
        }
    } else {
        header("Location: admin_dashboard.php");
        die("ID del producto no especificado");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body class="bg-dark text-white">
<div class="container mt-5">
        <h2>Editar Producto</h2>
        <form method="post" action="editar_producto.php">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" class="form-control" id="marca" name="marca" value="<?php echo $producto['marca']; ?>" required>
            </div>
            </br>
            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" class="form-control" id="modelo" name="modelo" value="<?php echo $producto['modelo']; ?>" required>
            </div>
            </br>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo $producto['descripcion']; ?></textarea>
            </div>
            </br>
            <div class="form-group">
                <label for="precio">Precio:</label>:
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
            </div>
            </br>
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="text" class="form-control" id="imagen" name="imagen" value="<?php echo $producto['imagen']; ?>" required>
            </div>
            </br>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </div>
</body>

</html>