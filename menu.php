<?php
// conexion a la base de datos
include 'tool_db.php';

// inicializar las variables de entrada y salida
$ficha = isset($_GET['ficha']) ? $_GET['ficha']: null;
$options = "";

// consulta para obtener los items y generar el tramo HTML
$stmt = $pdo -> prepare("SELECT id, nombre FROM fichas");
$stmt -> execute([]);

while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    if ($row['id'] == $ficha) {
        $options .= "<option value='" . $row['id'] . "' selected>" .
            $row['nombre'] . "</option>";
    }
    else {
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

    <!-- titulo principal -->
    <header>
        <h1 class="titulo">Sena Tareas</h1>
    </header>
    
    <!-- botones para elegir que hacer con la seleccion -->
    <form action="etc.php" method="post"  autocomplete="off">
        <div>
            <label>Ficha:</label>
            <select id="laficha" name="laficha" autocomplete="off">
                <?php echo $options; ?>
            </select>
        </div>
        <div>
            <a href="" class="btn" id="btnTareas">Listar Tareas</a>
        </div>
        <div>
            <a href="" class="btn" id="btnMaterias">Listar Materias</a>
        </div>
        <div>
            <a href="" class="btn" id="btnNueva">Nueva Tarea</a>
        </div>
        <div>
            <a href="" class="btn" id="btnAdmin">Administrar Materias</a>
        </div>
    </form>

    <script>
        const selector = document.getElementById("laficha");
        const botones = document.querySelectorAll(".btn");

        function actualizarEnlaces() {
            let valor = encodeURIComponent(selector.value);
            botones.forEach(function(boton) {
                let url = "";
                switch (boton.getAttribute('id')) {
                    case "btnTareas":
                        url = "tarea_lista.php?ficha=";
                        break;
                        case "btnMaterias":
                        url = "";
                        break;
                    case "btnNueva":
                        url = "tarea_nueva.php?ficha=";
                        break;
                    case "btnAdmin":
                        url = "";
                        break;
                }
                boton.setAttribute('href', url + valor);
            });
        }

        selector.addEventListener('change', actualizarEnlaces);
        actualizarEnlaces();
    </script>

</body>
</html>
