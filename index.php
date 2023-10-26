<?php
require("common.php");
include 'config.php';
include 'conexion.php';

// Verifica si se ha enviado una solicitud de búsqueda
if (isset($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $query = "SELECT * FROM `productos` WHERE modelo LIKE :busqueda OR descripcion LIKE :busqueda";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':busqueda', "%$busqueda%", PDO::PARAM_STR);
    $stmt->execute();
    $listaProductos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Si no se ha realizado una búsqueda, muestra todos los productos
    $sentencia = $pdo->prepare('SELECT * FROM `productos`');
    $sentencia->execute();
    $listaProductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar'])) {
        // En la parte superior de la página verificamos si el usuario ha iniciado sesión o no
        if (empty($_SESSION['user'])) {
            // Si no han iniciado sesión, los redirigimos a la página de inicio de sesión.
            header("Location: login.php");

            // Recuerda que esta declaración die es absolutamente crítica. Sin ella,
            // las personas pueden ver tu contenido solo para miembros sin iniciar sesión.
            die("Redirigiendo a login.php");
        }
        // Agrega el producto al carrito
        $productoId = $_POST['producto_id'];
        $_SESSION['carrito'][] = $productoId;

        echo "<script>alert('SE AGREGO AL CARRITO')</script>";

        //echo "<script>alert('" . implode(",", $_SESSION['carrito']) . "');</script>";
    }
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

    // Suma la cantidad de cada producto en el carritO
    $cantidadTotalProductos = array_sum($contadorProductos);

}else {
    $cantidadTotalProductos = 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Tienda</title>
</head>

<body>

    <nav class="navbar navbar-expand-sm navbar-dark px-4" style="background-color: #000;">
        <a class="navbar-brand" href="#">Logo empresa</a>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation"></button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav me-auto mt-2 mt-lg-2">
                <li class="nav-item">
                    <a class="nav-link active" href="#" aria-current="page">Home <span class="visually-hidden">(current)</span></a>
                </li>
                
                <?php
                // El código para verificar si el usuario está logeado permanece igual
                try {
                    if (empty($_SESSION['user'])) {
                        echo '<a class="nav-link" href="login.php"><i class="fas fa-user"></i> Iniciar Sesión</a>';
                    } else {
                        echo '<a class="nav-link" href="#"><i class="fas fa-user"></i>' . htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') . '</a>';
                        // Comprueba si el rol del usuario es "admin"
                        if ($_SESSION['user']['rol'] == 'admin') {

                            echo '<a class="nav-link" href="admin_dashboard.php">Panel de Administrador</a>';
                        }

                        echo '<a class="nav-link" href="carrito.php">Carrito ( ' . htmlentities($cantidadTotalProductos, ENT_QUOTES, 'UTF-8') . ' )</a>';
                        echo '<a class="nav-link" href="memberlist.php">Memberlist</a><br />';
                        echo '<a class="nav-link" href="edit_account.php">Edit Account</a><br />';
                        echo '<a class="nav-link" href="logout.php">Logout</a>';
                    }
                } catch (\Throwable $th) {
                    // Manejar errores aquí
                }
                ?>
            </ul>
            <form class="d-flex my-2 my-lg-0">
                <input class="form-control mx-2" type="text" name="busqueda" placeholder="Buscar">
                <button class="btn btn-outline-success" type="submit">Buscar</button>
            </form>
        </div>
    </nav>

    </br>
    <div class="container">
        <div class="alert alert-success">
            Pantalla de mensaje...
            <a href="carrito.php" class="btn btn-success">Ver Carrito</a>
        </div>

        <!-- Resultados de la búsqueda -->
        <div id="resultados-busqueda"></div>

        <div class="row">
            <?php foreach ($listaProductos as $producto) { ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card mb-4">
                        <img class="card-img-top" src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['modelo']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $producto['modelo']; ?></h5>
                            <p class="card-text"><?php echo $producto['descripcion']; ?></p>
                            <h6 class="card-subtitle mb-2 text-muted">$<?php echo $producto['precio']; ?></h6>
                            <form method="POST" action="index.php">
                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                <button class="btn btn-primary" name="agregar" type="submit">
                                    Agregar al carrito
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

</body>

</html>