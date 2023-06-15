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
if (isset($_POST['ustaw'])){//ustawienie_nowych danych_uzytkownika
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
        $idUzytkownik = $_POST['id_uzytkownika'];
        $nowyTyp = $_POST['nowytyp'];
        $queryNowyTyp = "UPDATE uzytkownicy SET id_typ_uzytkownika='$nowyTyp' WHERE ID_uzytkownik='$idUzytkownik'";
        $db->query($queryNowyTyp);
        if (!empty($_POST['noweimie'])){
            $noweImie = $_POST['noweimie'];
            if (!preg_match('#^\s+$#',$noweImie)) {
                $queryNoweImie = "UPDATE uzytkownicy SET imie='$noweImie' WHERE ID_uzytkownik='$idUzytkownik'";
                $db->query($queryNoweImie);
            }else{
                echo 'Imię nie może być spacją!<br>';
            }
        }
        if (!empty($_POST['nowenazwisko'])){
            $noweNazwisko = $_POST['nowenazwisko'];
            if (!preg_match('#^\s+$#',$noweNazwisko)) {
                $queryNoweNazwisko = "UPDATE uzytkownicy SET nazwisko='$noweNazwisko' WHERE ID_uzytkownik='$idUzytkownik'";
                $db->query($queryNoweNazwisko);
            }else{
                echo 'Nazwisko nie może być spacją!<br>';
            }
        }
        if (!empty($_POST['nowehaslo'])){
            $noweHaslo = $_POST['nowehaslo'];
            if (preg_match('/^(?=.*[!@#\$%\^&*()\-=_+[\]{};\':\"|,.<>\/?])\S{6,}$/',$noweHaslo)) {
                $noweHaslo = password_hash($noweHaslo, PASSWORD_DEFAULT);
                $queryNoweHaslo = "UPDATE uzytkownicy SET haslo='$noweHaslo' WHERE ID_uzytkownik='$idUzytkownik'";
                $db->query($queryNoweHaslo);
            }else{
                echo 'Za słabe hasło!<br>';
            }
        }
    }
    $db = null;
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
if ($connected) {//wyswietlanie_uzytkownikow
    $db->exec("SET NAMES utf8");
    $queryUzytkownicy = "SELECT * FROM uzytkownicy";
    $resultUzytkownicy = $db->query($queryUzytkownicy);
    echo '<table cellspacing="20px">';
    echo '<tr>';
    echo '<td align="center">ID</td>';
    echo '<td align="center">Imię</td>';
    echo '<td align="center">Nazwisko</td>';
    echo '<td align="center">Adres email</td>';
    echo '<td align="center">Typ</td>';
    echo '<td align="center"></td>';
    echo '<td align="center">Nowy typ</td>';
    echo '<td align="center">Nowe hasło</td>';
    echo '<td align="center">Nowe imię</td>';
    echo '<td align="center">Nowe nazwisko</td>';
    echo '<td align="center"></td>';
    echo '</tr>';
    while ($rowUzytkownicy = $resultUzytkownicy->fetch(PDO::FETCH_ASSOC)){
        echo '<tr>';
        echo '<td align="center">'.$rowUzytkownicy['ID_uzytkownik'].'</td>';
        echo '<td align="center">'.$rowUzytkownicy['imie'].'</td>';
        echo '<td align="center">'.$rowUzytkownicy['nazwisko'].'</td>';
        echo '<td align="center">'.$rowUzytkownicy['adres_email'].'</td>';
        $idTyp = $rowUzytkownicy['id_typ_uzytkownika'];
        $queryTyp = "SELECT * FROM typ_uzytkownika WHERE ID_typ_uzytkownika='$idTyp'";
        $resultTyp = $db->query($queryTyp);
        while ($rowTyp = $resultTyp->fetch(PDO::FETCH_ASSOC)){
            echo '<td align="center">'.$rowTyp['typ'].'</td>';
        }
        echo '<form method="post">';
        echo '<td align="center"><select name="nowytyp">
        <option value="2">User</option>
        <option value="1">Admin</option>
        </select></td>';
        echo '<td><input type="hidden" name="id_uzytkownika" value="' . $rowUzytkownicy['ID_uzytkownik'] . '">
        <input type="text" name="nowehaslo" placeholder="nowe hasło"></td>';
        echo '<td><input type="hidden" name="id_uzytkownika" value="' . $rowUzytkownicy['ID_uzytkownik'] . '">
        <input type="text" name="noweimie" placeholder="nowe imię"></td>';
        echo '<td><input type="hidden" name="id_uzytkownika" value="' . $rowUzytkownicy['ID_uzytkownik'] . '">
        <input type="text" name="nowenazwisko" placeholder="nowe nazwisko"></td>';
        echo '<td align="center">
        <input type="hidden" name="id_uzytkownika" value="' . $rowUzytkownicy['ID_uzytkownik'] . '">
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
</body>
</html>
