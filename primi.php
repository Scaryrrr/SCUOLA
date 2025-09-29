<?php
function èPrimo($n) {
    if ($n < 2) return false; // 0 e 1 non sono primi

    for ($i = 2; $i <= sqrt($n); $i++) {
        if ($n % $i == 0) {
            return false; // trovato un divisore → non è primo
        }
    }
    return true; // nessun divisore trovato → è primo
}

$min = $_GET['min'];
$max = $_GET['max'];

echo "<h1>Numeri primi tra $min e $max</h1>";

for ($n = $min; $n <= $max; $n++) {
    if (èPrimo($n)) {
        echo $n . "<br>";
    }
}
?>

<br>
<a href="primo.html">
  <button>Torna alla pagina di inserimento</button>
</a>