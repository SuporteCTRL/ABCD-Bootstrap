<?php
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include ("../common/get_post.php");
include("../config.php");
include("../lang/prestamo.php");
include("../common/header.php");
include("../common/institutional_info.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
if (isset($arrHttp["Expresion"])) $arrHttp["Expresion"]=urldecode($arrHttp["Expresion"]);
function CancelReserve($Mfn){global $db_path,$Wxis,$xWxis,$arrHttp;	$fecha_dev=date("Ymd");
	$hora_dev=date("H:i:s");
	$ValorCapturado="00011\n";
	$ValorCapturado.="0130$fecha_dev\n";
	$ValorCapturado.="0131$hora_dev\n";
	$ValorCapturado.="0132".$_SESSION["login"]."\n";
	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."actualizar_registro.xis";
	$Formato="";
	$query = "&base=reserve&cipar=$db_path"."par/reserve.par&login=".$_SESSION["login"]."&Mfn=".$Mfn."&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");}
function DeleteReserve($Mfn){
global $db_path,$Wxis,$xWxis,$arrHttp;	$query = "&base=reserve&cipar=$db_path"."par/reserve.par&login=".$_SESSION["login"]."&Mfn=" . $Mfn."&Opcion=eliminar";
	$IsisScript=$xWxis."eliminarregistro.xis";
	include("../common/wxis_llamar.php");}

function AssignReserve($Mfn){global $db_path,$Wxis,$xWxis,$arrHttp;	$fecha=date("Ymd");
	$hora=date("H:i:s");
	$ValorCapturado= "00011\n";
	$ValorCapturado.="0060$fecha\n";
	$ValorCapturado.="0132".$_SESSION["login"]."\n";
	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."actualizar_registro.xis";
	$Formato="";
	$query = "&base=reserve&cipar=$db_path"."par/reserve.par&login=".$_SESSION["login"]."&Mfn=".$Mfn."&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");}
?>
<html>
<head>
<script>
function Cerrar(){	document.forma1.submit()}
</script>
</head>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["reserve"]?>
	</div>
	<div class="actions">
	 <?php // include("../circulation/submenu_prestamo.php");?>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
	<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/circulacion/reserva.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"])) echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/circulacion/reserva.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: reserve/delete_reserve.php" ?></font>
</div>
<div class="middle form">
	<div class="formContent">
<form name=forma1 action=<?php echo $arrHttp["retorno"] ?> method=post>
<?php
if (isset($arrHttp["Expresion"])) $arrHttp["Expresion"]=urlencode($arrHttp["Expresion"]);
foreach ($arrHttp as $var=>$value){
	echo "<input type=hidden name=$var value=\"".$value."\">\n";}
switch ($arrHttp["Accion"]){	case "delete":
		$res=DeleteReserve($arrHttp["Mfn_reserve"]);
		$msg=$msgstr["reserve_deleted"];
		break;
	case "cancel":
		CancelReserve($arrHttp["Mfn_reserve"]);
		$msg=$msgstr["reserve_canceled"];
		break;
	case "assign":
		AssignReserve($arrHttp["Mfn_reserve"]);
		$msg=$msgstr["copy_assigned"];
		break;}
?>
</form>
<?php echo "<h4>".$msg. "</h4>"?>
<input type=button  onclick=Cerrar() value="<?php echo $msgstr["back"]?>">

   </div>
</div>
<?php
	include("../common/footer.php");
?>
</body>

</html>