<?php
// conexion a la base de datos
include 'tool_db.php';

// inicializar las variables de entrada y salida
$ficha = isset($_POST['ficha']) ? $_POST['ficha']: null;
$nombre = isset($_POST['nombre']) ? $_POST['nombre']: null;
$materia = isset($_POST['materia']) ? $_POST['materia']: null;
$password = isset($_POST['password']) ? $_POST['password']: null;
$tipo = isset($_POST['tipo']) ? $_POST['tipo']: null;
$okey = false;

function Mat_create() {
    global $pdo, $ficha, $nombre, $materia, $okey;
    // verificar si existe la materia en la tabla de materias
    $stmt = $pdo -> prepare("SELECT id FROM materias WHERE nombre=? LIMIT 1");
    $stmt -> execute([$nombre]);
    $materia = $stmt -> fetchColumn();
    if ($materia != false) {
        // verificar si existe la conexion entre la materia y la ficha
        $stmt = $pdo -> prepare("SELECT id FROM fichamat WHERE ficha=? AND materia=? LIMIT 1");
        $stmt -> execute([$ficha, $materia]);
        if ($stmt -> fetchColumn() == false) {
            // hacer la conexion de la materia con la ficha
            $stmt = $pdo -> prepare("INSERT INTO fichamat (ficha, materia) VALUES (?, ?)");
            $okey = $stmt -> execute([$ficha, $materia]);
        }
    }
    else {
        try {
            $pdo -> beginTransaction();
            // agregar la nueva materia a la tabla
            $stmt = $pdo -> prepare("INSERT INTO materias (nombre) VALUES (?)");
            $stmt -> execute([$nombre]);
            $materia = $pdo -> lastInsertId();
            // hacer la conexion de la materia con la ficha
            $stmt = $pdo -> prepare("INSERT INTO fichamat (ficha, materia) VALUES (?, ?)");
            $stmt -> execute([$ficha, $materia]);
            // ejecutar todas las operaciones
            $pdo -> commit();
            $okey = true;
        }
        catch (Exception $e) {
            $pdo -> rollBack();
        }
    }
}

function Mat_update() {
    global $pdo, $ficha, $nombre, $materia, $okey;
    // verificar si la materia esta registrada con otra ficha tambien
    $stmt = $pdo -> prepare("SELECT id FROM fichamat WHERE ficha!=? AND materia=? LIMIT 1");
    $stmt -> execute([$ficha, $materia]);
    if ($stmt -> fetchColumn() == true) {
        // verificar si el nombre nuevo existe en la lista de materias
        $stmt = $pdo -> prepare("SELECT id FROM materias WHERE nombre=? LIMIT 1");
        $stmt -> execute([$nombre]);
        $newmat = $stmt -> fetchColumn();
        if ($newmat == true) {
            try {
                $pdo -> beginTransaction();
                // actualizar todas las tareas de materia a newmat
                $stmt = $pdo -> prepare("UPDATE tareas SET materia=? WHERE ficha=? AND materia=?");
                $stmt -> execute([$newmat, $ficha, $materia]);
                // verificar si ya esta la ficha registrada con conexion a newmat
                $stmt = $pdo -> prepare("SELECT id FROM fichamat WHERE ficha=? AND materia=? LIMIT 1");
                $stmt -> execute([$ficha, $newmat]);
                if ($stmt -> fetchColumn() == true) {
                    // eliminar la conexion a la vieja materia
                    $stmt = $pdo -> prepare("DELETE FROM fichamat WHERE ficha=? AND materia=?");
                    $stmt -> execute([$ficha, $materia]);
                }
                else {
                    // actualizar la conexion de materia a newmat
                    $stmt = $pdo -> prepare("UPDATE fichamat SET materia=? WHERE ficha=? AND materia=?");
                    $stmt -> execute([$newmat, $ficha, $materia]);
                }
                // ejecutar todas las operaciones
                $pdo -> commit();
                $okey = true;
            }
            catch (Exception $e) {
                $pdo -> rollBack();
            }
        }
        else {
            // crear una nueva materia y actualizar las conexiones y tareas a ella
            try {
                $pdo -> beginTransaction();
                // agregar la nueva materia a la tabla
                $stmt = $pdo -> prepare("INSERT INTO materias (nombre) VALUES (?)");
                $stmt -> execute([$nombre]);
                $newmat = $pdo -> lastInsertId();
                // actualizar las conexiones con la ficha, de materia a newmat
                $stmt = $pdo -> prepare("UPDATE fichamat SET materia=? WHERE ficha=? AND materia=?");
                $stmt -> execute([$newmat, $ficha, $materia]);
                // actualizar las tareas con la ficha, de materia a newmat
                $stmt = $pdo -> prepare("UPDATE tareas SET materia=? WHERE ficha=? AND materia=?");
                $stmt -> execute([$newmat, $ficha, $materia]);
                // ejecutar todas las operaciones
                $pdo -> commit();
                $okey = true;
            }
            catch (Exception $e) {
                $pdo -> rollBack();
            }
        }
    }
    else {
        // actualizar el nombre de la materia
        $stmt = $pdo -> prepare("UPDATE materias SET nombre=? WHERE id=?");
        $okey = $stmt -> execute([$nombre, $materia]);
    }
}

function Mat_delete() {
    global $pdo, $ficha, $nombre, $materia, $okey;
    try {
        $pdo -> beginTransaction();
        // eliminar conexion entre materia y ficha
        $stmt = $pdo -> prepare("DELETE FROM fichamat WHERE ficha=? AND materia=?");
        $stmt -> execute([$ficha, $materia]);
        // eliminar las tareas asociadas a la materia
        $stmt = $pdo -> prepare("DELETE FROM tareas WHERE ficha=? AND materia=?");
        $stmt -> execute([$ficha, $materia]);
        // verificar si alguna otra ficha esta conectada a la materia
        $stmt = $pdo -> prepare("SELECT id FROM fichamat WHERE ficha!=? AND materia=? LIMIT 1");
        $stmt -> execute([$ficha, $materia]);
        if ($stmt -> fetchColumn() == false) {
            // eliminar la materia si nadie mas la utiliza
            $stmt = $pdo -> prepare("DELETE FROM materias WHERE id=?");
            $stmt -> execute([$materia]);
        }
        // ejecutar todas las operaciones
        $pdo -> commit();
        $okey = true;
    }
    catch (Exception $e) {
        $pdo -> rollBack();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $tipo != null) {
    $stmt = $pdo -> prepare("SELECT password FROM fichas WHERE id=?");
    $stmt -> execute([$ficha]);
    $the_password = $stmt -> fetchColumn();

    // verificar pasword para poder hacer la transaccion
    if (password_verify($password, $the_password)) {
        switch ($tipo) {
            case "C":
                Mat_create();
                break;
            
            case "U":
                Mat_update();
                break;
            
            case "D":
                Mat_delete();
                break;
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

    <!-- titulo principal -->
    <header>
        <?php
        if ($okey) {
            echo "<h1 class='titulo'>Solicitud Aprobada!!!</h1>";
        }
        else {
            echo "<h1 class='titulo'>clave incorrecta...</h1>";
        }
        ?>
    </header>
    
    <!-- boton para redireccionarse -->
    <form action="etc.php" method="post" autocomplete="off">
        <div>
            <a href="materia_admin.php?ficha=<?php echo $ficha; ?>" class="btn">Volver</a>
        </div>
    </form>

</body>
</html>
