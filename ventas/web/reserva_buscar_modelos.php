<?php
$options="";


    include("../funciones/func_mysql.php");
    conectar();
   mysql_query("SET NAMES 'utf8'");

    $SQL="SELECT * FROM modelos WHERE activo = 1 AND idgrupo=".$_POST["elegido"];
    $modelos=mysqli_query($con, $SQL); ?>
    <option value="" selected></option>

    <?php
    while ($mod=mysqli_fetch_array($modelos)) { ?>
        <option value="<?php echo $mod["idmodelo"]; ?>"><?php echo $mod["modelo"]; ?></option>
   <?php }
    mysqli_close($con);
    echo $options;
    ?>
