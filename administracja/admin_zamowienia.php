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
    $idZamowienia = $_POST['id_zamowienia'];
    $idUzytkownik = $_POST['id_uzytkownika'];
    $idAdres = $_POST['id_adres'];
    $idDaneKontaktowe = $_POST['id_dane_kontaktowe'];
    echo '<p align="center">Sczegóły zamówienia '.$idZamowienia.' :</p><br>';
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
        $query = "SELECT * FROM zamowione_produkty WHERE ID_zamowienie='$idZamowienia'";
        $result = $db->query($query);
        echo '<table align="center" cellspacing="15px">';
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
        echo '</table><br>';//dane_uzytkownika
        $queryUzytkownik = "SELECT ID_uzytkownik,imie,nazwisko,adres_email FROM uzytkownicy WHERE ID_uzytkownik='$idUzytkownik'";
        $resultUzytkownik = $db->query($queryUzytkownik);
        echo '<table align="center" cellspacing="15px">';
        echo '<tr>';
        echo '<td align="center">ID</td>';
        echo '<td align="center">Imię</td>';
        echo '<td align="center">Nazwisko</td>';
        echo '<td align="center">Adres email</td>';
        echo '</tr>';
        while ($rowUzytkownik = $resultUzytkownik->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td align="center">' . $rowUzytkownik['ID_uzytkownik'] . '</td>';
            echo '<td align="center">' . $rowUzytkownik['imie'] . '</td>';
            echo '<td align="center">' . $rowUzytkownik['nazwisko'] . '</td>';
            echo '<td align="center">' . $rowUzytkownik['adres_email'] . '</td>';
            echo '</tr>';
        }
        echo '</table><br>';//dane_adresu
        $queryAdres = "SELECT miasto,ulica,kod_pocztowy,nr_mieszkania,nr_domu FROM adres WHERE ID_adres='$idAdres'";
        $resultAdres = $db->query($queryAdres);
        echo '<table align="center" cellspacing="15px">';
        echo '<tr>';
        echo '<td align="center">Miasto</td>';
        echo '<td align="center">Ulica</td>';
        echo '<td align="center">Kod pocztowy</td>';
        echo '<td align="center">Nr mieszkania</td>';
        echo '<td align="center">Nr domu</td>';
        echo '</tr>';
        while ($rowAdres = $resultAdres->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td align="center">' . $rowAdres['miasto'] . '</td>';
            echo '<td align="center">' . $rowAdres['ulica'] . '</td>';
            echo '<td align="center">' . $rowAdres['kod_pocztowy'] . '</td>';
            echo '<td align="center">' . $rowAdres['nr_mieszkania'] . '</td>';
            echo '<td align="center">' . $rowAdres['nr_domu'] . '</td>';
            echo '</tr>';
        }
        echo '</table><br>';//dane_kontaktowe
         $queryDaneKontaktowe = "SELECT nr_telefonu,adres_email FROM dane_kontaktowe WHERE ID_dane_kontaktowe='$idDaneKontaktowe'";
        $resultDaneKontaktowe = $db->query($queryDaneKontaktowe);
        echo '<table align="center" cellspacing="15px">';
        echo '<tr>';
        echo '<td align="center">Nr telefonu</td>';
        echo '<td align="center">Adres email</td>';
        echo '</tr>';
        while ($rowDaneKontaktowe = $resultDaneKontaktowe->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td align="center">' . $rowDaneKontaktowe['nr_telefonu'] . '</td>';
            echo '<td align="center">' . $rowDaneKontaktowe['adres_email'] . '</td>';
            echo '</tr>';
        }
        echo '</table><br>';
    }
    $db = null;
}
if (isset($_POST['ustaw'])){//ustawienie_nowego_stanu_zamowienia
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
        $nowyStan = $_POST['nowystanzamowienia'];
        $idZamowienia = $_POST['id_zamowienia'];
        $queryNowyStanZamowienia= "UPDATE zamowienia SET stan='$nowyStan' WHERE ID_zamowienia='$idZamowienia'";
        $db->query($queryNowyStanZamowienia);
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
if ($connected) {//wyswietlanie_zamowien
    $db->exec("SET NAMES utf8");
    $queryZamowienia = "SELECT * FROM zamowienia";
    $resultZamowienia = $db->query($queryZamowienia);
    echo '<table cellspacing="20px">';
    echo '<tr>';
    echo '<td align="center">ID zamówienia</td>';
    echo '<td align="center">ID użytkownika</td>';
    echo '<td align="center">ID adresu</td>';
    echo '<td align="center">ID dane kontaktowe</td>';
    echo '<td align="center">Data złożenia</td>';
    echo '<td align="center">Sposób płatności</td>';
    echo '<td align="center">Wartość</td>';
    echo '<td align="center">Stan</td>';
    echo '<td align="center">Rabat</td>';
    echo '<td align="center"></td>';
    echo '<td align="center">Nowy stan</td>';
    echo '<td align="center"></td>';
    echo '</tr>';
    while ($rowZamowienia = $resultZamowienia->fetch(PDO::FETCH_ASSOC)){
        echo '<tr>';
        echo '<td align="center">'.$rowZamowienia['ID_zamowienia'].'</td>';
        echo '<td align="center">'.$rowZamowienia['id_uzytkownik'].'</td>';
        echo '<td align="center">'.$rowZamowienia['id_adres'].'</td>';
        echo '<td align="center">'.$rowZamowienia['id_dane_kontaktowe'].'</td>';
        echo '<td align="center">'.$rowZamowienia['data_zlozenia'].'</td>';
        echo '<td align="center">'.$rowZamowienia['sposob_platnosci'].'</td>';
        echo '<td align="center">'.$rowZamowienia['wartosc_zamowienia'].'</td>';
        echo '<td align="center">'.$rowZamowienia['stan'].'</td>';
        $rabat = '';
        if ($rowZamowienia['czy_rabat']){
            $rabat = 'TAK';
        }else{
            $rabat = 'NIE';
        }
        echo '<td align="center">'.$rabat.'</td>';
        echo '<form method="post">';
        echo '<td align="center">
        <input type="hidden" name="id_zamowienia" value="' . $rowZamowienia['ID_zamowienia'] . '">
        <input type="hidden" name="id_uzytkownika" value="' . $rowZamowienia['id_uzytkownik'] . '">
        <input type="hidden" name="id_adres" value="' . $rowZamowienia['id_adres'] . '">
        <input type="hidden" name="id_dane_kontaktowe" value="' . $rowZamowienia['id_dane_kontaktowe'] . '">
        <input type="submit" name="pokazszczegoly" value="Pokaż szczegóły zamówienia"></td>';
        echo '<td align="center"><select name="nowystanzamowienia">
        <option value="zrealizowano">Zrealizowano</option>
        <option value="w_trakcie_realizacji">W trakcie realizacji</option>
        <option value="przyjęte">Przyjęte</option>
        </select></td>';
        echo '<td align="center">
        <input type="hidden" name="id_zamowienia" value="' .  $rowZamowienia['ID_zamowienia'] . '">
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
