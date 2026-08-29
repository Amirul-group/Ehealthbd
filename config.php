<?php

//$conn = mysqli_connect('sql113.infinityfree.com','if0_42773547_web','if0_42773547','0zLQZhzeEsNDV') or die('connection failed');

?>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
// Setting up the time zone
date_default_timezone_set('Africa/Nairobi');
    // Host Name
    $db_hostname = 'sql113.infinityfree.com';
    // Database Name
    $db_name = 'if0_42773547_web';
    // Database Username
    $db_username = 'if0_42773547';
    // Database Password
    $db_password = '0zLQZhzeEsNDV';

try {

    $conn = new PDO("mysql:host=$db_hostname;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    echo $e->getMessage();
}
?>