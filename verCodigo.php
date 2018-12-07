<?php 

	session_start(); 
	include_once("config.php");
	include_once("includes/db_con.php");
	
date_default_timezone_set('America/Caracas');
?>
<html>
<head>
		<link rel="stylesheet" type="text/css" href="css/Dino_style.css" />
		<title>Dino Online Judge - Ver Codigo Fuente</title>
			<script src="js/jquery-ui.custom.min.js"></script>
		<link type="text/css" rel="stylesheet" href="css/SyntaxHighlighter.css">
		<script language="javascript" src="js/shCore.js"></script>
		<script language="javascript" src="js/shBrushCSharp.js"></script>
		<script language="javascript" src="js/shBrushJava.js"></script>
		<script language="javascript" src="js/shBrushCpp.js"></script>
		<script language="javascript" src="js/shBrushPython.js"></script>
		<script language="javascript" src="js/shBrushXml.js"></script>
</head>
<body>

<div class="wrapper">
	<?php include_once("includes/head.php"); ?>
    <div><br><br><br></div>
    <?php include_once("includes/header.php"); ?>

	<div class="post" style="background:white;">

	<h2>Revisar un codigo fuente</h2>

<?php



	function mostrarCodigo( $lenguaje, $execID , $row){

		$file  = "../codigos/" . $execID  ;

		switch($lenguaje){
			case "JAVA": 	$file .= ".java"; 	$sintaxcolor = "java"; 		break;
			case "C": 		$file .= ".c"; 		$sintaxcolor = "c"; 		break;
			case "C++": 	$file .= ".cpp"; 	$sintaxcolor = "cpp"; 		break;
			default : 		$file .= ".java"; 	$sintaxcolor = "java";
		}

		$codigo=$row["Source"]

		
		?>
		<div class="post">
			<div align=center >
				<table border='0' style="font-size: 14px;" > 
				<thead> <tr >
					<th width='12%'>EjecID</th> 
					<th width='12%'>Usuario</th> 
					<th width='12%'>Lenguaje</th> 
					<th width='12%'>Resultado</th> 
					<th width='10%'>Tiempo</th>
					 <th width='10%'>Memoria</th> 
					<th width='14%'>Fecha</th>
					</tr> 
				</thead> 
				<tbody>
				<?php
						$nick = $row['UserName'];
			        	echo "<TR style=\"background:#e7e7e7;\">";
						$cuando = date("F j, Y h:i:s A", strtotime($row['Fecha']));
						echo "<TD align='center' >". $row['EjecID'] ."</TD>";
						echo "<TD align='center' ><a href='runs.php?user=". $row['UserName']  ."'>". $nick   ."</a> </TD>";
						echo "<TD align='center' >". $row['Lenguaje']   ."</TD>";
						echo "<TD align='center' >". $row['Estado']   ."</TD>";
						echo "<TD align='center' ><b>". $row['Tiempo'] / 1000  ."</b> Segundos </TD>";
						echo "<TD align='center' >". $row['Memoria'] ." </TD>";
						echo "<TD align='center' >". $cuando   ." </TD>";
						echo "</TR>";

				?>		
				</tbody>
				</table>
			</div>
			&nbsp;
		</div>
		
		<?php
		
		echo "<textarea name=\"code\" class=\"$sintaxcolor\" cols=\"60\" rows=\"10\">{$codigo}</textarea>";
	}


	// --- revisar login ---
	function revisarLogin(){
		global $enlace;
		$asdf =  mysqli_real_escape_string($enlace,$_REQUEST["execID"]);
		$consulta = "select * from ejecucion where BINARY ( EjecID = '{$asdf}' )";
		$resultado = mysqli_query($enlace,$consulta) or die('Algo anda mal: ' . mysqli_error());
	
		if(mysqli_num_rows($resultado) != 1){
			echo "<b>Este codigo no existe</b>";
			return;
		}

		$row = mysqli_fetch_array($resultado);

		if(!isset($_SESSION['UserName'])){
			?> <div align='center'> Inicia sesion con la barra de arriba para comprobar que este codigo es tuyo. </div> <?php
			return;
		}

		if( ($row['UserName'] == $_SESSION['UserName']) ){
			//este codigo es tuyo o eres OWNER
			mostrarCodigo($row['Lenguaje'], $_REQUEST["execID"] , $row);
	
		}else{
			
			
			//no puedes ver codigos que estan mal
			if($row['Estado'] != "Accepted"){
				?><div style="font-size: 16px;"> <img src="img/12.png">No puedes ver codigos que no estan aceptados aunque cumplas con los requisitos.</div><?php
				return;
			}
			
			//no puedes ver codigos que son parte de algun concurso
			if($row['ConcursoID'] != "-1"){
				?><div style="font-size: 16px;"> <img src="img/12.png">No puedes ver codigos que pertenecen a un concurso aunque cumplas con los requisitos.</div><?php
				return;
			}
			
			//este codigo no es tuyo, pero vamos a ver si ya lo resolviste con mejor tiempo y que no sea parte de un concurso
			$consulta = "select * from Ejecucion where ProbID = '". $row['ProbID'] ."' AND UserName = '". $_SESSION['UserName'] ."' AND Tiempo < " . $row['Tiempo'] . " AND Estado = 'Accepted' ;";
			$resultado2 = mysqli_query($enlace,$consulta) or die('Algo anda mal: ' . mysqli_error());
			$nr = mysqli_num_rows($resultado2);
			
			if($nr >= 1){
				//ok, te lo voy a mostrar...
				?><div style="font-size: 16px;"> <img src="img/49.png">Este codigo no es tuyo, pero lo puedes ver porque ya lo resolviste con un mejor tiempo.</div><?php
				mostrarCodigo($row['Lenguaje'], $_REQUEST["EjecID"] , $row );
			}else{
				//no cumples con los requisitos
				?> 	
					<div align='center'> 
						<h2>Disculpa</h2> 
						<br>
						<div style="font-size: 16px;"> <img src="img/12.png">Estas intentado ver un codigo que no es tuyo. Para poder verlo tienes que resolver este problema y tener un mejor tiempo que el codigo que quieres ver.</div>
					</div> 
				<?php
			}
			

		}

	}


	// --- conectarse a la bd ---
	include_once("includes/db_con.php");


	revisarLogin();

	// --- cerrar conexion ---
	if( isset($resultado))
		 mysqli_free_result($resultado);
	if( isset($enlace))
		mysqli_close($enlace);
?>

	
	</div>





	<?php include_once("includes/footer.php"); ?>

</div>


<script language="javascript">
window.onload = function () {

    dp.SyntaxHighlighter.ClipboardSwf = 'flash/clipboard.swf';
    dp.SyntaxHighlighter.HighlightAll('code');
}

</script>
<?php include("includes/ga.php"); ?>
</body>
</html>

