<?php
//phpstorm
define('NAME', 0);
define('GENDER', 1);
define('SUBJECTS', 2);

session_start();
require_once "classroom-helper.php";
require_once "classroom-data.php";



//
//
//GLOBALS
//
//




$data = getData();
htmlHead();

$bigBook = generateSchoolbook(DATA);
$avgbook = [];
$book = [];
foreach ($bigBook as $className => $students){
    if ($className != 'average'){
        $book[$className] =  $students;
    }
    if ($className == 'average'){
        $avgbook[$className] =  $students;
    }
}

echo $bigBook['average']['11a'];
//var_dump($book['11a']);
//echo $book['11a']["average"];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Reset or update selected class based on button clicked
    

    if (isset($_POST['btn-all'])) {
        $_SESSION['selected_class'] = "Összes"; // Include all students
    } 
    else{
        foreach ($book as $className => $students) {
            if (isset($_POST[$className])) {
                $_SESSION['selected_class'] = $className; // Store the selected class
                break;
            }
        }
    }
    if (isset($_POST[''])) $_SESSION['selected_class'] = null;
    
}

// Retrieve the selected class or default to "All"
$selectedClass = $_SESSION['selected_class'] ?? "Összes";

// Filter the book data based on the selected class

// Debug or output average grades

//$top3Results = findTop3($book, $selectedClass);

// Displaying the results
/*foreach ($top3Results as $subject => $ranking) {
    echo "Subject: $subject<br>";

    // Loop through each rank (first, second, third)
    foreach ($ranking as $rank => $students) {
        // Loop through each student for the current rank (to handle multiple students per rank)
        foreach ($students as $student) {
            echo "$rank: " . $student['name'] . " with average: " . $student['average'] . "<br>";
        }
    }
    echo "<br>";
}*/
$showavg = false;

////
////
///FUNCTIONS
////
////


function showNav($data) { // BUTTONS
    echo '
<nav class="mt-4">
<h1 class="text-center">Osztálynapló 2024-2025</h1>
    <form name="nav" method="post" action="" class="text-center align-middle">
        <div class="d-flex justify-content-center gap-3">
            <button type="submit" name="btn-all" value="1" class="btn btn-primary">Összes diák</button>';
            foreach ($data as $className => $students) {
                echo '<button type="submit" name="' . $className . '" value="1" class="btn btn-outline-primary">' . $className . '</button>';
            }
        echo '
        </div>
    </form>
</nav>
<form method="POST">
    <div class=text-center>
    <br>
        <button type="submit" name="generate_csv" class="btn btn-success btn-lg rounded-pill shadow">Napló Mentése CSV fájlba</button>
    </div>
</form>
<form method="GET">
    <div class=text-center>
    <br>';

            echo '<button type="submit" name="queries" class="btn btn-warning btn-lg rounded-pill shadow">Lekérdezések</button>';

    echo '</div>
</form>';
}


