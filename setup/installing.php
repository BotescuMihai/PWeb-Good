<link rel='stylesheet' href='style.css'/>
<?php
session_start();
if ($_POST['dbtype'] == 'Maria DB') {
    $_SESSION['HOST'] = $_POST['dbhost'];
    $_SESSION['dbname'] = $_POST['dbname'];
    $_SESSION['USER'] = $_POST['dbuser'];
    $_SESSION['DB_password'] = 'root';
    $_SESSION['PDO'] = 'Maria DB';
    //$PDO = new PDO('mysql:host=localhost;dbname=proiectpw', 'root', 'root');
} else {
    $_SESSION['HOST'] = $_POST['dbhost'];
    $_SESSION['dbname'] = $_POST['dbname'];
    $_SESSION['USER'] = $_POST['dbuser'];
    $_SESSION['DB_password'] = '';
    $_SESSION['PDO'] = 'sqlite3';
}

/*echo "HOST: ".  $_SESSION['HOST'];
echo "dbname: ".  $_SESSION['dbname'];
echo "USER: " .  $_SESSION['HOST'];
echo "DB password: " . $_SESSION['DB_password'];
*/
if (empty($_POST['dbhost'] &&
    $_POST['dbname'] &&
    $_POST['dbuser'] &&
    $_POST['dbpass'])) {
    echo "<h2 align=center >All Fields are required! Please Re-enter</h2>";

} else {
    if (isset($_POST['install'])) {
        echo "<div class='box'>
                <form class='ins' action='installed.php' method='post'>
                    <h2 align=center color=red>Database Info Succecfully Entered<h2>
                    <input class='submit' type='submit' value='NEXT' name='next'/>
                </form>
                </div>";
    } else {
        echo "<h2 align=center >Error<h2>";
    }
}
?>