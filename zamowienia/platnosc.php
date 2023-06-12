<?php
session_start();
require_once('../koszyk/dodaj_do_koszyka.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Płatność</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<hr color="grey">
<br>
<p align="center"><b>Płatność</b></p>
<?php
if (isset($_POST['place'])) {
    $idUzytkownik = $_SESSION['user_id'];
    $nrTelefonu = $_SESSION['phonenumber'];
    $miasto = $_SESSION['city'];
    $ulica = $_SESSION['street'];
    $kodPocztowy = $_SESSION['kodpocztowy'];
    $nrDomu = $_SESSION['nrdomu'];
    $nrMieszkania = 0;
    if (isset($_SESSION['nrmieszkania'])) {
        $nrMieszkania = $_SESSION['nrmieszkania'];
    }
    $formaPlatnosci = $_SESSION['formaplatnosci'];
    $doZaplaty = $_SESSION['dozaplaty'];
    $walidacja = false;
    if ($formaPlatnosci === 'kartadebetowa') {//karta_debetowa
        if (!empty($_POST['nrkarty']) && !empty($_POST['miesiac']) && !empty($_POST['rok']) && !empty($_POST['kodkarta'])) {
            if (preg_match('/^([0-9]{1}\s*){16}$/', $_POST['nrkarty'])) {
                if (is_int($_POST['miesiac']) && is_int($_POST['rok'])) {
                    if ($_POST['miesiac'] > 0 && $_POST['miesiac'] < 12) {
                        if ($_POST['rok'] > 0) {
                            if (preg_match('/^[0-9]{3}$/', $_POST['kodkarta'])) {
                                $walidacja = true;
                            }
                        }
                    }
                }
            }
        }
    } else {//blik
        if (!empty($_POST['kodblik'])) {
            if (preg_match('/^[0-9]{6}$/', $_POST['kodblik']) || preg_match('/^[0-9]{3}-[0-9]{3}$/')) {
                $walidacja = true;
            }
        }
    }
    if ($walidacja) {
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
            $nowyAdres = true;//sprawdzenie_czy jest_juz_taki_adres
            $query = "SELECT * FROM adres WHERE id_uzytkownik='$idUzytkownik'";
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
               if ($miasto === $row['miasto']){
                   if ($ulica === $row['ulica']){
                       if ($kodPocztowy == $row['kod_pocztowy']){
                           if ($nrDomu === $row['nr_domu']){
                               if ($nrMieszkania === $row['nr_mieszkania']){
                                   $nowyAdres = false;
                               }
                           }
                       }
                   }
               }
            }
            if ($nowyAdres) {
                $query = "INSERT INTO adres(id_uzytkownik, miasto, ulica, kod_pocztowy, nr_mieszkania, nr_domu) 
                      VALUES ('$idUzytkownik','$miasto','$ulica','$kodPocztowy','$nrMieszkania','$nrDomu')";
                $db->query($query);
            }
            $idAdres = 0;//branie_id_adresu
            $query = "SELECT ID_adres FROM adres WHERE id_uzytkownik='$idUzytkownik'";
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $idAdres = $row['ID_adres'];
            }
            $adresEmail = "test@test.com";//pobranie_adresu_email
            $query = "SELECT * FROM uzytkownicy WHERE ID_uzytkownik='$idUzytkownik'";
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $adresEmail = $row['adres_email'];
            }
            $noweDaneKontatkowe = true;//sprawdzenie_czy_sa_juz_takie_dane_kontaktowe
            $query = "SELECT * FROM dane_kontaktowe WHERE id_uzytkownik='$idUzytkownik'";
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if ($nrTelefonu === $row['nr_telefonu']){
                    if ($adresEmail === $row['adres_email']){
                        $noweDaneKontatkowe = false;
                    }
                }
            }
            if ($noweDaneKontatkowe) {
                $query = "INSERT INTO dane_kontaktowe(id_uzytkownik, nr_telefonu, adres_email) 
                      VALUES ('$idUzytkownik','$nrTelefonu','$adresEmail')";
                $db->query($query);
            }
            $idDaneKontaktowe = 0;//branie_id_dane_kontakowe
            $query = "SELECT ID_dane_kontaktowe FROM dane_kontaktowe WHERE id_uzytkownik='$idUzytkownik'";
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                $idDaneKontaktowe = $row['ID_dane_kontaktowe'];
            }
            $dataZlozenia = date("Y-m-d");
            $czyRabat = $_SESSION['czy_rabat'];
            $query = "INSERT INTO zamowienia(id_uzytkownik, id_adres, sposob_platnosci, wartosc_zamowienia, id_dane_kontaktowe, stan, data_zlozenia,czy_rabat) 
                      VALUES ('$idUzytkownik','$idAdres','$formaPlatnosci','$doZaplaty','$idDaneKontaktowe','przyjete','$dataZlozenia','$czyRabat')";
            $db->query($query);
            $koszyk = unserialize($_SESSION['koszyk']);
            $produkty = $koszyk->getProduktyWKoszyku();
            $idZamowienia = $db->lastInsertId();
            foreach ($produkty as $id => $ile){
                $query = "INSERT INTO zamowione_produkty(id_produktu, ilosc_sztuk, id_zamowienie) 
                      VALUES ('$id','$ile','$idZamowienia')";
                $db->query($query);
                $query = "UPDATE produkty SET ilosc=ilosc-'$ile' WHERE ID_produkt='$id'";
                $db->query($query);
            }
        }
        $db = null;
        unset($_SESSION['koszyk']);
        echo '<h2 align="center">Zamówienie przyjęte do realizacji!</h2>';
        header('Location:po_zaplacie.php');
    } else {
        echo 'Niepoprawne dane!<br>';
    }
}
?>
<?php
$formaPlatnosci = $_SESSION['formaplatnosci'];
$doZaplaty = $_SESSION['dozaplaty'];
echo 'Do zapłaty: <b>' . $doZaplaty . '</b><br>';
echo '<form method="post">';
echo '<label>';
if ($formaPlatnosci === 'kartadebetowa') {
    echo 'Numer karty <input type="text" name="nrkarty" minlength="16" required><br>';
    echo 'Miesiąc <input type="text" name="miesiac" min="1" max="12" required>';
    echo '/Rok <input type="text" name="rok" min="1" required><br>';
    echo 'Kod zabezpieczający <input type="text" name="kodkarta" required>';
} elseif ($formaPlatnosci === 'blik') {
    echo 'Kod BLIK <input type="text" name="kodblik" minlength="6" required><br>';
} else {
    echo 'Błąd!<br>';
}
echo '<input type="submit" name="place" value="Płacę"><br>';
echo '</label>';
echo '</form>';
?>
</body>
</html>