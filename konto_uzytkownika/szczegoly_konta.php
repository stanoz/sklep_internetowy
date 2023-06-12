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
<a href="../strona_glowna.php">Powrót do strony głównej.</a>
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
        echo '<td>
        <input type="hidden" name="id_zamowienia_reklamacja" value="' . $row['ID_zamowienia'] . '">
        <input type="submit" name="napiszreklamacje" value="Napisz reklamację"></td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</form>';
}
$db = null;
?>
</body>
</html>