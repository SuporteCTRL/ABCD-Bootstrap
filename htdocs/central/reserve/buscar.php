<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      browse.php
 * @desc:      Browse the loan's databases
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
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value)  echo "$var=$value<br>";
$arrHttp["Target"]="reserve";
$arrHttp["desde"]="reserve";
include ("../config.php");
include("../lang/dbadmin.php");

include("../lang/admin.php");
include("../lang/prestamo.php");
if (isset($arrHttp["usuario"]))
	$_SESSION["user_reserve"]=$arrHttp["usuario"];

$Permiso=$_SESSION["permiso"];

include("../common/header.php");
include("../common/institutional_info.php");
include("../dataentry/formulariodebusqueda.php");
include("reserves_read.php");
//SE LEE LA CONFIGURACION LAS POLITICAS DE PRESTAMO
include("../circulation/loanobjects_read.php");
include("../circulation/borrowers_configure_read.php");

$reserves_u_cn="";
$bases_p=SeleccionarBaseDeDatos($db_path,$msgstr);


function LeerPft($pft_name,$base){
global $arrHttp,$db_path,$lang_db;
	$pft="";
	$archivo=$db_path.$base."/loans/".$_SESSION["lang"]."/$pft_name";
	if (!file_exists($archivo)) $archivo=$db_path.$base."/loans/".$lang_db."/$pft_name";
	$fp=file_exists($archivo);
	if ($fp){
		$fp=file($archivo);
		foreach ($fp as $value){
			$pft.=$value;
		}

	}
    return $pft;
}

function ExtraerTipoUsuario($usuario){
global $db_path,$Wxis,$xWxis,$arrHttp,$pft_uskey,$pft_ustype,$pft_usvig,$pft_usdisp,$msgstr;	// se lee la configuración de la base de datos de usuarios

	# Se lee el prefijo y el formato para extraer el código de usuario
	$us_tab=LeerPft("loans_uskey.tab","users");
	$t=explode("\n",$us_tab);
	$uskey=$t[0];
	//Se obtiene el código, tipo y vigencia del usuario
	$formato=$pft_ustype.'\'$$\''.$pft_usdisp;
	$formato=urlencode($formato);
	$query = "&Expresion=".trim($uskey).$usuario."&base=users&cipar=$db_path"."par/users.par&Pft=$formato";
	$contenido="";
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$user="";
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if ($linea!="")  $user.=$linea;
	}
	$l=explode('$$',$user);
	echo "<table width=100%><td>";
	echo $l[1];
	echo"</td>";
	echo "<td valign=top width=14%>\n";
	echo "<input type=hidden name=usuario value=";
	if (isset($_SESSION["user_reserve"])){
		echo $_SESSION["user_reserve"];
	}
	echo "> " ;
	//echo '<input type="button" name="list" value="'. $msgstr["list"].'" class="submit" onclick="javascript:AbrirIndice(document.reserva.usuario)"/>';
	echo "<p><input type=button name=reserve value=".$msgstr["reserve"]." onclick=javascript:Reservar(\"".$arrHttp["base"]."\")>";
	echo "</td></table>";
	return $l[0];
}



//se busca el numero de control en el archivo de transacciones para ver si el usuario tiene otro ejemplar prestado
function LocalizarTransacciones($control_number,$prefijo){
global $db_path,$Wxis,$xWxis,$arrHttp,$msgstr,$tr_prestamos;
	$formato_obj=$db_path."trans/pfts/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path."trans/pfts/".$lang_db."/loans_display.pft";
	$query = "&Expresion=".$prefijo."_P_".$control_number."&base=trans&cipar=$db_path"."par/trans.par&Formato=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$prestamos=array();
	foreach ($contenido as $linea){
		if (trim($linea)!=""){
			$lp=explode('^',$linea);
			$prestamos[$lp[0]]=$lp[4];
        }
	}
	return $prestamos;
}

