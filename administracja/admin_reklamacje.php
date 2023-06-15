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
if (isset($_POST['pokazszczegoly'])){//wyswietlanie_szczegolow_zamowienia
    $idZamowieniaReklamacja = $_POST['id_zamowienia_reklamacja'];
    echo '<p align="center">Sczegóły zamówienia '.$idZamowieniaReklamacja.' :</p><br>';
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
        $query = "SELECT * FROM zamowione_produkty WHERE ID_zamowienie='$idZamowieniaReklamacja'";
        $result = $db->query($query);
        echo '<table align="center">';
        echo '<tr>';
        echo '<td align="center">Nazwa</td>';
        echo '<td align="center">Ilość sztuk</td>';
        echo '<td align="center">Cena za 1 sztukę</td>';
        echo '</tr>';
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $ilosc = $row['ilosc_sztuk'];
            $idProdukt = $row['id_produktu'];
            $queryProdukt = "SELECT * FROM produkty WHERE ID_produkt='$idProdukt'";
            $resultProdukt = $db->query($queryProdukt);
            while ($rowProdukt = $resultProdukt->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td align="center">' . $rowProdukt['nazwa'] . '</td>';
                echo '<td align="center">' . $ilosc . '</td>';
                echo '<td align="center">' . $rowProdukt['cena'] . '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
    }
    $db = null;
}
if (isset($_POST['ustaw'])){//ustawienie_nowego_stanu_reklamacji
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
        $nowyStan = $_POST['nowystanreklamacji'];
        $idReklamacja = $_POST['id_reklamacja'];
        $queryNowyStanReklamacji = "UPDATE reklamacje SET stan='$nowyStan' WHERE ID_reklamacja='$idReklamacja'";
        $db->query($queryNowyStanReklamacji);
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
if ($connected) {//wyswietlanie_reklamacji
    $db->exec("SET NAMES utf8");
    $queryReklamacje = "SELECT * FROM reklamacje";
    $resultReklamacje = $db->query($queryReklamacje);
    echo '<table cellspacing="20px">';
    echo '<tr>';
    echo '<td align="center">ID reklamacji</td>';
    echo '<td align="center">ID zamówienia</td>';
    echo '<td align="center">ID użytkownika</td>';
    echo '<td align="center">Data złożenia</td>';
    echo '<td align="center">Treść</td>';
    echo '<td align="center">Stan</td>';
    echo '<td align="center"></td>';
    echo '<td align="center">Nowy stan</td>';
    echo '<td align="center"></td>';
    echo '</tr>';
    while ($rowReklamacje = $resultReklamacje->fetch(PDO::FETCH_ASSOC)){
        echo '<tr>';
        echo '<td align="center">'.$rowReklamacje['ID_reklamacja'].'</td>';
        echo '<td align="center">'.$rowReklamacje['id_zamowienia'].'</td>';
        echo '<td align="center">'.$rowReklamacje['id_uzytkownik'].'</td>';
        echo '<td align="center">'.$rowReklamacje['data_zlozenia'].'</td>';
        echo '<td align="center">'.$rowReklamacje['tresc'].'</td>';
        echo '<td align="center">'.$rowReklamacje['stan'].'</td>';
        echo '<form method="post">';
        echo '<td align="center">
        <input type="hidden" name="id_zamowienia_reklamacja" value="' . $rowReklamacje['id_zamowienia'] . '">
        <input type="submit" name="pokazszczegoly" value="Pokaż szczegóły zamówienia"></td>';
        echo '<td align="center"><select name="nowystanreklamacji">
        <option value="rozpatrywanie">Rozpatrywanie</option>
        <option value="odrzucono">Odrzucono</option>
        <option value="przyjęto">Przyjęto</option>
        </select></td>';
        echo '<td align="center">
        <input type="hidden" name="id_reklamacja" value="' . $rowReklamacje['ID_reklamacja'] . '">
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