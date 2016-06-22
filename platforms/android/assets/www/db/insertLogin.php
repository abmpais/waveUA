<?php
include "db.php";
$name = $_POST['nameUser'];
$id = $_POST['idUser'];
$img = $_POST['imgUser'];
$imgC = $_POST['oe'];

date_default_timezone_set('Europe/Lisbon');
$date = date('Y/m/d h:i:s a', time());
$query="SELECT * FROM `USERS` WHERE `idUSERS` LIKE '".$id."'";
$query1="INSERT INTO USERS (`idUSERS`,`nameUser`,`email`,`foto`,`dateUsers`,`type`) VALUES ('$id','$name','','$img&oe=$imgC','$date','1')";
$result=mysqli_query($link,$query);
if ($result -> num_rows){
    echo "existe";
}
else {
    $q=mysqli_query($link,$query1);
}

?>