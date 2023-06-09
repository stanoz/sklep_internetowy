<?php
session_start();
//haslo_admin123
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>sklep_internetowy</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<hr color="grey">
<br>
<form method="post">
    <?php
    $_SESSION['login'] = false;
    if (isset($_SESSION['idprodukt'])){
        unset($_SESSION['idprodukt']);
    }
    if (isset($_POST['signout'])) {
        unset($_SESSION['login']);
        unset($_SESSION['user_id']);
    }
    if (isset($_POST['signin'])) {
        $_SESSION['fromsite'] = "main";
        header('Location:logowanie_uzytkownika/logowanie.php');
    }
    if (isset($_POST['register'])) {
        $_SESSION['fromsite'] = "main";
        header('Location:rejestracja_uzytkownika/rejestracja.php');
    }
    echo '<p align="right"><input type="submit" name="register" value="Zarejestruj się"></p>';
    if (isset($_SESSION['login'])) {
        if ($_SESSION['login']) {
            echo '<p align="right"><input type="submit" name="signout" value="Wyloguj się"></p><br>';
        } else {
            echo '<p align="right"><input type="submit" name="signin" value="Zaloguj się"></p><br>';
        }
    } else {
        echo '<p align="right"><input type="submit" name="signin" value="Zaloguj się"></p><br>';
    }
    ?>
</form>
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
if ($connected) {
    $db->exec("SET NAMES utf8");
    echo "<h2 align='center'>Wszystkie produkty:</h2>";
    echo '<table align="center">';
    $query = "SELECT * FROM produkty";
    $result = $db->query($query);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        $photoPath = 'photos/' . $row['zdjecie'];
        echo '<td><img src="' . $photoPath . '" alt="' . $photoPath . '" width="150px" height="150px"></td>';
        echo '<td><a href="produkt_szczegoly/szczegoly_produkt.php?id=' . $row['ID_produkt'] . '">' . $row['nazwa'] . '</a></td>';
        echo '<td> &nbsp' . $row['cena'] . '&nbspzł</td>';
        echo '<td> &nbsp<input type="submit" name="addtocart" value="Dodaj do koszyka"></td>';
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
