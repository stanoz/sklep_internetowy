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
<?php
if (isset($_POST['reklamacje'])){
    header('Location:admin_reklamacje.php');
}
if (isset($_POST['zamowienia'])){
    header('Location:admin_zamowienia.php');
}
if (isset($_POST['magazyn'])){
    header('Location:admin_magazyn.php');
}
if (isset($_POST['uzytkownicy'])){
    header('Location:admin_uzytkownicy.php');
}
if (isset($_POST['rabaty'])){
    header('Location:admin_rabaty.php');
}
if (isset($_POST['raport'])){
    include 'admin_raport.php';
}
?>
<h1 align="center">Sklep internetowy z artykułami biurowymi</h1><br>
<h2 align="center">Panel administracyjny</h2><br>
<hr color="grey">
<br>
<a href="../strona_glowna.php">Powrót do strony głównej.</a><br>
<form method="post">
    <table align="center" cellspacing="20px">
        <tr>
            <td align="center">Reklamacje</td>
            <td align="center"><input type="submit" name="reklamacje" value="Zarządzaj"></td>
        </tr>
        <tr>
            <td align="center">Zamówienia</td>
            <td align="center"><input type="submit" name="zamowienia" value="Zarządzaj"></td>
        </tr>
        <tr>
            <td align="center">Magazyn</td>
            <td align="center"><input type="submit" name="magazyn" value="Zarządzaj"></td>
        </tr>
        <tr>
            <td align="center">Użytkownicy</td>
            <td align="center"><input type="submit" name="uzytkownicy" value="Zarządzaj"></td>
        </tr>
        <tr>
            <td align="center">Rabaty</td>
            <td align="center"><input type="submit" name="rabaty" value="Zarządzaj"></td>
        </tr>
        <tr>
            <td align="center">Raport z ostatniego miesiąca</td>
            <td align="center"><input type="submit" name="raport" value="Wygeneruj"></td>
        </tr>
    </table>
</form>
</body>
</html>