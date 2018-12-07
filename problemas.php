<?php 

    require_once("bootstrap.php");

?><html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/dino_style.css" />
        <title>Dino Online Judge - Problem Set</title>
            <script src="js/jquery-ui.custom.min.js"></script>
    </head>
<body>

<div class="wrapper">
    <?php include_once("includes/head.php"); ?>
    <div><br><br><br></div>
    <?php include_once("includes/header.php"); ?>

    
    <div class="post" style="background: white; border:2px solid #bbb; width:900px; margin:auto; border-radius: 15px;">
        
<div align="center">
    <h2>Problem-Set</h2>

    <?php
    include_once("includes/db_con.php");
    $consulta = "select ProbID, Titulo,Vistas, Aceptados, Intentos from problema where Publico = 'NO'";

    $solved = array();

    if(isset($_GET["UserName"])){
        
        $query = "select distinct ProbID from ejecucion where UserName = '" . mysqli_real_escape_string($enlace,$_GET['UserName']) . "' AND Estado = 'Accepted'";
        $resueltos = mysqli_query($enlace,$query) or die('Algo anda mal: ' . mysqli_error());

        while($row = mysqli_fetch_array($resueltos)){
            $solved[] = $row[0];
        }
    }    
    //ordenar los problemas segun titulo vistos aceptados intentos
    if(isset($_GET["orden"])){
        if($_GET["orden"]=="Titulo"){
            $consulta .= (" ORDER BY " . mysqli_real_escape_string($enlace,$_GET["orden"])) ;
        }
        else{
            $consulta .= (" ORDER BY " . mysqli_real_escape_string($enlace,$_GET["orden"])." DESC") ;
        }
        
    }else{
        $consulta .= (" ORDER BY probID") ;
    }
    $resultado = mysqli_query($enlace,$consulta) or die('Algo anda mal: ' . mysqli_error());

    
    echo "Hay un total de <b>" . mysqli_num_rows($resultado) . "</b> problemas<br>";


    if(isset($_GET["UserName"]) ){
        ?> 
        <div align="center">
        </div>
        <?php
    }

    ?>
    </div>
    <br>    <br>
    <div align="center">
    <table border='0' style="font-size: 20px;"> 
    <thead> <tr >
        <th width='5%'></th> 
        <th width='5%'>ID</th> 
        <th width='25%'><a href="problemas.php?orden=Titulo">Titulo</a></th> 
        <th width='12%'><a href="problemas.php?orden=Vistas">Vistas</a></th> 
        <th width='12%'><a href="problemas.php?orden=Aceptados">Aceptados</a></th> 
        <th width='12%'><a href="problemas.php?orden=Intentos">Intentos</a></th> 
        <th width='12%'>Radio</th>
        </tr> 
    </thead> 
    <tbody>
    <?php

    $flag = true;
    $left = 0;
        while($row = mysqli_fetch_array($resultado)){
        //no muestra los problemas solucionados
        /*$ss = false;

        foreach ($solved as $probsolved) {
            if($row['ProbID'] == $probsolved)
                $ss = false;
        }

        if($ss)continue;*/

        if($row['Intentos']!=0)
            $ratio = ($row['Aceptados'] / $row['Intentos'])*100;
        else
            $ratio = "0.0";

        $ratio = substr($ratio, 0, 6);

        if($flag){
                echo "<TR style=\"background:#e7e7e7;\">";
            $flag = false;
        }else{
                echo "<TR style=\"background:white;\">";
            $flag = true;
        }
        if(isset($_REQUEST['UserName'])){
            $consult = "select ProbID from ejecucion where UserName = '" . mysqli_real_escape_string($enlace,$_GET['UserName']) . "'AND ProbID='".$row['ProbID']."' AND Estado = 'Accepted'";
            $respuesta = mysqli_query($enlace,$consult) or die('Algo anda mal: ' . mysqli_error());
            if(mysqli_num_rows($respuesta) > 0){
                echo "<TD align='center' ><img src='img/ok.png'/></TD>";

            }
            else{
                $consult = "select ProbID from ejecucion where UserName = '" . mysqli_real_escape_string($enlace,$_GET['UserName']) . "'AND ProbID='".$row['ProbID']."'";
                $respuesta = mysqli_query($enlace,$consult) or die('Algo anda mal: ' . mysqli_error());
                if(mysqli_num_rows($respuesta) > 0){
                    echo "<TD align='center' ><img src='img/agt_update_critical.png'/></TD>";
                }else
                {
                    echo "<TD align='center' ></TD>"; 
                }
               
            }
        }
        else{
             echo "<TD align='center' ></TD>"; 
        }
        
        echo "<TD align='center' >". $row['ProbID'] ."</TD>";
        echo "<TD align='left' ><a href='verProblema.php?id=". $row['ProbID']  ."'> &nbsp; ". $row['Titulo']   ."</a> </TD>";
        echo "<TD align='center' >". $row['Vistas']   ." </TD>";
        echo "<TD align='center' >". $row['Aceptados']   ." </TD>";
        echo "<TD align='center' >". $row['Intentos']   ." </TD>";
        printf("<TD align='center' >%2.2f%%</TD>", $ratio);
        echo "</TR>";
        $left++;
    }

    if(isset($_GET["UserName"])){
        ?>
            <script>document.getElementById("probs_left").innerHTML = "<?php echo $left; ?>";</script>
        <?php
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
