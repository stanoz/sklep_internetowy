<?php
//session_start();
?>
<?php

trait Sprawdzenie
{
    public function czyMoznaDodac($idProdukt)
    {
        $dbuser = 'root';
        $dbpassword = '';
        $connected = false;
        $db = null;
        $moznaDodac = false;
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
            $query = "SELECT ilosc FROM produkty WHERE ID_produkt='$idProdukt'";
            $result = $db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if ($row['ilosc'] >= 1) {
                    $moznaDodac = true;
                }
            }
        }
        $db = null;
        return $moznaDodac;
    }

    public function czyMoznaOdjac($iloscKoszyk)
    {
        return $iloscKoszyk - 1 >= 0;
    }
}

class Koszyk
{
    use Sprawdzenie;

    private array $produktyWKoszyku;//asocjacyjna_idprodukt_ilosc

    public function __construct()
    {
    }

    public function dodaj()
    {
        $idProdukt = $_SESSION['koszykIDprodukt'];
        if ($this->czyMoznaOdjac($idProdukt)) {
            if (count($this->produktyWKoszyku) === 0) {//tablica_jest_pusta
                $this->produktyWKoszyku[$idProdukt] = 1;
            } else {//w_tablicy_cos_jest
                $produktJest = false;
                foreach ($this->produktyWKoszyku as $produkt) {
                    if ($produkt === $idProdukt) {
                        $this->produktyWKoszyku[$produkt]++;
                        $produktJest = true;
                    }
                }
                if (!$produktJest) {
                    $this->produktyWKoszyku[$idProdukt] = 1;
                }
                //zmniejszenie_ilosci_w_bazie_danych
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
                    $query = "UPDATE produkty SET ilosc=ilosc-1 WHERE ID_produkt='$idProdukt'";
                    if (!$db->query($query)) {
                        echo 'Nie udało się wprowadzić zmian w stan magazynowy produktu!<br>';
                    }
                }
                $db = null;
            }
        } else {
            echo 'Brak produktu w magazynie!<br>';
        }
    }

    public function odejmij()
    {
        $idProdukt = $_SESSION['koszykIDprodukt'];
        if (count($this->produktyWKoszyku) > 0) {
            $produktJest = false;
            foreach ($this->produktyWKoszyku as $produkt) {
                if ($produkt === $idProdukt) {
                    $produktJest = true;
                }
            }
            if ($produktJest) {
                if ($this->czyMoznaOdjac($this->produktyWKoszyku[$idProdukt])) {
                    $this->produktyWKoszyku[$idProdukt]--;
                    //zwiekszenie_ilosci_produktu_w_magazynie
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
                        $query = "UPDATE produkty SET ilosc=ilosc+1 WHERE ID_produkt='$idProdukt'";
                        if (!$db->query($query)) {
                            echo 'Nie udało się wprowadzić zmian w stan magazynowy produktu!<br>';
                        }
                    }
                    $db = null;
                }
            } else {
                echo 'W koszyku nie ma tego produktu!<br>';
            }
        } else {
            echo 'Koszyk nie istnieje!<br>';
        }
    }

    public function obliczWartosc()
    {
        $wartosc = 0;
        foreach ($this->produktyWKoszyku as $produkt => $ilosc) {
            //pobieranie_ceny_produktu_i_liczenie_wartosc
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
                $query = "SELECT cena FROM produkty WHERE ID_produkt='$produkt'";
                $result = $db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $wartosc += doubleval($row['cena']) * $ilosc;
                }
            }
            $db = null;
        }
        return $wartosc;
    }
}
?>
