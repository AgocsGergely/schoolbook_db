<?php
require_once 'db_create.php';

function getStudents($classId){
    global $servername,$username,$password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");

    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }

    // Lekérdezés végrehajtása
    $eredmeny = $kapcsolat->query("
                            SELECT name, s.id, gender, round(AVG(g.grade),2) as avg FROM students s
                            JOIN grades g ON g.student_id = s.id
                            where class_id = $classId
                            group by name
                            ORDER by name");

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
    $eredmeny = $kapcsolat->query("SELECT code, id FROM classes where year = $year");

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