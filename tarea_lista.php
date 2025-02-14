<?php
// conexion a la base de datos
include 'tool_db.php';

// inicializar las variables de entrada y salida
$ficha = isset($_GET['ficha']) ? $_GET['ficha']: null;
$nameficha = "???";

if ($ficha != null) {
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

    <!-- titulo principal -->
    <header>
        <h1 class="titulo">Tareas para <?php echo $nameficha; ?></h1>
        <a href="menu.php?ficha=<?php echo $ficha; ?>" class="btn">Volver al menú</a>
    </header>
    
    <!-- listado de tareas pendientes -->
    <section class="item-list">
        <?php
        include 'tool_grupos.php';
        include 'tool_fecha.php';
        // Consulta para obtener los items
        $stmt = $pdo -> prepare("SELECT tareas.id, tareas.titulo, tareas.integrantes,
            tareas.fecha, materias.nombre AS matname
            FROM tareas JOIN materias ON tareas.materia = materias.id
            WHERE ficha=? AND fecha >= CURDATE()
            ORDER BY fecha ASC");
        $stmt -> execute([$ficha]);
        // se hara un ciclo para poner todas las entradas
        $results = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                echo "<div class='item'>";
                echo "<p><a href='tarea_tarea.php?ficha=" . $ficha .
                    "&tarea=" . $row['id'] . "'>" .
                    "<strong>" . $row['titulo'] . "</strong></a></p>";
                echo "<p><u>" . $row['matname'] . "</u>, " . IntegrToTxt($row['integrantes']);
                echo "<p>" . FormatoDate($row['fecha']) . "</p>";
                echo "</div>";
            }
        }
        else {
            echo "<p>No hay tareas pendientes</p>";
        }
        ?>
    </section>

    <!-- formulario eliminar antiguas -->
    <form action="accion_limpiar_tareas.php" method="post"  autocomplete="off">
    <div>
        <label for="password">Contraseña de <?php echo $nameficha; ?>:</label>
        <input type="password" id="password" name="password"  autocomplete="off" required>
    </div>
    <input type="hidden" name="ficha" value="<?php echo $ficha ?>">
    <button type="submit" class="btn">Eliminar Antiguas</button>
    </form>

    <!-- listado de tareas antiguas -->
    <section class="item-list">
        <?php
        // consulta para obtener los items
        $stmt = $pdo -> prepare("SELECT tareas.id, tareas.titulo, tareas.integrantes,
            tareas.fecha, materias.nombre AS matname
            FROM tareas JOIN materias ON tareas.materia = materias.id
            WHERE ficha=? AND fecha < CURDATE()
            ORDER BY fecha ASC");
        $stmt -> execute([$ficha]);
        // se hara un ciclo para poner todas las entradas
        $results = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                echo "<div class='item'>";
                echo "<p><a href='tarea_tarea.php?ficha=" . $ficha .
                    "&tarea=" . $row['id'] . "'>";
                echo "<strong>" . $row['titulo'] . "</strong></a></p>";
                echo "<p><u>" . $row['matname'] . "</u>, " . IntegrToTxt($row['integrantes']);
                echo "<p>" . FormatoDate($row['fecha']) . "</p>";
                echo "</div>";
            }
        }
        else {
            echo "<p>No hay tareas antiguas</p>";
        }
        ?>
    </section>

</body>
</html>
