<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dodaj opinię</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<hr color="grey">
<br>
<?php
echo '<a href="../produkt_szczegoly/szczegoly_produkt.php">Powrót</a><br>';
?>
<form method="post">
    <?php
    if (isset($_POST['signout'])) {
        $_SESSION['login'] = false;
        unset($_SESSION['user_id']);
    }
    if (isset($_POST['signin'])) {
        $_SESSION['fromsite'] = "opinion";
        header('Location:../logowanie_uzytkownika/logowanie.php');
    }
    if (isset($_POST['register'])) {
        $_SESSION['fromsite'] = "opinion";
        header('Location:../rejestracja_uzytkownika/rejestracja.php');
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
if (isset($_SESSION['login'])) {
    if (!$_SESSION['login']) {
        echo '<h3 align="center">Aby dodać opinię musisz się zalogować!</h3>';
    } else {//czy_kupil_produkt
        $kupilProdukt = false;
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
            $id_uzytkownik = $_SESSION['user_id'];
            $query = "SELECT id_zamowione_produkty FROM zamowienia WHERE id_uzytkownik='$id_uzytkownik'";
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                $idZamowioneProdukty = $row['id_zamowione_produkty'];
                $queryProdukt = "SELECT id_produktu FROM zamowione_produkty WHERE id_produktu='$idZamowioneProdukty'";
                if ($db->query($queryProdukt)){
                    $kupilProdukt = true;
                }
            }
        }
        $db = null;
        if ($kupilProdukt) {
            echo '<h2 align="center">Dodaj opinię</h2>';
            echo '<form method="post">';
            echo '<label><p>Ocena w skali 1 - 10</p></label>';
            echo '<input type="number" min="1" max="10" name="ocena" required><br>';
            echo '<label></label><textarea name="opinion" rows="5" cols="50" maxlength="255" placeholder="Napisz opinię" required></textarea></label><br>';
            echo '<input type="submit" name="newopinion" value="Dodaj opinię"><br>';
            echo '<form>';
        }else{
            echo '<h3 align="center">Aby dodać opinię musisz kupić ten produkt!</h3>';
        }
    }
} else {
    echo '<h3 align="center">Aby dodać opinię musisz się zalogować!</h3>';
}
?>
<?php
if (isset($_POST['newopinion'])) {
    if (isset($_COOKIE['antyspam'])) {
        echo '<h2 align="center">Opinię można dodać raz na 5 minut!</h2><br>';
    } else {
        $expirationTime = time() + (5 * 60); //5_minut_w_sekundach
        setcookie('opinia', 'antyspam', $expirationTime);
        if (isset($_POST['ocena']) && isset($_POST['opinion'])) {
            if (is_numeric($_POST['ocena'])) {
                if (is_int($_POST['ocena']) && strlen($_POST['opinion']) <= 255) {
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
                        $opinia = $_POST['opinion'];
                        $ocena = $_POST['ocena'];
                        $id_uzytkownik = $_SESSION['user_id'];
                        $data_wystawienia = date("Y-m-d");
                        $query = "INSERT INTO opinie (opinia,ocena,id_uzytkownik,data_wystawienia) 
                        VALUES ('$opinia','$ocena','$id_uzytkownik',$data_wystawienia)";
                        $db->query($query);
                    }
                    $db = null;
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
}
?>
</body>
</html>