// Se localiza el número de inventario en la base de datos de objetos  de préstamo
function LocalizarCopiasLoanObjects($control_number,$prestamos,$tipoUsuario){
global $db_path,$Wxis,$xWxis,$arrHttp,$politica;
    $Expresion="CN_".$arrHttp["base"]."_".$control_number;
	//READ LOANOBJECT DATABASE TO GET THE RECORD WITH THE ITEMS OF THE TITLE
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";

	$Expresion=urlencode($Expresion);
	$formato_ex="(if p(v959) then v959^i,'||',v959^o/ fi)";
    // control number||database||inventory||main||branch||type||volume||tome
	$formato_obj=urlencode($formato_ex);
	$query = "&Opcion=disponibilidad&base=loanobjects&cipar=$db_path"."par/loanobjects.par&Expresion=".$Expresion."&Pft=$formato_obj";
	include("../common/wxis_llamar.php");
	$total=0;
	$copies_title=array();
	$item="";
    $items_reserve=0;
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if ($linea!=""){			if (substr($linea,0,8)=='$$TOTAL:'){
				$total=trim(substr($linea,8));
			}else{
				$l=explode('||',$linea);
				if (count($l)>0){
					if (isset($prestamos[$l[0]]))
						$f_dev= $prestamos[$l[0]];
					else
						$f_dev="";
					$can_reserve=$politica[$l[1]][$tipoUsuario];
					if ($can_reserve[11]=="Y"){
						$puede_reservar="Y";
						$items_reserve=$items_reserve+1;
					}else{
						$puede_reservar="";
					}
					$copies_title[]="<tr><td bgcolor=white>".$l[0]."</td><td bgcolor=white>".$l[1]."</td><td bgcolor=white>$f_dev</td><td bgcolor=white align=center>$puede_reservar</td></tr>\n";
				}
			}
		}
	}
	return array($copies_title,$items_reserve);
}


function LocalizarCopias($control_number,$prestamos,$tipoUsuario){
global $db_path,$Wxis,$xWxis,$arrHttp,$politica,$msgstr,$lang_db;
	//SE LEE EL PREFIJO PARA EXTRAER EL NUMERO DE CONTROL DE LA BASE DE DATOS
	$archivo=$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_conf.tab";
	if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/loans/".$lang_db."/loans_conf.tab";
	if (!file_exists($archivo)){
		echo $msgstr["falta"]." ".$arrHttp["db"]."/loans/".$_SESSION["lang"]."/loans_conf.tab";
		die;
	}else{
		$fp=file($archivo);
		foreach ($fp as $value){
			if (trim($value)!=""){
				$ix=strpos($value," ");
				$tag=trim(substr($value,0,$ix));
				switch($tag){
					case "IN": $prefix_in=trim(substr($value,$ix));
						break;
					case "NC": $prefix_cn=trim(substr($value,$ix));
						break;
				}
			}
		}
	}
	$pft_totalitems=LeerPft("loans_inventorynumber.pft",$arrHttp["base"]);
	$pft_typeofr=LeerPft("loans_typeofobject.pft",$arrHttp["base"]);
    $Expresion=$prefix_cn.$control_number;
	//READ LOANOBJECT DATABASE TO GET THE RECORD WITH THE ITEMS OF THE TITLE
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$query = "&Opcion=disponibilidad&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par&Expresion=".$Expresion."&Pft=($pft_totalitems'||'".$pft_typeofr."/)/";
	include("../common/wxis_llamar.php");
	$total=0;
	$copies_title=array();
	$item="";
    $items_reserve=0;
	foreach ($contenido as $linea){		$linea=trim($linea);
		if ($linea!=""){
			if (substr($linea,0,8)=='$$TOTAL:'){
				$total=trim(substr($linea,8));
			}else{
				$l=explode('||',$linea);
				if (isset($prestamos[$l[0]]))
					$f_dev= $prestamos[$l[0]];
				else
					$f_dev="";
				$can_reserve=explode('|',$politica[$l[1]][$tipoUsuario]);
				if ($can_reserve[11]=="Y"){
					$puede_reservar="Y";
					$items_reserve=$items_reserve+1;
				}else{					$puede_reservar="";
				}
				$copies_title[]="<tr><td bgcolor=white>".$l[0]."</td><td bgcolor=white>".$l[1]."</td><td bgcolor=white>$f_dev</td><td bgcolor=white align=center>$puede_reservar</td></tr>\n";
			}
		}
	}
	return array($copies_title,$items_reserve) ;
}


//SE VERIFICA SI EL TITULO ESTA DISPONIBLE PARA RESERVA
function Disponibilidad($control_n,$copies,$tipoUsuario){	// SE DETERMINA SI HAY EJEMPLARES PRESTADOS
	$prestamos=LocalizarTransacciones($control_n,"TC");
	$disponibles=0;
	$items=array();
	//SE DETERMINA LOS EJEMPLARES EXISTENTES
	switch ($copies){		case "S":
			// SE DETERMINA LA DISPONIBILIDAD DE LOS EJEMPLARES DESDE LOANOBJECTS
			$items=LocalizarCopiasLoanobjects($control_n,$prestamos,$tipoUsuario);
			$disponibles=$items[1];
			break;
		case "N":
			// SE DETERMINA LA DISPONIBILIDAD DE LOS EJEMPLARES DESDE EL CATÁLOGO
			$items=LocalizarCopias($control_n,$prestamos,$tipoUsuario);
			$disponibles=$items[1];
			break;	}
	return array($disponibles,$prestamos,$items[0]);
}

