<?php
// conexion a la base de datos
include 'tool_db.php';
include 'tool_longtext.php';

// inicializar las variables de entrada y salida
$ficha = isset($_GET['ficha']) ? $_GET['ficha']: null;
$materia = isset($_GET['materia']) ? $_GET['materia']: "";
$fecha = isset($_GET['fecha']) ? $_GET['fecha']: "";
$titulo = isset($_GET['titulo']) ? $_GET['titulo']: "";
$descripcion = isset($_GET['descripcion']) ? $_GET['descripcion']: "";
$integrantes = isset($_GET['integrantes']) ? $_GET['integrantes']: "";
$link = isset($_GET['link']) ? $_GET['link']: "";
$options = "";
$nameficha = "???";

if ($ficha != null) {
    // consulta para obtener los items y generar el tramo HTML
    $stmt = $pdo -> prepare("SELECT id, nombre FROM materias WHERE
        id IN (SELECT materia FROM fichamat WHERE ficha=?)");
    $stmt -> execute([$ficha]);

    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        if ($row['id'] == $materia) {
            $options .= "<option value='" . $row['id'] . "' selected>" .
                $row['nombre'] . "</option>";
        }
        else {
            $options .= "<option value='" . $row['id'] . "'>" .
                $row['nombre'] . "</option>";
        }
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
        <h1 class="titulo">Crear Tarea para <?php echo $nameficha; ?></h1>
        <a href="menu.php?ficha=<?php echo $ficha; ?>" class="btn">Cancelar Creación de Tarea</a>
    </header>

    <!-- formulario -->
    <form action="accion_nueva_tarea.php" method="post"  autocomplete="off">
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
            <label for="fecha">Fecha de Entrega Final:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>"
                autocomplete="off" required>
        </div>
        <div>
            <label for="titulo">Título para la Tarea:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo $titulo; ?>"
                autocomplete="off" required>
        </div>
        <div>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="9" autocomplete="off" required
                ><?php echo Salto_to_nr($descripcion); ?></textarea>
        </div>
        <div>
            <label for="integrantes">Número de Integrantes:</label>
            <input type="number" id="integrantes" name="integrantes" value="<?php echo $integrantes; ?>"
                autocomplete="off" required>
        </div>
        <div>
            <label for="link">Link (opcional):</label>
            <input type="url" id="link" name="link" value="<?php echo $link; ?>" autocomplete="off">
        </div>
        <input type="hidden" name="ficha" value="<?php echo $ficha ?>">
        <button type="submit" class="btn">Crear Tarea</button>
    </form>

</body>
</html>