function showFunction($book) {
    if($GLOBALS["showavg"]){
        showSubjectAverages($book,$GLOBALS["selectedClass"]);
    }
    if (isset($_REQUEST["btn-all"])) {
        
        
        echo '<h1 class="text-center text-primary bg-light p-3 rounded">Kiválasztva: Összes osztály</h1>';
        //showSubjectAverages($book,$GLOBALS['selectedClass']);
        echo '<div class="container mt-4" style="max-width: 80%;">
        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
                <tr>
                    <th>Diák neve</th>
                    <th>Osztály</th>
                    <th>Nem</th>
                    <th>Osztályzatok</th>
                </tr>
              </thead>
              <tbody>';

        foreach ($book as $className => $students) {
                foreach($students as $student){
                echo '<tr>
                <td class="text-center align-middle"><p class="fs-2 fw-bold text-success">'. htmlspecialchars($student[NAME]) . '</p></td>
                <td class="text-center align-middle">' . htmlspecialchars($className) . '</td>
                <td class="text-center align-middle">' . htmlspecialchars($student[GENDER] == "W" ? "Nő" : "Férfi") . '</td>
                <td>';

                // Grades table
                echo '<div class="table-responsive">
                <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Tantárgy</th>
                        <th>Jegyek</th>
                        <th>Átlag</th>
                    </tr>
                </thead>
                <tbody>';
                foreach ($student[SUBJECTS] as $subject => $grades) {
                    echo '<tr>
                    <td class="text-center align-middle">' . htmlspecialchars($subject) . '</td>
                    <td>' . htmlspecialchars(implode(', ', $grades['grade'])) . '</td>';
                    $subjectAvg = 0;
                        foreach ($grades['grade'] as $grade){
                            $subjectAvg += $grade;
                        }
                        if (count($grades['grade']) == 0) echo '<td></td>';
                        if (count($grades['grade']) > 0){
                            echo '<td>' . round($subjectAvg/count($grades['grade']),2). '</td>';
                        }
                    echo'</tr>';
                }
                echo "<tr>";
                echo '<td class="text-center align-middle">' . 'Összesített átlag'. '</td>';
                echo '<td>'. studentAVG($student).'1' .'</td>';
                echo "</tr>";
                echo '</tbody>
                </table>
                </div>
                </td>
                </tr>';
                }
        }
        echo '</tbody>
        </table>
        </div>
        </div>';
    } 
    else {
        foreach ($book as $className => $students) {
            if (isset($_POST[$className])) {
                echo '<h1 class="text-center text-primary bg-light p-3 rounded">Kiválasztva: ' . htmlspecialchars($className) . '</h1>';
                //showSubjectAverages($book,$GLOBALS['selectedClass']);
                echo '<div class="container mt-4" style="max-width: 80%;">
                <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Diák neve</th>
                        <th>Nem</th>
                        <th>Osztályzatok</th>
                    </tr>
                </thead>
                <tbody>';

                foreach ($students as $student) {
                    echo '<tr>
                    <td class="text-center align-middle"><p class="fs-2 fw-bold text-success">'. htmlspecialchars($student[NAME]) . '</p></td>
                    <td class="text-center align-middle">' . htmlspecialchars($student[GENDER] == "W" ? "Nő" : "Férfi") . '</td>
                    <td>';

                    // Grades table
                    echo '<div class="table-responsive">
                    <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center align-middle">Tantárgy</th>
                            <th class="text-center align-middle">Jegyek</th>
                            <th class="text-center align-middle">Átlag</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach ($student[SUBJECTS] as $subject => $grades) {

                        echo '<tr>
                        <td class="text-center align-middle">' . htmlspecialchars($subject) . '</td>
                        <td>' . htmlspecialchars(implode(', ', $grades['grade'])) . '</td>';
                        
                        $subjectAvg = 0;
                        foreach ($grades['grade'] as $grade){
                            $subjectAvg += $grade;
                        }
                        if (count($grades['grade']) == 0) echo '<td></td>';
                        if (count($grades['grade']) > 0){
                            echo '<td>' . round($subjectAvg/count($grades['grade']),2). '</td>';
                        }
                        
                        echo '</td>
                        </tr>';
                        
                    }
                    echo '</tbody>
                    </table>
                    </div>
                    </td>
                    </tr>';
                }
                echo '</tbody>
                </table>
                </div>
                </div>';
            }
        }
    }
}
function studentAVG($student){
    $returnAverage = 0;
    $totalGrades = 0;
    $gradeCount = 0;
    foreach ($student[SUBJECTS] as $subjects) {
                $totalGrades += array_sum($subjects["grade"]);
                $gradeCount += count($subjects["grade"]);
                echo $gradeCount. " ";
    }
    $returnAverage = ($gradeCount > 0) ? $totalGrades / $gradeCount : 0;
    return round($returnAverage,2);
}
function exportToCSV($data, $filename = "export/schoolbook.csv") {
    // Create the export directory if it doesn't exist
    if (!is_dir("export")) {
        mkdir("export");
    }

    $file = fopen($filename, "w");
    fwrite($file, "\xEF\xBB\xBF"); // UTF-8 encoding for the CSV

    // Define subjects and CSV header
    $DATA = getData();
    $subjects = $DATA["subjects"];
    $header = ['ID', 'Class', 'Name', 'Gender'];
    $header = array_merge($header, array_map('ucfirst', $subjects)); // Add subjects to the header
    fputcsv($file, $header, ";");

    $idCounter = 0;

    // Iterate over each class and its students
    foreach ($data as $className => $students) {
        foreach ($students as $student) {
            // Generate ID, split name, and convert gender
            $id = $idCounter++;

            $gender = $student[GENDER] == "W" ? 2 : 1;

            // Initialize row with student details
            $row = [$id, $className, $student[NAME], $gender];

            // Add grades for each subject
            foreach ($subjects as $subject) {
                $grades = isset($student[SUBJECTS][$subject]) ? implode(",", $student[SUBJECTS][$subject]['grade']) : ''; // Format grades as CSV string
                $row[] = $grades; // Add grades to the row
            }

            // Write the row to the CSV
            fputcsv($file, $row, ";");
        }
    }

    fclose($file);
}

