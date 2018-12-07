<?php 
	session_start();

	include_once("config.php");
	include_once("includes/db_con.php");	



?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/dino_style.css" />
		<title>Dino Online Judge - Ver Problema</title>
			<script src="js/jquery-ui.custom.min.js"></script>
	</head>
<body>

<div class="wrapper">
	<?php include_once("includes/head.php"); ?>
    <div><br><br><br></div>
    <?php include_once("includes/header.php"); ?>
	
	<div class="post">
       	<?php
	
	include_once("includes/db_con.php");

	$consulta = "select Titulo, Descripcion,Entrada,Salida,EjemploEntrada,EjemploSalida, TiempoLimite, Aceptados, Intentos from Problema where ProbID = '" . addslashes($_GET["id"]) . "';";
	$resultado = mysqli_query($enlace,$consulta) or die('Algo anda mal: ' . mysqli_error());
	$row = mysqli_fetch_array($resultado);

	if(mysqli_num_rows($resultado) == 1){
		$tiempo = $row['TiempoLimite'] / 1000;
		echo '<div align="center">';
		echo "<h2>" . $_GET["id"] . ". " . $row['Titulo'] ."</h2>";
		echo "<p>Limite de tiempo : <b>" . $tiempo . "</b> seg. &nbsp;&nbsp;";
		echo "Total runs : <b>" . $row['Intentos'] . "</b>&nbsp;&nbsp;";
		echo "Aceptados : <b>" . $row['Aceptados'] . "</b></p> ";
		echo '</div>';
		echo "<h3>Descripcion</h3>";
		echo $row['Descripcion'] . "</p> ";
		echo "<h3>Entrada</h3>";
		echo $row['Entrada'];
		echo "<h3>Salida</h3>";
		echo $row['Salida'];
		echo "<h3>Ejemplo Entrada</h3>";
		echo $row['EjemploEntrada'];
		echo "<h3>Ejemplo Salida</h3>";
		echo $row['EjemploSalida'];
		$consulta = "UPDATE Problema 
					SET Vistas = (vistas + 1) 
					WHERE ProbID = \"".mysqli_real_escape_string($enlace,$_GET["id"])."\" 
					LIMIT 1 ";	

		mysqli_query($enlace,$consulta) or die('Algo anda mal: ' . mysqli_error());
	

	if(!isset($_REQUEST['CId'])){
		//si no es concurso
		?>
		<div align="center">
			<form action="enviar.php" method="post">
				<input type="hidden" name="enviar_a" value="<?php echo $_GET['id']; ?>">
				<input type="submit" value="enviar solucion">
			</form>
		</div>
		<?php

	}else{
		//si es concurso


		?>
		<!--
		<div align="center" >
			Enviar problema para el concurso
		<form action="contest_rank.php?cid=<?php echo $_REQUEST['cid']; ?>" method="POST" enctype="multipart/form-data">
			<br>
			<table border=0>
				 <tr><td  style="text-align: right">Codigo fuente&nbsp;&nbsp;</td><td><input name="userfile" type="file"></td></tr>
				 <tr><td></td><td><input type="submit" value="Enviar Solucion"></td></tr>
			</table>
		    <input type="hidden" name="ENVIADO" value="SI">
		    <input type="hidden" name="prob" value="<?php echo $_REQUEST['id']; ?>">
		    <input type="hidden" name="cid" value="<?php echo $_REQUEST['cid']; ?>">

		</form> 
		</div>
		-->
		<?php
	}

		// <-- php 
		}else{
			echo "<div align='center'><h2>El problema " . $_GET["id"] . " no existe.</h2></div>";
		}
		//<-- php
	?>
</div>

<?php
	if(!isset($_REQUEST['CId'])){
?>
	<div class="post" style="background: white; border:1px solid #bbb;">
		<?php
		// mejores tiempos !
		$consulta = "SELECT DISTINCT  UserName ,  EjecID ,  Estado , MIN(  Tiempo ) as 'Tiempo' , Fecha,Lenguaje  FROM  ejecucion WHERE (	ProbID =  ". mysqli_real_escape_string($enlace,$_GET["id"]) ."	AND Estado =  'Accepted'	)	GROUP BY  UserName	 order by Tiempo asc LIMIT 5";
		$resultado = mysqli_query($enlace,$consulta) or die('Algo anda mal: ' . mysqli_error());
		?>

		<div align="center" >
		<h3>Top 5 tiempos para este problema</h3><br>

		<table border='0' style="font-size: 14px;" > 
		<thead> <tr >
			<th width='12%'>EjecID</th> 
			<th width='12%'>Usuario</th> 
			<th width='12%'>Lenguaje</th> 

			<th width='12%'>Tiempo</th> 
			<th width='12%'>Fecha</th>
			</tr> 
		</thead> 
		<tbody>
		<?php
		$flag = true;
	    	while($row = mysqli_fetch_array($resultado)){

				$nick = $row['UserName'];


				if($flag){
		        	echo "<TR style=\"background:#e7e7e7;\">";
					$flag = false;
				}else{
		        	echo "<TR style=\"background:white;\">";
					$flag = true;
				}

				$cuando = date("F j, Y", strtotime($row['Fecha']));
				echo "<TD align='center' ><a href='verCodigo.php?EjecID={$row['EjecID']}'>". $row['EjecID'] ."</a></TD>";
				echo "<TD align='center' ><a href='runs.php?user=". $row['UserName']  ."'>". $nick   ."</a> </TD>";
				echo "<TD align='center' >". $row['Lenguaje']   ."</TD>";
				echo "<TD align='center' ><b>". $row['Tiempo'] / 1000  ."</b> Segundos </TD>";
				echo "<TD align='center' >". $cuando   ." </TD>";
				echo "</TR>";
		}
		?>		
		</tbody>
		</table>
		</div>
	</div>
	<?php
	
	}
	?>


	<?php include_once("includes/footer.php"); ?>

</div>
<?php include("includes/ga.php"); ?>
</body>
</html>