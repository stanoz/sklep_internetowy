<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panel administracyjny</title>
</head>
<body>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<h2 align="center">Panel administracyjny</h2><br>
<hr color="grey">
<br>
<a href="panel_admina.php">Powrót do panelu administracyjnego.</a><br>
<?php
if (isset($_POST['ustaw'])){//ustawienie_nowych_danych
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
        $idProdukt = $_POST['id_produkt'];
        if (!empty($_POST['nowanazwa'])){//nowa_nazwa
            $nowaNazwa = $_POST['nowanazwa'];
            if (!preg_match('#^\s+$#',$nowaNazwa)){
                $queryNowaNazwa = "UPDATE produkty SET nazwa='$nowaNazwa' WHERE ID_produkt='$idProdukt'";
                $db->query($queryNowaNazwa);
            }else{
                echo 'Nazwa nie może być spacją!<br>';
            }
        }
        if (!empty($_POST['nowanazwazdjecia'])){//nowa_nazwa_zdjecia
            $nowaNazwaZdjecia = $_POST['nowanazwazdjecia'];
            if (preg_match('#.+\.png$#',$nowaNazwaZdjecia)){
                $queryNowaNazwaZdjecia = "UPDATE produkty SET zdjecie='$nowaNazwaZdjecia' WHERE ID_produkt='$idProdukt'";
                $db->query($queryNowaNazwaZdjecia);
            }else{
                echo 'Niepoprawna nazwa zdjęcia!(sprawdź rozszerzenie pliku)<br>';
            }
        }
        if (!empty($_POST['nowyopis'])){//nowy_opis
            $nowyOpis = $_POST['nowyopis'];
            if (preg_match('#.+\.png$#',$nowyOpis)){
                if (strlen($nowyOpis) <= 255){
                    $queryNowyOpis = "UPDATE produkty SET opis='$nowyOpis' WHERE ID_produkt='$idProdukt'";
                    $db->query($queryNowyOpis);
                }else{
                    echo 'Maksymalna długość opisu to 255 znaków!<br>';
                }
            }else{
                echo 'Opis nie może być spacją!<br>';
            }
        }
        $nowaKategoria = $_POST['nowakategoria'];//nowa_kategoria
        $queryNowaKategoria = "UPDATE produkty SET id_kategoria='$nowaKategoria' WHERE ID_produkt='$idProdukt'";
        $db->query($queryNowaKategoria);
        if (!empty($_POST['nowailoscmagazyn'])){//nowa_ilosc_w_magazynie
            $nowaIlosc = $_POST['nowailoscmagazyn'];
            if (is_numeric($nowaIlosc)) {
                $nowaIlosc = intval($_POST['nowailoscmagazyn']);
                if ($nowaIlosc >= 0) {
                    $queryNowaIlosc = "UPDATE produkty SET ilosc='$nowaIlosc' WHERE ID_produkt='$idProdukt'";
                    $db->query($queryNowaIlosc);
                } else {
                    echo 'Ilość w magazynie musi być >= 0<br>';
                }
            }else{
                echo 'Muisz podać liczbę całkowitą!<br>';
            }
        }
        if (!empty($_POST['nowacena'])){//nowa_cena
            $nowaCena = $_POST['nowacena'];
            if (preg_match('#^[0-9]+\.[0-9]{2}$#',$nowaCena)){
                $nowaCena = round(doubleval($nowaCena),2);
                $queryNowaCena = "UPDATE produkty SET cena='$nowaCena' WHERE ID_produkt='$idProdukt'";
                $db->query($queryNowaCena);
            }else{
                echo 'Niepoprawny format ceny!<br>';
            }
        }
    }
    $db = null;
}
?>
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
if ($connected) {//wyswietlanie_produktow
    $db->exec("SET NAMES utf8");
    $queryProdukty = "SELECT * FROM produkty";
    $resultProdukty = $db->query($queryProdukty);
    echo '<table cellspacing="20px">';
    echo '<tr>';
    echo '<td align="center">ID</td>';
    echo '<td align="center">Nazwa</td>';
    echo '<td align="center">Nazwa zdjęcia(plik.png)</td>';
    echo '<td align="center">Opis</td>';
    echo '<td align="center">Kategoria</td>';
    echo '<td align="center">Ilość sztuk w magazynie</td>';
    echo '<td align="center">Cena za 1 sztukę</td>';
    echo '<td align="center">Nowa nazwa</td>';
    echo '<td align="center">Nowa nazwa zdjęcia(plik.png)</td>';
    echo '<td align="center">Nowy opis</td>';
    echo '<td align="center">Nowa kategoria</td>';
    echo '<td align="center">Nowa ilość sztuk w magazynie</td>';
    echo '<td align="center">Nowa cena za 1 sztukę</td>';
    echo '<td align="center"></td>';
    echo '</tr>';
    while ($rowProdukty = $resultProdukty->fetch(PDO::FETCH_ASSOC)){
        echo '<tr>';
        echo '<td align="center">'.$rowProdukty['ID_produkt'].'</td>';
        echo '<td align="center">'.$rowProdukty['nazwa'].'</td>';
        echo '<td align="center">'.$rowProdukty['zdjecie'].'</td>';
        echo '<td align="center">'.$rowProdukty['opis'].'</td>';
        $idKategoria = $rowProdukty['id_kategoria'];
        $queryKategoria = "SELECT kategoria FROM kategoria WHERE ID_kategoria='$idKategoria'";
        $resultKategoria = $db->query($queryKategoria);
        while ($rowKategoria = $resultKategoria->fetch(PDO::FETCH_ASSOC)){
            echo '<td align="center">'.$rowKategoria['kategoria'].'</td>';
        }
        echo '<td align="center">'.$rowProdukty['ilosc'].'</td>';
        echo '<td align="center">'.$rowProdukty['cena'].'</td>';
        echo '<form method="post">';
        echo '<td align="center">
        <input type="hidden" name="id_produkt" value="' . $rowProdukty['ID_produkt'] . '">
        <input type="text" name="nowanazwa" placeholder="nowa nazwa"></td>';
        echo '<td align="center">
        <input type="hidden" name="id_produkt" value="' . $rowProdukty['ID_produkt'] . '">
        <input type="text" name="nowanazwazdjecia" placeholder="nowa nazwa zdjęcia"></td>';
        echo '<td align="center">
        <input type="hidden" name="id_produkt" value="' . $rowProdukty['ID_produkt'] . '">
        <input type="text" name="nowyopis" placeholder="nowy opis" maxlength="255"></td>';//nowa_kategoria
        echo '<td align="center"><select name="nowakategoria">';
        $query = "SELECT * FROM kategoria";
        $result = $db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo ' <option value="' . $row['ID_kategoria'] . '">' . $row['kategoria'] . '</option>';
        }
        echo '</select></td>';
        echo '<td align="center">
        <input type="hidden" name="id_produkt" value="' . $rowProdukty['ID_produkt'] . '">
        <input type="number" name="nowailoscmagazyn" placeholder="nowa ilość sztuk w magazynie" min="0"></td>';
        echo '<td align="center">
        <input type="hidden" name="id_produkt" value="' . $rowProdukty['ID_produkt'] . '">
        <input type="number" name="nowacena" placeholder="wpisz używając ." min="0"></td>';
        echo '<td align="center">
        <input type="hidden" name="id_produkt" value="' .$rowProdukty['ID_produkt'] . '">
        <input type="submit" name="ustaw" value="Ustaw"></td>';
        echo '</form>';
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