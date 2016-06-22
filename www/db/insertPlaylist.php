<?php
include "db.php";
$id = $_GET['id'];
$idUser = $_GET['idUser'];
$queryMatch="SELECT * FROM `MUSICS` WHERE `ref` LIKE '".$id."'";
$result=mysqli_query($link,$queryMatch);
$name = $_GET['name'];
$artist = $_GET['artist'];
$album = $_GET['album'];
$url = $_GET['link'];
date_default_timezone_set('Europe/Lisbon');
$date = date('Y/m/d H:i:s', time());
if ($result -> num_rows){
    $row=mysqli_fetch_array($result,MYSQLI_NUM);
    $query1="INSERT INTO MUSICS_has_USERS (`MUSICS_idmusics`,`USERS_idUSERS`) VALUES ('$row[0]','$idUser')";
    $q1=mysqli_query($link,$query1);
    $query4="INSERT INTO POSTS (`idPOSTS`,`linkPosts`,`datePosts`,`decaWeb`,`USERS_idUSERS`,`LOCATIONS_idLOCATIONS`,`MUSICS_idmusics`) VALUES ('','','$date','0','$idUser','1','$row[0]')";
    $q4=mysqli_query($link,$query4);
}
else {
    $query2="INSERT INTO MUSICS (`idmusics`,`ref`,`nameMusics`,`artist`,`album`,`linkMusics`,`dateMusics`) VALUES ('','$id','$name','$artist','$album','$url','$date')";
    $q2=mysqli_query($link,$query2);
    $result1=mysqli_query($link,$queryMatch);
    $row1=mysqli_fetch_array($result1,MYSQLI_NUM);
    $query3="INSERT INTO MUSICS_has_USERS (`MUSICS_idmusics`,`USERS_idUSERS`) VALUES ('$row1[0]','$idUser')";
    $q3=mysqli_query($link,$query3);
    $query4="INSERT INTO POSTS (`idPOSTS`,`linkPosts`,`datePosts`,`decaWeb`,`USERS_idUSERS`,`LOCATIONS_idLOCATIONS`,`MUSICS_idmusics`) VALUES ('','','$date','0','$idUser','1','$row1[0]')";
    $q4=mysqli_query($link,$query4);
}
echo $query2
?>