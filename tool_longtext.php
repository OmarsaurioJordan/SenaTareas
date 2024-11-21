<?php
function Salto_to_palo($txt) {
    $new_txt = str_replace("\n", "|", $txt);
    $new_txt = str_replace("<br>", "|", $new_txt);
    return $new_txt;
}
function Salto_to_br($txt) {
    $new_txt = str_replace("\n", "<br>", $txt);
    $new_txt = str_replace("|", "<br>", $new_txt);
    return $new_txt;
}
function Salto_to_nr($txt) {
    $new_txt = str_replace("<br>", "\n", $txt);
    $new_txt = str_replace("|", "\n", $new_txt);
    return $new_txt;
}
function Set_palo($txt) {
    $new_txt = str_replace("|", "", $txt);
    return Salto_to_palo($new_txt);
}
?>
