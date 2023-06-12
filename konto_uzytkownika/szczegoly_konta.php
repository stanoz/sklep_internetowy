<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Szczegóły konta</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<hr color="grey">
<br>
<?php
$idUzytkownik = $_SESSION['user_id'];
$dbuser = 'root';
$dbpassword = '';
$connected = false;
$db = null;
$imie = "";
$nazwisko = "";
$adresEmail = "";
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
    $query = "SELECT * FROM uzytkownicy";
    $result = $db->query($query);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo 'Imię: ' . $row['imie'] . '<br>';
        echo 'Nazwisko: ' . $row['nazwisko'] . '<br>';
        echo 'Adres email: ' . $row['adres_email'] . '<br>';
    }
    $query = "SELECT * FROM zamowienia";
    $result = $db->query($query);
    echo '<form method="post">';
    echo '<table>';
    echo '<tr>';
    echo '<td align="center">Wartość</td>';
    echo '<td align="center">Data złożenia</td>';
    echo '<td align="center">Stan</td>';
    echo '<td align="center"></td>';
    echo '</tr>';
    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
        echo '<tr>';
        echo '<td align="center">'.$row['wartosc'].'</td>';
        echo '<td align="center">'.$row['data_zlozenia'].'</td>';
        echo '<td align="center">'.$row['stan'].'</td>';
        echo '<td align="center">'.$row['stan'].'</td>';//tu_ma_byc_przycisk_z_id_zamowienia
        echo '</tr>';
    }
    echo '</table>';
    echo '</form>';
}
$db = null;
?>
</body>
</html>