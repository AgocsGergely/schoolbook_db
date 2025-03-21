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
function addSubject($subject){
    global $servername, $username, $password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");
    
    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }
    
    
    // UPDATE parancs előkészítése
    $sql = "INSERT INTO subjects (name)
    SELECT ?
    WHERE NOT EXISTS (
        SELECT 1 FROM subjects WHERE name = ?
    );";
    
            
    
    // Előkészített parancs használata
    $stmt = $kapcsolat->prepare($sql);
    $stmt->bind_param("ss",$subject,$subject);
    if ($stmt->execute()) {
        header("Location: schoolbook.php");
        echo "<script>alert('Sikeres hozzáadás!');</script>";
    } else {
        echo "Hiba történt: " . $kapcsolat->error;
    }
    
    // Kapcsolat lezárása
    $stmt->close();
    $kapcsolat->close();
}
function addYear($year,$class){
    global $servername, $username, $password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");
    
    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }
    
    
    // UPDATE parancs előkészítése
    $sql = "INSERT INTO classes (code, year)
    SELECT ?, ?
    WHERE NOT EXISTS (
        SELECT 1 FROM classes WHERE code = ? AND year = ?
    );";
    
            
    
    // Előkészített parancs használata
    $stmt = $kapcsolat->prepare($sql);
    $stmt->bind_param("sisi",  $class,$year,$class,$year);
    if ($stmt->execute()) {
        header("Location: schoolbook.php");
        echo "<script>alert('Sikeres hozzáadás!');</script>";
    } else {
        echo "Hiba történt: " . $kapcsolat->error;
    }
    
    // Kapcsolat lezárása
    $stmt->close();
    $kapcsolat->close();

}
function getSubjects(){
    global $servername,$username,$password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");

    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }

    // Lekérdezés végrehajtása
    $eredmeny = $kapcsolat->query("SELECT name FROM subjects");

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
function deleteSubject($subjectToDelete) {
    global $servername, $username, $password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");


    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }


    $kapcsolat->begin_transaction();

    try {
        //Első törlés: grades tábla
        $sql2 = "
DELETE FROM `grades` WHERE subject_id = (SELECT subject_id FROM grades g JOIN subjects s ON g.subject_id = s.id WHERE s.name = ? LIMIT 1);";
        $stmt2 = $kapcsolat->prepare($sql2);
        if ($stmt2 === false) {
            throw new Exception("Prepare failed for grades: " . $kapcsolat->error);
        }
        $stmt2->bind_param("s", $subjectToDelete);
        $stmt2->execute();
        $stmt2->close();

        //Második törlés: subjects tábla
        $sql1 = "DELETE FROM `subjects` WHERE `name` = ?";
        $stmt1 = $kapcsolat->prepare($sql1);
        if ($stmt1 === false) {
            throw new Exception("Prepare failed for subjects: " . $kapcsolat->error);
        }
        $stmt1->bind_param("s", $subjectToDelete);
        $stmt1->execute();
        $stmt1->close();

        

        $kapcsolat->commit();


        echo "<script>alert('Sikeres törlés!');</script>";

        header("Location: schoolbook.php");
    } catch (Exception $e) {

        $kapcsolat->rollback();
        echo "<script>alert('Hiba történt: " . $kapcsolat->error . "');</script>";
    }

    $kapcsolat->close();
}
function deleteYear($yearToDelete){
    global $servername, $username, $password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");
    
    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }
    
    
    // UPDATE parancs előkészítése
    $sql = "DELETE FROM `classes`
