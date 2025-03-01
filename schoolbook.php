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
                    <div class='button-container'>
                    <button type='submit' name='admin/' value='1' class='danger'>Admin felület</button>

                    </div>
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
        <h1 class="centerText">Váci Szakképzési Centrum</h1>
        <h2 class="centerText">Boronkay György Műszaki Technikum és Gimnázium</h2>
    </div>';
}

function displayLogo(): void
{
    echo "<img class='image' src='http://www.boronkay.vac.hu/images/boronkaylogo.png' alt='Boronkay Logo' id='logo'>
        <hr>";
}
function displaySideMenu(): void
{
    echo "
        <div id='side-menu'>";
            displayLogo();
        
            echo "
            <div style='text-align:center;'>
            <form method='POST'>
            
            
                 <ul>
                    <li><button type='submit' name='years/'>Years</button></li>
                    <li><button type='submit' name='hallOfFame/'>Hall Of Fame</button></li>
                </ul>
                </form>
                </div>"
                
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

/*function displayMessage($message, $type = 'text', $important = false) {
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
}*/

displayBodyStart();


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
        <form method='POST'>
        <div style='text-align: center;''>
        <button id='classAvgButton' type=submit name='classAverage/{$class[0]['id']}'>Osztályátlag tantárgyanként</button>
        </div>
        </form>
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
                    <tr><td>{$student['id']}</td><td><button id='studentButton' type=submit name='specificStudent/{$student['id']}'>{$student['name']}</button></td><td>$gender</td><td>{$student['avg']}</td></tr>
                ";
            }
            echo "</form>";
            echo "
            </tbody>
            <tfoot>
            </tfoot>
        </table>";
}

