<?php
$dbuser = 'root';
$dbpassword = '';
$connected = false;
$db = null;
$popularnosc = "";
$wartoscSprzedazy = 0;
$iloscZamowien = 0;
$data = "Popularnosc:\n";
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
    $query = "SELECT produkty.nazwa, SUM(zamowione_produkty.ilosc_sztuk) as total
          FROM zamowione_produkty
          JOIN produkty ON produkty.ID_produkt = zamowione_produkty.id_produktu
          JOIN zamowienia ON zamowienia.ID_zamowienia = zamowione_produkty.id_zamowienie
          WHERE DATE(zamowienia.data_zlozenia) >= DATE(NOW() - INTERVAL 1 MONTH)
          GROUP BY produkty.nazwa";
    $result = $db->query($query);
    while($popularnosc = $result->fetch(PDO::FETCH_ASSOC)){
        $data .= $popularnosc['nazwa'] . ": " . $popularnosc['total'] . "\n";
    }
    $query = "SELECT SUM(wartosc_zamowienia) as total
          FROM zamowienia WHERE DATE(zamowienia.data_zlozenia) >= DATE(NOW() - INTERVAL 1 MONTH)";
    $result = $db->query($query);
    while($wartoscSprzedazy = $result->fetch(PDO::FETCH_ASSOC)){
        $data .= "\nWartosc sprzedazy: " . $wartoscSprzedazy['total'] . "\n";
    }
    $query = "SELECT COUNT(ID_zamowienia) as total
          FROM zamowienia WHERE DATE(zamowienia.data_zlozenia) >= DATE(NOW() - INTERVAL 1 MONTH)";
    $result = $db->query($query);
    while($iloscZamowien = $result->fetch(PDO::FETCH_ASSOC)){
        $data .= "Ilosc zamowien: " . $iloscZamowien['total'] . "\n";
    }
}
$db = null;
$data_wystawienia = date("Y-m-d");
file_put_contents("../raporty/raport_z_dnia_{$data_wystawienia}.txt", $data);
echo 'Plik został wygenerowany!<br>';
?>