function Reservas($cn,$base){global $msgstr,$arrHttp,$db_path,$xWxis,$tagisis,$Wxis,$wxisUrl,$lang_db,$config_date_format,$reservas_u_cn;
	$output=ReservesRead("CN_".$base."_".$cn);
	if ($output!="")
		$output= "<br><strong><font color=darkred>".$msgstr["reserves"].": </font></strong><br>".$output;
	return $output;}

function MostrarResultados($contenido){global $msgstr,$arrHttp,$db_path,$xWxis,$tagisis,$Wxis,$wxisUrl,$lang_db,$Expresion,$copies,$reservas_u_cn;
	$con="";
	$ix=0;

	echo "<form name=reserva method=post action=reservar_ex.php>\n";
	echo "<input type=hidden name=base value=".$arrHttp["base"].">\n";
	echo "<input type=hidden name=cipar value=".$arrHttp["cipar"].">\n";
	echo "<input type=hidden name=Expresion value=\"".urlencode($Expresion)."\">\n";
	echo "<input type=hidden name=chk_reserva>";
	echo "<input type=hidden name=reservados>";
	echo "<input type=hidden name=copies value=$copies>";
	$tipoUsuario=ExtraerTipoUsuario($_SESSION["user_reserve"]);
	echo "<input type=hidden name=tipoUsuario value='$tipoUsuario'>\n";
	foreach ($contenido as $value) $con.=$value;
	$registro=explode('####',$con);
	echo "<br><table bgcolor=#eeeeee width=100% border=0>";
	echo "<td bgcolor=white width=85% valign=top>\n";
	foreach ($registro as $linea){		if (trim($linea)!="") {
			$lin=explode('$$$$',$linea);
			$msgerr="";
			if (isset($lin[1])){
				echo "<p>".$lin[1]."</p>";
				$output=Reservas($lin[1],$arrHttp["base"]);
				$disponibilidad=Disponibilidad($lin[1],$arrHttp["copies"],$tipoUsuario);
				$habilitar="";
				if ($disponibilidad[0]>0 and $disponibilidad[0]-count($disponibilidad[1])<=0){
					$habilitar="S";
				}else{					$msgerr=" <strong><font color=red>".$msgstr["reserve_tit_5"]."</font></strong>";
					$habilitar="N";				}
				if (strpos($reservas_u_cn,$arrHttp["base"].$lin[1]." ".$_SESSION["user_reserve"])===false){					if ($habilitar=="") $habilitar="S";				}else{
					$msgerr=" <strong><font color=red>".$msgstr["reserve_tit_6"]."</font></strong>";					$habilitar="N";				}
				if ($habilitar=="S"){					$ix=$ix+1;
	            	echo "<input type=checkbox name=chk_reserva value=\"".$lin[1]."\"><font color=darkred>".$msgstr["reserve"]."</font><br>";				}
				//echo $msgstr["control_n"]." = ".$lin[1]."<br>";				echo $lin[0]."\n";
				echo "<br><font color=darkblue><strong>".$msgstr["disp_loan"].": ".$disponibilidad[0]." &nbsp; ".$msgstr["loaned"].": ".count($disponibilidad[1])."</strong></font> $msgerr<br>";
				//if ($disponibilidad[0]>0){					echo "<table bgcolor=#eeeeee>";
					echo "<td>".$msgstr["inventory"]."</td><td>".$msgstr["typeofitems"]."</td><td>".$msgstr["devdate"]."</td>";
					echo  "<td>".$msgstr["tit_reserva"]."</td>";					foreach ($disponibilidad[2] as $value){						echo $value."\n";					}
					echo "</table>";				//}
				echo $output;
				echo "<hr>";
			}

		}
	}

	echo "</td>\n";

	echo "</form>";
	echo "</table>";
}


