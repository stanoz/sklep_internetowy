<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
</head>
<body>
<h1 align="center">Logowanie użytkownika</h1><br>
<hr color="grey">
<br>
<form method="post">
    <label>
        Podaj adres email: <input type='email' name='email' required><br>
        Podaj hasło: <input type='password' name='haslo' minlength="6" required><br>
        <input type="submit" name='zaloguj' value="Zaloguj się"><br>
    </label>
</form>
<?php
if ($_SESSION['fromsite'] === "main") {
    echo '<a href="../strona_glowna.php">Powrót do strony głównej</a><br>';
} elseif ($_SESSION['fromsite'] === "details") {
    echo '<a href="../produkt_szczegoly/szczegoly_produkt.php">Powrót</a><br>';
}elseif ($_SESSION['fromsite'] === 'opinion'){
    echo '<a href="../dodawanie_opini/dodaj_opinie.php">Powrót</a><br>';
}elseif ($_SESSION['fromsite'] === 'cart'){
    if (isset( $_SESSION['previousfromsite'])) {
        $_SESSION['fromsite'] = $_SESSION['previousfromsite'];
    }
    echo '<a href="../koszyk/pokaz_koszyk.php">Powrót</a><br>';
}
echo 'Nie masz konta? <a href="../rejestracja_uzytkownika/rejestracja.php">Zarejestruj się</a><br>';
?>
<?php
if (isset($_POST['zaloguj'])) {
    if (empty($_POST['email']) || empty($_POST['haslo'])) {
        echo "Brak danych!<br>";
    } else {
        if (preg_match("/^[^.].(([a-zA-Z0-9\.\-\!\#\$\%\&\'\*\+\/\=\?\^\_\`\{\|\}\~])(?!\.\.)){1,64}@{1}[a-zA-Z0-9\-]{1,255}\.[a-zA-Z]{2,3}(\.[a-zA-Z]{2,3})?$/", $_POST['email'])) {
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
                $login = false;
                $email = $_POST['email'];
                $haslo = $_POST['haslo'];
                $query = "SELECT haslo,ID_uzytkownik FROM uzytkownicy WHERE adres_email='$email'";
                $result = $db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    if (password_verify($haslo, $row['haslo'])) {
                        $login = true;
                        $_SESSION['user_id'] = $row['ID_uzytkownik'];
                    }
                }
                $db = null;
                if ($login) {
                    $_SESSION['login'] = true;
                    if ($_SESSION['fromsite'] === "main") {
                        header('Location:../strona_glowna.php');
                    } elseif ($_SESSION['fromsite'] === "details") {
                        header('Location:../produkt_szczegoly/szczegoly_produktu.php');
                    }elseif ($_SESSION['fromsite'] === 'opinion'){
                        header('Location:../dodawanie_opini/dodaj_opinie.php');
                    }elseif ($_SESSION['fromsite'] === 'cart'){
                        $_SESSION['fromsite'] = $_SESSION['previousfromsite'];
                         header('Location:../koszyk/pokaz_koszyk.php');
                    }
                } else {
                    echo "Nie udało się zalogować! Spróbój ponownie.<br>";
                }
            } else {
                echo "Nie połączono z bazą danych<br>";
            }
        } else {
            echo "Niepoprawny adres email!<br>";
        }

    }
}
?>
</body>
</html>