function displayClassSubjectData($classId)
{
    $data = getSubjectDataByStudentId($classId);
    /*$avg = getClassAvg($classId); <h2>Átlag: {$avg['value']}</h2>*/
    echo "
        <h1 style='text-align:center;'>{$data[0]['osztaly']} / {$data[0]['ev']}</h1>
        <table class='table'>
            <thead>
                <tr><th>Tantárgy</th><th>Átlag</th></tr>
            </thead>    
            <tbody>
            ";
            
            foreach ($data as $class) {
                echo "
                    <tr><td>{$class['tantargy']}</td><td>{$class['atlag']}</td></tr>
                ";
            }
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
function displayTop10($year)
{
    $data = getTop10($year);
    echo "
        <h1 style='text-align:center;'>{$data[0]['year']} : TOP 10</h1>
        <table class='table'>
            <thead>
                <tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>
            </thead>    
            <tbody>
            ";
            $sorszam = 0;
            foreach ($data as $student) {
                $sorszam += 1;
                echo "
                    <tr><td>{$sorszam}</td><td>{$student['name']}</td><td>{$student['atlag']}</td></tr>
                ";
            }
            echo "
            </tbody>
            <tfoot>
            </tfoot>
        </table>";
}
function displayHallOfFame()
{
    $data = getHallOfFame();
    /*$avg = getClassAvg($classId); <h2>Átlag: {$avg['value']}</h2>*/
    echo "
    <h1 style='text-align:center;'>Hall Of Fame</h1>
        <h1 style='text-align:center;'>Legjobb osztály: {$data[0]['osztaly']}/{$data[0]['ev']}</h1>
        <table class='table'>
            <thead>
                <tr><th>Helyezés</th><th>Név</th><th>Átlag</th></tr>
            </thead>    
            <tbody>
            ";
            $sorszam = 0;
            foreach ($data as $student) {
                $sorszam += 1;
                echo "
                    <tr><td>{$sorszam}</td><td>{$student['name']}</td><td>{$student['atlag']}</td></tr>
                ";
            }
            echo "
            </tbody>
            <tfoot>
            </tfoot>
        </table>";
}

function displayYears($years): void
{
    echo "<div class='years-section' id='years-section' style='text-align:center;'>";
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
    echo "<div class='years-section' id='years-section' style='text-align:center;'>";
    echo "<form method='POST'>";
    foreach ($values as $class) {
        echo "<button type='submit' name='students/{$class['id']}' class='year-button ' id='classButton' data-year='{$class['code']}'>{$class['code']}</button>";
    }
    echo "<button type='submit' name='top10/{$year}' class='year-button' id='top10Button'>TOP 10</button>";
    echo "</form>";
    echo "</div>";
    echo "<div class='classes-section' id='classes-section'></div>";
    echo "<div class='students-section' id='students-section'></div>";
    
    
}

/*if (isset($_POST['years'])){
    
}*/
function DBExists(){
    global $servername,$username,$password;
    try {
        // Kapcsolódás az adatbázis szerverhez (de nem egy konkrét adatbázishoz)
        $conn = new PDO("mysql:host=$servername", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Lekérdezés: létezik-e az adatbázis?
        $stmt = $conn->query("SHOW DATABASES LIKE 'schoolbook'");
        
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Hiba: " . $e->getMessage();
    }
    
    $conn = null;
}
function displayAdminSide(){
    echo "<form name='nav' method='post' action=''>
                <div>
    <div class='button-container'>
                    <button type='submit' name='admin/ev' value='1' class='danger'>Évek</button>
                    <button type='submit' name='admin/osztaly' value='1' class='danger'>Osztályok</button>
                    <button type='submit' name='admin/tantargy' value='1' class='danger'>Tantárgyak</button>
                    <button type='submit' name='admin/diak' value='1' class='danger'>Diákok</button>
                    <button type='submit' name='admin/jegy' value='1' class='danger'>Jegyek</button>
                    </div>
                    </div>
                    </form>";
}
function displayAdminYears($years){
    echo "<div class='years-section' id='years-section' style='text-align:center;'>";
    echo "<form method='POST'>";
    foreach ($years as $year) {
        echo "<button type='submit' name='admin/ev/{$year['year']}' class='danger' data-year='{$year['year']}'>{$year['year']}</button>";
    }
    echo "<button type='submit' name='admin/ev/add' class='primary' data-year='{$year['year']}'>Év hozzáadása</button>";
    echo "</form>";
    echo "</div>";
    echo "<div class='classes-section' id='classes-section'></div>";
    echo "<div class='students-section' id='students-section'></div>";
}
function displayYearOptions($year){
    echo "<div class='years-section' id='years-section' style='text-align:center;'>";
    echo '<form method="POST">
        <label for="admin/ev/'.$year.'/mod">'.$year.':</label>
        <input type="number" min="0" id="admin/ev/'.$year.'/mod" name="admin/ev/'.$year.'/mod">
        <input type="submit" value="Módosítás" class="danger">
      </form>';
    echo "<form method='POST'>";
    
        /*echo "<button type='submit' name='admin/ev/{$year}/mod' class='danger' >Módosítás</button>";*/
        echo "<button type='submit' name='admin/ev/{$year}/del' class='danger' >Törlés</button>";
        
        
    echo "</form>";
    echo "</div>";
    echo "<div class='classes-section' id='classes-section'></div>";
    echo "<div class='students-section' id='students-section'></div>";
}

function checkPost(){
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'admin/') === 0) {
            $t = explode('/', $key);
            if ($t[1] == null){
                displayAdminSide();
            }
            if ($t[1] == 'ev'){
                displayAdminYears(getYears());
                if(isset($t[2])){
                    displayYearOptions($t[2]);
                    if(isset($t[3])){
                        
                        if($t[3] == "mod"){modifyYear($t[2],$_POST["admin/ev/{$t[2]}/mod"]);}
                        if($t[3] == "del"){deleteYear($t[2]);}
                    }
                }
            }
            
        }
        if (strpos($key, 'years/') === 0) {
            $t = explode('/', $key);

            // A kulcs a 'years/' prefixszel kezdődik
            if($t[1] != null){
            displayClass($t[1]);
            }
        }
        if (strpos($key, 'students/') === 0) {
            $t = explode('/', $key);
            displayNav();
            displayStudents(getStudents($t[1]),$t[1]);
            
        }
        if (strpos($key, 'years/') === 0) {
            if(!DBExists()){
                global $servername,$username,$password;
                $conn = new mysqli($servername, $username, $password);
                installDB($conn);
                refreshDB($conn);
            }
            // A kulcs a 'years/' prefixszel kezdődik
            displayNav();
        }
        if (strpos($key, 'specificStudent/') === 0) {
            
            $t = explode('/', $key);
            displayNav();
            displayStudentSubjectData($t[1]);
        }
        if (strpos($key, 'classAverage/') === 0) {
            
            $t = explode('/', $key);
            displayNav();
            displayClassSubjectData($t[1]);
        }
        if (strpos($key, 'hallOfFame/') === 0) {
            
            if(!DBExists()){
                global $servername,$username,$password;
                $conn = new mysqli($servername, $username, $password);
                installDB($conn);
                refreshDB($conn);
            }
            displayHallOfFame();
        }
        if (strpos($key, 'top10/') === 0) {
            $t = explode('/', $key);
            displayTop10($t[1]);

        }
    }
}

checkPost();
htmlHead();
HTMLbody();
displayBodyEnd();

?>