function EjecutarBusqueda(){
global $arrHttp,$db_path,$xWxis,$tagisis,$Wxis,$wxisUrl,$lang_db,$Expresion;

	$formato_cn=$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_cn.pft";
	if (!file_exists($formato_cn)) $formato_cn=$db_path.$arrHttp["base"]."/loans/$lang_db/loans_cn.pft";
	$fp=file($formato_cn);
	$Pft_cn="";
	foreach ($fp as $value){		$value=trim($value);
		if ($value!="")
			$Pft_cn.=$value;
	}

	$formato_obj=$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path.$arrHttp["base"]."/loans/".$lang_db."/loans_display.pft";
	$fp=file($formato_obj);
	$Pft="";
	foreach ($fp as $value){		$Pft.=$value;	}
	$Pft=$Pft."'$$$$'".$Pft_cn."'####'";
	$Pft=urlencode($Pft);
 	$arrHttp["Formato"]=$formato_obj;
	$vienede=$arrHttp["Opcion"];
	if ($arrHttp["Opcion"]!="continuar" and $arrHttp["Opcion"]!="buscar_en_este"){
		$Expresion=PrepararBusqueda();
	}else{
		$Expresion=urldecode($arrHttp["Expresion"]);
	 	$Expresion=stripslashes($Expresion);
		$arrHttp["Opcion"]="busquedalibre";
	}
	//echo $Expresion;
	$Expresion=urlencode(trim($Expresion));
	if (!isset($arrHttp["from"])) $arrHttp["from"]=1;
	if (!isset($arrHttp["Mfn"])) $arrHttp["Mfn"]=1;
	$arrHttp["count"]="";
	if (!isset($arrHttp["Formato"]))$arrHttp["Formato"]="ALL";
	$Formato=$arrHttp["Formato"];
	$IsisScript=$xWxis."buscar_ingreso.xis";
	$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Expresion=".$Expresion."&Opcion=".$arrHttp["Opcion"]."&count=".$arrHttp["count"]."&Mfn=".$arrHttp["Mfn"]."&Pft=$Pft";
	include("../common/wxis_llamar.php");
	MostrarResultados($contenido);

}


// Prepara la fórmula de búsqueda cuando viene de la búsqueda avanzada

function PrepararBusqueda(){
global $arrHttp,$matriz_c,$camposbusqueda;

	if (!isset($arrHttp["Campos"])) $arrHttp["Campos"]="";
	$expresion=explode(" ~~~ ",$arrHttp["Expresion"]);
	$campos=explode(" ~~~ ",$arrHttp["Campos"]);
	if (isset($arrHttp["Operadores"])){
		$operadores=explode(" ~~~ ",$arrHttp["Operadores"]);
	}
	if (isset($arrHttp["Prefijos"])){
		$prefijos=explode(" ~~~ ",$arrHttp["Prefijos"]);
	}
	// se analiza cada sub-expresion para preparar la fórmula de búsqueda
	$nse=-1;
	for ($i=0;$i<count($expresion);$i++){
		$expresion[$i]=trim(stripslashes($expresion[$i]));
		if ($expresion[$i]!=""){

			$cb=$matriz_c[$prefijos[$i]];
			$cb=explode('|',$cb);
			$pref=trim($cb[2]);
			$pref1='"'.$pref;
			if (substr(strtoupper($expresion[$i]),0,strlen($pref1))==strtoupper($pref1) or substr(strtoupper($expresion[$i]),0,strlen($pref))==strtoupper($pref)){

			}else{

				$expresion[$i]=$pref.$expresion[$i];
			}
			$formula=str_replace("  "," ",$expresion[$i]);
			$subex=Array();
			if (trim($campos[$i])!="" and trim($campos[$i])!="---"){
				$id="/(".trim($campos[$i]).")";
			}else{
				$id="";
			}
			$xor="¬or¬$pref";
			$xand="¬and¬$pref";

			$formula=stripslashes($formula);
			while (is_integer(strpos($formula,'"'))){
				$nse=$nse+1;
				$pos1=strpos($formula,'"');
				$xpos=$pos1+1;
				$pos2=strpos($formula,'"',$xpos);
				$subex[$nse]=trim(substr($formula,$xpos,$pos2-$xpos));
				if ($pos1==0){
					$formula="{".$nse."}".substr($formula,$pos2+1);
				}else{
					$formula=substr($formula,0,$pos1-1)."{".$nse."}".substr($formula,$pos2+1);
				}
			}
			$formula=str_replace (" {", "{", $formula);
			$formula=str_replace (" or ", $xor, $formula);
			$formula=str_replace ("+", $xor, $formula);
			$formula=str_replace (" and ", $xand, $formula);
			$formula=str_replace ("*", $xand, $formula);
			$formula=str_replace ('\"', '"', $formula);
		//	if (substr($formula,0,strlen($pref))!=$pref)
		//		$formula=$pref.$formula;
			while (is_integer(strpos($formula,"{"))){
				$pos1=strpos($formula,"{");
				$pos2=strpos($formula,"}");
				$ix=substr($formula,$pos1+1,$pos2-$pos1-1);
				if ($pos1==0){
					$formula=$subex[$ix].substr($formula,$pos2+1);
				}else{
					$formula=substr($formula,0,$pos1)." ".$subex[$ix]." ".substr($formula,$pos2+1);
				}
			}

			$formula=str_replace ("¬", " ", $formula);
//			if (substr($formula,0,strlen($pref))!=$pref) $formula=$pref.$formula;
			$expresion[$i]=trim($formula);
		}
	}
	$formulabusqueda="";
	for ($i=0;$i<count($expresion);$i++){
		if (trim($expresion[$i])!=""){
			$formulabusqueda=$formulabusqueda." (".$expresion[$i].") ";
			$resto="";
			for ($j=$i+1;$j<count($expresion);$j++){
				$resto=$resto.trim($expresion[$j]);
			}
			if (trim($resto)!="") $formulabusqueda=$formulabusqueda." ".$operadores[$i];
		}
	}
	return $formulabusqueda;

}




