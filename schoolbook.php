<?php
require_once 'db_queries.php';
require_once 'db_create.php';

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

function displayBanner()
{
    echo '
    <div id="banner">
        <h1>Váci Szakképzési Centrum</h1>
        <h2>Boronkay György Műszaki Technikum és Gimnázium</h2>
    </div>';
}

function displayLogo(): void
{
    echo "<img src='http://www.boronkay.vac.hu/images/boronkaylogo.png' alt='Boronkay Logo' id='logo'>
        <hr>";
}
function displaySideMenu(): void
{
    echo "
        <div id='side-menu'>";
            displayLogo();
        
            echo "
            <form method='POST'>
                 <ul>
                    <li><input type='submit' name='years/' value='Years' href='#years-section'></li>
                    <li><a href='#classes-section'>Classes</a></li>
                    <li><a href='#students-section'>Students</a></li>
                </ul>
                </form>"
                ;
        
        
    echo "
        </div>";
}
function displayBodyStart(): void
{
    echo "
        <body>";
    displayBanner();

    displaySideMenu();
    echo "   <div class='container'>
                <main>
        ";
}

function displayBodyEnd(): void
{
    displayFooter();
    echo "        
                </main>    
            </div> <!-- .container -->
        </body>
    </html>
    ";
}
function displayInstallBtn()
{
    echo '
        <form method="post">
            <button type="submit" name="btn-install" title="Adatbázis telepítése">Telepítés</button>
            <input type="checkbox" name="with-data" value="1" checked title="Adatokkal"><label for="with-data">Véletlenszerű adatokkal</label>
        </form>
        <p>A program első indítása esetén az adatbázis telepítése.</p>
        ';
}

function displayMessage($message, $type = 'text', $important = false) {
    // Definiáljuk az üzenet-típusokhoz tartozó stílusokat
    $fontWeight = '';
    if ($important) {
        $fontWeight = ' font-weight: bold;';
    }
    $styles = [
        // text: Átlátszó háttér, fekete szöveg.
        'text' => 'background-color: transparent; color: #000; border: 1px solid #ddd;',
        // info: Halványkék háttér, sötétkék szöveg.
        'info' => 'background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;',
        // success: Halványzöld háttér, zöld szöveg.
        'success' => 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;',
        // warning: Halványsárga háttér, sötétsárga szöveg.
        'warning' => 'background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba;',
        // danger és error: Halvány piros háttér, piros szöveg.
        'danger' => 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;',
        'error' => 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'
    ];

    // Ellenőrizzük, hogy a megadott típus létezik-e
    $style = isset($styles[$type]) ? $styles[$type] : $styles['text'];

    // Kiírjuk az üzenetet a megfelelő stílussal
    echo "<div style='padding: 10px; margin: 10px 0; border-radius: 4px; $style $fontWeight'>$message</div>";
    flush();
}

displayBodyStart();
displayBodyEnd();


function displayNav(): void
{
    /*if (!dbExists()) {
        displayInstallBtn();
        return;
    }*/
    $requestUri = $_SERVER['REQUEST_URI'];
    if (in_array($requestUri, ["/schoolbook/", "/schoolbook_db/schoolbook.php"])) {
        $years = getYears();
        displayYears($years);
        return;
    }
    echo "
            <nav>
                <div>
                    <ul class='nav me-auto'>
                        <li class='nav-item'><a href='/school/classes' class='nav-link link-body-emphasis px-2'>Osztályok</a></li>
                        <li class='nav-item'><a href='/school/subjects' class='nav-link link-body-emphasis px-2'>Tantárgyak</a></li>
                        <li class='nav-item'><a href='/school/students' class='nav-link link-body-emphasis px-2'>Diákok</a></li>
                        <li class='nav-item'><a href='/school/lists' class='nav-link link-body-emphasis px-2'>Listák</a></li>
                    </ul>
                </div>
            </nav>
        ";
}
function displayFooter(): void
{
    echo "
            <hr>
            <footer>
                Agócs Gergely Botond &copy; 2025
            </footer>
        ";
}

