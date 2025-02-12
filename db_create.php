<?php
require_once "classroom-data.php";

// Database tulajdonságok:
$servername = "localhost";
$username = "root";
$password = "";

// Create
try{
$conn = new mysqli($servername, $username, $password);



// Check 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
}
catch(Exception $e){
    echo "Nem lehet csatlakozni az adatbázishoz!!!!!!!!!!!!!!!!";
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
function createTable($conn,$tableName,$body){
    $sql  = sprintf('
    
    CREATE TABLE IF NOT EXISTS schoolbook.%s (
      %s
    )
    ENGINE = INNODB;
    ',$tableName,$body);
    if ($conn->query($sql) === TRUE) {
        echo "<br> $tableName tábla sikeresen létrehozva!";
    } else {
        echo "Error creating database: " . $conn->error;
    }
}
/*Subjects tábla*/
function HTMLbody(){
    echo "<body>";
    echo "<form name='nav' method='post' action=''>
                <div>
                    <div class='button-container'>
                    <button type='submit' name='install' value='1' class='primary'>Adatbázis létrehozása!</button>
                    <button type='submit' name='Refresh' value='1' class='secondary'>Adatbázis feltöltése!</button>
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
    <link rel="stylesheet" href="style.css">
</head>';
}
htmlHead();
HTMLbody();
/*Táblák létrehozása*/
if (isset($_REQUEST["install"])) {
    
    databaseCreate($conn);
    /*Grades tábla*/
    createTable($conn,"grades","id int(4) NOT NULL AUTO_INCREMENT,
                                                        student_id int(16) DEFAULT NULL,
                                                        subject_id int(16) DEFAULT NULL,
                                                        grade int(4) NOT NULL,
                                                        date date DEFAULT NULL,
                                                        PRIMARY KEY (id)");
    /*Classes tábla*/
    createTable($conn,"Classes","id int(5) NOT NULL AUTO_INCREMENT,
                                                        code varchar(3) DEFAULT NULL,
                                                        year int(4) DEFAULT NULL,
                                                        PRIMARY KEY (id)");
    /*Students tábla*/
    createTable($conn,"Students","id int(11) NOT NULL AUTO_INCREMENT,
                                                        name varchar(50) DEFAULT NULL,
                                                        gender int(11) DEFAULT NULL,
                                                        class_id int(11) DEFAULT NULL,
                                                        PRIMARY KEY (id)");
    /*Subjects tábla*/
    createTable($conn,"Subjects","id int(11) NOT NULL AUTO_INCREMENT,
                                                        name varchar(32) DEFAULT NULL,
                                                        PRIMARY KEY (id)");
}
if (isset($_REQUEST["Refresh"])) {
    deleteDatabase($conn);
    tableHeader();
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
        echo "<br><p style='text-align:center;'>Az összes tábla sikeresen törölve lett az adatbázis létrehozása előtt!</p>";
        // Minden utasítás feldolgozása
        while ($conn->next_result()) {
            // Kötelező, hogy minden eredményt feldolgozzunk
        }
    } else {
        echo "Hiba az adatbázis törlésekor: " . $conn->error;
    }
}

function tableHeader(){
    echo "<table>";
    echo '<thead>
    <tr>
        <th>Egyetlen Oszlop</th>
    </tr>
</thead>
<tbody>';
}


function generateSubjects($data, $conn) {
    
    $stmt = $conn->prepare("INSERT INTO Subjects (name) VALUES (?)");
    
    foreach ($data['subjects'] as $subject) {
        $stmt->bind_param("s", $subject); // "s" = string
        if ($stmt->execute()) {
            echo "<tr><td>'$subject' sikeresen hozzáadva a Subjects táblához!</td></tr>";
        } else {
            echo "Hiba az adat beszúrásakor: " . $stmt->error;
        }
    }
    
    $stmt->close();
}

/*Classes generálása */
function generateClasses($data, $conn) {
    
    // Előkészített SQL lekérdezés
    $stmt = $conn->prepare("INSERT INTO Classes (code, year) VALUES (?, ?)");

    foreach ($data['classes'] as $class) {
        // Paraméter bekötése
        $year = 2025;
        $stmt->bind_param("si", $class, $year); // "s" = string típus
        if ($stmt->execute()) {
            echo "<tr><td>'$class' sikeresen hozzáadva a Classes táblához!</td></tr>";
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
                echo "<tr><td>'$name' sikeresen hozzáadva a Students táblához!</td></tr>";
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
                        echo "<tr><td>'$name' diák jegye sikeresen hozzáadva!</td></tr>";
                    } else {
                        echo "Hiba a jegy beszúrásakor: " . $stmt->error;
                    }

                    // Lekérdezés lezárása
                    $stmt->close();
                    
                }
            }
            //echo "</tbody></table>";
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

