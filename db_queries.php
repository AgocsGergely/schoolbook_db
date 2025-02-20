<?php
require_once 'db_create.php';

function getYears(){
    global $servername,$username,$password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");

    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }

    // Lekérdezés végrehajtása
    $eredmeny = $kapcsolat->query("SELECT year FROM classes group by year");

    if ($eredmeny->num_rows > 0) {
        // Adatok tömbbe mentése
        $adatok = $eredmeny->fetch_all(MYSQLI_ASSOC);
        $kapcsolat->close();
        return $adatok; // Ellenőrzéshez kiíratás
    } else {
        echo "Nincs találat.";
        return [];
    }

}



function getClass($year){
    global $servername,$username,$password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");

    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }

    // Lekérdezés végrehajtása
    $eredmeny = $kapcsolat->query("SELECT code FROM classes where year = $year");

    if ($eredmeny->num_rows > 0) {
        // Adatok tömbbe mentése
        $adatok = $eredmeny->fetch_all(MYSQLI_ASSOC);
        $kapcsolat->close();
        return $adatok; // Ellenőrzéshez kiíratás
    } else {
        echo "Nincs találat.";
        return [];
    }

}
?>