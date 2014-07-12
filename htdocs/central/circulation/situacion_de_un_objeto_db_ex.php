<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      situacion_de_un_objeto_db_ex.php
 * @desc:      Shows the status of the items of an bibliographic record when the items are defined inside the bilbiographic record
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0
 *
 * == BEGIN LICENSE ==
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * == END LICENSE ==
*/
session_start();
// Situación de un objeto
if (!isset($_SESSION["permiso"])){	header("Location: ../common/error_page.php") ;
}if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../common/get_post.php");
include("../config.php");
include("../config_loans.php");
$lang=$_SESSION["lang"];

include("../lang/admin.php");
include("../lang/prestamo.php");

include("../reserve/reserves_read.php");

//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
if (isset($arrHttp["inventory"]))
	$Opcion="inventario";
else
	$Opcion="control";
include("leer_pft.php");
include("borrowers_configure_read.php");
include("calendario_read.php");
include("locales_read.php");


// Se localiza el número de control en la base de datos de objetos  de préstamo
function ReadControlNumber($control_number,$Opcion,$db,$prefix_cn,$pft_cn){
global $db_path,$Wxis,$xWxis,$lang_db,$msgstr;

	//Read the FDT of the database for extracting the prefix used for indexing the control number
//	echo $control_number;
    $Expresion=$prefix_cn.$control_number;
	//se ubica el título en la base de datos de objetos de préstamo
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$query = "&Opcion=disponibilidad&base=$db&cipar=$db_path"."par/$db.par&Expresion=".$Expresion."&Pft=$pft_cn";
	include("../common/wxis_llamar.php");
	$total=0;
	$titulo="";
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if ($linea!=""){
			if (substr($linea,0,8)=='$$TOTAL:'){
				$total=substr($linea,8);
			}else{				return $linea;
				break;			}
		}
	}
}

// Se localiza el número de inventario en la base de datos de objetos  de préstamo
function ReadCatalographicRecord($control_number,$Opcion,$db,$prefix_in){
global $db_path,$Wxis,$xWxis,$lang_db,$pft_in,$msgstr;

    $Expresion=$prefix_in.$control_number;
    //se ubica el título en la base de datos de objetos de préstamo
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj=$db_path."$db/loans/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path."$db/loans/".$lang_db."/loans_display.pft";
	$query = "&Opcion=disponibilidad&base=$db&cipar=$db_path"."par/$db.par&Expresion=".$Expresion."&Formato=$formato_obj";
	include("../common/wxis_llamar.php");
	$total=0;
	$titulo="";
	foreach ($contenido as $linea){
		$linea=trim($linea);

		if (substr($linea,0,8)=='$$TOTAL:')
			$total=trim(substr($linea,8));
		else
			echo $linea."\n";

	}
	//SE LEEN LOS ITEMS

	$query = "&Opcion=disponibilidad&base=$db&cipar=$db_path"."par/$db.par&Expresion=".$Expresion."&Pft=($pft_in/)";
	include("../common/wxis_llamar.php");
	$total=0;
	echo "<table bgcolor=#dddddd cellpadding=5>
			<th></th>
			<th>".$msgstr["inventory"]."</th>
			<th>".$msgstr["usercode"]."</th>
			<th>".$msgstr["devdate"]."</th>";
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if (trim($linea)!=""){
			if (substr($linea,0,8)=='$$TOTAL:')
				$total=trim(substr($linea,8));
			else
				ShowItems($linea,$control_number,$Opcion);
		}

	}
}

Function ShowItems($item,$codigos,$Opcion){
global $config_date_format;
	echo "<tr><td bgcolor=white></td>";
	echo "<td bgcolor=white>";
	echo $item."</td>";
    $Expresion="TR_P_".$item;
    $cont=ListarPrestamo($Expresion);
    $cont=implode("",$cont);
    if (substr($cont,0,8)!='$$TOTAL:'){
    	$cont=explode('###',$cont);
    	$c=explode('$$$',$cont[0]);
    	echo "<td bgcolor=white>".$c[0]."</td><td bgcolor=white>";
    	$date = new DateTime($c[1]);
    	switch (substr($config_date_format,0,2)){
    		case "DD":
    			echo $date->format("d/m/Y");
    			break;
    		default:
    			echo $date->format("m/d/Y");
    			break;
    	}
    	echo "</td>";
    }else{
    	echo "<td bgcolor=white>&nbsp;</td><td bgcolor=white>&nbsp;</td>";
    }

}



