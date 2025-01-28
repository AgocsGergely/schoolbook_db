<?php
require_once "classroom-data.php";
echo"hello";
// Database tulajdonságok:
$servername = "localhost";
$username = "root";
$password = "";

// Create
$conn = new mysqli($servername, $username, $password);

// Check 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CR Scriptek:
function databaseCreate($conn){
    $sql = "
    CREATE DATABASE IF NOT EXISTS schoolbook
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;
    ";

    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
}
}

function createGrades($conn) {
    $sql = "
        CREATE TABLE IF NOT EXISTS schoolbook.grades (
  id int(4) NOT NULL AUTO_INCREMENT,
  student_id int(16) DEFAULT NULL,
  subject_id int(16) DEFAULT NULL,
  grade int(4) NOT NULL,
  date date DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB;
    ";

    if ($conn->query($sql) === TRUE) {
        echo "<br>Grades tábla sikeresen létrehozva!";
    } else {
        echo "Error creating database: " . $conn->error;
}
}
/*Subjects tábla*/
function createSubjects($conn){
    $sql = '
    
CREATE TABLE IF NOT EXISTS schoolbook.Subjects (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(32) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB;
';
if ($conn->query($sql) === TRUE) {
    echo "<br>Subjects tábla sikeresen létrehozva!";
} else {
    echo "Error creating database: " . $conn->error;
}
}
/*Classes tábla*/
function createClasses($conn){
    $sql = '
    CREATE TABLE IF NOT EXISTS schoolbook.Classes (
  id int(5) NOT NULL AUTO_INCREMENT,
  code varchar(3) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB;
';
/*year int(4) DEFAULT NULL,*/
if ($conn->query($sql) === TRUE) {
    echo "<br>Classes tábla sikeresen létrehozva!";
} else {
    echo "Error creating database: " . $conn->error;
}
}
/*Students tábla*/
function createStudents($conn){
    $sql = '
    CREATE TABLE IF NOT EXISTS schoolbook.Studetns (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(50) DEFAULT NULL,
  gender int(11) DEFAULT NULL,
  class_id int(11) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB;';
if ($conn->query($sql) === TRUE) {
    echo "<br>Students tábla sikeresen létrehozva!";
} else {
    echo "Error creating database: " . $conn->error;
}
} 

function HTMLbody(){
    echo "<body>";
    echo "dsadasdsa";
    echo "<form name='nav' method='post' action=''>
    <div>
            <button type='submit' name='install' value='1'>Install now!</button>
            </div>
            <div>
            <button type='submit' name='Refresh' value='1'>Upload now!</button>
            </div>
            </form>
    ";
    echo "</body>";

}
function htmlHead(){
    echo'<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osztálynapló</title>
    <link rel="stylesheet" href="bootstrap.css">
</head>';
}
htmlHead();
HTMLbody();

if (isset($_REQUEST["install"])) {
    databaseCreate($conn);
    createGrades($conn);
    createClasses($conn);
    createStudents($conn);
    createSubjects($conn);
}
if (isset($_REQUEST["Refresh"])) {
    deleteDatabase($conn);
    generateSubjects(DATA,$conn);
    generateClasses(DATA,$conn);
    generateSchoolbook(DATA,$conn);
}

function deleteDatabase($conn) {
    // Adatbázis kiválasztása
    if (!$conn->select_db("schoolbook")) {
        die("Hiba az adatbázis kiválasztásakor: " . $conn->error);
    }

    // SQL utasítások
    $sql = "
        SET FOREIGN_KEY_CHECKS = 0;
        TRUNCATE TABLE Subjects;
        TRUNCATE TABLE Classes;
        TRUNCATE TABLE grades;
        TRUNCATE TABLE Students;
        SET FOREIGN_KEY_CHECKS = 1;
    ";

    // Többutasításos lekérdezés futtatása
    if ($conn->multi_query($sql)) {
        echo "<br>Az összes tábla sikeresen törölve lett!";
        // Minden utasítás feldolgozása
        while ($conn->next_result()) {
            // Kötelező, hogy minden eredményt feldolgozzunk
        }
    } else {
        echo "Hiba az adatbázis törlésekor: " . $conn->error;
    }
}



function generateSubjects($data, $conn) {
    $stmt = $conn->prepare("INSERT INTO Subjects (name) VALUES (?)");
    
    foreach ($data['subjects'] as $subject) {
        $stmt->bind_param("s", $subject); // "s" = string
        if ($stmt->execute()) {
            echo "<br>'$subject' sikeresen hozzáadva a Subjects táblához!";
        } else {
            echo "Hiba az adat beszúrásakor: " . $stmt->error;
        }
    }
    
    $stmt->close();
}



/*Classes generálása */
function generateClasses($data, $conn) {
    // Előkészített SQL lekérdezés
    $stmt = $conn->prepare("INSERT INTO Classes (code) VALUES (?)");

    foreach ($data['classes'] as $class) {
        // Paraméter bekötése
        $stmt->bind_param("s", $class); // "s" = string típus
        if ($stmt->execute()) {
            echo "<br>'$class' sikeresen hozzáadva a Classes táblához!";
        } else {
            echo "Hiba a beszúrás során: " . $stmt->error;
        }
    }

    // Előkészített lekérdezés lezárása
    $stmt->close();
}


/*Diákok létrehozása*/
function generateSchoolbook($data,$conn) {
    foreach ($data["classes"] as $class) {

        $numberOfPeople = rand(10, 15); // Random number of students per class

        for ($i = 0; $i < $numberOfPeople; $i++) {
            $gender = rand(1, 2) == 1 ? "W" : "M"; // Randomly determine gender
            $firstname = $gender == "W"
                ? $data["firstnames"]["women"][rand(0, count($data["firstnames"]["women"]) - 1)]
                : $data["firstnames"]["men"][rand(0, count($data["firstnames"]["men"]) - 1)];
            $lastname = $data["lastnames"][rand(0, count($data["lastnames"]) - 1)];
            $name = $firstname . " " . $lastname;

            /*student létrehozása*/

            $stmt = $conn->prepare("
                INSERT INTO students (name, gender, class_id)
                VALUES (?, ?, (SELECT id FROM classes WHERE code = ?))
            ");

            // Paraméterek bekötése
            $stmt->bind_param("sss", $name, $gender, $class); /* "sss" == string,string,string */
            // Lekérdezés végrehajtása
            if ($stmt->execute()) {
                echo "<br>'$name' sikeresen hozzáadva a Students táblához!";
            } else {
                echo "Hiba a student beszúrásakor: " . $stmt->error;
            }

            // Lekérdezés lezárása
            $stmt->close();
            /*grades létrehozása*/
            foreach ($data["subjects"] as $subject) {
                $grades = [];
                for ($j = 0; $j < rand(0, 5); $j++) {
                    $stmt = $conn->prepare("
                        INSERT INTO grades (student_id, subject_id, grade, date)
                        VALUES (
                            (SELECT id FROM students WHERE name = ? LIMIT 1),
                            (SELECT id FROM Subjects WHERE name = ? LIMIT 1),
                            ?, NOW()
                        )
                    ");

                    // Véletlen jegy generálása
                    $grade = rand(1, 5);

                    // Paraméterek bekötése
                    $stmt->bind_param("ssi", $name, $subject, $grade);

                    // Lekérdezés végrehajtása
                    if ($stmt->execute()) {
                        echo "<br>'$name' diák jegye sikeresen hozzáadva!";
                    } else {
                        echo "Hiba a jegy beszúrásakor: " . $stmt->error;
                    }

                    // Lekérdezés lezárása
                    $stmt->close();
                    
                }
            }
        }
    }
}

// Kapcsolat bezárása:
//$conn->close();


/*Rossz megoldás:*/
            /*$sql = '
            USE schoolbook;
            INSERT INTO students(name, gender, class_id)
            VALUES ('.$name.','.$gender.',(SELECT id FROM classes WHERE code = "'.$class.'"))
            ';
            if ($conn->query($sql) === TRUE) {
                echo "<br>Subjects tábla sikeresen létrehozva!";
            } else {
                echo "Error creating database: " . $conn->error;
            }*/
?>