WHERE `year` = ?;";
    
            
    
    // Előkészített parancs használata
    $stmt = $kapcsolat->prepare($sql);
    $stmt->bind_param("i",  $yearToDelete);
    if ($stmt->execute()) {
        header("Location: schoolbook.php");
        echo "<script>alert('Sikeres törlés!');</script>";
    } else {
        echo "Hiba történt: " . $kapcsolat->error;
    }
    
    // Kapcsolat lezárása
    $stmt->close();
    $kapcsolat->close();
}
function modifySubject($subject, $modifiedSubject){
    global $servername, $username, $password;
    $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");
    
    // Kapcsolat ellenőrzése
    if ($kapcsolat->connect_error) {
        die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
    }
    
    
    // UPDATE parancs előkészítése
    $sql = "UPDATE `subjects` SET `name` = ? WHERE `name` = ?";
    
            
    
    // Előkészített parancs használata
    $stmt = $kapcsolat->prepare($sql);
    $stmt->bind_param("ss", $modifiedSubject, $subject);
    if ($stmt->execute()) {
        header("Location: schoolbook.php");
        echo "<script>alert('Sikeres frissítés!')</script>";
       
        
    } else {
        echo "Hiba történt: " . $kapcsolat->error;
    }
    
    // Kapcsolat lezárása
    $stmt->close();
    $kapcsolat->close();
}
function modifyYear($year,$modifiedYear){
    global $servername, $username, $password;
$kapcsolat = new mysqli($servername, $username, $password, "schoolbook");

// Kapcsolat ellenőrzése
if ($kapcsolat->connect_error) {
    die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
}


// UPDATE parancs előkészítése
$sql = "UPDATE `classes` SET `year` = ? WHERE `year` = ?";

        

// Előkészített parancs használata
$stmt = $kapcsolat->prepare($sql);
$stmt->bind_param("ii", $modifiedYear, $year);
if ($stmt->execute()) {
    header("Location: schoolbook.php");
    echo "<script>alert('Sikeres frissítés!')</script>";
   
    
} else {
    echo "Hiba történt: " . $kapcsolat->error;
}

// Kapcsolat lezárása
$stmt->close();
$kapcsolat->close();
}


function getClassDataByStudentId($classId){
    
    global $servername,$username,$password;
        $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");
    
        // Kapcsolat ellenőrzése
        if ($kapcsolat->connect_error) {
            die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
        }
    
        // Lekérdezés végrehajtása
        $eredmeny = $kapcsolat->query("
SELECT c.year as ev, c.code as osztaly,su.name as tantargy, AVG(g.grade) as atlag from grades g
JOIN students s ON s.id = g.student_id
JOIN classes c ON s.class_id = c.id
JOIN subjects su ON su.id = g.subject_id
WHERE c.id = $classId
GROUP BY s.class_id, g.subject_id");
    
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
SELECT c.id as classId, c.code as osztaly,c.year as ev,s.name as nev, su.name as tantargy, round(AVG(g.grade),2) as atlag FROM students s
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

function getHallOfFame(){
    

global $servername,$username,$password;
        $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");
    
        // Kapcsolat ellenőrzése
        if ($kapcsolat->connect_error) {
            die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
        }
    
        // Lekérdezés végrehajtása
        $eredmeny = $kapcsolat->query("
SELECT s.name, ROUND(AVG(g.grade), 2) AS atlag, c.code as osztaly, c.year as ev 
FROM students s
JOIN grades g ON g.student_id = s.id
JOIN classes c ON c.id = s.class_id
WHERE class_id = (SELECT c.id FROM grades g
                  JOIN students s ON s.id = g.student_id
                  JOIN classes c ON s.class_id = c.id
                  GROUP BY s.class_id
                  ORDER BY AVG(g.grade) DESC
                  LIMIT 1)
GROUP BY s.id, s.name, c.code, c.year
ORDER BY atlag DESC
LIMIT 10;");
    
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

function getTop10($year){
    
    global $servername,$username,$password;
        $kapcsolat = new mysqli($servername, $username, $password, "schoolbook");
    
        // Kapcsolat ellenőrzése
        if ($kapcsolat->connect_error) {
            die("Kapcsolódási hiba: " . $kapcsolat->connect_error);
        }
    
        // Lekérdezés végrehajtása
        $eredmeny = $kapcsolat->query("
    SELECT name, round(AVG(g.grade),2) as atlag, year FROM students s
JOIN grades g ON g.student_id = s.id
JOIN classes c ON c.id = s.class_id
where year = $year
group by name
ORDER by atlag DESC
LIMIT 10");
    
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