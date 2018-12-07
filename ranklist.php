<?php 
	session_start(); 
	include_once("config.php");
	include_once("includes/db_con.php");

?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/dino_style.css" />
		<title>Dino Online Judge - Ranking</title>
			<script src="js/jquery-ui.custom.min.js"></script>
	</head>
<body>

<div class="wrapper">
	<?php include_once("includes/head.php"); ?>
	<div><br><br></div>
	<?php include_once("includes/header.php"); ?>

	<div class="post_blanco" align="center">
	<h2>Ranking de Dino</h2>        
	<?php

	include_once("includes/db_con.php");


	//vamos a imprimir cosas del usuario

	$query = "select UserName, institucion, TotalAceptados, TotalEnvios, ubicacion from Usuario order by ";


	if(isset($_GET["order"])){
		switch ($_REQUEST["order"] ) {
			case "ubicacion" : 	$query .= "ubicacion"; break;
			case "institucion" :	$query .= "institucion"; break;
			case "TotalAceptados" : 	$query .= "TotalAceptados"; break;
			case "TotalEnvios" : 	$query .= "TotalEnvios"; break;
			default:
				$query .= "TotalAceptados desc, TotalAceptados";
		}		
	}else{
		$query .= "TotalAceptados desc, TotalAceptados";
	}

	

	$resultado = mysqli_query($enlace,$query) or die('Algo anda mal: ' . mysql_error());
	echo "<b> ". mysqli_num_rows($resultado) . "</b> usuarios<br>";
	?>

	<div align="center" >
	<table border='0' style="font-size: 20px;" > 
	<thead> <tr >
		<th width='5%'>Rank</th> 
		<th width='5%'>Usuario</th> 
		<th width='15%'>
			<!--<a href="rank.php?order=ubicacion">-->
			Ubicacion
			</a>
		</th> 
		<th width='15%'><!--<a href="rank.php?order=escuela">-->Institucion<!--</a>--></th> 
		<th width='5%'><!--<a href="rank.php?order=resueltos">-->Resueltos<!--</a>--></th> 
		<th width='5%'><!--<a href="rank.php?order=envios">-->Envios<!--</a>--></th> 
		<th width='5%'>Radio</th> 
		</tr> 
	</thead> 
	<tbody>
	<?php
	$rank = 1;
	$flag = true;
    	while($row = mysqli_fetch_array($resultado)){

		$nick = $row['UserName'];

		if( $row['TotalAceptados'] != 0 )
			$ratio = substr( ($row['TotalAceptados'] / $row['TotalEnvios'])*100 , 0, 5);
		else
			$ratio = 0.0;
		
		//checar si hay una sesion y si si hay mostrar el usuario actual en cierto color
		if(isset($_SESSION['UserID']) &&  $_SESSION['UserName'] == $row['UserName'] ){
	        echo "<TR style=\"background:#566D7E; color:white;\">";

			$flag = !$flag;
		}else{ 
			if($flag){
				echo "<TR style=\"background:#e7e7e7;\">";
				$flag = false;
			}else{
				echo "<TR style=\"background:white;\">";
				$flag = true;
			}
		}

		echo "<TD align='center' >". $rank ."</TD>";
		
		if(isset($_SESSION['UserName']) &&  $_SESSION['UserName'] == $row['UserName'] ){
			echo "<TD align='center' ><a style=\"color:white;\" href='status.php?user=". htmlentities($row['UserName'])  ."'>". $nick   ."</a> </TD>";
		}else{
			echo "<TD align='center' ><a href='status.php?user=". htmlentities($row['UserName'])  ."'>". $nick   ."</a> </TD>";
		}
		echo "<TD align='center' >".  htmlentities(utf8_decode($row['ubicacion'])) ." </TD>";
		echo "<TD align='center' >".  htmlentities(utf8_decode($row['institucion'])) ." </TD>";
		echo "<TD align='center' >". $row['TotalAceptados']  ." </TD>";
		echo "<TD align='center' >". $row['TotalEnvios']   ." </TD>";
		//echo "<TD align='center' > {$ratio}% </TD>";
		printf("<TD align='center' > %2.2f%% </TD>", $ratio);


		echo "</TR>";
		$rank++;
	}
	?>		
	</tbody>
	</table>
	</div>
	</div>



	<?php include_once("includes/footer.php"); ?>

</div>
<?php include("includes/ga.php"); ?>
</body>
</html>