if (isset($_POST['generate_csv'])) {
    
    // Retrieve selected class from session
    $selectedClass = $_SESSION['selected_class'] ?? null;
    
    // Filter the book based on the selected class
    
    if ($selectedClass && $selectedClass !== "Összes") {
        if (array_key_exists($selectedClass, $book)) {
            $filteredBook = [$selectedClass => $book[$selectedClass]];
        } else {
            echo "<p>Hiba: Az osztály nem található.</p>";
            $filteredBook = [];
        }
    } else {
        // Include all classes if "All" is selected or no selection
        $filteredBook = $book;
    }
    // Export to CSV
    if (!empty($filteredBook)) {
        exportToCSV($filteredBook);

        // Rename the file with a timestamp and class name
        $originalFile = "export/schoolbook.csv";
        $newFile = "export/" . ($selectedClass ?? "Összes") . "_" . date("Y-m-d-H.i.s") . ".csv";
        if (file_exists($originalFile)) {
            rename($originalFile, $newFile);
            echo "<p>Sikeres mentés: <strong>" . ($selectedClass ?? "Összes osztály") . "</strong></p>";
        } else {
            echo "<p>Hiba: Az exportált fájl nem található.</p>";
        }
    }
}



//
//LEKÉRDEZÉSEK
//

function subjectAVG($classes) {
    $DATA = getData();
    $subjects = $DATA["subjects"]; 
    $returnArray = []; // Store averages

    foreach ($subjects as $subject) {
        $totalGrades = 0;
        $gradeCount = 0;

        foreach ($classes as $student) {
            if (isset($student[SUBJECTS][$subject])) {
                $totalGrades += array_sum($student[SUBJECTS][$subject]["grade"]);
                $gradeCount += count($student[SUBJECTS][$subject]["grade"]);
            }
        }

        // Calculate average if there are grades
        $returnArray[$subject] = ($gradeCount > 0) ? $totalGrades / $gradeCount : 0;
    }

    return $returnArray;
}



