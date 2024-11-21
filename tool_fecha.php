<?php
$ok_timestamp = setlocale(LC_TIME, 'es_CO.UTF-8');
function FormatoDate($lafecha) {
    global $ok_timestamp;
    $timestamp = strtotime($lafecha);
    $formato = strftime('%A, %d de %B de %Y', $timestamp);
    // en caso de que no se encuentre el idioma espannol
    if (!$ok_timestamp) {
        $traduce = [
            'Monday' => 'lunes',
            'Tuesday' => 'martes',
            'Wednesday' => 'miércoles',
            'Thursday' => 'jueves',
            'Friday' => 'viernes',
            'Saturday' => 'sábado',
            'Sunday' => 'domingo',
            'January' => 'enero',
            'February' => 'febrero',
            'March' => 'marzo',
            'April' => 'abril',
            'May' => 'mayo',
            'June' => 'junio',
            'July' => 'julio',
            'August' => 'agosto',
            'September' => 'septiembre',
            'October' => 'octubre',
            'November' => 'noviembre',
            'December' => 'diciembre'
        ];
        foreach ($traduce as $en => $es) {
            $formato = str_replace($en, $es, $formato);
        }
    }
    return $formato;
}
?>
