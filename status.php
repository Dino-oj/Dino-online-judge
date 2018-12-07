<?php 

	require_once("bootstrap.php");
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/Dino_style.css" />
		<title>Dino Online Judge - Runs</title>
			<script src="js/jquery-ui.custom.min.js"></script>
	</head>
<body>

<div class="wrapper">
	<?php include_once("includes/head.php"); ?>
	<div><br><br></div>
	<?php include_once("includes/header.php"); ?>
	<div class="post_blanco">
	<?php

	include_once("includes/db_con.php");

	//encontrar todos los titulos y asignarselos a un array
	$query = mysqli_query($enlace,"select ProbID, Titulo from Problema");
	$probset = array();
	while($foo = mysqli_fetch_array($query)){
		//print( $foo["probID"] . " "  . $foo["titulo"] );
		$probset[ $foo["ProbID"] ] = $foo["Titulo"];
	}


	$consulta = "SELECT `EjecID`, `UserName`, `ProbID`, `Estado`, `Tiempo`, `Memoria`, `Fecha`, `Lenguaje`, `ConcursoID` FROM `ejecucion` order by fecha desc LIMIT 100";

	if(isset($_GET["user"])){
		/*
		 * ESTADISTICAS DEL USUARIO
		 * */
		
		
		//concursos
     	$sql = 'SELECT DISTINCT ConcursoID FROM  `Ejecucion` WHERE ConcursoID > 0 AND UserName =  "' . addslashes($_GET['user']) . '"';
		$resultado = mysqli_query($enlace,$sql) or die('Algo anda mal: ' . mysqli_error());
		$ncontests = mysqli_num_rows($resultado);
						
		//vamos a imprimir cosas del usuario
		$query2 = "SELECT UserID, Nombre, TotalAceptados, TotalEnvios, Institucion, Ubicacion, Email from usuario where UserName = '" . addslashes($_GET['user']) . "'";
		$resultado = mysqli_query($enlace,$query2) or die('Algo anda mal: ' . mysqli_error());
	
		if(mysqli_num_rows($resultado) != 1){
			echo "<h2>Ups</h2>";
			echo "Este usuario no existe";
			return;
		}
		$row = mysqli_fetch_array($resultado);
		?>
			<table border=0><tr>
			<td>
			
				<img 
					id="" 
					src="https://secure.gravatar.com/avatar/<?php echo md5( $row['Email']); ?>?s=140" 
					alt="" 
					width="75" 
					height="75"  />
			</td>
			<td width='400px'>

		<?php

		echo "		<h2>" . htmlentities(utf8_decode( $_GET['user'])) . "</h2>";
		echo "		<b>" .  htmlentities(utf8_decode( $row['Nombre'])) . "</b><br>". htmlentities(utf8_decode($row['Ubicacion'])) ." <b> - </b> ". htmlentities(utf8_decode($row['Institucion'])) ;
		echo "</td><td>";		
		if( $row[3] != 0 ){
			$rat = ($row[2]/$row[3])*100;
			$rat = substr( $rat , 0 , 5 ) . "%";
		}else
			$rat = "0.0%";

		if(strlen($row[6]) > 0){
			$twitter = "$row[6]";
		}else{
			$twitter = "";
		}


		echo "		<table>";
		echo "		<tr><td width='100px'><b>Enviados</b></td><td width='100px'><b>Resueltos</b></td><td width='100px'><b>Radio</b></td><td><b>Concursos</b>&nbsp;&nbsp;</td><td><b>Email</b></td></tr>";
		echo "		<tr><td>{$row[3]}</td><td><b>{$row[2]}</b></td><td>{$rat}</td><td>{$ncontests}</td><td>{$twitter}</td></tr>";
		echo "		</table>";

		echo "</td></tr></table>";
		

		//problemas resultos
		
		$query = "select distinct ProbID from Ejecucion where UserName = '" . $_GET['user'] . "' AND Estado = 'Accepted' order by ProbID";
		$resultado = mysqli_query($enlace,$query) or die('Algo anda mal: ' . mysqli_error());

		if( mysqli_num_rows( $resultado ) == 0 )
			echo "<div align=center><br><h3>Problemas resueltos</h3><b>" . $_GET["user"] . "</b> no ha resuelto ningun problema hasta ahora.<br>";
		else
			echo "<div align=center><br><h3>Problemas resueltos</h3><br>";
		

		while($row = mysqli_fetch_array($resultado)){
			echo " <a title='{$probset[$row['ProbID'] ]}' href=\"verProblema.php?id={$row['ProbID']}\">{$row['ProbID']}</a> &nbsp;";
		}

		
		echo "</div><br><br>";
		//dejar esta consulta en 
		$consulta = "SELECT `EjecID`, `UserName`, `ProbID`, `Estado`, `Tiempo`, `Memoria`, `Fecha`, `Lenguaje`, `ConcursoID`  FROM `Ejecucion` where UserName = '" . addslashes($_GET["user"]) . "' order by fecha desc";
		?>
			<div align="center">
				<h3>Run-Status</h3>
				Estos son TODOS los envios que ha hecho <?php echo $_GET["user"]; ?> :
			</div>
			<br/>		
		<?php
	}else{
			?>
			<div align="center">
				<h2>Run-Status</h2>
				Mostrando los ultimos 100 envios a Dino. 
			</div>
			<br/>
			<?php
	}

	$resultado = mysqli_query($enlace,$consulta);


	
	?>

	<div align="center" >
	<!-- <h2>Ultima actividad</h2> -->

	<table border='0' style="font-size: 18px;" > 
	<thead> <tr >
		<th width='12%'>Run ID</th> 
		<th width='12%'>Problema</th> 
		<th width='12%'>Usuario</th> 
		<th width='12%'>Lenguaje</th> 
		<th width='12%'>Resultado</th> 
		<th width='12%'>Tiempo</th> 
		<th width='12%'>Memoria</th> 
		<th width='12%'>Fecha</th>
		</tr> 
	</thead> 
	<tbody>
	<?php
	$flag = true;
    	while($row = mysqli_fetch_array($resultado)){
		$color = "black";
		$ESTADO = $row['Estado'];
	
		

		$nick = htmlentities( $row['UserName'] );

		//checar si hay una sesion y si si hay mostrar el usuario actual en cierto color
		//$foobar = $row['EjecID'];
		$tooltip = "Ver este Codigo";
		if( $row["ConcursoID"] != -1 ){
			//$foobar = "" . $row['EjecID'] . "*";
			$tooltip = "Este run fue parte de algun concurso online";
		}


		if($flag){
			echo "<TR style=\"background:#e7e7e7;\">";
			$flag = false;
		}else{
			echo "<TR style=\"background:white;\">";
			$flag = true;
		}



		echo "<TD align='center' ><a title='$tooltip' href='verCodigo.php?execID={$row['EjecID']}'>". $row['EjecID']  ."</a></TD>";
		echo "<TD align='center' ><a title='". $probset[ $row["ProbID"] ] ."' href='verProblema.php?id=". $row['ProbID']  ."'>". $row['ProbID']   ."</a> </TD>";
		echo "<TD align='center' ><a title='Ver perfil' href='status.php?user=". $row['UserName']  ."'>". $nick   ."</a> </TD>";
		echo "<TD align='center' >". $row['Lenguaje']   ."</TD>";
		echo "<TD align='center' ><div style=\"color:".$color."\">". utils::color_result($ESTADO)   ."</div> </TD>";
		printf("<TD align='center' >%1.3fs </TD>", $row['Tiempo'] / 1000);
		echo "<TD align='center' >". $row['Memoria']   ." </TD>";
		echo "<TD align='center' >". $row['Fecha']   ." </TD>";
		echo "</TR>";
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
