<link rel='stylesheet' href='style.css'/>

<?php // create form
$HOST_name = "<input class='input' type='text' name='dbhost' placeholder='Enter Host Name' />";
$DB_username = "<input class='input' type='text' name='dbuser' placeholder='Enter DB User Name' />";
$DB_password = "<input class='input' type='password' name='dbpass' placeholder='***********' />";
$DB_name = "<input class='input' type='text' name='dbname' placeholder='Enter DB Name' />";
$DB_type = "<select name='dbtype'><option value='Maria DB'>Maria DB</option><option value='Sqlite 3'>Sqlite 3</option></select>";
echo "<div class='box' >
            <form class='ins' method='post' action='installing.php' >
                    <p>Enter Host Name:<p>
                    $HOST_name
                    <p>Enter DB User Name:<p>
                    $DB_username
                    <p>Enter DB PassWord:<p>
                    $DB_password
                    <p>Enter DB Name:<p>
                    $DB_name
                    <p>Enter DB Type: (Maria DB/Sqlite 3)</p>
                    $DB_type
                    <input class='submit' type='submit'name='install' value='NEXT' />
            </form>
        </div>";
?>