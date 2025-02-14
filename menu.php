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

    <!-- funcion para actualizar links a diferentes paginas -->
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
                        url = "materia_lista.php?ficha=";
                        break;
                    case "btnNueva":
                        url = "tarea_nueva.php?ficha=";
                        break;
                    case "btnAdmin":
                        url = "materia_admin.php?ficha=";
                        break;
                }
                boton.setAttribute('href', url + valor);
            });
        }

        selector.addEventListener('change', actualizarEnlaces);
        actualizarEnlaces();
    </script>

    <!-- acerca de del software -->
    <p class="w"><strong>¿Para qué sirve esto?</strong></p>
    <p class="w">para mantenerte al tanto de las tareas que te dejan, algunos alumnos 
        pilos manejarán la contraseña de sus fichas y publicarán las nuevas tareas, 
        todos los demás podrán verlas</p>
    <p class="w"><strong>¿Por qué fué creado esto?</strong></p>
    <p class="w">este desarrollo No es oficial del Sena, fué hecho por Omar Jordan J 
        (Omwekiatl), alumno de Software en 2024 como parte de su práctica para 
        desarrollo web y bases de datos</p>
    <p class="w"><strong>¿Puedo ver el código fuente?</strong></p>
    <p class="w">por supuesto honey, aquí tienes el repositorio para que lo explores</p>
    <p class="w"><a href="https://github.com/OmarsaurioJordan/sena_tareas"
        class="button">GitHub</a></p>
    <p class="w"><strong>¿Cómo obtengo una contraseña?</strong></p>
    <p class="w">si quieres administrar las tareas que se suben a tu ficha, contacta 
        al administrador al correo:</p>
    <p class="w"><a href="" class="button" id="btnMail"
        onclick="cambiaBotoncito(event)">Ver Mail</a></p>

    <script>
        function cambiaBotoncito(event) {
            event.preventDefault();
            const button = document.getElementById('btnMail');
            switch (button.textContent) {
                case "Ver Mail":
                    button.textContent = "Siga Pulsando";
                    break;
                case "Siga Pulsando":
                    button.textContent = "Dale Más";
                    break;
                case "Dale Más":
                    button.textContent = "Ya Casi!!!";
                    break;
                case "Ya Casi!!!":
                    button.textContent = "ojorcio@gmail.com";
                    break;
                case "ojorcio@gmail.com":
                case "Recarga la Página":
                    button.textContent = "Te Pasaste...";
                    break;
                case "Te Pasaste...":
                    button.textContent = "Recarga la Página";
                    break;
            }
        }
    </script>

</body>
</html>
