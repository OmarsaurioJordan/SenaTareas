<?php
// conexion a la base de datos
include 'tool_db.php';

// inicializar las variables de entrada y salida
$ficha = isset($_GET['ficha']) ? $_GET['ficha']: null;
$tarea = isset($_GET['tarea']) ? $_GET['tarea']: null;
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
        <h1 class="titulo">Tarea de <?php echo $nameficha; ?></h1>
        <h1 class="titulo">Tarea de <?php echo $nameficha; ?></h1>
        <a href="tarea_lista.php?ficha=<?php echo $ficha; ?>" class="btn">Volver al Listado</a>
    </header>
    
    <!-- informacion de la tarea -->
    <section class="item-list">
        <?php
        include 'tool_grupos.php';
        include 'tool_fecha.php';
        include 'tool_longtext.php';
        // Consulta para obtener la informacion
        $stmt = $pdo -> prepare("SELECT tareas.titulo, tareas.integrantes,
            tareas.fecha, materias.nombre AS matname, tareas.descripcion
            FROM tareas JOIN materias ON tareas.materia = materias.id
            WHERE tareas.id=? AND ficha=?");
        $stmt -> execute([$tarea, $ficha]);
        // se pondra la informacion de la tarea
        $results = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $row) {
                echo "<div class='item'>";
                echo "<p><strong>" . $row['titulo'] . "</strong></p>";
                echo "<p><u>" . $row['matname'] . "</u>, " . IntegrToTxt($row['integrantes']);
                echo "<p class='parrafo'>" . Salto_to_br($row['descripcion']) . "</p>";
                echo "<p>" . FormatoDate($row['fecha']) . "</p>";
                echo "</div>";
                break;
            }
        }
        else {
            echo "<p>No se encontró la tarea</p>";
        }
        ?>
    </section>

    <?php
    // agregar el boton para administrar la tarea
    if (count($results) > 0) {
        echo "<br>";
        echo "<form action='etc.php' method='post'  autocomplete='off'>";
        echo "<div>";
        echo "<a href='tarea_modificar.php?ficha=$ficha&tarea=$tarea" .
            "' class='btn' >Administrar Tarea</a>";
        echo "</div>";
        echo "</form>";
    }
    ?>

</body>
</html>
