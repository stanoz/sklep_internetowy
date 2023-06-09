<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
</head>
<body>
<h1 align="center">Rejestracja użytkownika</h1><br>
<hr color="grey">
<br>
<form method="post">
    <label>
        Podaj imię: <input type='text' name='imie' required><br>
        Podaj nazwisko: <input type='text' name='nazwisko' required><br>
        Podaj adres email: <input type='email' name='email' required><br>
        Podaj hasło: <input type='password' name='haslo' minlength="6" required><br>
        *hasło ma mieć przynajmniej 6 znaków i jeden znak specjalny<br><br>
        <input type="submit" name='zarejestruj' value="Zarejestruj się"><br>
    </label>
</form>
<a href="../logowanie_uzytkownika/logowanie.php">Mam już konto (zaloguj się)</a><br>
<?php
if ($_SESSION['fromsite'] === "main") {
    echo '<a href="../strona_glowna.php">Powrót do strony głównej</a><br>';
} elseif ($_SESSION['fromsite'] === "details") {
    echo '<a href="../produkt_szczegoly/szczegoly_produkt.php">Powrót</a><br>';
}elseif ($_SESSION['fromsite'] === 'opinion'){
    echo '<a href="../dodawanie_opini/dodaj_opinie.php">Powrót</a><br>';
}
?>
<?php
if (isset($_POST['zarejestruj'])) {
    if (empty($_POST['imie']) || empty($_POST['nazwisko']) || empty($_POST['email']) || empty($_POST['haslo'])) {
        echo "Brak danych!<br>";
    } else {
        if (preg_match("/^[^.].(([a-zA-Z0-9\.\-\!\#\$\%\&\'\*\+\/\=\?\^\_\`\{\|\}\~])(?!\.\.)){1,64}@{1}[a-zA-Z0-9\-]{1,255}\.[a-zA-Z]{2,3}(\.[a-zA-Z]{2,3})?$/", $_POST['email'])) {
            if (preg_match('/^(?=.*[!@#\$%\^&*()\-=_+[\]{};\':\"|,.<>\/?])\S{6,}$/', $_POST['haslo'])) {
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
                    $imie = $_POST['imie'];
                    $nazwisko = $_POST['nazwisko'];
                    $email = $_POST['email'];
                    $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
                    $typ = 2;
                    //sprawdzenie_czy_taki_uzytkownik_istnieje
                    $userAlreadyExists = false;
                    $query = "SELECT adres_email FROM uzytkownicy";
                    $result = $db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        if ($email === $row['adres_email']) {
                            $userAlreadyExists = true;
                        }
                    }
                    if (!$userAlreadyExists) {
                        //rejestracja
                        $query = "INSERT INTO uzytkownicy (imie, nazwisko, adres_email, haslo, id_typ_uzytkownika) 
                                  VALUES ('$imie','$nazwisko','$email','$haslo','$typ')";
                        if ($db->query($query)) {
                            header('Location:../logowanie_uzytkownika/logowanie.php');
                        } else {
                            echo "Rejestracja nie powiodła się! Spróbój ponownie.<br>";
                        }
                    }else{
                        echo "Konto o takim adresie email już istnieje!<br>";
                    }
                    $db = null;
                } else {
                    echo "Nie połączono z bazą danych<br>";
                }
            } else {
                echo "Za słabe hasło!<br>";
            }
        } else {
            echo "Niepoprawny adres email!<br>";
        }

    }
}
?>
</body>
</html>