$n = $_GET['numero'];

for ($i = 1; $i <= $n; $i++) {
    for ($j = 1; $j <= $i; $j++) {
        echo "*";
    }
    echo "<br>";
}
?>

<br>
<a href="piramide.html">
  <button>Torna alla pagina di inserimento</button>
</a>