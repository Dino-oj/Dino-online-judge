<?php
	function isNullDatosProblema($titulo,$autor,$tiempo,$memoria,$descripcion,$entrada,$salida,$ejemploentrada,$ejemplosalida){
		if(strlen(trim($titulo)) < 1 || strlen(trim($autor)) < 1 || strlen(trim($tiempo)) < 1 || strlen(trim($memoria)) < 1 || strlen(trim($descripcion)) < 1|| strlen(trim($entrada)) < 1|| strlen(trim($salida)) < 1|| strlen(trim($ejemploentrada)) < 1|| strlen(trim($ejemplosalida)) < 1)
		{
			return true;
			} else {
			return false;
		}		
	}
	function isNull($nombre, $user, $pass, $pass_con, $email){
		if(strlen(trim($nombre)) < 1 || strlen(trim($user)) < 1 || strlen(trim($pass)) < 1 || strlen(trim($pass_con)) < 1 || strlen(trim($email)) < 1)
		{
			return true;
			} else {
			return false;
		}		
	}
	
	function isEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)){
			return true;
			} else {
			return false;
		}
	}
	
	function emailExiste($email)
	{
		global $enlace;
		
		$stmt = $enlace->prepare("SELECT UserID FROM usuario WHERE Email = ? LIMIT 1");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		$stmt->close();
		
		if ($num > 0){
			return true;
			} else {
			return false;	
		}
	}
	function resultBlock($errors){
		if(count($errors) > 0)
		{
			echo "<div id='error' class='alert alert-danger' role='alert'>
			<a href='#' onclick=\"showHide('error');\">[X]</a>
			<ul>";
			foreach($errors as $error)
			{
				echo "<li>".$error."</li>";
			}
			echo "</ul>";
			echo "</div>";
		}
	}
	function generateToken()
	{
		$gen = md5(uniqid(mt_rand(), false));	
		return $gen;
	}
	
	function hashPassword($password) 
	{
		$hash = password_hash($password, PASSWORD_DEFAULT);
		return $hash;
	}
	
	
	
	
	function enviarEmail($email, $nombre, $asunto, $cuerpo){
		
		require_once 'PHPMailer/PHPMailerAutoload.php';
		
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'tls'; //Modificar
		$mail->Host = 'smtp.office365.com'; //Modificar
		$mail->Port = 587; //Modificar
		
		$mail->Username = 'Judge_Dino_Online@hotmail.com'; //Modificar
		$mail->Password = 'juezdinoinformatica2018'; //Modificar
		
		$mail->setFrom('Judge_Dino_Online@hotmail.com', 'Dino Judge'); //Modificar
		$mail->addAddress($email, $nombre);
		
		$mail->Subject = $asunto;
		$mail->Body    = $cuerpo;
		$mail->IsHTML(true);
		
		if($mail->send())
		return true;
		else
		return false;
	}
	
	function validaIdToken($id, $token){
		global $enlace;
		
		$stmt = $enlace->prepare("SELECT activacion FROM usuarios WHERE id = ? AND token = ? LIMIT 1");
		$stmt->bind_param("is", $id, $token);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;
		
		if($rows > 0) {
			$stmt->bind_result($activacion);
			$stmt->fetch();
			
			if($activacion == 1){
				$msg = "La cuenta ya se activo anteriormente.";
				} else {
				if(activarUsuario($id)){
					$msg = 'Cuenta activada.';
					} else {
					$msg = 'Error al Activar Cuenta';
				}
			}
			} else {
			$msg = 'No existe el registro para activar.';
		}
		return $msg;
	}
	
	function validaPassword($var1, $var2)
	{
		if (strcmp($var1, $var2) !== 0){
			return false;
			} else {
			return true;
		}
	}
	
	function isNullLogin($usuario, $password){
		if(strlen(trim($usuario)) < 1 || strlen(trim($password)) < 1)
		{
			return true;
		}
		else
		{
			return false;
		}		
	}
	
	
	
	
	
	function generaTokenPass($user_id,$email)
	{
		global $enlace;
		
		$token = generateToken(); 
		
		$stmt = $enlace->prepare("insert lostpassword SET Token=?,MailSent=1, UserID=?");
		$stmt->bind_param('si', $token, $user_id);
		$stmt->execute();
		$stmt->close();
		
		return $token;
	}
	
	function getValor($campo, $campoWhere, $valor)
	{
		global $enlace;
		
		$stmt = $enlace->prepare("SELECT $campo FROM usuario WHERE $campoWhere = ? LIMIT 1");
		$stmt->bind_param('s', $valor);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		
		if ($num > 0)
		{
			$stmt->bind_result($_campo);
			$stmt->fetch();
			return $_campo;
		}
		else
		{
			return null;	
		}
	}
	function getPasswordRequest($id)
	{
		global $enlace;
		
		$stmt = $enlace->prepare("SELECT password_request FROM usuarios WHERE id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($_id);
		$stmt->fetch();
		
		if ($_id == 1)
		{
			return true;
		}
		else
		{
			return null;	
		}
	}
	
	function verificaTokenPass($user_id, $token){
		
		global $enlace;
		
		$stmt = $enlace->prepare("SELECT ID FROM lostpassword WHERE UserID = ? AND Token = ? AND MailSent = 1 LIMIT 1");
		$stmt->bind_param('is', $user_id, $token);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		
		if ($num > 0)
		{
			$stmt2 = $enlace->prepare("SELECT Estado FROM usuario WHERE UserID = ? LIMIT 1");
			$stmt2->bind_param('i', $user_id);
			$stmt2->execute();
			$stmt2->store_result();
			$stmt2->bind_result($estado);
			$stmt2->fetch();
			if($estado == 1)
			{
				return true;
			}
			else 
			{
				return false;
			}
			
		}
		else
		{
			return false;	
		}
	}
	
	function cambiaPassword($password, $user_id){
		
		global $enlace;
		
		$stmt = $enlace->prepare("UPDATE usuario SET Password = ? WHERE UserID = ?");
		$stmt->bind_param('si', $password, $user_id);
		
		if($stmt->execute()){
			return true;
			} else {
			return false;		
		}
	}	

	function registraProblema($id,$titulo,$autor,$tiempo,$memoria,$descripcion,$entrada,$salida,$ejemploentrada,$ejemplosalida,$arc){
		
		global $enlace;
		
		$stmt = $enlace->prepare("INSERT INTO problema (UserID, Titulo,TiempoLimite,MemoriaLimite,Source,descripcion,Entrada,Salida,EjemploEntrada,EjemploSalida,Autor) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param('isiisssssss',$id,$titulo,$tiempo,$memoria,$arc,$descripcion,$entrada,$salida,$ejemploentrada,$ejemplosalida,$autor);
		if ($stmt->execute()){
			return $enlace->insert_id;
			} else {
			return 0;	
		}		
	}	
	function isNumero($num){
		if (is_numeric($num)){
			return true;
		} else {
			return false;	
		}		
	}	
	function saltoLinea($str) { 
	  return preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $str); 
	} 
	function registraEnvio($user,$enviar_a,$lenguaje,$codigo){
		global $enlace;
		
		$stmt = $enlace->prepare("INSERT INTO ejecucion (UserName, ProbID,Lenguaje,Source) VALUES(?,?,?,?)");
		$stmt->bind_param('siss',$user,$enviar_a,$lenguaje,$codigo);
		if ($stmt->execute()){
			return $enlace->insert_id;
			} else {
			return 0;	
		}		
	}	