function findTop3Overall($book, $selectedClass) {
    $overallAverages = [];
    if($selectedClass == 'Összes'){
        foreach ($book as $className => $students) {
        foreach ($students as $student) {
            $totalAverage = 0;
            $subjectCount = 0;

            // Calculate the student's overall average across all subjects
            foreach ($student[SUBJECTS] as $subjectData) {
                if (isset($subjectData["AVG"]) && is_numeric($subjectData["AVG"])) {
                    $totalAverage += $subjectData["AVG"];
                    $subjectCount++;
                }
            }

            // Avoid division by zero
            if ($subjectCount > 0) {
                $overallAverages[] = [
                    'name' => $student[NAME],
                    'average' => round($totalAverage / $subjectCount,2)
                ];
            }
        }
        }
    }
    else{
    // Step 1: Collect overall averages for all students in the selected class
    foreach ($book as $className => $students) {
        if ($selectedClass == $className) {
            foreach ($students as $student) {
                $totalAverage = 0;
                $subjectCount = 0;

                // Calculate the student's overall average across all subjects
                foreach ($student[SUBJECTS] as $subjectData) {
                    if (isset($subjectData["AVG"]) && is_numeric($subjectData["AVG"])) {
                        $totalAverage += $subjectData["AVG"];
                        $subjectCount++;
                    }
                }

                // Avoid division by zero
                if ($subjectCount > 0) {
                    $overallAverages[] = [
                        'name' => $student[NAME],
                        'average' => round($totalAverage / $subjectCount,2)
                    ];
                }
            }
        }
        }
    }

    // Step 2: Sort students by overall average in descending order
    usort($overallAverages, function ($a, $b) {
        return $b['average'] <=> $a['average'];
    });

    // Step 3: Keep only the top 3 students
    $top3Students = array_slice($overallAverages, 0, 3);

    // Step 4: Format and return the result
    return $overallAverages;
}


function displayTopAndWorstStudents($book, $selectedClass){
    $tomb = findTop3Overall($book, $selectedClass);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; text-align: center;'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th colspan='2'>Legjobb tanulók</th>";
    echo "<th colspan='2'>Legrosszabb tanulók</th>";
    echo "</tr>";
    for ($i = 0; $i < 3; $i++){
        echo "<tr>";
        echo "<th>".$tomb[$i]["name"]."</th>";
        echo "<th>".$tomb[$i]["average"]."</th>";
        echo "<th>".$tomb[count($tomb)-1-$i]["name"]."</th>";
        echo "<th>".$tomb[count($tomb)-1-$i]["average"]."</th>";
        echo "</tr>";

    }
    
    
    
    echo "</thead>";
    echo "<tbody>";
}


function showSubjectAverages($book, $selectedClass) {
    echo '<div class="container mt-4" style="max-width: 80%;">';
    
    if ($selectedClass) {
        echo '<h2 class="text-center text-info bg-light p-3 rounded">Tantárgyi Átlagok: ' . htmlspecialchars($selectedClass) . '</h2>';
        
        // Check if "All" is selected or a specific class
        $students = ($selectedClass === "Összes") ? 
            array_merge(...array_values($book)) : 
            ($book[$selectedClass] ?? []);
        
        if (!empty($students)) {
            // Calculate subject averages
            $averages = subjectAVG($students);

            // Display as a table
            echo '<div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Tantárgy</th>
                    <th>Átlag</th>
                </tr>
            </thead>
            <tbody>';

            foreach ($averages as $subject => $average) {
                echo '<tr>
                        <td class="text-center align-middle">' . htmlspecialchars(ucfirst($subject)) . '</td>
                        <td class="text-center align-middle">' . number_format($average, 2) . '</td>
                      </tr>';
            }

            echo '</tbody>
            </table>
            </div>';
        } else {
            echo '<p class="text-danger text-center">Nincs adat a kiválasztott osztályhoz.</p>';
        }
    } else {
        echo '<p class="text-danger text-center">Nincs kiválasztott osztály.</p>';
    }
    
    echo '</div>';
}

