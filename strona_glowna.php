<?php
session_start();
require_once ('koszyk/dodaj_do_koszyka.php');
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
    <input type="text" name="searchNazwaOpis" placeholder="Wyszukaj">
    <select name="searchKategoria">
        <option value="1">ołówki</option>
        <option value="2">długopisy</option>
        <option value="3">temperówki</option>
        <option value="4">przyrządy do mierzenia</option>
        <option value="5">inne</option>
        <option value="all">wszystkie kategorie</option>
    </select>
    <input type="submit" name="searchProdukt" value="Wyszukaj">
</form>
<form method="post">
    <?php
    $_SESSION['login'] = false;
    if (isset($_POST['addtocart'])){
        $_SESSION['koszykIDprodukt'] = $_POST['koszyk_id_produktu'];
       if (isset($_SESSION['koszyk'])){
           $koszyk = $_SESSION['koszyk'];
           $koszyk->dodaj();
           $_SESSION['koszyk'] = serialize($koszyk);
       }else{
           $koszyk = new Koszyk();
           $koszyk->dodaj();
           $_SESSION['koszyk'] = serialize($koszyk);
       }
    }
    if (isset($_SESSION['idprodukt'])) {
        unset($_SESSION['idprodukt']);
    }
    if (isset($_POST['signout'])) {
        $_SESSION['login'] = false;
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
$query = "";
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=sklep_internetowy", $dbuser, $dbpassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $connected = true;
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}
if ($connected) {
    $db->exec("SET NAMES utf8");//szukanie
    if (isset($_POST['searchProdukt'])) {
        if (isset($_POST['searchNazwaOpis']) && !empty($_POST['searchNazwaOpis'])) {//nazwa_lub_opis
            $search = $_POST['searchNazwaOpis'];
            $query = "SELECT * FROM `produkty` WHERE `nazwa` LIKE '%$search%' OR `opis` LIKE '%$search%' OR `cena` LIKE '%$search%'";
        } else {//kategoria
            $idKategoria = $_POST['searchKategoria'];
            if (is_numeric($idKategoria)) {
                $query = "SELECT * FROM produkty WHERE id_kategoria='$idKategoria'";
            }else{
                $query = "SELECT * FROM produkty";
            }
        }
    } else {
        $query = "SELECT * FROM produkty";
    }
    echo "<h2 align='center'>Wszystkie produkty:</h2>";
    echo '<table align="center">';
    $result = $db->query($query);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        $photoPath = 'photos/' . $row['zdjecie'];
        echo '<td><img src="' . $photoPath . '" alt="' . $photoPath . '" width="150px" height="150px"></td>';
        echo '<td><a href="produkt_szczegoly/szczegoly_produkt.php?id=' . $row['ID_produkt'] . '">' . $row['nazwa'] . '</a></td>';
        echo '<td> &nbsp' . $row['cena'] . '&nbspzł</td>';
        echo '<td> &nbsp<form method="post">
        <input type="hidden" name="koszyk_id_produktu" value="' . $row['ID_produkt'] . '">
        <input type="submit" name="addtocart" value="Dodaj do koszyka">
        </form></td>';
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
