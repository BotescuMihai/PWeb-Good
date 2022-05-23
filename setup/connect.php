<?php // Always Include/Require This File For Connect Database
echo $HOST . "\n";
echo $USER . "\n";
echo $DB_password . "\n";
echo $DBNAME;
@mysqli_connect(
    $HOST,
    $USER,
    $DB_password
) or die(
"<h2>Database Error, Contact With Admin 1 </h2>"
);

@mysqli_select_db(
    $DBNAME
) or die(
"<h2>Database Error, Contact With Admin 2</h2>"
);
?>