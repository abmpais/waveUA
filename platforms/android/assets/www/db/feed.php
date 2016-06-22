<?php
include "db.php";
$idUser = $_GET['idUser'];
$queryMatch="SELECT * FROM `posts` INNER JOIN musics ON posts.MUSICS_idmusics=musics.idmusics  INNER JOIN locations ON posts.LOCATIONS_idLOCATIONS=locations.idLOCATIONS INNER JOIN users ON posts.USERS_idUSERS=users.idUSERS WHERE `USERS_idUSERS` LIKE '$idUser'";
$result=mysqli_query($link,$queryMatch);
$row_cnt = $result->num_rows;
$row=array();
$replace= array("[","]");
$export="";
while ($r = mysqli_fetch_assoc($result)){
    $row[]=$r;
}
$output = json_encode($row);
echo $output;

?>