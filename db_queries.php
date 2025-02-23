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

function getClassByID($id){
    global $servername,$username,$password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");

    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }

    // Lekérdezés végrehajtása
    $eredmeny = $kapcsolat->query("SELECT code, id, year FROM classes where id = $id");

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

function getClassByYear($year){
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

function getSubjectDataByStudentId($studentId){
    
global $servername,$username,$password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");

    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }

    // Lekérdezés végrehajtása
    $eredmeny = $kapcsolat->query("
SELECT c.code as osztaly,c.year as ev,s.name as nev, su.name as tantargy, round(AVG(g.grade),2) as atlag FROM students s
JOIN grades g ON g.student_id = s.id
JOIN subjects su ON su.id = g.subject_id
JOIN classes c ON c.id = s.class_id
where g.student_id = $studentId
group by g.student_id, g.subject_id
ORDER by s.name");

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

function getClassAVG($classId){
    
global $servername,$username,$password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");

    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }

    // Lekérdezés végrehajtása
    $eredmeny = $kapcsolat->query("
SELECT AVG(grade) as atlag FROM grades g
join students s ON s.id = g.student_id
JOIN classes c ON c.id = s.class_id
where class_id = $classId
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
?>