<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      situacion_de_un_objeto_ex.php
 * @desc:      Shows the status of the items of an bibliographic record when the items are defined in the loanobjects database
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

//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
include("leer_pft.php");
include("borrowers_configure_read.php");

include("loanobjects_read.php");
include("calendario_read.php");
include("locales_read.php");

include("../reserve/reserves_read.php");


function LocalizarInventario($inventory,$Opcion){
global $db_path,$Wxis,$xWxis;
	//$Expresion=trim($prefix_in).$arrHttp["inventory"];
    if ($Opcion=="inventario")
    	$Expresion="IN_".$inventory;
    else
    	$Expresion="CN_".$inventory;
	$formato_ex="v1'||'v10'||',(v959^i,'$$$ ',v959^l,'$$$ ',v959^b,'$$$ ',v959^o,'$$$ ',v959^v,'$$$ ',v959^t'###'),/";
	//se ubica el título en la base de datos de objetos de préstamo
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj=urlencode($formato_ex);
	$query = "&Opcion=disponibilidad&base=loanobjects&cipar=$db_path"."par/loanobjects.par&Expresion=".$Expresion."&Pft=$formato_ex";
	include("../common/wxis_llamar.php");
	$tit_cat=array();
	$ix=0;
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if ($linea!="")
			$tit_cat[]=$linea;

	}

	return $tit_cat;
}


// Se localiza el número de inventario en la base de datos de objetos  de préstamo
function ReadCatalographicRecord($control_number,$db){
global $db_path,$Wxis,$xWxis,$arrHttp,$pft_totalitems,$pft_in,$pft_nc,$pft_typeofr,$titulo,$prefix_in,$multa,$lang_db;

	//Read the FDT of the database for extracting the prefix used for indexing the control number
//	echo $control_number;
    $Expresion="CN_".$control_number;
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
			$titulo.=$linea."\n";
	}
	return $total;
}



function ListarPrestamo($Expresion){
//se ubican todas las copias disponibles para verificar si están prestadasglobal $xWxis,$arrHttp,$db_path,$Wxis;
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj="v20'$$$'v40,'###'" ;
 	$query = "&Opcion=".$arrHttp["Opcion"]."&base=trans&cipar=$db_path"."par/trans.par&Expresion=".$Expresion."&Pft=$formato_obj&cuenta=10";
    include("../common/wxis_llamar.php");
	return $contenido;
}


// ------------------------------------------------------
// INICIO DEL PROGRAMA
// ------------------------------------------------------
include("../common/header.php");
include("../common/institutional_info.php");
$reserves=0;
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
echo "<font color=white>&nbsp; &nbsp; Script: situacion_de_un_objeto_ex.php</font>\n";

echo "
	</div>
<div class=\"middle form\">
	<div class=\"formContent\">";

if (isset($arrHttp["inventory"]))
	$Opcion="inventario";
else
	$Opcion="control";
$disp=0;
$lista_control_no="";
// se busca el título para ver el total de ejemplares y los ejemplares prestados
$arrHttp["Opcion"]="disponibilidad";
if (isset($arrHttp["inventory"]))
	$arrHttp["inventory"]=urldecode($arrHttp["inventory"]);
else
	$arrHttp["inventory"]=$arrHttp["control"];
$codigos=explode("\n",$arrHttp["inventory"]);
foreach ($codigos as $cod_inv){	echo "$cod_inv $Opcion<br>";
	$cod_inv=trim($cod_inv);
	if ($cod_inv=="")continue;
	$ejemp=LocalizarInventario($cod_inv,$Opcion);
	var_dump($ejemp);
	if (count($ejemp)==0){		echo $msgstr["ctrlnumno"];
	}else{
       	foreach ($ejemp as $tit){       		if (trim($tit)!=""){
       			if (substr($tit,0,8)!='$$TOTAL:') {
					$t=explode('||',$tit);
					$catalog_db=$t[1];
					$control_no=$t[0];
					if (strpos($lista_control_no,";".$control_no.";")===false){						$lista_control_no.=";".$control_no.";";
					}else{						continue;					}
					if (trim($catalog_db)!=""){
						$arrHttp["db"]=$catalog_db;
						require_once("databases_configure_read.php");
						$total=ReadCatalographicRecord($control_no,$catalog_db);
						if ($total>1){							echo "<font color=red>".$msgstr["dupctrl"]."</font><p>";						}
						echo '<font color=darkblue><strong>'.$msgstr["bd"].": ". $catalog_db.". ".$msgstr["control_n"].": ".$control_no."</strong></font><br>";
						echo $titulo;

						$items=explode("###",$t[2]);
						echo "<table bgcolor=#dddddd cellpadding=5>
								<th></th>
								<th>".$msgstr["inventory"]."</th>
								<th>".$msgstr["volume"]."</th>
								<th>".$msgstr["tome"]."</th>
								<th>".$msgstr["typeofitems"]."</th>
								<th>".$msgstr["usercode"]."</th>
								<th>".$msgstr["devdate"]."</th>";
						foreach ($items as $val) {							if (trim($val)!="") ShowItems($val,$codigos,$Opcion);						}
	                	echo "</table>";
	              	}
	              	$reserves_user=ReservesRead("CN_".strtoupper($catalog_db)."_".$control_no,"N");
					if ($reserves_user!="")
						echo "<p><strong>".$msgstr["reserves"]."</strong><br>".$reserves_user."<p>";
	      		}
			}
			echo "<p>";
		}
	}
}

