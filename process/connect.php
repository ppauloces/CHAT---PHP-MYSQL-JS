<?php 

$conn = mysqli_connect("localhost","root","","chat");
mysqli_query($conn,"SET time_zone='+00:00'");

date_default_timezone_set("America/Sao_Paulo");

if(mysqli_connect_errno()){
	echo "Falha ao conectar com o banco".mysqli_connect_error();
	exit();
}


?>