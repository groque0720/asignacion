<?php
include("../z_comun/funciones/funciones.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
extract($_POST);

@session_start();
$usuario=$_SESSION['usuario'];
$contrasena =mysqli_real_escape_string($con, $password);

$SQL="SELECT * FROM usuarios WHERE usuario COLLATE utf8_bin = '".$usuario."' AND clave COLLATE utf8_bin= '".$contrasena."'";
$result=mysqli_query($con, $SQL);

$campo=mysqli_fetch_assoc($result);

if (!empty($campo['usuario'])) {

	if ($newpassword === $confirmnewpassword) {

		$SQL="UPDATE usuarios SET clave = '".$newpassword."' WHERE id_usuario = ".$_SESSION["id_usuario"];
		mysqli_query($con, $SQL);

		echo '<script>
				swal("Contraseña Actualizada", "", "success").then(function(){ window.location.href = "../index.php"; })
			</script>';
	}else{
		echo '<script>
				swal("NO COINCIDE LAS CONTRASEÑAS", "Por favor verifique que sean iguales", "error");
				$("#confirmnewpassword").focus();
			</script>';
	}

	// echo '<script>	window.location.href = "periodos/";</script>';

	mysqli_close($con);

	}else{

		echo '<script>	swal("VERIFIQUE CONTRASEÑA ACTUAL", "Por favor verifique que sus datos sean correctos", "error");
				$("#password").focus();
			</script>';

	}
?>