function displayStudents($students, $classId)
{
    $class = getClassByID($classId);
    $classAVG = getClassAVG($classId);
    /*$avg = getClassAvg($classId); <h2>Átlag: {$avg['value']}</h2>*/
    echo "
        <h1 style='text-align:center;'>{$class[0]['code']} / {$class[0]['year']}</h1>
        <h1 style='text-align:center;'>Osztályátlag: {$classAVG[0]['atlag']}</h1>
        <button type=submit name='specificStudent/'>Osztályátlag tantárgyanként</button>
        <table class='table'>
            <thead>
                <tr><th>#</th><th>Név</th><th>Nem</th><th>Átlag</th></tr>
            </thead>    
            <tbody>
            ";
            echo "<form method='POST'>";
            foreach ($students as $student) {
                $gender = $student['gender'] == 'W' ? 'Lány' : 'Fiú';
                echo "
                    <tr><td>{$student['id']}</td><td><button type=submit name='specificStudent/{$student['id']}'>{$student['name']}</button></td><td>$gender</td><td>{$student['avg']}</td></tr>
                ";
            }
            echo "</form>";
            echo "
            </tbody>
            <tfoot>
            </tfoot>
        </table>";
}
function displayStudentSubjectData($studentId)
{
    $data = getSubjectDataByStudentId($studentId);
    /*$avg = getClassAvg($classId); <h2>Átlag: {$avg['value']}</h2>*/
    echo "
        <h1 style='text-align:center;'>{$data[0]['osztaly']} / {$data[0]['ev']}</h1>
        <h1 style='text-align:center;'>{$data[0]['nev']}</h1>
        <table class='table'>
            <thead>
                <tr><th>Tantárgy</th><th>Átlag</th></tr>
            </thead>    
            <tbody>
            ";
            
            foreach ($data as $student) {
                echo "
                    <tr><td>{$student['tantargy']}</td><td>{$student['atlag']}</td></tr>
                ";
            }
            echo "
            </tbody>
            <tfoot>
            </tfoot>
        </table>";
}

/*function displayStudentAvgDetails($student, $studentAvgDetails, $classId)
{
    $class = getClass($classId);
    $average = getStudentAvg($student['id']);
    echo "
        <h1>{$student['name']} ({$class['code']} / {$class['year']})</h1>
        <h2>Átlag: {$average['average']}</h2>
        <table class='table'>
            <thead>
                <tr><th>Tantárgy</th><th>Átlag</th></tr>
            </thead>    
            <tbody>
            ";
            foreach ($studentAvgDetails as $detail) {
                echo "
                    <tr><td><a href='/school/classes?class-id={$class['id']}&student-id={$student['id']}&subject-id={$detail['id']}'>{$detail['subject']}</a</td><td>{$detail['avg']}</td></tr>
                ";
            }
            echo "
            </tbody>
            <tfoot>
            </tfoot>
        </table>";
}*/
/*
function displayStudentMarksBySubject($marks, $studentId, $subjectId)
{
    $student = getStudent($studentId);
    $class = getClass($student['class_id']);
    $subject = getSubject($subjectId);
    $subjectAvg = getStudentAvgBySubjectId($studentId, $subjectId);

    echo "
        <h1>{$student['name']} ({$class['code']} / {$class['year']})</h1>
        <h2>{$subject['name']}: {$subjectAvg['average']}</h2>
        <table class='table'>
            <thead>
                <tr><th>Osztályzat</th><th>Dátum</th></tr>
            </thead>    
            <tbody>
            ";
    foreach ($marks as $mark) {
        echo "
                    <tr><td>{$mark['mark']}</td><td>{$mark['date']}</td></tr>
                ";
    }
    echo "
            </tbody>
            <tfoot>
            </tfoot>
        </table>";
}
*/
function displayYears($years): void
{
    echo "<div class='years-section' id='years-section'>";
    echo "<form method='POST'>";
    foreach ($years as $year) {
        echo "<button type='submit' name='years/{$year['year']}' class='year-button' data-year='{$year['year']}'>{$year['year']}</button>";
    }
    echo "</form>";
    echo "</div>";
    echo "<div class='classes-section' id='classes-section'></div>";
    echo "<div class='students-section' id='students-section'></div>";
}

function displayClass($year){
    $values = getClassByYear($year);
    echo "<div class='years-section' id='years-section'>";
    echo "<form method='POST'>";
    foreach ($values as $class) {
        echo "<button type='submit' name='students/{$class['id']}' class='year-button' data-year='{$class['code']}'>{$class['code']}</button>";
    }
    echo "</form>";
    echo "</div>";
    echo "<div class='classes-section' id='classes-section'></div>";
    echo "<div class='students-section' id='students-section'></div>";
    
    
}

/*if (isset($_POST['years'])){
    
}*/

foreach ($_POST as $key => $value) {
    if (strpos($key, 'years/202') === 0) {
        $t = explode('/', $key);

        // A kulcs a 'years/' prefixszel kezdődik
        displayClass($t[1]);
    }
    if (strpos($key, 'students/') === 0) {
        $t = explode('/', $key);
        displayNav();
        displayStudents(getStudents($t[1]),$t[1]);
        
    }
    if (strpos($key, 'years/') === 0) {
        // A kulcs a 'years/' prefixszel kezdődik
        displayNav();
    }
    if (strpos($key, 'specificStudent/') === 0) {
        
        $t = explode('/', $key);
        displayNav();
        displayStudentSubjectData($t[1]);
    }
}


htmlHead();
HTMLbody();
?>