<?php    


// CONFIGURAZIONE

$filename = "random-grades 1.csv";


// FUNZIONI BASE

function getInput($name) {
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
}   

function getElencoClassi($filename) {
    $elencoClassi = array();
    $lines = file($filename);
    for($i = 1; $i < count($lines); $i++){
        $row = explode(",", $lines[$i]);
        $elencoClassi[$row[2]] = 1;
    }
    $chiavi = array_keys($elencoClassi); 
    sort($chiavi);
    return $chiavi;
}   

function getElencoMaterie($filename) {
    $elencoMaterie = array();
    $lines = file($filename);
    for($i = 1; $i < count($lines); $i++){
        $row = explode(",", $lines[$i]);
        $elencoMaterie[$row[3]] = 1;
    }
    ksort($elencoMaterie);
    return array_keys($elencoMaterie);
}   

function calcolaMedia($filename, $nome = null, $cognome = null, $classe = null, $disciplina = null){
    $somma = 0;
    $conta = 0;
    $lines = file($filename);
    for($i = 1; $i < count($lines); $i++){
        $row = explode(",", $lines[$i]);
        if( $cognome != null && $row[0] != $cognome ) continue;
        if( $nome != null && $row[1] != $nome ) continue;
        if( $classe != null && $row[2] != $classe ) continue;
        if( $disciplina != null && $row[3] != $disciplina ) continue;
        $somma += $row[5];
        $conta++;
    }
    if($conta == 0) return null;
    return ($somma / $conta);    
}


// SEZIONE: CALCOLO MEDIA

function elaboraInput($filename) {
    if( count($_REQUEST) == 0) return;

    $cognome = isset($_REQUEST['cognome']) ? $_REQUEST['cognome'] : null;
    $nome = isset($_REQUEST['nome']) ? $_REQUEST['nome'] : null;
    $classe = isset($_REQUEST['classe']) ? $_REQUEST['classe'] : null;
    $disciplina = isset($_REQUEST['disciplina']) ? $_REQUEST['disciplina'] : null;

    $media = calcolaMedia($filename, $nome, $cognome, $classe, $disciplina);

    if($media == null) {
        $testoNome = isset($nome) ? "di $nome" : "";
        $testoCognome = isset($cognome) ? "$cognome" : "";
        $testoClasse = isset($classe) ? "della classe $classe" : "";
        $testoDisciplina = isset($disciplina) ? "di $disciplina" : "";
        return "Nessun voto trovato per $testoNome $testoCognome $testoClasse $testoDisciplina.";
    } else {
        $media = round($media, 2);
        $testoNome = isset($nome) ? "di $nome" : "";
        $testoCognome = isset($cognome) ? "$cognome" : "";
        $testoClasse = isset($classe) ? "della classe $classe" : "";
        $testoDisciplina = isset($disciplina) ? "di $disciplina" : "";
        return "La media $testoNome $testoCognome $testoClasse $testoDisciplina è: <b>$media</b>";
    }
}


// SEZIONE: AGGIUNTA NUOVA VALUTAZIONE

function aggiungiValutazione($filename, $cognome, $nome, $classe, $disciplina, $voto) {
    if (!ctype_digit($voto)) {
        return "Il voto deve essere un numero intero!";
    }

    $data = date('Y-m-d');
    $file = fopen($filename, "a");
    if ($file) {
        fputcsv($file, [$cognome, $nome, $classe, $disciplina, $data, $voto]);
        fclose($file);
        return "Valutazione aggiunta correttamente.";
    }
    return "Errore durante il salvataggio.";
}

// SEZIONE: MODIFICA VALUTAZIONE

function leggiValutazioni($filename) {
    return file($filename, FILE_IGNORE_NEW_LINES);
}

function salvaValutazioni($filename, $righe) {
    file_put_contents($filename, implode("\n", $righe));
}

