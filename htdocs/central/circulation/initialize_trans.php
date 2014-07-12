<?php

//**********************************
// script: homepage.php
// editado: 11/07/2014
// editor: Roger C. Guilherme
//*********************************


// Globales.
//set_time_limit (0);
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
include ("../config.php");
include("../lang/dbadmin.php");
include("../lang/prestamo.php");
//echo $wxisUrl;

function InicializarBd($base){
global $xWxis,$wxisUrl,$db_path,$Wxis,$msgstr;
 	$query = "&base=".$base."&cipar=$db_path"."par/$base".".par&Opcion=inicializar";
 	$IsisScript=$xWxis."administrar.xis";
 	include("../common/wxis_llamar.php");
	foreach ($contenido as $linea){
	 	if ($linea=="OK"){	 		echo "<div class=\"alert alert-success\" role=\"alert\">".$base." ".$msgstr["init"]."</div>";	 	}
 	}
}

include("../common/header.php");
echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	//include("../common/institutional_info.php");
?>

<h2><?php echo $msgstr["init_trans"]; ?></h2>

</div>
<?php }


$base[]="trans";
$base[]="suspml";
$base[]="reserve";
foreach ($base as $bd){
    if (!file_exists($db_path.$bd)){    	echo "<div class=\"alert alert-success\" role=\"alert\">".$db_path.$bd.": ".$msgstr["folderne"]."</div>";
    	continue;    }
    if (!file_exists($db_path."par/".$bd.".par")){
    	echo "<div class=\"alert alert-success\" role=\"alert\">".$db_path."par/".$bd.".par: ".$msgstr["ne"]."</div>";
    	continue;
    }
?>

<?php
    $arrHttp["IsisScript"]="administrar.xis";
	InicializarBd($bd);

}

?>
</div></div>

<?php	include("../common/footer.php"); ?>

</body></html>

<?php die;?>
