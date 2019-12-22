<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Comprobar Unidad</title>

	<style type="text/css">
	   body{
	   	width: 100%;
	   	};
	   table{
	   	width: 60%;
	   	text-align: center;
	   	margin: 0 auto;
	   };
 	</style>
</head>
<body>
<?php
include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'"); ?>

<table rules="all" border="1" id="tabla_res"


	<?php

	$SQL="SELECT reservas.nrounidad AS nrounidad, count(*) AS cantidad FROM reservas GROUP BY reservas.nrounidad HAVING count(*) > 1 ";
	$res=mysqli_query($con, $SQL);

	while ($reg=mysqli_fetch_array($res)) {

		//echo $reg["nrounidad"]." - ".$reg["idcliente"]." - ".$reg["cantidad"];


		if ($reg["nrounidad"]!="" && $reg["nrounidad"]!=null && $reg["nrounidad"]!=0) {



			$SQL="SELECT  reservas.idreserva, reservas.fecres as fecha, reservas.nrounidad as nrounidad, clientes.nombre as cliente, usuarios.nombre as asesor FROM clientes
			Inner Join reservas ON reservas.idcliente = clientes.idcliente
			Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
			WHERE reservas.nrounidad =".$reg["nrounidad"];
			$resf=mysqli_query($con, $SQL);

			while ($regf=mysqli_fetch_array($resf)) {
				echo "<tr>";
					echo "<td>".$regf["fecha"]."</td>";
					echo "<td>".$regf["nrounidad"]."</td>";
					echo "<td>".$regf["cliente"]."</td>";
					echo "<td>".$regf["asesor"]."</td>";
				echo "</tr>";
			 };


		}

	}

	mysqli_close($con);
	 ?>

</table>
</body>
</html>








