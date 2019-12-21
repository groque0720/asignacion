<?php 
include("../z_comun/funciones/funciones.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
extract($_POST);

$usuario=mysqli_real_escape_string($con, $usuario);
// $contrasena =mysqli_real_escape_string($con, $password);

$SQL="SELECT * FROM usuarios WHERE usuario COLLATE utf8_bin = '".$usuario."'"; // AND clave COLLATE utf8_bin= '".$contrasena."'";
$result=mysqli_query($con, $SQL);

$campo=mysqli_fetch_assoc($result);

if (!empty($campo['usuario'])) {

	@session_start();
	
	$_SESSION["autentificado"]= "SI";
	$_SESSION["id_usuario"]= $campo['id_usuario'];
	$_SESSION["nombre_usuario"] = $campo['nombre'];

	echo '<script>	window.location.href = "periodos/";</script>';

	mysqli_close($con); 

	}else{

		echo '<script>	swal("ERROR AL VALIDAR ACCESO", "Por favor verifique que sus datos sean correctos", "error");
				$("#usuario").facus();
			</script>';

	}	
?>