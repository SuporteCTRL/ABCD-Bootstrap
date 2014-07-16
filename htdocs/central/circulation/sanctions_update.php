<?php
///////////////////////////////////////////////////////////////
// Update sanctions in database
//////////////////////////////////////////////////////////////
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../common/get_post.php");
include("../config.php");
include("../config_loans.php");
$lang=$_SESSION["lang"];
include("../lang/prestamo.php");

//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";
//die;
// se leen los valores locales para convertir la fecha a ISO
include("locales_read.php");
// Se lee el calendario de días hábiles
include("calendario_read.php");

include("fecha_de_devolucion.php");

$FechaP=$arrHttp["date"];
$df=explode('/',$config_date_format);
switch ($df[0]){
	case "DD":
		$dia=substr($FechaP,0,2);
		break;
	case "MM":
		$mes=substr($FechaP,0,2);
		break;
}
switch ($df[1]){
	case "DD":
		$dia=substr($FechaP,3,2);
		break;
	case "MM":
		$mes=substr($FechaP,3,2);
		break;
}
$year=substr($FechaP,6,4);
$fecha_desde= $year.$mes.$dia;

// se calcula la fecha de vencimiento de la sanción sumando los días de suspensión
if ($arrHttp["type"]=="S"){
}
switch ($arrHttp["type"]){
	case "M":
		$tipor="M";                                     		//v1
		$status="0";	                                  		//v10
		$cod_usuario=$arrHttp["usuario"];                  		//v20
 		$concepto=$arrHttp["reason"];    						//v40
    	$fecha=$fecha_desde;	              					//v30
      	$monto=$arrHttp["units"]*$p[7]*$locales["fine"];        //v50
      	$ValorCapturado="0001$tipor\n0010$status\n0020$cod_usuario\n0030$fecha\n0040$concepto\n0050$monto\n";
      	if (isset($arrHttp["comments"])) $ValorCapturado.="0100".$arrHttp["comments"];
		break;
	case "S":
	// se calcula la fecha en que vence la suspensión
		$fecha_v=FechaDevolucion($arrHttp["units"],"D",$arrHttp["date"]);

		$tipor="S";                      						//v1
		$status="0";	                 						//v10
		$cod_usuario=$arrHttp["usuario"]; 						//v20
 		$concepto=$arrHttp["reason"];    						//v40
    	$fecha=$fecha_desde;	          						//v30
    	$fecha_v=substr($fecha_v,0,8);	                 					//v60
    	$ValorCapturado="0001$tipor\n0010$status\n0020$cod_usuario\n0030$fecha\n0040$concepto\n0060$fecha_v\n";
    	if (isset($arrHttp["comments"])) $ValorCapturado.="0100".$arrHttp["comments"];
		break;
}
$ValorCapturado=urlencode($ValorCapturado);
$IsisScript=$xWxis."actualizar.xis";
$query = "&base=suspml&cipar=$db_path"."par/suspml.par&login=".$_SESSION["login"]."&Mfn=New&Opcion=crear&ValorCapturado=".$ValorCapturado;
include("../common/wxis_llamar.php");

header("Location: usuario_prestamos_presentar.php?base=users&usuario=".$arrHttp["usuario"]);
?>
