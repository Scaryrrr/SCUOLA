<?php
$morse = [
    'A'=>'.-', 'B'=>'-...', 'C'=>'-.-.', 'D'=>'-..',
    'E'=>'.', 'F'=>'..-.', 'G'=>'--.', 'H'=>'....',
    'I'=>'..', 'J'=>'.---', 'K'=>'-.-', 'L'=>'.-..',
    'M'=>'--', 'N'=>'-.', 'O'=>'---', 'P'=>'.--.',
    'Q'=>'--.-', 'R'=>'.-.', 'S'=>'...', 'T'=>'-',
    'U'=>'..-', 'V'=>'...-', 'W'=>'.--', 'X'=>'-..-',
    'Y'=>'-.--', 'Z'=>'--..',
    '0'=>'-----','1'=>'.----','2'=>'..---','3'=>'...--',
    '4'=>'....-','5'=>'.....','6'=>'-....','7'=>'--...',
    '8'=>'---..','9'=>'----.',' '=>'/'
];

function textToMorse($stringa, $morse) {
    $output = '';
    for ($i=0;$i<strlen($stringa);$i++) {
        $output .= $morse[$stringa[$i]] . ' ';
    }
    return trim($output);
}

function morseToText($stringa, $morse) {
    $output = '';
    $simbolo = '';
    $stringa .= ' ';
    for ($i=0;$i<strlen($stringa);$i++) {
        $c = $stringa[$i];
        if ($c!=' ' && $c!='/') {
            $simbolo .= $c;
        } else {
            if ($simbolo!='') {
                $output .= array_search($simbolo,$morse);
                $simbolo = '';
            }
            if ($c=='/') $output .= ' ';
        }
    }
    return $output;
}

$risultato = '';
if (isset($_GET['converti'])) {
    $input = $_GET['testo'];
    $modalita = $_GET['modalita'];
    if ($modalita=='textToMorse') $risultato = textToMorse(strtoupper($input), $morse);
    else $risultato = morseToText($input,$morse);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Traduttore Morse ↔ Testo</title>
</head>
<body>

<h2>Traduttore Testo ↔ Morse</h2>

<form method="get">
    <input type="text" name="testo" value="<?= $_GET['testo'] ?? '' ?>"><br>
    <select name="modalita">
        <option value="textToMorse" <?= ($_GET['modalita']??'')=='textToMorse'?'selected':'' ?>>Testo → Morse</option>
        <option value="morseToText" <?= ($_GET['modalita']??'')=='morseToText'?'selected':'' ?>>Morse → Testo</option>
    </select><br>
    <input type="submit" name="converti" value="Converti">
</form>

<?php if ($risultato): ?>
    <h3>Risultato:</h3>
    <p><?= $risultato ?></p>
<?php endif; ?>

</body>
</html>