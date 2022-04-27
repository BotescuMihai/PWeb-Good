<?php session_start(); ?>

<?php
include __DIR__ . '/db.php';
function set_url($url)
{
    echo("<script>history.replaceState({},'','$url');</script>");
}

//header('Location:/');
$content = '';
$username = 'florin.fortis@e-uvt.ro'; /* to be modified! */

/** header("Content-Type:application/json"); **/ /* the format we're going to use. */
//include('database.php');
$uri = array('username' => $username);
$DB = new db();
$stmt = $DB->execute_SELECT("SELECT denumire FROM materie INNER JOIN profesor WHERE materie.profesor_id=profesor.id AND profesor.username='" . $username . "'");
// $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$stmt=$con->query("SELECT denumire FROM materie INNER JOIN profesor WHERE materie.profesor_id=profesor.id AND profesor.username='" . $username . "'")->fetchAll();
foreach ($stmt as $row) {
    $denumire = $row['denumire'];
    $content .= '<div class="col-xl-2 col-md-4">
                                       <div class="icon-box">
                                        <h3><a href="">' . $denumire . '</a></h3>
                                       </div>
                                     </div>';
}
//   session_start();
echo $_SESSION['email'];
echo $content;

// set the resulting array to associative

//$result = $con->query("SELECT denumire FROM materie INNER JOIN profesor WHERE materie.profesor_id=profesor.id AND profesor.username='" . $username . "'");
//	echo "SELECT denumire FROM materie INNER JOIN profesor WHERE materie.profesor_id=profesor.id AND profesor.username='" . $username . "'";
/*	if($result->num_rows>0){
$row = $result->fetch_assoc();
$denumire = $row['denumire'];
$content .= '<div class="col-xl-2 col-md-4">
                       <div class="icon-box">
                        <h3><a href="">' . $denumire . '</a></h3>
                       </div>
                     </div>';
$uri['denumire1'] = $denumire;
mysqli_close($con);
}else{
    //response(NULL, NULL, 200,"No Record Found");
    $content .= '<tr><th scope="row">Status</th><td>INDISPONIBIL</td></tr>';
    }
echo $content;
//    echo $username;
// echo http_build_query($uri) . "\n";
// echo $_SERVER['REQUEST_URI'];
/*
switch($_SERVER['REQUEST_URI']){
    case 'profesor/florin.fortis@e-uvt.ro/materii':
        require '/PWeb/api/materii_prof_curent.php';
        break;
} */
?>