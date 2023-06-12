<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reklamacja</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<hr color="grey">
<br>
<h2 align="center">Reklamacja</h2><br>
<?php
if (isset($_POST['zlozreklamacje'])){
    if (!empty($_POST['trescreklamacji'])){
        if (preg_match('#^\s+$#',$_POST['trescreklamacji'])){
            echo 'Niepoprawne dane!<br>';
        }else{
            if (strlen($_POST['trescreklamacji']) <= 255) {
                $idUzytkownik = $_SESSION['user_id'];
                $idZamowieniaReklamacja = $_SESSION['id_zamowienia_reklamacja'];
                $tresc = $_POST['trescreklamacji'];
                $dataZlozenia = date("Y-m-d");
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
                    $query = "INSERT INTO reklamacje(id_uzytkownik, id_zamowienia, tresc, data_zlozenia) 
                              VALUES ('$idUzytkownik','$idZamowieniaReklamacja','$tresc','$dataZlozenia')";
                    $db->query($query);
                }
                $db = null;
                echo '<h2 align="center">Reklamacja została złożona.</h2>';
                header('Location:../konto_uzytkownika/szczegoly_konta.php');
            }else{
                echo "Twoja wiadomość jest zbyt długa!<br>";
            }
        }
    }else{
        echo 'Brak danych!<br>';
    }
}
?>
<a href="../konto_uzytkownika/szczegoly_konta.php">Powrót.</a>
<?php
$idZamowieniaReklamacja = $_SESSION['id_zamowienia_reklamacja'];
echo 'Zawartość zamówienia:<br>';
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
    $query = "SELECT * FROM zamowione_produkty WHERE ID_zamowienia='$idZamowieniaReklamacja'";
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
?>
<br>
<form method="post">
    <labeL>
        Treść:<br>
        <input type="text" name="trescreklamacji" placeholder="Napisz wiadomość" maxlength="255" required><br>
        <input type="submit" name="zlozreklamacje" value="Złóż reklamację"><br>
    </labeL>
</form>
</body>
</html>