if(!isset($_GET['queries'])){
showNav($book);
}
if(isset($_GET['queries'])){
echo "
    <button><a href='?'>Gonb</a></button>
<ul>
    <li>
        Átlagok
    </li>
    <ul><a href='?queries=osszes'>Iskola</a></ul>
    <ul><a href='?queries=11a'>11a</a></ul>
    <ul><a href='?queries=11b'>11b</a></ul>
    <ul><a href='?queries=11c'>11c</a></ul>
    <ul><a href='?queries=12a'>12a</a></ul>
    <ul><a href='?queries=12b'>12b</a></ul>
    <ul><a href='?queries=12c'>12c</a></ul>
    <li>Legjobb és legrosszabb tanulók</li>
    <ul><a href='?queries=lösszes'>Iskola</a></ul>
    <ul><a href='?queries=l11a'>11a</a></ul>
    <ul><a href='?queries=l11b'>11b</a></ul>
    <ul><a href='?queries=l11c'>11c</a></ul>
    <ul><a href='?queries=l12a'>12a</a></ul>
    <ul><a href='?queries=l12b'>12b</a></ul>
    <ul><a href='?queries=l12c'>12c</a></ul>
    <li>Legjobb és leggyengébb osztály</li>
    <ul><a href='?queries=oosszes'>Iskola</a></ul>
    <ul><a href='?queries=otantargy'>Tantárgyanként</a></ul>
    <li></li>
</ul>";
}

function showClassAverage($book){

    $topVal = 0;
    $worstVal = 6;
    foreach ($book["average"] as $classes => $value){
        if ($value > $topVal) $topVal = $value;
        if ($value < $worstVal) $worstVal = $value;
        echo "<br>".$classes." ".$value."   ";
    }
    
        echo $topVal." ".$worstVal;

    
}

//TOP3
if(isset($_GET['queries']) && $_GET['queries'] === 'lösszes'){
    $tomb = array_slice(findTop3Overall($book,"Összes"), 0, 3);
    displayTopAndWorstStudents($book, 'Összes');
}
if(isset($_GET['queries']) && $_GET['queries'] === 'l11a'){
    $tomb = array_slice(findTop3Overall($book,"11a"), 0, 3);
    displayTopAndWorstStudents($book, '11a');
}
if(isset($_GET['queries']) && $_GET['queries'] === 'l11b'){
    $tomb = array_slice(findTop3Overall($book,"11b"), 0, 3);
    displayTopAndWorstStudents($book, '11b');
}
if(isset($_GET['queries']) && $_GET['queries'] === 'l11c'){
    $tomb = array_slice(findTop3Overall($book,"11c"), 0, 3);
    displayTopAndWorstStudents($book, '11c');
}
if(isset($_GET['queries']) && $_GET['queries'] === 'l12a'){
    $tomb = array_slice(findTop3Overall($book,"12a"), 0, 3);
    displayTopAndWorstStudents($book, '12a');
}
if(isset($_GET['queries']) && $_GET['queries'] === 'l12b'){
    $tomb = array_slice(findTop3Overall($book,"12b"), 0, 3);
    displayTopAndWorstStudents($book, '12b');
}
if(isset($_GET['queries']) && $_GET['queries'] === 'l12c'){
    $tomb = array_slice(findTop3Overall($book,"12c"), 0, 3);
    displayTopAndWorstStudents($book, '12c');
}

//Átlagok
if(isset($_GET['queries']) && $_GET['queries'] === 'osszes'){
    showSubjectAverages($book,"Összes");
}
if(isset($_GET['queries']) && $_GET['queries'] === '11a'){
    showSubjectAverages($book,"11a");
}
if(isset($_GET['queries']) && $_GET['queries'] === '11b'){
    showSubjectAverages($book,"11b");
}
if(isset($_GET['queries']) && $_GET['queries'] === '11c'){
    showSubjectAverages($book,"11c");
}
if(isset($_GET['queries']) && $_GET['queries'] === '12a'){
    showSubjectAverages($book,"12a");
}
if(isset($_GET['queries']) && $_GET['queries'] === '12b'){
    showSubjectAverages($book,"12b");
}
if(isset($_GET['queries']) && $_GET['queries'] === '12c'){
    showSubjectAverages($book,"12c");
}
//Osztályok

