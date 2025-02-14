<?php
// conexion a la base de datos
include 'tool_db.php';

// inicializar las variables de entrada y salida
$ficha = isset($_POST['ficha']) ? $_POST['ficha']: null;
$password = isset($_POST['password']) ? $_POST['password']: null;
$okey = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo -> prepare("SELECT password FROM fichas WHERE id=?");
    $stmt -> execute([$ficha]);
    $the_password = $stmt -> fetchColumn();

    // verificar pasword para poder hacer la transaccion
    if (password_verify($password, $the_password)) {
        $stmt = $pdo -> prepare("DELETE FROM tareas WHERE ficha=? AND fecha < CURDATE()");
        $okey = $stmt -> execute([$ficha]);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SenaTareas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- titulo principal -->
    <header>
        <?php
        if ($okey) {
            echo "<h1 class='titulo'>Tareas Antiguas Eliminadas!!!</h1>";
        }
        else {
            echo "<h1 class='titulo'>clave incorrecta...</h1>";
        }
        ?>
    </header>
    
    <!-- boton para redireccionarse -->
    <form action="etc.php" method="post"  autocomplete="off">
        <div>
            <a href="tarea_lista.php?ficha=<?php echo $ficha; ?>" class="btn">Volver</a>
        </div>
    </form>

</body>
</html>
