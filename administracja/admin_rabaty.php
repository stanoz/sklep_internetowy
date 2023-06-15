<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panel administracyjny</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<h2 align="center">Panel administracyjny</h2><br>
<hr color="grey">
<br>
<a href="panel_admina.php">Powrót do panelu administracyjnego.</a><br>
<?php
if (isset($_POST['ustaw'])){//ustawienie_nowej_tresci_rabatu
    $nowaTresc = $_POST['nowatresc'];
    $idRabat = $_POST['id_rabat'];
    if (!preg_match('#^\s+$#',$nowaTresc)) {
        if (strlen($nowaTresc) > 0 && strlen($nowaTresc) <= 50) {
            $dbuser = 'root';
            $dbpassword = '';
            $connected = false;
            $db = null;
            try {
                $db = new PDO("mysql:host=127.0.0.1;dbname=sklep_internetowy", $dbuser, $dbpassword);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $db->exec("SET NAMES utf8");
                $connected = true;
            } catch (PDOException $e) {
                echo "Błąd połączenia z bazą danych: " . $e->getMessage();
            }
            if ($connected) {
                $query = "UPDATE rabaty SET tresc='$nowaTresc' WHERE ID_rabat='$idRabat'";
            }
            $db = null;
        }else{
            echo 'Nieprawidłowa długość treści!<br>';
        }
    }else{
        echo 'Treść nie może być spacją!<br>';
    }
}
?>
<?php
if (isset($_POST['nowyRabat'])){//dodanie_nowego_rabatu
    $nowyRabatTresc = $_POST['nowyRabatTresc'];
    $walidacja = false;
    if (!empty($nowyRabatTresc)){
        if (!preg_match('#^\s+$#',$nowyRabatTresc)){
            if (strlen($nowyRabatTresc) > 0 && strlen($nowyRabatTresc) <= 50){
                $walidacja = true;
            }else{
                echo 'Nieprawidłowa długość treści!<br>';
            }
        }else{
            echo 'Treść nie może być spacją!<br>';
        }
    }else{
        echo 'Musisz wpisać treść!<br>';
    }
    if ($walidacja){
        $dbuser = 'root';
        $dbpassword = '';
        $connected = false;
        $db = null;
        $czySiePowtarza = false;
        try {
            $db = new PDO("mysql:host=127.0.0.1;dbname=sklep_internetowy", $dbuser, $dbpassword);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $db->exec("SET NAMES utf8");
            $connected = true;
        } catch (PDOException $e) {
            echo "Błąd połączenia z bazą danych: " . $e->getMessage();
        }
        if ($connected) {//sprawdzenie_czy_sie_powtarza
            $query = 'SELECT tresc FROM rabaty';
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                if ($row['tresc'] == $nowyRabatTresc){
                    $czySiePowtarza = true;
                }
            }
            if ($czySiePowtarza){
                $queryDodajRabat = "INSERT INTO rabaty(tresc) VALUES ('$nowyRabatTresc')";
                $db->query($queryDodajRabat);
            }else{
                echo 'Taki rabat już istnieje!<br>';
            }
        }
        $db = null;
    }
}
?>
<?php
$dbuser = 'root';
$dbpassword = '';
$connected = false;
$db = null;
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=sklep_internetowy", $dbuser, $dbpassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $connected = true;
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}
if ($connected) {//wyswietlanie_rabatow
    $db->exec("SET NAMES utf8");
    $queryRabaty = "SELECT * FROM rabaty";
    $resultRabaty  = $db->query($queryRabaty);
    echo '<table cellspacing="20px">';
    echo '<tr>';
    echo '<td align="center">ID rabatu</td>';
    echo '<td align="center">Treść</td>';
    echo '<td align="center">Nowa Treść</td>';
    echo '<td align="center"></td>';
    echo '</tr>';
    while ($rowRabaty = $resultRabaty->fetch(PDO::FETCH_ASSOC)){
        echo '<tr>';
        echo '<td align="center">'.$rowRabaty['ID_rabat'].'</td>';
        echo '<td align="center">'.$rowRabaty['tresc'].'</td>';
        echo '<td align="center"><input type="text" name="nowatresc" maxlength="50" placeholder="Treść rabatu" required></td>';
        echo '<form method="post">';
        echo '<td align="center">
        <input type="hidden" name="id_rabat" value="' . $rowRabaty['ID_rabat'] . '">
        <input type="submit" name="ustaw" value="Ustaw"></td>';
        echo '</form>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo "Nie połączono z bazą danych!<br>";
}
$db = null;
?>
<h3>Tworzenie nowego rabatu</h3>
<form method="post">
    <label>
        Treść rabatu <input type="text" name="nowyRabatTresc" maxlength="50" placeholder="Wpisz treść" required><br>
        <input type="submit" name="nowyRabat" value="Utwórz">
    </label>
</form>
</body>
</html>