if(isset($_GET['queries']) && $_GET['queries'] === 'oosszes'){
    showClassAverage($avgbook);
}
if(isset($_GET['queries']) && $_GET['queries'] === 'otantargy'){
    $tomb = array_slice(findTop3Overall($book,"Összes"), 0, 3);
    displayTopAndWorstStudents($book, 'Összes');
}
/*var_dump($book[1][SUBJECTS]["math"]);*/
showFunction($book);
//if session isset classes





// Példa osztálynapló struktúra

// Osztályonkénti teljesítmény előkészítése

/*
// Osztályonkénti teljesítmény előkészítése
$classPerformances = [];

foreach ($book as $className => $students) {
    $classPerformances[$className] = [
        "subjects" => [],
        "overallSum" => 0,
        "overallCount" => 0,
    ];

    foreach ($students as $student) {
        $subjects = $student[2]; // Ez a tömb a tantárgyakat és jegyeket tartalmazza
        foreach ($subjects as $subject => $grade) {
            if (!is_numeric($grade)) {
                continue; // Csak numerikus jegyeket dolgozunk fel
            }

            if (!isset($classPerformances[$className]['subjects'][$subject])) {
                $classPerformances[$className]['subjects'][$subject] = [
                    "sum" => 0,
                    "count" => 0,
                ];
            }

            // Tantárgyankénti összegzés
            $classPerformances[$className]['subjects'][$subject]['sum'] += $grade;
            $classPerformances[$className]['subjects'][$subject]['count']++;

            // Összesített átlaghoz
            $classPerformances[$className]['overallSum'] += $grade;
            $classPerformances[$className]['overallCount']++;
        }
    }
}

// Átlagok számítása
foreach ($classPerformances as $className => &$classData) {
    foreach ($classData['subjects'] as $subject => &$subjectData) {
        $subjectData['average'] = $subjectData['sum'] / $subjectData['count'];
    }
    if($classData['overallCount']>0)$classData['overallAverage'] = $classData['overallSum'] / $classData['overallCount'];
}

// Legjobb és legrosszabb osztály összesítésben
$bestOverallClass = null;
$worstOverallClass = null;

foreach ($classPerformances as $className => $classData) {
    if ($bestOverallClass === null || $classData['overallAverage'] > $classPerformances[$bestOverallClass]['overallAverage']) {
        $bestOverallClass = $className;
    }
    if ($worstOverallClass === null || $classData['overallAverage'] < $classPerformances[$worstOverallClass]['overallAverage']) {
        $worstOverallClass = $className;
    }
}

// Eredmények kiíratása
echo "Legjobb osztály összesítésben: $bestOverallClass (" . round($classPerformances[$bestOverallClass]['overallAverage'], 2) . ")\n";
echo "Legrosszabb osztály összesítésben: $worstOverallClass (" . round($classPerformances[$worstOverallClass]['overallAverage'], 2) . ")\n";

// Tantárgyankénti legjobb és legrosszabb osztály
$subjects = [];
foreach ($classPerformances as $className => $classData) {
    foreach ($classData['subjects'] as $subject => $subjectData) {
        if (!isset($subjects[$subject])) {
            $subjects[$subject] = [
                "best" => null,
                "worst" => null,
            ];
        }

        if ($subjects[$subject]['best'] === null || $subjectData['average'] > $classPerformances[$subjects[$subject]['best']]['subjects'][$subject]['average']) {
            $subjects[$subject]['best'] = $className;
        }
        if ($subjects[$subject]['worst'] === null || $subjectData['average'] < $classPerformances[$subjects[$subject]['worst']]['subjects'][$subject]['average']) {
            $subjects[$subject]['worst'] = $className;
        }
    }
}

foreach ($subjects as $subject => $data) {
    echo "Legjobb osztály $subject tantárgyban: " . $data['best'] . " (" . round($classPerformances[$data['best']]['subjects'][$subject]['average'], 2) . ")\n";
    echo "Legrosszabb osztály $subject tantárgyban: " . $data['worst'] . " (" . round($classPerformances[$data['worst']]['subjects'][$subject]['average'], 2) . ")\n";
}*/