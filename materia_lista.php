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
        <h1 class="titulo">Materias de <?php echo $nameficha; ?></h1>
        <a href="menu.php?ficha=<?php echo $ficha; ?>" class="btn">Volver al menú</a>
    </header>
    
    <!-- listado de materias -->
    <section class="item-list">
        <?php
        include 'tool_fecha.php';
        // obtener el listado de tareas ordenado por fecha
        $stmt = $pdo -> prepare("SELECT tareas.id, tareas.titulo,
            tareas.fecha, materias.id AS matid
            FROM tareas JOIN materias ON tareas.materia = materias.id
            WHERE ficha=? AND fecha >= CURDATE()
            ORDER BY fecha ASC");
        $stmt -> execute([$ficha]);
        $tareis = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        // obtener todas las materias ordenadas segun sus tareas pendientes
        $stmt = $pdo -> prepare(
            "SELECT materias.id, materias.nombre, COUNT(tareas.id) AS tot_tar,
            COUNT(CASE WHEN tareas.fecha >= CURDATE() THEN 1 END) AS pen_tar
            FROM materias JOIN fichamat ON materias.id = fichamat.materia
            LEFT JOIN tareas ON tareas.ficha = fichamat.ficha AND
            tareas.materia = materias.id WHERE fichamat.ficha=?
            GROUP BY materias.id, materias.nombre ORDER BY pen_tar DESC"
        );
        $stmt -> execute([$ficha]);
        // hacer ciclo para cada materia pintar una entrada
        $results = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                echo "<div class='item'>";
                echo "<p><strong>" . $row['nombre'] . "</strong></p>";
                // para cada materia pondra la lista de tareas pendientes
                if (count($tareis) > 0) {
                    foreach ($tareis as $t) {
                        if ($row['id'] == $t['matid']) {
                            echo "<p><a href='tarea_tarea.php?ficha=" . $ficha .
                                "&tarea=" . $t['id'] . "&rematerias=1'>" .
                                "<u>" . $t['titulo'] . "</u></a> (" .
                                Los_dias($t['fecha']) . " días)</p>";
                        }
                    }
                }
                echo "<p>Tareas pendientes: " . $row['pen_tar'] . " / " .
                    $row['tot_tar'] . "</p>";
                echo "</div>";
            }
        }
        else {
            echo "<p>No hay materias asociadas</p>";
        }
        ?>
    </section>

</body>
</html>
