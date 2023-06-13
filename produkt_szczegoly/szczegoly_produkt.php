<?php
session_start();
require_once ('../koszyk/dodaj_do_koszyka.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sklep internetowy</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<hr color="grey">
<br>
<?php
if (isset($_POST['addtocart'])){
    $_SESSION['koszykIDprodukt'] = $_POST['koszyk_id_produktu'];
    if (isset($_SESSION['koszyk'])){
        $koszyk = unserialize($_SESSION['koszyk']);
        $koszyk->dodaj();
        $_SESSION['koszyk'] = serialize($koszyk);
    }else{
        $koszyk = new Koszyk();
        $koszyk->dodaj();
        $_SESSION['koszyk'] = serialize($koszyk);
    }
}
?>
<form method="post">
    <?php
    if (isset($_SESSION['previousfromsite'])){
        $_SESSION['fromsite'] = $_SESSION['previousfromsite'];
    }
    if ($_SESSION['fromsite'] === "main") {
        echo '<a href="../strona_glowna.php">Powrót do strony głównej</a><br>';
    }elseif ($_SESSION['fromsite'] === 'opinion'){
        echo '<a href="../dodawanie_opini/dodaj_opinie.php">Powrót</a><br>';
    }elseif ($_SESSION['fromsite'] === 'cart'){
        $_SESSION['fromsite'] = $_SESSION['previousfromsite'];
        echo '<a href="../koszyk/pokaz_koszyk.php">Powrót</a><br>';
    }
    if (isset($_POST['signout'])) {
        $_SESSION['login'] = false;
        unset($_SESSION['user_id']);
    }
    if (isset($_POST['signin'])) {
        $_SESSION['fromsite'] = "details";
        header('Location:../logowanie_uzytkownika/logowanie.php');
    }
    if (isset($_POST['register'])) {
        $_SESSION['fromsite'] = "details";
        header('Location:../rejestracja_uzytkownika/rejestracja.php');
    }
    if (isset($_POST['showcart'])){
        $_SESSION['previousfromsite'] = 'main';
        $_SESSION['fromsite'] = "details";
        header('Location:../koszyk/pokaz_koszyk.php');
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
    echo '<p align="right"><input type="submit" name="showcart" value="Koszyk"></p><br>';
    ?>
</form>
<?php
$id = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['idprodukt'] = $id;
} else {
    $id = $_SESSION['idprodukt'];
}
$idOpinia = 0;
$idKategoria = 0;
$dbuser = 'root';
$dbpassword = '';
$connected = false;
$db = null;
$srednia = 0;
$liczba = 0;
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=sklep_internetowy", $dbuser, $dbpassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $connected = true;
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}
if ($connected) {//liczenie_sredniej
    $query = "SELECT id_opinia FROM produkty WHERE ID_produkt='$id'";
    $result = $db->query($query);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
        $id_opinia = $row['id_opinia'];
        $LiczOpinieQuery = "SELECT ocena FROM opinie";
        $LiczOpinieResult = $db->query($LiczOpinieQuery);
        while ($row2 = $LiczOpinieResult->fetch(PDO::FETCH_ASSOC)){
            $srednia += $row2['ocena'];
            $liczba++;
        }
    }
    $db->exec("SET NAMES utf8");
    echo "<h2 align='center'>Informacje o produkcie:</h2>";
    echo '<table align="center">';
    echo '<tr>';
    echo '<th></th>';
    echo '<th align="center">Opis</th>';
    echo '<th align="center">Cena</th>';
    echo '</tr>';
    $query = "SELECT * FROM produkty WHERE ID_produkt='$id'";
    $result = $db->query($query);//szczegoly_produktu
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        $photoPath = '../photos/' . $row['zdjecie'];
        echo '<td><img src="' . $photoPath . '" alt="' . $photoPath . '" width="180px" height="180px"></td>';
        echo '<td align="center"> &nbsp' . $row['opis'] . '</td>';
        echo '<td align="center"> &nbsp' . $row['cena'] . '&nbspzł</td>';
        echo '<td> &nbsp ilość sztuk w magazynie:&nbsp' . $row['ilosc'] . '&nbsp</td>';
        echo '<td> &nbsp<form method="post">
        <input type="hidden" name="koszyk_id_produktu" value="' . $row['ID_produkt'] . '">
        <input type="submit" name="addtocart" value="Dodaj do koszyka">
        </form></td>';
        echo '<form method="post">';
        echo '<td align="center"> &nbsp<input type="submit" name="addopinion" value="Dodaj opinię"></td>';
        echo '</form>';
        echo '</tr>';
        echo '<tr>';
        echo '<td align="center">' . $row['nazwa'] . '</td>';
        echo '</tr>';
        $idOpinia = $row['id_opinia'];
        $idKategoria = $row['id_kategoria'];
    }
    echo '</table>';//opinie
    if ($idOpinia === 0 || is_null($idOpinia)) {
        echo '<p align="center">Brak opinii o produkcie.</p>';
    } else {
        echo 'Średnia ocena o produkcie: '.round($srednia/$liczba).'/10<br>';
        $query = "SELECT * FROM opinie WHERE ID_opinia='$idOpinia'";
        $result = $db->query($query);
        echo '<table align="center">';
        echo '<tr>';
        echo '<th align="center">Użytkownik</th>';
        echo '<th align="center">Data wystawienia</th>';
        echo '<th align="center">Ocena</th>';
        echo '<th align="center">Opinia</th>';
        echo '</tr>';
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $userNameQuery = "SELECT adres_email FROM uzytkownicy WHERE ID_uzytkownik='" . $row['id_uzytkownik'] . "'";
            $userNameResult = $db->query($userNameQuery);
            $userNameRow = $userNameResult->fetch(PDO::FETCH_ASSOC);
            echo '<tr>';
            echo '<td align="center">' . $userNameRow['adres_email'] . '</td>';
            echo '<td align="center">' . $row['data_wystawienia'] . '</td>';
            echo '<td align="center">' . $row['ocena'] . '/10</td>';
            echo '<td align="center">' . $row['opinia'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    //produkty_z_tej_samej_kategorii
    $query = "SELECT * FROM produkty WHERE id_kategoria='$idKategoria'";
    $result = $db->query($query);
    echo '<hr color="grey">';
    echo '<p align="center">Produkty, które mogą Cię zainteresować:</p>';
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if ($id != $row['ID_produkt']) {
            echo '<fieldset>';
            $photoPath = '../photos/' . $row['zdjecie'];
            echo '<img src="' . $photoPath . '" alt="' . $photoPath . '" width="150px" height="150px">';
            echo '<a href="szczegoly_produkt.php?id=' . $row['ID_produkt'] . '">' . $row['nazwa'] . '</a>';
            echo '&nbsp' . $row['cena'] . '&nbspzł';
            echo '<td> &nbsp ilość sztuk w magazynie:&nbsp' . $row['ilosc'] . '&nbsp</td>';
            echo '&nbsp<form method="post">
            <input type="hidden" name="koszyk_id_produktu" value="' . $row['ID_produkt'] . '">
            <input type="submit" name="addtocart" value="Dodaj do koszyka">
            </form>';
            echo '</fieldset>';
        }
    }
} else {
    echo "Nie połączono z bazą danych!<br>";
}
$db = null;
if (isset($_POST['addopinion'])){
    $_SESSION['fromsite'] = "details";
    header('Location:../dodawanie_opini/dodaj_opinie.php');
}
?>
</body>
</html>