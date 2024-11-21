<?php
include 'tool_db.php';
// commands git - github
/*
cd carpeta_proyecto_path
git add .
git commit -m "su texto rancio"
git push origin main
*/

// inicializar las variables de entrada y salida
$password = isset($_POST['password']) ? $_POST['password']: null;
$id = isset($_POST['id']) ? $_POST['id']: null;
$nombre = isset($_POST['nombre']) ? $_POST['nombre']: null;
$passficha = isset($_POST['passficha']) ? $_POST['passficha']: null;
$tipo = isset($_POST['tipo']) ? $_POST['tipo']: null;
$mensaje = "";

if ($passficha != null) {
    if ($passficha != "") {
        $passficha = password_hash($passficha, PASSWORD_DEFAULT);
    }
}

// hacer alguna accion administrativa
if ($tipo != null) {
    
    // verificar la clave maestra
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $stmt = $pdo -> prepare("SELECT password FROM master LIMIT 1");
        $stmt -> execute([]);
        $the_password = $stmt -> fetchColumn();

        // depurar las acciones
        $mensaje = "clave incorrecta...";
        if (password_verify($password, $the_password)) {
            switch ($tipo) {

                // nota: el nombre es UNIQUE en la DB asi no requiere codigo de comprobaciones

                case "C":
                    $stmt = $pdo -> prepare("INSERT INTO fichas (nombre,
                        password) VALUES (?, ?)");
                    if ($stmt -> execute([$nombre, $passficha])) {
                        $mensaje = "Ficha Creada!!!";
                    }
                    break;
                
                case "U":
                    $ok = true;
                    if ($nombre != "" && $passficha != "") {
                        $stmt = $pdo -> prepare("UPDATE fichas SET nombre=?,
                            password=? WHERE id=?");
                        $stmt -> execute([$nombre, $passficha, $id]);
                    }
                    else if ($nombre != "") {
                        $stmt = $pdo -> prepare("UPDATE fichas SET nombre=?
                            WHERE id=?");
                        $stmt -> execute([$nombre, $id]);
                    }
                    else if ($passficha != "") {
                        $stmt = $pdo -> prepare("UPDATE fichas SET password=?
                            WHERE id=?");
                        $stmt -> execute([$passficha, $id]);
                    }
                    else {
                        $ok = false;
                        $mensaje = "nada que actualizar...";
                    }
                    if ($ok) {
                        if ($stmt == true) {
                            $mensaje = "Ficha Actualizada!!!";
                        }
                    }
                    break;
                
                case "D":
                    try {
                        $pdo -> beginTransaction();
                        // eliminar la ficha, esto deberia eliminar tareas y conexiones
                        // con materias dado que son tipo CASCADE
                        $stmt = $pdo -> prepare("DELETE FROM fichas WHERE id=?");
                        $stmt -> execute([$id]);
                        // eliminar materias que no tengan conexion a fichas
                        $stmt = $pdo -> prepare("DELETE FROM materias WHERE id NOT IN
                            (SELECT materia FROM fichamat)");
                        $stmt -> execute([]);
                        // ejecutar todas las operaciones
                        $pdo -> commit();
                        $mensaje = "Ficha Eliminada!!!";
                    }
                    catch (Exception $e) {
                        $pdo -> rollBack();
                    }
                    break;
            }
        }
    }
}

// cargar el listado de fichas
$data = [];
$stmt = $pdo -> prepare("SELECT id, nombre FROM fichas");
$stmt -> execute([]);
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $data[] = "<li><strong>" . $row['id'] . ":</strong> " .
        $row['nombre'] . "</li>";
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
        <h1 class="titulo">Administración de Fichas</h1>
        <a href="menu.php" class="btn">Menú Principal</a>
        <?php
        if ($mensaje != "") {
            echo "<p class='txt_fondo'>$mensaje</p>";
        }
        ?>
    </header>

    <!-- formulario creacion -->
    <form action="master.php" method="post"  autocomplete="off">
        <div>
            <label for="password">Contraseña Maestra:</label>
            <input type="password" id="password" name="password"  autocomplete="off" required>
        </div>
        <div>
            <label for="nombre">Nombre para la Nueva Ficha:</label>
            <input type="text" id="nombre" name="nombre" autocomplete="off" required>
        </div>
        <div>
            <label for="passficha">Contraseña para la Ficha:</label>
            <input type="password" id="passficha" name="passficha" autocomplete="off" required>
        </div>
        <input type="hidden" name="tipo" value="C">
        <button type="submit" class="btn">Crear Ficha</button>
    </form>

    <!-- formulario actualizacion -->
    <form action="master.php" method="post"  autocomplete="off">
        <div>
            <label for="password">Contraseña Maestra:</label>
            <input type="password" id="password" name="password"  autocomplete="off" required>
        </div>
        <div>
            <label for="id">ID de la Ficha:</label>
            <input type="number" id="id" name="id" autocomplete="off" required>
        </div>
        <div>
            <label for="nombre">Nuevo Nombre para la Ficha (vacío No Cambia):</label>
            <input type="text" id="nombre" name="nombre" autocomplete="off">
        </div>
        <div>
            <label for="passficha">Nueva Contraseña para la Ficha (vacía No Cambia):</label>
            <input type="password" id="passficha" name="passficha" autocomplete="off">
        </div>
        <input type="hidden" name="tipo" value="U">
        <button type="submit" class="btn">Actualizar Ficha</button>
    </form>

    <!-- formulario eliminacion -->
    <form action="master.php" method="post"  autocomplete="off">
        <div>
            <label for="password">Contraseña Maestra:</label>
            <input type="password" id="password" name="password"  autocomplete="off" required>
        </div>
        <div>
            <label for="id">ID de la Ficha:</label>
            <input type="number" id="id" name="id" autocomplete="off" required>
        </div>
        <input type="hidden" name="tipo" value="D">
        <button type="submit" class="btn">Eliminar Ficha</button>
    </form>

    <!-- mostrar la lista de fichas -->
    <h1 class="titulo">Listado de Fichas</h1>
    <?php
    if (empty($data)) {
        echo "<p class='txt_fondo'>No hay fichas...</p>";
    }
    else {
        echo "<ul class='txt_fondo'>";
        foreach ($data as $d) {
            echo $d;
        }
        echo "</ul>";
    }
    ?>

</body>
</html>
