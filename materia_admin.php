<?php
// conexion a la base de datos
include 'tool_db.php';

// inicializar las variables de entrada y salida
$ficha = isset($_GET['ficha']) ? $_GET['ficha']: null;
$options = "";
$nameficha = "???";

if ($ficha != null) {
    // consulta para obtener los items y generar el tramo HTML
    $stmt = $pdo -> prepare("SELECT id, nombre FROM materias WHERE
        id IN (SELECT materia FROM fichamat WHERE ficha=?)");
    $stmt -> execute([$ficha]);

    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $options .= "<option value='" . $row['id'] . "'>" .
            $row['nombre'] . "</option>";
    }

    // obtener el nombre de la ficha
    $stmt = $pdo -> prepare("SELECT nombre FROM fichas WHERE id=?");
    $stmt -> execute([$ficha]);
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $nameficha = $row['nombre'];
        break;
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

    <!-- boton de regreso al menu principal -->
    <header>
        <h1 class="titulo">Administrar Materias para <?php echo $nameficha; ?></h1>
        <a href="menu.php?ficha=<?php echo $ficha; ?>" class="btn">Volver al Menú</a>
    </header>

    <!-- formulario creacion -->
    <form action="accion_materia.php" method="post"  autocomplete="off">
        <div>
            <label for="password">Contraseña de <?php echo $nameficha; ?>:</label>
            <input type="password" id="password" name="password"  autocomplete="off" required>
        </div>
        <div>
            <label for="nombre">Nombre para la Nueva Materia:</label>
            <input type="text" id="nombre" name="nombre" autocomplete="off" required>
        </div>
        <input type="hidden" name="tipo" value="C">
        <button type="submit" class="btn">Crear Materia</button>
    </form>

    <!-- formulario actualizacion -->
    <form action="accion_materia.php" method="post"  autocomplete="off">
        <div>
        <label for="password">Contraseña de <?php echo $nameficha; ?>:</label>
        <input type="password" id="password" name="password"  autocomplete="off" required>
        </div>
        <div>
            <label for="materia">Materia:</label>
            <select id="materia" name="materia" autocomplete="off" required>
                <?php echo $options; ?>
            </select>
        </div>
        <div>
            <label for="nombre">Nuevo Nombre para la Materia:</label>
            <input type="text" id="nombre" name="nombre" autocomplete="off">
        </div>
        <input type="hidden" name="tipo" value="U">
        <button type="submit" class="btn">Actualizar Materia</button>
    </form>

    <!-- formulario eliminacion -->
    <form action="accion_materia.php" method="post"  autocomplete="off">
        <div>
            <label for="password">Contraseña de <?php echo $nameficha; ?>:</label>
            <input type="password" id="password" name="password"  autocomplete="off" required>
        </div>
        <div>
            <label for="materia">Materia:</label>
            <select id="materia" name="materia" autocomplete="off" required>
                <?php echo $options; ?>
            </select>
        </div>
        <input type="hidden" name="tipo" value="D">
        <button type="submit" class="btn">Eliminar Materia</button>
    </form>

</body>
</html>
