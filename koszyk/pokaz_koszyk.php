<?php
session_start();
require_once ('dodaj_do_koszyka.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Koszyk</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<hr color="grey">
<br>
<?php
if ($_SESSION['fromsite'] === "main") {
    echo '<a href="../strona_glowna.php">Powrót do strony głównej</a><br>';
} elseif ($_SESSION['fromsite'] === "details") {
    echo '<a href="../produkt_szczegoly/szczegoly_produkt.php">Powrót</a><br>';
}elseif ($_SESSION['fromsite'] === 'opinion'){
    echo '<a href="../dodawanie_opini/dodaj_opinie.php">Powrót</a><br>';
}elseif ($_SESSION['fromsite'] === 'cart'){
    echo '<a href="../koszyk/pokaz_koszyk.php">Powrót</a><br>';
}
?>
<form method="post">
    <?php
    $_SESSION['login'] = false;
    if (isset($_POST['signout'])) {
        $_SESSION['login'] = false;
        unset($_SESSION['user_id']);
    }
    if (isset($_POST['signin'])) {
        $_SESSION['fromsite'] = "cart";
        header('Location:../logowanie_uzytkownika/logowanie.php');
    }
    if (isset($_POST['register'])) {
        $_SESSION['fromsite'] = "cart";
        header('Location:../rejestracja_uzytkownika/rejestracja.php');
    }
    echo '<p align="right"><input type="submit" name="register" value="Zarejestruj się"></p>';
    if (isset($_SESSION['login'])) {
        if ($_SESSION['login']) {
            echo '<p align="right"><input type="submit" name="signout" value="Wyloguj się"></p>';
        } else {
            echo '<p align="right"><input type="submit" name="signin" value="Zaloguj się"></p>';
        }
    } else {
        echo '<p align="right"><input type="submit" name="signin" value="Zaloguj się"></p>';
    }
    ?>
</form>
<?php
echo '<p align="center"><b>Zawartość koszyka:</b></p>';
if (isset($_SESSION['koszyk'])){
    $koszyk = unserialize($_SESSION['koszyk']);
    if (isset($_POST['deletefromcart'])){
        $_SESSION['koszykIDprodukt'] = $_POST['koszyk_id_produktu'];
        $koszyk->odejmij();
    }
    $koszyk->wyswietl();
    $doZaplaty = $koszyk->obliczWartosc();
    $_SESSION['koszyk'] = serialize($koszyk);
    echo 'Całkowita wartość koszyka: '.$doZaplaty.' zł<br>';
}else{
    echo '<p align="center">Koszyk jest pusty!</p>';
}
?>
</body>
</html>