function SeleccionarBaseDeDatos($db_path,$msgstr){global $copies,$ix_nb,$base_sel;	$sel_base="N";
	$ix_nb=-1;
	$base_sel="";
	if (file_exists($db_path."loans.dat")){		$copies="N";
		$bases_p=SeleccionarLoansDat($db_path,$msgstr);
	}else{		$copies="S";
		$bases_p=SeleccionarBasesDat($db_path,$msgstr);
	}
	return $bases_p;
}

function SeleccionarLoansDat($db_path,$msgstr){
global $ix_nb,$base_sel;
	$fp=file($db_path."loans.dat");
	foreach ($fp as $value){		if (trim($value)!=""){
			$ix_nb=$ix_nb+1;
			$value=trim($value);
			$bases_p[]=$value;
			$v=explode('|',$value);
			$base_sel=$v[0];
		}
	}
	return $bases_p;
}

function SeleccionarBasesDat($db_path,$msgstr){
global $ix_nb,$base_sel;
	$fp=file($db_path."bases.dat");
	$bases_p=array();
	foreach ($fp as $value){
		$value=trim($value);
		if (trim($value)!=""){
			$v=explode('|',$value);
			if (isset($v[2])){
				if ($v[2]=="Y"){
					$ix_nb=$ix_nb+1;
					$bases_p[]=$v;
					$base_sel=$v[0];
				}
			}
		}
	}
	return $bases_p;
?>
		</select>
		</td>
	</table>

<?php
}
?>
<script src=../dataentry/js/lr_trim.js></script>
<script>

	function Delete(Mfn){
		alert(Mfn)
	}

function AbrirIndice(xI){
	Ctrl_activo=xI
	lang="<?php echo $_SESSION["lang"]?>"
<?php
# Se lee el prefijo y el formato para extraer el código de usuario
$us_tab=LeerPft("loans_uskey.tab","users");
$t=explode("\n",$us_tab);
$codigo=LeerPft("loans_uskey.pft","users");
?>
	Separa=""
	Formato="<?php if (isset($t[2]))  echo trim($t[2]); else echo 'v30';?>,`$$$`,<?php echo str_replace("'","`",$codigo)?>"
    Prefijo=Separa+"&prefijo=<?php if (isset($t[1])) echo trim($t[1]); else echo 'NO_';?>"
    ancho=200
	url_indice="../circulation/capturaclaves.php?opcion=autoridades&base=users&cipar=users.par"+Prefijo+"&postings=1"+"&lang="+lang+"&repetible=0&Formato="+Formato
	msgwin=window.open(url_indice,"Indice","width=480, height=450,left=300,scrollbars")
	msgwin.focus()
}


function Enviar(){	ix=document.seleccionar.bd.selectedIndex	if (ix>0){		base=document.seleccionar.bd.options[ix].value
		document.busqueda.base.value=base
		document.busqueda.cipar.value=base+".par"
		document.busqueda.copies.value=copies
		document.busqueda.submit()	} else{		alert("<?php echo $msgstr["seldb"]?>")
		return	}}
