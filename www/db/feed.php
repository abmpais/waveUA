<?php
include "db.php";
$idUser = $_GET['idUser'];
#$queryMatch="SELECT * FROM `POSTS` INNER JOIN MUSICS ON POSTS.MUSICS_idmusics=MUSICS.idmusics  INNER JOIN LOCATIONS ON POSTS.LOCATIONS_idLOCATIONS=LOCATIONS.idLOCATIONS INNER JOIN USERS ON POSTS.USERS_idUSERS=USERS.idUSERS WHERE `USERS_idUSERS` LIKE '$idUser'";
$queryMatch="SELECT * FROM `POSTS` INNER JOIN MUSICS ON POSTS.MUSICS_idmusics=MUSICS.idmusics  INNER JOIN LOCATIONS ON POSTS.LOCATIONS_idLOCATIONS=LOCATIONS.idLOCATIONS INNER JOIN USERS ON POSTS.USERS_idUSERS=USERS.idUSERS ORDER BY `datePosts` DESC";
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