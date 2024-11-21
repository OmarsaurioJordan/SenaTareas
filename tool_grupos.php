<?php
function IntegrToTxt($integrnts) {
    if ($integrnts <= 1) {
        return "individual (1)</p>";
    }
    else if ($integrnts == 2) {
        return "en parejas (2)</p>";
    }
    else if ($integrnts == 3) {
        return "en trios (3)</p>";
    }
    else if ($integrnts == 4) {
        return "en cuartetos (4)</p>";
    }
    else if ($integrnts == 5) {
        return "en quintetos (5)</p>";
    }
    else if ($integrnts == 6) {
        return "en sextetos (6)</p>";
    }
    return "en grupos de (" . $integrnts . ")</p>";
}
?>