function modificaVoto($filename, $cognome, $nome, $classe, $disciplina, $nuovoVoto) {
    if (!ctype_digit($nuovoVoto)) {
        return "Il nuovo voto deve essere un numero intero!";
    }

    $righe = leggiValutazioni($filename);
    $trovato = false;

    for ($i = 1; $i < count($righe); $i++) {
        $row = explode(",", $righe[$i]);
        if (trim($row[0]) == $cognome && trim($row[1]) == $nome && trim($row[2]) == $classe && trim($row[3]) == $disciplina) {
            $row[5] = $nuovoVoto;
            $righe[$i] = implode(",", $row);
            $trovato = true;
            break;
        }
    }

    if ($trovato) {
        salvaValutazioni($filename, $righe);
        return "Voto aggiornato correttamente.";
    } else {
        return "Nessuna valutazione trovata con quei dati.";
    }
}

// GESTIONE DELLE AZIONI

$messaggio = "";

if (isset($_POST['azione'])) {
    switch ($_POST['azione']) {
        case "calcola_media":
            $messaggio = elaboraInput($filename);
            break;

        case "aggiungi":
            $messaggio = aggiungiValutazione(
                $filename,
                $_POST['cognome'],
                $_POST['nome'],
                $_POST['classe'],
                $_POST['disciplina'],
                $_POST['voto']
            );
            break;

        case "modifica":
            $messaggio = modificaVoto(
                $filename,
                $_POST['cognome'],
                $_POST['nome'],
                $_POST['classe'],
                $_POST['disciplina'],
                $_POST['voto']
            );
            break;
    }
}

$elencoClassi = getElencoClassi($filename);
$elencoMaterie = getElencoMaterie($filename);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestione Completa dei Voti</title>
</head>
<body>

<h1>Gestione Completa dei Voti</h1>

<?php if ($messaggio): ?>
    <p><?php echo $messaggio; ?></p>
<?php endif; ?>

<!-- CALCOLO MEDIA -->
<h2>Calcola Media</h2>
<form method="post">
    <input type="hidden" name="azione" value="calcola_media">

    <p>Cognome:<br><input type="text" name="cognome" value="<?php echo getInput('cognome'); ?>"></p>
    <p>Nome:<br><input type="text" name="nome" value="<?php echo getInput('nome'); ?>"></p>

    <p>Classe:<br>
        <select name="classe">
            <option value="">--Tutte le classi--</option>
            <?php foreach($elencoClassi as $classe): 
                $selected = (getInput('classe') == $classe) ? "selected" : "";
                echo "<option value=\"$classe\" $selected>$classe</option>";
            endforeach; ?>
        </select>
    </p>

    <p>Disciplina:<br>
        <select name="disciplina">
            <option value="">--Tutte le discipline--</option>
            <?php foreach($elencoMaterie as $materia): 
                $selected = (getInput('disciplina') == $materia) ? "selected" : "";
                echo "<option value=\"$materia\" $selected>$materia</option>";
            endforeach; ?>
        </select>
    </p>

    <p><button type="submit">Calcola Media</button></p>
</form>

<hr>

<!-- AGGIUNGI VALUTAZIONE -->
<h2>Aggiungi Nuova Valutazione</h2>
<form method="post">
    <input type="hidden" name="azione" value="aggiungi">
    <p>Inserisci i dati dell'alunno e il voto da aggiungere:</p>

    <p>Cognome:<br><input name="cognome"></p>
    <p>Nome:<br><input name="nome"></p>
    <p>Classe:<br><input name="classe"></p>
    <p>Disciplina:<br><input name="disciplina"></p>
    <p>Voto:<br><input type="text" name="voto"></p>

    <p><button type="submit">Aggiungi Valutazione</button></p>
</form>

<hr>

<!-- MODIFICA VOTO -->
<h2>Modifica Voto Esistente</h2>
<form method="post">
    <input type="hidden" name="azione" value="modifica">
    <p>Inserisci i dati dell'alunno e il nuovo voto da aggiornare:</p>

    <p>Cognome:<br><input name="cognome"></p>
    <p>Nome:<br><input name="nome"></p>
    <p>Classe:<br><input name="classe"></p>
    <p>Disciplina:<br><input name="disciplina"></p>
    <p>Nuovo voto:<br><input type="text" name="voto"></p>

    <p><button type="submit">Modifica Voto</button></p>
</form>

</body>
</html>