function Reservar(Base,Control){
	if (document.reserva.usuario.value==""){		alert("<?php echo $msgstr["falta"]." ".$msgstr["usercode"]?>")
		return false	}	tope=document.reserva.chk_reserva.length
	r=""
	for (i=1;i<tope;i++) {		if (document.reserva.chk_reserva[i].checked){
			r=r+document.reserva.chk_reserva[i].value +"|"		}	}
	if (r==""){		alert("<?php echo $msgstr["selreserves"]?>")
		return false	}
	document.reserva.reservados.value=r
	document.reserva.submit()}
</script>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["reserve"];
		if (isset($arrHttp["base"]))
			echo " - ".$arrHttp["base"];
		else
			echo " - ".$base_sel;
		?>
	</div>
	<div class="actions">
<?php include("../circulation/submenu_prestamo.php");?>

	</div>
	<div class="spacer">&#160;</div>
</div>
<?php if (!isset($arrHttp["base"])){?>
<div class="helper">
	<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/circulation/reserva.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"])) echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/circulation/reserva.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: reserve/buscar.php</font>
	</div>";
 } ?>
<div class="middle form">
		<div class="formContent">

<?php
if (!isset($arrHttp["base"])){
	if ($ix_nb==0){		$arrHttp["base"]=$base_sel;
		$arrHttp["cipar"]=$base_sel.".par";
		$arrHttp["Opcion"]="formab";
		$arrHttp["copies"]=$copies;
		$arrHttp["desde"]="reserva";
		$arrHttp["count"]=1;	}else{
		echo "\n<script>copies='$copies'</script>\n";		?>
		<form name=seleccionar>
		<input type=hidden name=Opcion value=formab>
		<table width=100% border=0>
			<td width=150>
				<label for="dataBases">
				<strong><?php echo $msgstr["basedatos"]?></strong>
				</label>
				</td><td>
				<select name=bd onchange=Enviar()>
				<option></option>
		<?php
		foreach ($bases_p as $value){
			echo "<option value=".$value[0].">".$value[1]."</option>\n";
		}
		echo "</select>
		      </table>
		      </form>";	}
}
if (!isset($arrHttp["base"])){}else{
	$a= $db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/camposbusqueda.tab";
	$fp=file_exists($a);
	if (!$fp){
		$a= $db_path.$arrHttp["base"]."/pfts/".$lang_db."/camposbusqueda.tab";
		$fp=file_exists($a);
		if (!$fp){
			echo "<br><br><h4><center>".$msgstr["faltacamposbusqueda"]."</h4>";
			die;
		}
	}
	$fp = fopen ($a, "r");
	$fp = file($a);
	foreach ($fp as $linea){
		$linea=trim($linea);
		if ($linea!=""){
	        $camposbusqueda[]= $linea;
			$t=explode('|',$linea);
			$pref=$t[2];
			$matriz_c[$pref]=$linea;
		}
	}
	switch ($arrHttp["Opcion"]){		case "formab":
		    $arrHttp["Opcion"]="buscar";
			DibujarFormaBusqueda();

			die;
			break;
		case "buscar":
		case "buscar_en_este":
			$arrHttp["Expresion"]=urldecode($arrHttp["Expresion"]);
			EjecutarBusqueda();
			break;

		}

}
echo "</div></div>";
include("../common/footer.php");
?>
<form name=busqueda action=buscar.php method=post>
<input type=hidden name=base>
<input type=hidden name=desde value=reserva>
<input type=hidden name=count value=1>
<input type=hidden name=cipar>
<input type=hidden name=Opcion value=formab>
<input type=hidden name=copies>

</form>
</body>
</Html>

<form name=reservas method=post action=../reserve/delete_reserve.php>
<input type=hidden name=Mfn_reserve>
<input type=hidden name=Accion>
<?php
$arrHttp["Expresion"]=urlencode($arrHttp["Expresion"]);
foreach ($arrHttp as $var=>$value){	echo "<input type=hidden name=$var value=\"$value\">\n";}
?>
<input type=hidden name=retorno value="../reserve/buscar.php">
</form>
<script>
function  DeleteReserve(Mfn){
	document.reservas.Accion.value="delete"
	document.reservas.Mfn_reserve.value=Mfn
	document.reservas.submit()
}
function  CancelReserve(Mfn){	document.reservas.Accion.value="cancel"
	document.reservas.Mfn_reserve.value=Mfn
	alert(document.reservas.Mfn_reserve.value)
	document.reservas.submit()
}
</script>