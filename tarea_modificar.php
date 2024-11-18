<?php
// Conexion a la base de datos
include 'db.php';

// inicializar las variables de entrada y salida
$ficha = isset($_GET['ficha']) ? $_GET['ficha']: null;
$tarea = isset($_GET['tarea']) ? $_GET['tarea']: null;
$options = "";
// id, ficha, materia, fecha, titulo, descripcion, integrantes
$data = ["-1", "", "", "", "", "", ""];

if ($ficha != null && $tarea != null) {
    // Consulta para obtener toda la informacion de la tarea
    $stmt = $pdo -> query("SELECT id, ficha, materia, fecha, titulo,
        descripcion, integrantes FROM tareas WHERE id='$tarea'");
    if ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        // Asignar los resultados de la consulta al array $data
        $data[0] = $row['id'];
        $data[1] = $row['ficha'];
        $data[2] = $row['materia'];
        $data[3] = $row['fecha'];
        $data[4] = $row['titulo'];
        $data[5] = $row['descripcion'];
        $data[6] = $row['integrantes'];
    }

    // Consulta para obtener los items y generar el tramo HTML
    $stmt = $pdo -> query("SELECT id, nombre FROM materias WHERE
        id IN (SELECT materia FROM fichamat WHERE ficha='$ficha')");

    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        if ($row['id'] == $data[2]) {
            $options .= "<option value='" . $row['id'] . "' selected>" .
                $row['nombre'] . "</option>";
        }
        else {
            $options .= "<option value='" . $row['id'] . "'>" .
                $row['nombre'] . "</option>";
        }
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

    <!-- Boton de regreso al menu principal -->
    <header>
        <a href="menu.php" class="btn">Cancelar Modificación de Tarea</a>
    </header>

    <!-- Formulario -->
    <form action="db_modificar.php" method="post"  autocomplete="off">
        <div>
            <label for="password">Contraseña de la Ficha:</label>
            <input type="password" id="password" name="password"  autocomplete="off" required>
        </div>
        <div>
            <label for="materia">Materia:</label>
            <select id="materia" name="materia" autocomplete="off" required>
                <?php echo $options; ?>
            </select>
        </div>
        <div>
            <label for="fecha">Fecha de Entrega Final:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $data[3]; ?>"
                autocomplete="off" required>
        </div>
        <div>
            <label for="titulo">Título para la Tarea:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo $data[4]; ?>"
                autocomplete="off" required>
        </div>
        <div>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" autocomplete="off" required
                ><?php echo $data[5]; ?></textarea>
        </div>
        <div>
            <label for="integrantes">Número de Integrantes:</label>
            <input type="number" id="integrantes" name="integrantes" value="<?php echo $data[6]; ?>"
                autocomplete="off" required>
        </div>
        <input type="hidden" name="tarea" value="<?php echo $data[0] ?>">
        <button type="submit" class="btn">Modificar Tarea</button>
    </form>

    <!-- Formulario -->
    <form action="db_destruir.php" method="post"  autocomplete="off">
    <div>
        <label for="password">Contraseña de la Ficha:</label>
        <input type="password" id="password" name="password"  autocomplete="off" required>
    </div>
    <input type="hidden" name="tarea" value="<?php echo $data[0] ?>">
    <button type="submit" class="btn">Eliminar Tarea</button>
    </form>

</body>
</html>
