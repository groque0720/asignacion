<?php
// include ("../include/security.php");

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");
// extract($_POST);

$usuario=mysql_real_escape_string($_POST["usuario"]);
$contrasena = mysql_real_escape_string($_POST["contraseña"]);

$SQL="SELECT * FROM usuarios WHERE activo = 1 AND usuario = '$usuario' AND contraseña = '$contrasena'";
$result=mysqli_query($con, $SQL);
$campo=mysqli_fetch_array($result);

if (!empty($campo['usuario'])) {
	@session_start();
	$_SESSION["autentificado"]= "SI";
	$_SESSION["idperfil"]=$campo['id_perfil'];
	$_SESSION["idusuario"]=$campo['id_usuario'];

	echo '<script src="js/pace.min.js"></script>';
	echo '<script>	self.location = "webs/panel.php"</script>';

} else {
	echo '<p>El usuario y/o la contraseña son incorrectas. Asegúrate de usar datos de tu cuenta Derka y Vargas.</p>';
}

mysqli_close($con);
?>
