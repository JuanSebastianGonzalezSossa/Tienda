<?php
require("common.php");

// En la parte superior de la página verificamos si el usuario ha iniciado sesión o no
if(empty($_SESSION['user'])) 
{ 
    // Si no han iniciado sesión, los redirigimos a la página de inicio de sesión.
    header("Location: login.php"); 

    // Recuerda que esta declaración die es absolutamente crítica. Sin ella,
    // las personas pueden ver tu contenido solo para miembros sin iniciar sesión.
    die("Redirigiendo a login.php"); 
} 

// Consulta para obtener todos los productos
$query = "SELECT * FROM productos";
$stmt = $db->query($query);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['success'])) {
    $success_message = $_GET['success'];
    echo "<script>alert('$success_message')</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se envió el formulario para agregar un nuevo producto
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen = $_POST['imagen'];

    // Realizar una consulta para insertar el nuevo producto
    $insert_query = "INSERT INTO productos (marca, modelo, descripcion, precio, imagen) VALUES (:marca, :modelo, :descripcion, :precio, :imagen)";
    $insert_stmt = $db->prepare($insert_query);
    $insert_stmt->bindValue(':marca', $marca);
    $insert_stmt->bindValue(':descripcion', $descripcion);
    $insert_stmt->bindValue(':modelo', $modelo);
    $insert_stmt->bindValue(':precio', $precio);
    $insert_stmt->bindValue(':imagen', $imagen);

    if ($insert_stmt->execute()) {
        echo "<script>alert('Producto agregado exitosamente')</script>";
    } else {
        $error_message = "Error al agregar el producto";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body style="background-color: #222; color: white;">

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h2>Listado de Productos</h2>
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto) : ?>
                            <tr>
                                <td><?php echo $producto['marca']; ?></td>
                                <td><?php echo $producto['modelo']; ?></td>
                                <td><?php echo $producto['descripcion']; ?></td>
                                <td><?php echo $producto['precio']; ?></td>
                                <td>
                                    <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h2>Agregar Producto</h2>
                </br>
                <form method="post" action="admin_dashboard.php">
                <div class="form-group">
                        <label for="marca">Marca: </label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                        </br>
                    <div class="form-group">
                        <label for="modelo">Model: </label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    </br>
                    <div class="form-group">
                        <label for="descripcion">Descripción: </label>
                        <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                    </div>
                    </br>
                    <div class="form-group">
                        <label for="precio">Precio: </label>
                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
                    </div>
                    </br>
                    <div class="form-group">
                        <label for="imagen">Imagen: </label>
                        <input type="text" class="form-control" id="imagen" name="imagen" step="0.01" required>
                    </div>
                    </br>
                    <button type="submit" class="btn btn-success">Agregar Producto</button>
                </form>
                <?php if (isset($error_message)) : ?>
                    <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
                <?php endif; ?>
                </br>
                <a href="index.php" class="text-white">Volver</a>
            </div>
        </div>
    </div>

</body>

</html>