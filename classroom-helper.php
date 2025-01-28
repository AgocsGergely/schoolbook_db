<?php

require_once "classroom-data.php";
require_once "classroom.php";
function getData(){
    return DATA;
}

function generateSchoolbook($data) {
    /*if (isset($_SESSION["schoolBook"])) {
        return $_SESSION["schoolBook"];
    }*/
    $return = [];
    foreach ($data["classes"] as $class) {
        $classAVG = [];
        $numberOfPeople = rand(10, 15); // Random number of students per class
        $students = []; // Array to hold students for the current class
        for ($i = 0; $i < $numberOfPeople; $i++) {
            $student = []; // Initialize student array
            $gender = rand(1, 2) == 1 ? "W" : "M"; // Randomly determine gender
            $firstname = $gender == "W"
                ? $data["firstnames"]["women"][rand(0, count($data["firstnames"]["women"]) - 1)]
                : $data["firstnames"]["men"][rand(0, count($data["firstnames"]["men"]) - 1)];
            $lastname = $data["lastnames"][rand(0, count($data["lastnames"]) - 1)];
            $name = $firstname . " " . $lastname;

            $subjects = [];
            foreach ($data["subjects"] as $subject) {
                $grades = [];
                for ($j = 0; $j < rand(0, 5); $j++) {
                    $grades[] = rand(1, 5); // Generate random grades
                }
                $subjects[$subject]['grade'] = $grades;
                if(array_sum($grades) || count($grades) != 0){
                $subjects[$subject]['AVG'] = array_sum($grades) / count($grades);
                $classAVG[] = $subjects[$subject]['AVG'];
            }
            else{
                $subjects[$subject]['AVG'] = NULL;
            }
            }

            // Build the student array
            $student[0] = $name;
            $student[1] = $gender;
            $student[2] = $subjects;

            // Add the student to the current class
            $students[] = $student;
        }

        // Add the class with its students to the return array
        $return["average"][$class] = round(array_sum($classAVG) / count($classAVG),2);
        $return[$class] = $students;
        
    }

    // Cache the generated schoolbook in session
    $_SESSION["schoolBook"] = $return;
    return $return;
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
//Véletlenszerű névgenerálás, kiíratás. Tömbbe kell tenni, utána kiíratás. előbb fiú vagy lány név egy osztályba 0-15 közötti személy 6 osztály van