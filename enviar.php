 <?php 
    session_start();
    include_once("config.php");
    include_once("includes/db_con.php");
    include 'includes/funcs.php';
    $user=$_SESSION["UserName"];
    if(!isset($_SESSION["UserID"])){
        header("Location: problemas.php");
    }
    $errors = array();

    if( isset($_POST["btnEnviar"])){
        $codigo = saltoLinea($_POST['codigo']);//revisar para q no lo suprima el saltode linea de un codigo
        $lenguaje = $enlace->real_escape_string($_REQUEST['lenguaje']);
        $enviar_a=$enlace->real_escape_string($_REQUEST['enviar_a']);
        if(strlen(trim($codigo)) < 1){
            $errors[] = "Debe llenar el campo de codigo"; 
        }
         if(strlen(trim($lenguaje)) < 1){
            $errors[] = "Debe seleccionar un lenguaje"; 
         }
         if(strlen(trim($enviar_a)) < 1){
            $errors[] = "No se Seleciono el problema a enviar"; 
         }
         if(count($errors) == 0)
        {        
            $time =microtime(true);//mide el tiempo
            $micro_time=sprintf("%06d",($time - floor($time)) * 1000000);
            $date=new DateTime( date('Y-m-d H:i:s.'.$micro_time,$time) );
            $fecha=$date->format("Ymd-His-u");//almacena en $nom la fecha hora segundo y milesegundo en la q se subio el archivo

            $registro = registraEnvio($user,$enviar_a,$lenguaje,$codigo);           
            if($registro > 0)
                {               
                    $sms="Codigo enviado correctamente.";
                echo '<script>alert("'.$sms.'")</script> ';
                echo "<script>location.href='problemas.php'</script>";
                exit;
                    
            } else {
                $errors[] = "Error al enviar codigo";
                
            }
        }   
    }

  ?>
<html xml:lang="es" xmlns="http://www.w3.org/1999/xhtml">
 <head>
    <meta content="es_MX" http-equiv="Content-Language" />

    <link media="all" href="css/dino_style.css" type="text/css" rel="stylesheet" />
    <script src="js/jquery-ui.custom.min.js"></script>
     <title>Enviar Problema</title>
     <style>

        .post>form{
            width:950px;
            margin:auto;
            margin-top:5px;
            padding:30px;
            border:2px solid #bbb;
            -moz-border-radius:11px;
        }

        .post>form label{
            display:block;
            color:#777777;
            font-size:13px;
        }
        .post>form p{
            color:#777777;
            font-size:14px;
            text-align:justify;
            margin-bottom:20px;
        }
        
        .post>form textarea{
            background:#FBFBFB none repeat scroll 0 0;
            border:1px solid #E5E5E5;
            font-size:16px;
            margin-bottom:16px;
            width:97%;
            min-width: 97%;
            min-height: 200px;
            border-radius: 5px;
            

        }
        .post>form select{
            background:#FBFBFB none repeat scroll 0 0;
            border:1px solid #E5E5E5;
            font-size: 15px;
            width:15%;
            text-align:justify;
        }
        .post>form input.button {
            -moz-border-radius-bottomleft:6px;
            -moz-border-radius-bottomright:6px;
            -moz-border-radius-topleft:6px;
            -moz-border-radius-topright:6px;
            border:1px solid #AAAAAA;
            font-size:16px;
            padding:3px;
            border-radius: 5px;
        }
        #enviar{
            font-size: 30px;
            color:#777777;

            text-align:justify;
            margin-top: -20px;
        }
       
        .right{
            text-align:right;
        }
    </style>
 </head>
 <body>
<div class="wrapper">
    <?php 
if(isset($_SESSION["UserID"])){
    ?>
    <div><br><br><br></div>
    <?php include_once("includes/header.php"); ?>
    <div class="post" style="background:white;">
            <form action="enviar.php" method="post" enctype="multipart/form-data">
            <p id="enviar">
            Enviar Soluci&oacute;n
            </p>
            <div align="center" >
            Codigo Fuente.
            </div>
                    <textarea 
                        cols        =   40 
                        rows        =   15 
                        id           =   "editor1" 
                        name="codigo"
                        placeholder =   'Pega el codigo fuente aqui'     
                        onmousemove =   "checkForText(this.value)"></textarea>
                    <br>
                    <div align="center" >
                    Lenguaje :
                    <select name="lenguaje">
                        <option value="JAVA">Java</option>
                        <option value="C">C</option>
                        <option value="C++">C++</option>
                        <option value="C++">C++11</option>
                        <!-- <option value="php">PHP</option> -->                                                                                
                    </select>
                    <br><br>
                    <input type="hidden" name="enviar_a" value="<?php echo $_POST['enviar_a']; ?>">
                    <input type="submit" class="button" name="btnEnviar" value="Enviar" />
                    </div>
            </form> 
    </div>
<?php 
}else{
?>
<div><br><br><br></div>
<div align='center'><h2>Debe iniciar sesi&oacute;n para enviar soluci&oacute;n.</h2></div> 
<?php 
}
?>
 </div>
 
 <div id="errores">
<?php echo resultBlock($errors); ?>
</div>
 </body>
 </html>
 