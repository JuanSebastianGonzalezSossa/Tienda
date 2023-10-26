<?php
require("common.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Realizar una consulta para eliminar el producto de la base de datos
    $delete_query = "DELETE FROM productos WHERE id = :id";
    $delete_stmt = $db->prepare($delete_query);
    $delete_stmt->bindValue(':id', $id);

    if ($delete_stmt->execute()) {
        $success_message = "Producto eliminado exitosamente";
        header("Location: admin_dashboard.php?success=" . urlencode($success_message));
        die("Producto no encontrado");
    } else {
        $error_message = "Error al eliminar el producto";
    }
} else {
    header("Location: admin.php");
    die("ID del producto no especificado");
}
?>
