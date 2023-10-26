<?php 

    // Primero ejecutamos nuestro código común para conectarnos a la base de datos y comenzar la sesión
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
     
    //Lo que está por debajo de este punto en el archivo está asegurado por el sistema de inicio de sesión
     
    // Podemos recuperar una lista de miembros de la base de datos utilizando una consulta SELECT. 
    // En este caso, no tenemos una cláusula WHERE porque queremos seleccionar todos 
    // los registros de la tabla de la base de datos.

    if ($_SESSION['user']['rol'] == 'admin'){
        $query = " 
        SELECT 
            id, 
            username, 
            email 
        FROM users 
        WHERE rol = 'admin'
    "; 
    } else {  
        $query = " 
        SELECT 
            id, 
            username, 
            email 
        FROM users 
        WHERE rol = 'usuario'
    "; 
    }
    
     
    try 
    { 
        // Estas dos declaraciones ejecutan la consulta en la tabla de tu base de datos. 
        $stmt = $db->prepare($query); 
        $stmt->execute(); 
    } 
    catch(PDOException $ex) 
    { 
        // Nota: En un sitio web de producción, no deberías mostrar $ex->getMessage(). 
        // Puede proporcionar a un atacante información útil sobre tu código.  
        die("Error al ejecutar la consulta: " . $ex->getMessage()); 
    } 
         
    // Finalmente, podemos recuperar todas las filas encontradas en un array utilizando fetchAll
    $rows = $stmt->fetchAll(); 
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Miembros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body class="bg-dark"> <!-- Agregamos la clase "bg-dark" para establecer el fondo en negro -->
    <div class="container mt-5">
        <h1 class="text-white">Lista de Miembros</h1> <!-- Texto blanco -->
        <table class="table table-dark"> <!-- Aplicamos estilos de tabla oscuros -->
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Dirección de Correo Electrónico</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $row): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlentities($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlentities($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="text-white">Volver</a> <!-- Texto blanco -->
    </div>
</body>
</html>
