<?php
session_start();
require_once('../koszyk/dodaj_do_koszyka.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tworzenie zamówienia</title>
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
} elseif ($_SESSION['fromsite'] === 'opinion') {
    echo '<a href="../dodawanie_opini/dodaj_opinie.php">Powrót</a><br>';
} elseif ($_SESSION['fromsite'] === 'cart') {
    echo '<a href="../koszyk/pokaz_koszyk.php">Powrót</a><br>';
}
?>
<p align="center">Składanie zamówienia</p>
<?php
if (isset($_POST['doplatnosci'])) {
    $doZaplaty = $_SESSION['dozaplaty'];
    $kodRabatowy = false;
    if (!empty($_POST['rabat'])) {
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
            $query = "SELECT * FROM rabaty";
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if ($_POST['rabat'] === $row['kod']) {
                    $kodRabatowy = true;
                }
            }
        }
        $db = null;
    }
    $_SESSION['czy_rabat'] = $kodRabatowy;
    if ($kodRabatowy) {
        $doZaplaty = round($doZaplaty*0.8,2);
        $_SESSION['dozaplaty'] = $doZaplaty;
    }
    if (!empty($_POST['phonenumber']) && !empty($_POST['city']) && !empty($_POST['street']) && !empty($_POST['kodpocztowy']) && !empty($_POST['nrdomu']) && !empty($_POST['formaplatnosci'])) {
        if (preg_match('/^[0-9]{2}-[0-9]{3}$/', $_POST['kodpocztowy'])) {
            if (preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{3,6}$/', $_POST['phonenumber'])) {
                if (!empty($_POST['nrmieszkania'])) {
                    if (is_int($_POST['nrdomu'])) {//jest_nrmieszkania
                        $_SESSION['phonenumber'] = $_POST['phonenumber'];
                        $_SESSION['city'] = $_POST['city'];
                        $_SESSION['street'] = $_POST['street'];
                        $_SESSION['kodpocztowy'] = $_POST['kodpocztowy'];
                        $_SESSION['nrdomu'] = $_POST['nrdomu'];
                        $_SESSION['nrmieszkania'] = $_POST['nrmieszkania'];
                        $_SESSION['formaplatnosci'] = $_POST['formaplatnosci'];
                        header('Location:platnosc.php');
                    } else {
                        echo 'Niepoprawne dane!<br>';
                    }
                } else {//nie_ma_nrmieszkania
                    $_SESSION['phonenumber'] = $_POST['phonenumber'];
                    $_SESSION['city'] = $_POST['city'];
                    $_SESSION['street'] = $_POST['street'];
                    $_SESSION['kodpocztowy'] = $_POST['kodpocztowy'];
                    $_SESSION['nrdomu'] = $_POST['nrdomu'];
                    $_SESSION['formaplatnosci'] = $_POST['formaplatnosci'];
                    header('Location:platnosc.php');
                }
            } else {
                echo 'Niepoprawne dane!<br>';
            }
        } else {
            echo 'Niepoprawne dane!<br>';
        }
    } else {
        echo 'Brak danych!<br>';
    }
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
}
$db = null;
?>
<form method="post">
    <label>
        Numer telefonu <input type="text" name="phonenumber" minlength="9" required>
        Miasto <input type="text" name="city" required>
        Ulica <input type="text" name="street" required>
        Kod pocztowy <input type="text" name="kodpocztowy" required>
        Numer domu <input type="text" name="nrdomu" required>
        Numer mieszkania <input type="text" name="nrmieszkania">
        Forma płatności <select name="formaplatnosci">
            <option value="kartadebetowa">Karta debetowa</option>
            <option value="blik">BLIK</option>
        </select><br>
        Kod rabatowy <input type="text" name="rabat"><br>
    </label>
    <?php
    $koszyk = unserialize($_SESSION['koszyk']);
    $wartosc = $koszyk->obliczWartosc();
    $_SESSION['dozaplaty'] = $wartosc;
    echo 'Do zapłaty: ' . $wartosc . '<br>';
    echo '<input type="submit" name="doplatnosci" value="Zamawiam i płacę"><br>';
    ?>
</form>
</body>
</html>