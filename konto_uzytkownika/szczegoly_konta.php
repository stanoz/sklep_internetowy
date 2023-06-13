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
<a href="../strona_glowna.php">Powrót do strony głównej.</a><br><br>
<?php
if (isset($_POST['napiszreklamacje'])){
    $_SESSION['id_zamowienia_reklamacja'] = $_POST['id_zamowienia_reklamacja'];
    header('Location:../reklamacje/zloz_reklamacje.php');
}
?>
<?php
$idUzytkownik = $_SESSION['user_id'];
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
    $query = "SELECT * FROM uzytkownicy WHERE ID_uzytkownik='$idUzytkownik'";
    $result = $db->query($query);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo 'Imię: ' . $row['imie'] . '<br>';
        echo 'Nazwisko: ' . $row['nazwisko'] . '<br>';
        echo 'Adres email: ' . $row['adres_email'] . '<br>';
    }
    echo '<h3>Moje zamówienia</h3>';
    $query = "SELECT * FROM zamowienia WHERE ID_uzytkownik='$idUzytkownik'";
    $result = $db->query($query);
    echo '<table cellspacing="30px">';
    echo '<tr>';
    echo '<td align="center">Numer zamówienia</td>';
    echo '<td align="center">Wartość</td>';
    echo '<td align="center">Data złożenia</td>';
    echo '<td align="center">Stan</td>';
    echo '<td align="center"></td>';
    echo '</tr>';
    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
        echo '<tr>';
        echo '<td align="center">'.$row['ID_zamowienia'].'</td>';
        echo '<td align="center">'.$row['wartosc_zamowienia'].'</td>';
        echo '<td align="center">'.$row['data_zlozenia'].'</td>';
        echo '<td align="center">'.$row['stan'].'</td>';
        echo '<form method="post">';
        echo '<td>
        <input type="hidden" name="id_zamowienia_reklamacja" value="' . $row['ID_zamowienia'] . '">
        <input type="submit" name="napiszreklamacje" value="Napisz reklamację"></td>';//czemu_ma_zawsze_ostatnie_idzamowienia
        echo '</form>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<br>';
    echo '<h3>Moje Reklamacje</h3>';
    $query = "SELECT * FROM reklamacje WHERE ID_uzytkownik='$idUzytkownik'";
    $result = $db->query($query);
    echo '<form method="post">';
    echo '<table cellspacing="30px">';
    echo '<tr>';
    echo '<td align="center">Numer reklamacji</td>';
    echo '<td align="center">Numer zamówienia</td>';
    echo '<td align="center">Data złożenia</td>';
    echo '<td align="center">Treść</td>';
    echo '<td align="center">Stan</td>';
    echo '<td align="center"></td>';
    echo '</tr>';
    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
        echo '<tr>';
        echo '<td align="center">'.$row['ID_reklamacja'].'</td>';
        echo '<td align="center">'.$row['id_zamowienia'].'</td>';
        echo '<td align="center">'.$row['data_zlozenia'].'</td>';
        echo '<td align="center">'.$row['tresc'].'</td>';
        echo '<td align="center">'.$row['stan'].'</td>';
        echo '<td>';
        echo '</tr>';
    }
    echo '</table>';
}
$db = null;
?>
</body>
</html>