function ListarPrestamo($Expresion){
//se ubican todas las copias disponibles para verificar si están prestadasglobal $xWxis,$arrHttp,$db_path,$Wxis;
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj="v20'$$$'if p(v500) then v500 else v40 fi,'###'" ;
 	$query = "&Opcion=disponibilidad&base=trans&cipar=$db_path"."par/trans.par&Expresion=".$Expresion."&Pft=$formato_obj";
    include("../common/wxis_llamar.php");
	return $contenido;
}


// ------------------------------------------------------
// INICIO DEL PROGRAMA
// ------------------------------------------------------
include("../common/header.php");
include("../common/institutional_info.php");
?>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["ecobj"]?>
	</div>
	<div class="actions">
		<a href="situacion_de_un_objeto.php?base=".$arrHttp["base"]."&encabezado=s" class="defaultButton backButton">
			<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
			<span><?php echo $msgstr["back"]?></strong></span>
		</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/situacion_de_un_objeto.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/situacion_de_un_objeto.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: situacion_de_un_objeto_db_ex.php</font>\n";

echo "
	</div>
<div class=\"middle form\">
	<div class=\"formContent\">";

//GET THE INFORMATION OF THE DATABASE SELECTED
$arrHttp["db_orig"]=$arrHttp["db"];
$b=explode('|',$arrHttp["db"]);
$catalog_db=$b[0];
$pref_in=$b[2];
$pft_in=$b[3];
$pref_cn=$b[4];
$pft_nc=$b[5];
$arrHttp["db"]=$catalog_db;
require_once("databases_configure_read.php");
if (isset($arrHttp["inventory"])){	$arrHttp["inventory"]=urldecode($arrHttp["inventory"]);}else{
	$arrHttp["inventory"]=$arrHttp["control"];
}

$codigos=explode("\n",$arrHttp["inventory"]);
foreach ($codigos as $cod_inv){
	$cod_inv=trim($cod_inv);
	if ($cod_inv=="")continue;
	if ($Opcion=="inventario"){		$ejemp=ReadCatalographicRecord($cod_inv,$Opcion,$catalog_db,$pref_in);
		$pref=$pref_in;
	}else{		$ejemp=ReadCatalographicRecord($cod_inv,$Opcion,$catalog_db,$pref_cn);
		$pref=$pref_cn;	}
	echo "</table>";
	$control_no=ReadControlNumber($cod_inv,$Opcion,$catalog_db,$pref,$pft_nc);
	$reserves_user=ReservesRead("CN_".$catalog_db."_".$control_no);
	if ($reserves_user!="")
		echo "<p><strong>".$msgstr["reserves"]."</strong><br>".$reserves_user."<p>";
}




echo "<p>";
echo "</div></div>";
include("../common/footer.php");
echo "</body></html>";




?>
<form name=reservas method=post action=../reserve/delete_reserve.php>
<input type=hidden name=Mfn_reserve>
<input type=hidden name=Accion>
<?php
if (isset($arrHttp["db_orig"]))
	echo "<input type=hidden name=db value=".$arrHttp["db_orig"].">\n";
if (isset($arrHttp["control"]))
	echo "<input type=hidden name=control value=".$arrHttp["control"].">\n";
else
	if (isset($arrHttp["inventory"]))
		echo "<input type=hidden name=inventory value=".$arrHttp["inventory"].">\n";
if (isset($arrHttp["inventory_sel"]))
	echo "<input type=hidden name=inventory_sel value=".$arrHttp["inventory_sel"].">\n";
?>
<input type=hidden name=retorno value="../circulation/situacion_de_un_objeto_db_ex.php">
</form>
<script>
function  DeleteReserve(Mfn){
	document.reservas.Accion.value="delete"
	document.reservas.Mfn_reserve.value=Mfn
	document.reservas.submit()
}
function  CancelReserve(Mfn){
	document.reservas.Accion.value="cancel"
	document.reservas.Mfn_reserve.value=Mfn
	document.reservas.submit()
}
</script>