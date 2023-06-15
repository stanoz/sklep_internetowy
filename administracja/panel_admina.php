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
            <td align="center">Raport z ostatniego miesiąca</td>
            <td align="center"><input type="submit" name="raport" value="Wygeneruj"></td>
        </tr>
    </table>
</form>
</body>
</html>