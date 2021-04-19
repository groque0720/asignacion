<?php
$options="";


  include("../funciones/func_mysql.php");
conectar();
 //mysql_query("SET NAMES 'utf8'");

        $SQL="SELECT grupos.idgrupo as idgrupo, grupos.grupo as grupo, tipos.idtipo
        FROM (grupos INNER JOIN modelos ON grupos.idgrupo = modelos.idgrupo) INNER JOIN tipos ON modelos.idtipo = tipos.idtipo
        WHERE modelos.activo = 1 and modelos.idgrupo <> 14 GROUP BY idgrupo, grupo, tipos.idtipo
        HAVING (((tipos.idtipo)=".$_POST["elegido"]."))";


    $grupos=mysqli_query($con, $SQL); ?>
    <option value="" selected></option>

    <?php
    while ($grup=mysqli_fetch_array($grupos)) { ?>
        <option value="<?php echo $grup["idgrupo"]; ?>"><?php echo $grup["grupo"]; ?></option>
   <?php }
    mysqli_close($con);
    echo $options;

    ?>