Function ShowItems($item,$codigos,$Opcion){
global $config_date_format;
	$l=explode('$$$',$item);
	echo "<tr><td bgcolor=white></td>";
	echo "<td bgcolor=white>";
	foreach ($codigos as $cod_inv){
		if ($l[0]==$cod_inv and $Opcion=="inventario")
			echo "<font color=red>";
	}
	echo $l[0]."</td>";
	echo "<td bgcolor=white>".$l[4]."</td>";
	echo "<td bgcolor=white>".$l[5]."</td>";
	echo "<td bgcolor=white>".$l[3]."</td>";
    $Expresion="TR_P_".$l[0];
    $cont=ListarPrestamo($Expresion);
    $cont=implode("",$cont);
    if (substr($cont,0,8)!='$$TOTAL:'){
    	$cont=explode('###',$cont);
    	$c=explode('$$$',$cont[0]);
    	echo "<td bgcolor=white>".$c[0]."</td><td bgcolor=white>";
    	$date = new DateTime($c[1]);
    	switch (substr($config_date_format,0,2)){    		case "DD":
    			echo $date->format("d/m/Y");
    			break;
    		default:
    			echo $date->format("m/d/Y");
    			break;    	}
    	echo "</td>";
    }else{    	echo "<td bgcolor=white>&nbsp;</td><td bgcolor=white>&nbsp;</td>";    }

}

echo "</div></div>";
include("../common/footer.php");
echo "</body></html>";

function EjemplaresPrestados($inventory){
global $db_path,$Wxis,$xWxis;	$formato_obj=$db_path."trans/pfts/".$_SESSION["lang"]."/loans_display.pft";
    if (!file_exists($formato_obj)) $formato_obj=$db_path."trans/pfts/".$lang_db."/loans_display.pft";
   	$query = "&Expresion=TR_P_".$inventory."&base=trans&cipar=$db_path"."par/trans.par&Formato=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$prestamos=array();
	foreach ($contenido as $linea){
		$prestamos[]=$linea;
	}
	$nv=0;   //número de préstamos vencidos
	$np=0;   //Total libros en poder del usuario
	if (count($prestamos)>0) {

		foreach ($prestamos as $linea) {
			$p=explode("^",$linea);
			$np=$np+1;
			$dif= compareDate ($p[9]);
			$fuente="";
			$mora="0";
			if ($dif<0) {
				$nv=$nv+1;
				$mora=abs($dif/(60*60*24));    //cuenta de préstamos vencidos
			    $fuente="<font color=red>";
			}
			echo "<td  bgcolor=white valign=top><xa href=usuario_prestamos_presentar.php?usuario=".$p[10]." target=_blank>";
			echo $p[10];
			echo "</a></td>

				<td bgcolor=white align=center valign=top><xa href=VerPolitica('".$p[3]."','".$p[6]."')>". $p[3]. "</a></td><td bgcolor=white nowrap align=center valign=top>".$p[4]."</td><td nowrap bgcolor=white align=center valign=top>$fuente".$p[5]."</td><td align=center bgcolor=white valign=top>".$mora."</td></tr>";

		}
//		echo "</table></dd><br>";
        echo "<script>
		np=$np
		nv=$nv
		</script>\n";
	}
}

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
<input type=hidden name=retorno value="../circulation/situacion_de_un_objeto_ex.php">
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