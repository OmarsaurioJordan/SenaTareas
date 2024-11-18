<?php
// Conexion a la base de datos
include 'db.php';

// inicializar las variables de entrada y salida
$ficha = isset($_GET['ficha']) ? $_GET['ficha']: null;
$options = "";

if ($ficha != null) {
    // Consulta para obtener los items y generar el tramo HTML
    $stmt = $pdo -> query("SELECT id, nombre FROM materias WHERE
        id IN (SELECT materia FROM fichamat WHERE ficha='$ficha')");

    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $options .= "<option value='" . $row['id'] . "'>" .
            $row['nombre'] . "</option>";
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
        <a href="menu.php" class="btn">Cancelar Creación de Tarea</a>
    </header>

    <!-- Formulario -->
    <form action="db_crear.php" method="post"  autocomplete="off">
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
            <input type="date" id="fecha" name="fecha"  autocomplete="off" required>
        </div>
        <div>
            <label for="titulo">Título para la Tarea:</label>
            <input type="text" id="titulo" name="titulo"  autocomplete="off" required>
        </div>
        <div>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4"  autocomplete="off" required></textarea>
        </div>
        <div>
            <label for="integrantes">Número de Integrantes:</label>
            <input type="number" id="integrantes" name="integrantes"  autocomplete="off" required>
        </div>
        <button type="submit" class="btn">Crear Tarea</button>
    </form>

</body>
</html>
