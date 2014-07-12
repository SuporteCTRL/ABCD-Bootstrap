<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      usuario_prestamos_presentar.php
 * @desc:      Analyzes the user and item for establishing the loan policy
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
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}

//date_default_timezone_set('UTC');
$debug="";
if (!isset($_SESSION["login"])) die;
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value)  echo "$var=>$value<br>";
include("../config.php");
//include("../config_loans.php");              // BORRADO EL 07/03/2013
$lang=$_SESSION["lang"];
include("../lang/admin.php");
include("../lang/prestamo.php");
include("fecha_de_devolucion.php");
include ('../dataentry/leerregistroisispft.php');
include("leer_pft.php");
//Calendario de días feriados
include("calendario_read.php");
//Horario de la biblioteca, unidades de multa, moneda
include("locales_read.php");
// se leen las politicas de préstamo y la tabla de tipos de usuario
include("loanobjects_read.php");
// se lee la configuración de la base de datos de usuarios
include("borrowers_configure_read.php");
# Se lee el prefijo y el formato para extraer el código de usuario
$us_tab=LeerPft("loans_uskey.tab","users");
$t=explode("\n",$us_tab);
$uskey=$t[0];

include("../reserve/reserves_read.php");
if (isset($arrHttp["reserve"])){	include("../reserve/seleccionar_bd.php");}

$valortag = Array();

$ec_output="" ;
$recibo_arr=array();

//Se averiguan los recibos que hay que imprimir
$recibo_list=array();
$Formato="";
if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/receipts.lst")){
		$Formato=$db_path."trans/pfts/".$_SESSION["lang"]."/receipts.lst";
	}else{
		if (file_exists($db_path."trans/pfts/".$lang_db."/receipts.lst")){
			$Formato=$db_path."trans/pfts/".$lang_db."/receipts.lst";
		}
	}
if ($Formato!=""){	$fp=file($Formato);
	foreach ($fp as $value){		if (trim($value)!=""){			$value=trim($value);
			$recibo_list[$value]=$value;		}	}}

function PrestamoMismoObjeto($control_number,$user,$base_origen){
global $copies_title,$msgstr,$obj;
	$msg="";	$tr_prestamos=LocalizarTransacciones($control_number,"ON",$base_origen);
	$items_prestados=count($tr_prestamos);
	if ($items_prestados>0){
		foreach($tr_prestamos as $value){
			if (trim($value)!=""){
				$nc_us=explode('^',$value);
		   		$pi=$nc_us[0];                                   //GET INVENTORY NUMBER OF THE LOANED OBJECT
		   		$pv=$nc_us[14];                                  //GET THE VOLUME OF THE LOANED OBJECT
		   		$pt=$nc_us[15];                                  //GET THE TOME OF THE LOANED OBJECT
				$comp=$pi." ".$pv." ".$pt;
				foreach ($copies_title as $cop){
					$c=explode('||',$cop);
					$comp_01=$c[2];
					if (isset($c[6]))
						$comp_01.=" ".$c[6];
					if (isset($c[7]))
						$comp_01.=" ".$c[7];
					if ($nc_us[10]==$user){    //SE VERFICA SI LA COPIA ESTÁ EN PODER DEL USUARIO
						if ($comp_01==$comp and $obj[14]!="Y"){
							if ($msg=="")
								$msg= $msgstr["duploan"];
							else
								$msg.="<br>".$msgstr["duploan"];
						}
					}
				}
			}
	    }

	}
	return array($msg,$items_prestados);}

function LocalizarReservas($control_number,$catalog_db,$usuario,$items_prestados,$prefix_cn,$copies,$pft_ni) {
global $xWxis,$Wxis,$db_path,$msgstr,$wxisUrl;
	$IsisScript=$xWxis."cipres_usuario.xis";
	// Mfn
	// 10:codigo de usuario
	// 30:Fecha reserva
	// 40:Fecha límite de retiro
	// 60:Fecha de asignacion de la reserva
	// 130:Fecha de cancelación de la reserva
	// 200:Fecha en que se ejecutó la reserva y se prestó el item al usuario
	$Pft="f(mfn,1,0)'|'v10'|'v30'|'v40'|'v60'|'v130'|'v200/";
	$Expresion="CN_".$catalog_db."_".$control_number." AND ST_0";
	$query="&base=reserve&cipar=$db_path"."par/reserve.par&Expresion=$Expresion&Pft=$Pft";
    //echo $query;
    //die;
	include("../common/wxis_llamar.php");
	$reservas=array();
	foreach ($contenido as $value){		$value=trim($value);
		if ($value!=""){
			$r=explode('|',$value);
			$fecha_cancelacion=$r[5];  //Fecha en la cual el operador canceló la reserva
			$fecha_anulacion=$r[3];    //Fecha hasta la cual la reserva asignada está disponible
			$fecha_asignacion=$r[4];   //Fecha en la cual se asignó la reserva
			$fecha_prestamo=$r[6];     //Fecha en la cual se prestó el objeto reservado
			//SE BUSCAN LAS RESERVAS ASIGNADAS
			if ($fecha_cancelacion!=""  or $fecha_prestamo!="") continue;
			if ($fecha_anulacion!=""){				if ($fecha_anulacion<date("Ymd")) continue;				if ($usuario==$r[1]){
					return array("continuar",$r[0]);
				}
			}
			$reservas[]=$value;
		}
	}
	if (count($reservas)==0){		return array("continuar",0);	}
	//SI HAY RESERVAS PENDIENTES SE ANALIZA SI QUEDAN EJEMPLARES DISPONIBLES LUEGO DE SACAR LOS DE RESERVA
	// A. LEER EL TOTAL DE ITEMS DEL TITULO
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	if ($copies=="Y"){
		$Expresion="CN_".$catalog_db."_".$control_number;
		$catalog_db="loanobjects";
		$pft_ni="(v959/)";
	}else{
		//SE LEE EL PREFIJO A UTILIZAR PARA LOCALIZAR EL OBJETO A TRAVÉS DE SU NÚMERO DE INVENTARIO
		$Expresion=$prefix_cn.$control_number;
		$catalog_db=strtolower($catalog_db);
		$pft_ni="(".$pft_ni."/)";
	}

	$query = "&Opcion=disponibilidad&base=$catalog_db&cipar=$db_path"."par/$catalog_db.par&Expresion=".$Expresion."&Pft=".urlencode($pft_ni);
	include("../common/wxis_llamar.php");
	$obj=array();
	foreach ($contenido as $value){
		$value=trim($value);
		echo $value;
		if (trim($value)!="" and substr($value,0,8)!='$$TOTAL:')
			$obj[]=$value;
	}
	$disponibilidad=count($obj)-$items_prestados-count($reservas);
	//SI LA DISPONIBILIDAD ES MAYOR QUE EL NUMERO DE RESERVAS PENDIENTES SE OTORGA EL PRESTAMO
	//Y SI EL USUARIO ESTÁ EN LA COLA DE PENDIENTES SE LE GRABAN LOS DATOS PARA INDICAR QUE YA SE
	//EJECUTO LA RESERVA
	//LA FUNCION DEVUELVE EL MFN DEL REGISTRO DE RESERVAS PARA ACTUALIZAR LOS DATOS DEL PRÉSTAMO CONCEDIDO
	//O CERO SI EL PRESTAMO NO SATISFACE NINGUNA RESERVA

	if ($disponibilidad>count($reservas)){
		foreach ($reservas as $value){
			$r=explode('|',$value);
			if ($r[1]==$usuario){
				return array("continuar",$r[0]);
			}
		}
		return array("continuar",0);
	}

	//SI LA DISPONIBILIDAD ES MENOR O IGUAL AL NUMERO DE RESERVAS, SE DA EL PRESTAMO SI EL USUARIO LO TIENE RESERVADO
	if ($disponibilidad<=count($reservas) and $disponibilidad!=0){
		foreach ($reservas as $value){
			$r=explode('|',$value);
			if ($r[1]==$usuario){
				return array("continuar",$r[0]);
			}
		}
		return array("no_continuar",0);
	}
    return array("no_continuar",0);
}

function ActualizarReserva($mfn_reserva,$diap,$horap){
global $db_path,$Wxis,$wxisUrl,$xWxis,$lang_db;
	$ValorCapturado ="00014\n";	$ValorCapturado.="0200$diap";
	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."actualizar_registro.xis";
	if (file_exists($db_path."reserve/pfts/".$_SESSION["lang"]."/reserve.pft")){
		$Formato=$db_path."reserve/pfts/".$_SESSION["lang"]."/reserve";
	}else{
		if (file_exists($db_path."reserve/pfts/".$lang_db."/reserve.pft")){
			$Formato=$db_path."reserve/pfts/".$lang_db."/reserve";
		}
	}
	$Formato="&Formato=$Formato";
	$query = "&base=reserve&cipar=$db_path"."par/reserve.par&login=".$_SESSION["login"]."&Mfn=".$mfn_reserva."&ValorCapturado=".$ValorCapturado."$Formato";
	include("../common/wxis_llamar.php");
}

function ProcesarPrestamo($usuario,$inventario,$signatura,$item,$usrtype,$copies,$ppres,$prefix_in,$prefix_cn,$mfn_reserva){
global $db_path,$Wxis,$wxisUrl,$xWxis,$pr_loan,$pft_storobj,$recibo_arr,$recibo_list;
	$item_data=explode('||',$item);
	$nc=$item_data[0];                  // Control number of the object
	$bib_db=$item_data[1];
	$arrHttp["db"]=$bib_db;
	$item="$pft_storobj";
	// Read the bibliographic database that contains the object using the control mumber extracted from the copy
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	if ($copies=="Y"){
		$Expresion="CN_".$nc;
	}else{
		//SE LEE EL PREFIJO A UTILIZAR PARA LOCALIZAR EL OBJETO A TRAVÉS DE SU NÚMERO DE INVENTARIO
		$Expresion=$prefix_cn.$nc;
	}
    $bib_db=strtolower($bib_db);
	$query = "&Opcion=disponibilidad&base=$bib_db&cipar=$db_path"."par/$bib_db.par&Expresion=".$Expresion."&Pft=".urlencode($item);
	include("../common/wxis_llamar.php");
	$obj="";
	foreach ($contenido as $value){
		$value=trim($value);
		if (trim($value)!="")
			$obj.=$value;
	}
	$objeto=explode('$$',$obj);
	$obj=explode('|',$ppres);
	$fp=date("Ymd h:i A");	// DEVOLUTION DATE
	$fd=FechaDevolucion($obj[3],$obj[5],"");
	$ix=strpos($fp," ");
	$diap=trim(substr($fp,0,$ix));
	$horap=trim(substr($fp,$ix));
	$ix=strpos($fd," ");
	$diad=trim(substr($fd,0,$ix));
	$horad=trim(substr($fd,$ix));

	$ValorCapturado="0001P\n";
	$ValorCapturado.="0010".trim($inventario)."\n";	// INVENTORY NUMBER
	if (isset($item_data[6])) $ValorCapturado.="0012".$item_data[6]."\n";         	// VOLUME
	if (isset($item_data[7])) $ValorCapturado.="0015".$item_data[7]."\n";             // TOME
	$ValorCapturado.="0020".$usuario."\n";
	$ValorCapturado.="0030".$diap."\n";
	//if ($obj[5]=="H")
	$ValorCapturado.="0035".$horap."\n";
	$ValorCapturado.="0040".$diad."\n";
	if ($obj[5]=="H")
		$ValorCapturado.="0045".$horad."\n";
	else
		$horad="";
	$ValorCapturado.="0070".$usrtype."\n";
	$ValorCapturado.="0080".$item_data[5]."\n";
	$ValorCapturado.="0095".$item_data[0]."\n";                   // Control number of the object
	$ValorCapturado.="0098".$item_data[1]."\n";             			// Database name
	if ( $signatura!="") $ValorCapturado.="0090".$signatura."\n";
	$ValorCapturado.="0100".$objeto[0]."\n";
	$ValorCapturado.="0400".$ppres."\n";
	$ValorCapturado.="0120^a".$_SESSION["login"]."^b".date("Ymd H:i:s");
	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."crear_registro.xis";
	$Formato="";
	$recibo="";
	if (isset($recibo_list["pr_loan"])){
		if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/r_loan.pft")){
			$Formato=$db_path."trans/pfts/".$_SESSION["lang"]."/r_loan";
		}else{
			if (file_exists($db_path."trans/pfts/".$lang_db."/r_loan.pft")){
				$Formato=$db_path."trans/pfts/".$lang_db."/r_loan";
			}
		}
	}
	if ($Formato!="") {		$Formato="&Formato=$Formato";
		$Pft="mfn/";	}

	$query = "&base=trans&cipar=$db_path"."par/trans.par&login=".$_SESSION["login"]."$Formato&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");
	if ($mfn_reserva!=0){
		ActualizarReserva($mfn_reserva,$diap,$horap);
	}
    $recibo="";
	if ($Formato!="") {		foreach ($contenido as $r){			$recibo.=trim($r);		}		$recibo_arr[]=$recibo;
		//ImprimirRecibo($recibo);	}
	$fechas=array($diad,$horad);
	return $fechas;}


// Se localiza el número de control en la base de datos bibliográfica
function ReadCatalographicRecord($control_number,$db,$inventory){
global $db_path,$Wxis,$xWxis,$wxisUrl,$arrHttp,$pft_totalitems,$pft_ni,$pft_nc,$pft_typeofr,$titulo,$prefix_in,$prefix_cn,$multa,$pft_storobj,$lang_db;
	//Read the FDT of the database for extracting the prefix used for indexing the control number
//	echo $control_number;
	if (isset($arrHttp["db_inven"])){		$dbi=explode("|",$arrHttp["db_inven"]);
	}else{		$dbi[0]="loanobjects";
	}
	if (isset($arrHttp["db_inven"]) and $dbi[0]!="loanobjects"){
		$Expresion=trim($prefix_cn).trim($control_number);
	}else{
	    $Expresion="CN_".trim($control_number);
	}
	if ($control_number=="")
		$Expresion=$prefix_in.$inventory;
//    echo $Expresion;
	// Se extraen las variables necesarias para extraer la información del título al cual pertenece el ejemplar
	// se toman de databases_configure_read.php
	// pft_totalitems= pft para extraer el número total de ejemplares del título
	// pft_in= pft para extraer el número de inventario
	// pft_nc= pft para extraer el número de clasificación
	// pft_typeofr= pft para extraer el tipo de registro

	$formato_ex="'||'".$pft_nc."'||'".$pft_typeofr."'###',";
	//se ubica el título en la base de datos de objetos de préstamo
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj=$db_path."$db/loans/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path.$db. "/loans/".$lang_db."/loans_display.pft";
	$formato_obj.=", /".urlencode($formato_ex).urlencode($pft_storobj);

	$query = "&Opcion=disponibilidad&base=". strtolower($db)."&cipar=$db_path"."par/$db.par&Expresion=".$Expresion."&Pft=@$formato_obj";
	include("../common/wxis_llamar.php");
	$total=0;
	$titulo="";
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if (trim($linea)!=""){
			if (substr($linea,0,8)=='$$TOTAL:')
				$total=trim(substr($linea,8));
			else
				$titulo.=$linea."\n";
		}
	}
	return $total;
}

// Se localiza el número de inventario en la base de datos de objetos  de préstamo
function LocalizarInventario($inventory){
global $db_path,$Wxis,$xWxis,$wxisUrl,$arrHttp,$pft_totalitems,$pft_ni,$pft_nc,$pft_typeofr,$copies_title,$prefix_in,$multa;

    $Expresion=$prefix_in.$inventory;
	// Se extraen las variables necesarias para extraer la información del título al cual pertenece el ejemplar
	// se toman de databases_configure_read.php
	// pft_totalitems= pft para extraer el número total de ejemplares del título
	// pft_in= pft para extraer el número de inventario
	// pft_nc= pft para extraer el número de clasificación
	// pft_typeofr= pft para extraer el tipo de registro


	//READ LOANOBJECT DATABASE TO GET THE RECORD WITH THE ITEMS OF THE TITLE
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";

	$Expresion=urlencode($Expresion);
	if (isset($arrHttp["db_inven"])){		$dbi=explode('|',$arrHttp["db_inven"]);
		$dbi_base=$dbi[0];	}
	if (isset($arrHttp["db_inven"]) and $dbi_base!="loanobjects"){
	//IF NO LOANOBJECTS READ THE PFT FOR EXTRACTING THEN INVENTORY NUMBER AND THE TYPE OF RECORD		$d=explode('|',$arrHttp["db_inven"]);
		$arrHttp["base"]=$d[0];
		$arrHttp["db_inven"]=$d[0];
		$pft_typeofrec=LeerPft("loans_typeofobject.pft",$d[0]);
		$pft_typeofrec=str_replace("/"," ",$pft_typeofrec);
		$pft_typeofrec=trim($pft_typeofrec);
		if (substr($pft_typeofrec,0,1)=="(")
			$pft_typeofrec=substr($pft_typeofrec,1);
        if (substr($pft_typeofrec,strlen($pft_typeofrec)-1,1)==")")
			$pft_typeofrec=substr($pft_typeofrec,0,strlen($pft_typeofrec)-1);
		$pft_ni=LeerPft("loans_inventorynumber.pft",$d[0]);
		$pft_ni=str_replace("/"," ",$pft_ni);
		$pft_nc=LeerPft("loans_cn.pft",$d[0]);
		$pft_nc=str_replace("/"," ",$pft_nc);
		$formato_ex="$pft_nc,('||".$d[0]."||',$pft_ni,'||||||',".$pft_typeofrec.",'||||||'/)";
	}else{		$arrHttp["base"]="loanobjects";
		$formato_ex="(v1[1]'||'v10[1],'||',v959^i,'||',v959^l,'||',v959^b,'||',v959^o,'||',v959^v,'||',v959^t,'||'/)";
    // control number||database||inventory||main||branch||type||volume||tome	}

	$formato_obj=urlencode($formato_ex);

	$query = "&Opcion=disponibilidad&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par&Expresion=".$Expresion."&Pft=$formato_obj";
	include("../common/wxis_llamar.php");
	$total=0;
	$copies_title=array();
	$item="";
    $cno="";
    $tto="";
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if ($linea!=""){
			if (substr($linea,0,8)=='$$TOTAL:'){
				$total=trim(substr($linea,8));
			}else{
				$t=explode('||',$linea);
				if ($t[0]!="" ) $cno=$t[0];
				if ($t[5]!="")  $tto=$t[5];
				if ($t[0]=="" ) $t[0]=$cno;
				if ($t[5]=="")  $t[5]=$tto;
				$linea="";
				foreach ($t as $value){					$linea.=trim($value)."||";				}
				if (strtoupper($inventory)==strtoupper(trim($t[2]))) $item=$linea;
				$copies_title[]=$linea;
			}
		}
	}
	$ret=array($total,$item);
	return $ret ;
}

//se busca el numero de control en el archivo de transacciones para ver si el usuario tiene otro ejemplar prestado
function LocalizarTransacciones($control_number,$prefijo,$base_origen){
global $db_path,$Wxis,$xWxis,$wxisUrl,$arrHttp,$msgstr;
	$tr_prestamos=array();
	$formato_obj=$db_path."trans/pfts/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path."trans/pfts/".$lang_db."/loans_display.pft";
	$query = "&Expresion=".$prefijo."_P_".$control_number."&base=trans&cipar=$db_path"."par/trans.par&Formato=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$prestamos=array();
	foreach ($contenido as $linea){
		if (trim($linea)!=""){
			$l=explode('^',$linea);
			if (isset($l[13])){
				if ($base_origen==$l[13])
					$tr_prestamos[]=$linea;
			}else{				$tr_prestamos[]=$linea;			}        }
	}
	return $tr_prestamos;
}



///////////////////////////////////////////////////////////////////////////////////////////


//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";

// ARE THE COPIES IN THE COPIES DATABASE OR IN THE BIBLIOGRAPHIC DATABASE?

if (isset($arrHttp["db_inven"])){	$dbi=explode('|',$arrHttp["db_inven"]);
	if ($dbi[0]!="loanobjects"){
		$from_copies="N";
		$x=explode('|',$arrHttp["db_inven"]);
    	$var=LeerPft("loans_conf.tab",$x[0]);
		$prefix_in=trim($x[2]);
	}else{
		$prefix_in="IN_";
		$from_copies="Y";
	}
}else{	$prefix_in="IN_";
	$from_copies="Y";}
if (isset($arrHttp["Opcion"])){
	if ( $arrHttp["Opcion"]=="reservar")
		$msg_1=$msgstr["reserve"];
	else
		if ($arrHttp["Opcion"]=="prestar") $msg_1=$msgstr["loan"];
}

// ------------------------------------------------------
//--------------------------------------------------------------

$link_u="";
if (isset($arrHttp["usuario"])) $link_u="&usuario=".$arrHttp["usuario"];
if (isset($arrHttp["inventory"])) $presentar_reservas="N";
$nmulta=0;
$cont="";
$np=0;
$nv=0;
include("ec_include.php");  //se incluye el procedimiento para leer el usuario y los préstamos pendientes
if ($sanctions_output!="") {	$cont="N";
	unset($arrHttp["inventory"]);}
if (count($prestamos)>0) $ec_output.= "<strong><a href=javascript:DevolverRenovar('D')>".$msgstr["return"]."</a> | <a href=javascript:DevolverRenovar('R')>".$msgstr["renew"]."</a></strong><p>";

//Se obtiene el código, tipo y vigencia del usuario
$formato=$pft_uskey.'\'$$\''.$pft_ustype.'\'$$\''.$pft_usvig;
$formato=urlencode($formato);
$query = "&Expresion=".trim($uskey).$arrHttp["usuario"]."&base=users&cipar=$db_path"."par/users.par&Pft=$formato";
$contenido="";
$IsisScript=$xWxis."cipres_usuario.xis";
include("../common/wxis_llamar.php");
$user="";
$msgsusp="";
$vig="";

foreach ($contenido as $linea){	$linea=trim($linea);
	if ($linea!="")  $user.=$linea;
}

if (trim($user)==""){	ProduceOutput("<h4>".$msgstr["userne"]."</h4>","");
	die;
}else{

	$reserves_user=ReservesRead("CU_".$arrHttp["usuario"]);
	if ($nsusp>0 or $nmulta>0) {		 $msgsusp= "pending_sanctions";
		 $vig="N";	}else{	//Se analiza la vigencia del usuario
		$userdata=explode('$$',$user);
	    if (trim($userdata[2])!=""){	    	if ($userdata[2]<date("Ymd")){	    		$msgsusp= "limituserdata";
				$vig="N";	    	}    	}
    }}
$ec_output.= "\n
<script>
  Vigencia='$vig'
  np=$np
  nv=$nv
</script>\n";
if ($msgsusp!=""){	$ec_output.="<font color=red><h3>**".$msgstr[$msgsusp]."</h3></font>";
	if ($reserves_user!="")
		$ec_output.="<p><strong>".$msgstr["reserves"]."</strong><br>".$reserves_user."<p>";
	ProduceOutput($ec_output,"");
	die;}
//OJO AGREGARLE AL TIPO DE USUARIO SI SE LE PUEDEN PRESTAR CUANDO ESTÁ VENCIDO
if ($nv>0 and isset($arrHttp["inventory"])){
	$ec_output.= "<font color=red><h3>".$msgstr["useroverdued"]."</h3></font>";
	ProduceOutput($ec_output,"");
	die;
}
//////////////////////////////////////////////////////////////////
// Si viene desde la opción de prestar, se localiza el número de inventario solicitado

$xnum_p=$np;
$prestamos_este=0;

if (isset($arrHttp["inventory"]) and $vig=="" and !isset($arrHttp["prestado"]) and !isset($arrHttp["renovado"]) and !isset($arrHttp["devuelto"])){

	$ec_output.="<table width=100% bgcolor=#cccccc><td></td>
		<td width=50 align=center><strong>".$msgstr["inventory"]."</strong></td><td width=50 align=center><strong>".$msgstr["control_n"]."<strong></td><td align=center><strong>".$msgstr["reference"]."<strong></td><td align=center><strong>".$msgstr["typeofitems"]."</strong></td><td align=center><strong>".$msgstr["devdate"]."</td>\n";

    $invent=explode("\n",trim(urldecode($arrHttp["inventory"])));
    $primera_vez="";
    foreach ($invent as $arrHttp["inventory"]){
    	$arrHttp["inventory"]=trim($arrHttp["inventory"]);
    	if ($arrHttp["inventory"]=="") continue;
    	$cont="Y";
    	if (isset($inventory_numeric) and $inventory_numeric =="Y"){
    		$i=0;
    		while (substr($arrHttp["inventory"],$i,1)=="0"){
    			$i++;
    			$arrHttp["inventory"]=substr($arrHttp["inventory"],$i,1);
    		}
    	}
    	$arrHttp["inventory"]=trim($arrHttp["inventory"]);
    	$ec_output.="<tr>";
    	$este_prestamo="";

    	$este_prestamo.= "<td bgcolor=white valign=top align=center><font color=red>".$arrHttp["inventory"]."</font></td>";
	//Se ubica el ejemplar en la base de datos de objeto
		$res=LocalizarInventario($arrHttp["inventory"]);

		$total=$res[0];
		$item=$res[1];

		if ($total==0){
			$este_prestamo.= "<td bgcolor=white valign=top></td><td bgcolor=white></td><td  bgcolor=white valign=top></td><td bgcolor=white valign=top></td><td  bgcolor=white valign=top><font color=red>".$msgstr["copynoexists"]."</font></td>";
			$cont="N";
			$ec_output.="<td bgcolor=white></td>".$este_prestamo;
 			//ProduceOutput($ec_output,"");
		}else{
		//se extrae la información del número de control del título y la base de datos catalográfica a la cual pertenece
			$tt=explode('||',$item);
			$control_number=$tt[0];

			$catalog_db=$tt[1];
    		$tipo_obj=trim($tt[5]);      //Tipo de objeto

// se lee la configuración de la base de datos de objetos de préstamos
			$arrHttp["db"]="$catalog_db";
			$este_prestamo.="<td bgcolor=white valign=top align=center>$control_number  ($catalog_db)</td>";
            require_once("databases_configure_read.php");
			$ppres="";
    		$tipo_obj=trim(strtoupper($tipo_obj));
    		$userdata[1]=trim(strtoupper($userdata[1]));

			if (isset($politica[$tipo_obj][$userdata[1]])){	    		$ppres=$politica[$tipo_obj][$userdata[1]];
	    		$using_pol=$tipo_obj." - " .$userdata[1];
			}
			if (trim($ppres)==""){				if (isset($politica[0][$userdata[1]])) {					$ppres=$politica[0][$userdata[1]];
					$using_pol="0 - " .$userdata[1];				}
			}
			if (trim($ppres)==""){
				if (isset($politica[$tipo_obj][0])){
	    			$ppres=$politica[$tipo_obj][0];
	    			$using_pol=$tipo_obj." - 0" ;
	  			}
			}
			if (trim($ppres)==""){
				if (isset($politica["0"]["0"])){
					$ppres=$politica["0"]["0"];
					$using_pol="0 - 0";
				}
			}
			$obj=explode('|',$ppres);
			$fechal_usuario="";
			$fechal_objeto="";
			if (!isset($obj[2]))
			    $total_prestamos_politica=0;
			else
				$total_prestamos_politica=$obj[2];
			if (trim($total_prestamos_politica)=="") $total_prestamos_politica=99999;
			if (isset($obj[15])){
				$fechal_usuario=$obj[15];
				$fecha_d=date("Ymd");
				if (trim($fechal_usuario)!=""){
					if ($fecha_d>$fechal_usuario){						$este_prestamo.= "fecha límite del usuario ";
						$norenovar="S";
						$cont="N";
						//die;					}
				}
			}
			if (isset($obj[15])){				$fechal_objeto=$obj[16];
				if (trim($fechal_objeto)!=""){
					if ($fecha_d>$fechal_objeto){
						$este_prestamo.= "fecha límite del objeto ";
						$cont="N";
						$este_prestamo.="<hr>";
					}
				}
			}
			//SE VERIFICA SI EL USUARIO TIENE PRÉSTAMOS VENCIDOS
            if ($nv>0 and isset($arrHttp["inventory"]) and $obj[12]!="Y"){
				$este_prestamo.= "<font color=red><h3>".$msgstr["useroverdued"]."</h3></font>";
				$cont="N";
			}
			//Se verifica si el usuario puede recibir más préstamos en total
			//SE ASIGNA EL TOTAL DE PRESTAMOS QUE PUEDE RECIBIR UN USUARIO  SEGUN EL TIPO DE OBJETO  (calculado en loanobjects_read.php)
			if (isset($tipo_u[$userdata[1]]))
				$tprestamos_p=$tipo_u[$userdata[1]];
			else
				$tprestamos_p=99999;
			if (trim($tprestamos_p)=="")    $tprestamos_p=99999;
			if ($cont=="Y"){
		// Se localiza el registro catalográfico utilizando los datos anteriores
				$ref_cat=ReadCatalographicRecord($control_number,$catalog_db,$arrHttp["inventory"]);
	 			if ($ref_cat==0){      //The catalographic record is not found
	 				$este_prestamo.= "<td  bgcolor=white valign=top></td><td  bgcolor=white valign=top></td><td  bgcolor=white valign=top><font color=red>".$msgstr["catalognotfound"]." ($catalog_db)</font></td>";
					$cont="N";
	 			}
	 			if ($ref_cat>1){      //More than one catalographic record
	 				$este_prestamo.= "<td  bgcolor=white valign=top></td><td  bgcolor=white valign=top></td><td  bgcolor=white valign=top><font color=red>".$msgstr["dupcopies"]." ($catalog_db)</font></td>";
					$cont="N";
	 			}
	 			if ($cont=="Y"){
		 			$tt=explode('###',trim($titulo));
		    		$obj_store=$tt[1];
					$tt=explode('||',$tt[0]);
					$titulo=$tt[0];
					$signatura=$tt[1];     //signatura topográfica
		    		$este_prestamo.= "<td bgcolor=white valign=top>$titulo</td>";
		    		$este_prestamo.= "<td bgcolor=white valign=top>";
		    		if (trim($ppres)==""){
						//$debug="Y";
						$este_prestamo.=$msgstr["nopolicy"]." ".$tipo_obj."-".$userdata[1]."<td bgcolor=white></td>";
                        $grabar="N";
					}else{
						$este_prestamo.= $msgstr["policy"].": ". $using_pol;
						$grabar="Y";
					}
					$este_prestamo.="</td>";
	// se verifica si el ejemplar está prestado
					$tr_prestamos=LocalizarTransacciones($arrHttp["inventory"],"TR",$catalog_db);
					$Opcion="";
					$msg="";
					$msg_1="";
					if (count($tr_prestamos)>0){   // Si ya existe una transacción de préstamo para ese número de inventario, el ejemplar está prestado
						$cont="N";
						$msg= $msgstr["itemloaned"];
						$este_prestamo.="<td valign=top bgcolor=white><font color=red>".$msg."</font></td><td bgcolor=white></td>";
	        		}
					//SE VERIFICA SI EL USUARIO YA TIENE UN MISMO EJEMPLAR, VOLUMEN Y TOMO DE ESE TÍTULO Y SI SE LE PERMITE O NO
					$var=PrestamoMismoObjeto($control_number,$arrHttp["usuario"],$catalog_db);
					$msg=$var[0];
					$items_prestados=$var[1];
					if ($msg!=""){
	        			$cont="N";
	        			$este_prestamo.="<td valign=top bgcolor=white><font color=red>".$msg."</font></td><td bgcolor=white></td>";
	        		}
	        		if ($cont=="Y"){
	        			$msg="";
	        			$ec_output.="<td bgcolor=white valign=top>";	        			if ($grabar=="Y"){
	        				//SE LOCALIZA SI EL TITULO ESTÁ RESERVADO
	        				$reservado=LocalizarReservas($control_number,$catalog_db,$arrHttp["usuario"],$items_prestados,$prefix_cn,$from_copies,$pft_ni);
	        				$mfn_reserva=$reservado[1];
	        				//echo "mfnreserva: ".$mfn_reserva;
	        				if (!isset($total_politica[$tipo_obj])) $total_politica[$tipo_obj]=0;
	        				if ($reservado[0]=="continuar"){
	        					//echo  "<p>np:".$np. " total_prestamos_usuario: $tprestamos_p total_prestamos_politica: ". $total_prestamos_politica ."  total_politica[$tipo_obj]: ". $total_politica[$tipo_obj]."<br>";
	        					if ($np<$tprestamos_p and $total_politica[$tipo_obj]< $total_prestamos_politica ){
	        						$total_politica[$tipo_obj]=$total_politica[$tipo_obj]+1;
	        						$np=$np+1;
	        						$xnum_p=$xnum_p+1;
	        						$prestamos_este=$prestamos_este+1;
									$ec_output.="$xnum_p. <input type=checkbox name=chkPr_".$xnum_p." value=0  id='".$arrHttp["inventory"]."'>";
	  								$ec_output.= "<input type=hidden name=politica value=\"".$ppres."\"> \n";
	  							}else{
	  								$grabar="N";
	  								$msg="<font color=red>".$msgstr["nomoreloans"]."</font>";
	  							}
	  						}else{
	  							$grabar="N";
	  							$msg="<font color=red><a href='javascript:ShowReservations(\"CN_".$catalog_db."_"."$control_number\")'>".$msgstr["reserved_other_user"]."</a></font>";
	  						}

  						}
						$ec_output.="</td>";
						$ec_output.=$este_prestamo;
						$Opcion="prestar";
				//	$action="usuario_prestamos_prestar.php";
						$msg_1=$msgstr["loan"];
						if ($grabar=="Y"){							$devolucion=ProcesarPrestamo($arrHttp["usuario"],$arrHttp["inventory"],$signatura,$item,$userdata[1],$from_copies,$ppres,$prefix_in,$prefix_cn,$mfn_reserva);

							if ($mfn_reserva!=0){
								$reserves_user=ReservesRead("CU_".$arrHttp["usuario"]);							}
						}else{							$devolucion=array();
						}
						$ec_output.="<td bgcolor=white valign=top >$msg";
						if (count($devolucion)>0) {
							if (substr($config_date_format,0,2)=="DD"){								$ec_output.=substr($devolucion[0],6,2)."/".substr($devolucion[0],4,2)."/".substr($devolucion[0],0,4);							}else{								$ec_output.=substr($devolucion[0],4,2)."/".substr($devolucion[0],6,2)."/".substr($devolucion[0],0,4);							}
							$ec_output.=" ".$devolucion[1];
						}
						$ec_output.="</td><td bgcolor=white valign=top ></td> ";
	           		}else{	           			$ec_output.="<td bgcolor=white></td>".$este_prestamo;	           		}
				} else{					$ec_output.="<td bgcolor=white></td>".$este_prestamo;				}
			}else{				$ec_output.="<td bgcolor=white></td>".$este_prestamo;			}
		}
	}
	$ec_output.="</table>";


}

if ($prestamos_este>0) $ec_output.= "<strong><a href=javascript:DevolverRenovar('D')>".$msgstr["return"]."</a></strong>\n";
if ($reserves_user!="")
	$ec_output.="<p><strong>".$msgstr["reserves"]." <font color=red>(user)</font></strong><br>".$reserves_user."<p>";
ProduceOutput($ec_output,"");

function ProduceOutput($ec_output,$reservas){global $msgstr,$arrHttp,$signatura,$msg_1,$cont,$institution_name,$lang_db,$copies_title,$link_u,$recibo_arr,$db_path,$Wxis,$xWxis;global $prestamos_este,$xnum_p;
	include("../common/header.php");    echo "<body>";
 	include("../common/institutional_info.php");
// 	if ($recibo!=""){// 		$recibo="&recibo=$recibo";
// 		$link_u.=$recibo;// 	}
?>
<script  src="../dataentry/js/lr_trim.js"></script>
<script>
document.onkeypress =
  function (evt) {
    var c = document.layers ? evt.which
            : document.all ? event.keyCode
            : evt.keyCode;
	if (c==13)
		self.location.href='prestar.php?encabezado=s<?php echo $link_u;?>'
    return true;
  };

function ShowReservations(CN,BD){	msgwin=window.open("../reserve/show_reservations.php?submenu=N&key="+CN+"&bases="+BD,"reservations","width=1000,height=400,resizable,scrollbars")
	msgwin.focus()}

function Reservar(usuario){	if (nMultas>0 || nv>0 || nSusp>0){		alert("<?php echo $msgstr["reservations_not_allowed"]?>")
		return	}
	ix=document.ecta.bd.selectedIndex
	if (ix>0){
		base=document.ecta.bd.options[ix].value
		document.busqueda.base.value=base
		document.busqueda.cipar.value=base+".par"
		document.busqueda.submit()
	} else{
		alert("<?php echo $msgstr["seldb"]?>")
		return
	}	document.busqueda.submit()}
function EvaluarRenovacion(p,atraso,fecha_d,nMultas,item){
	if (p[6]==0 && Trim(p[6]!="")){     // the object does not accept renovations
		alert(item+". <?php echo $msgstr["noitrenew"] ?>")
		return false
	}

	if (atraso!=0){
		if (p[13]!="Y"){
			alert(item+". <?php echo $msgstr["loanoverdued"]?>")
			return false
		}
	}
	if (Trim(p[15])!=""){
		if (fecha_d>p[15]){
			alert(item+". <?php echo $msgstr["limituserdata"]?>"+": "+p[15])
			return false
		}
	}
	if (Trim(p[16])!=""){
		if (fecha_d>p[16]){
			alert(item+". <?php echo $msgstr["limitobjectdata"]?>"+": "+p[16])
			return false
		}
	}
	if (nMultas!=0){
		alert(item+". <?php echo $msgstr["norenew"]?>")
		return false
	}
	return true}

function DevolverRenovar(Proceso) {	if (Proceso=="D"){
		document.devolver.action="devolver_ex.php"
	}else{
		if (Vigencia=="N"){
			alert("<?php echo $msgstr["norenew"]?>");
			return
		}
		document.devolver.action="renovar_ex.php"
	}
	marca="N"
	search=""
	atraso=""
	politica=""
	switch (np){     // número de préstamos del usuario
		case 1:
			if (document.ecta.chkPr_1.checked){
				search=document.ecta.chkPr_1.id
				atraso=document.ecta.chkPr_1.value
				politica=document.ecta.politica.value
				fecha_d="<?php echo date("Ymd")?>"
				p=politica.split('|')
				if (Proceso=="R"){
					res=EvaluarRenovacion(p,atraso,fecha_d,nMultas,1)
					if (res)
						marca="S"
					else
						marca="N"
				}else{					marca="S"				}
			}
			break
		default:
			for (i=1;i<=np;i++){				Ctrl=eval("document.ecta.chkPr_"+i)
				if (Ctrl.checked){
					marca="S"
					search+=Ctrl.id+"$$"
					atraso=Ctrl.value
					fecha_d="<?php echo date("Ymd")?>"
					politica=document.ecta.politica[i-1].value
					p=politica.split('|')
					if (Proceso=="R"){    // si es una renovación
						res=EvaluarRenovacion(p,atraso,fecha_d,nMultas,i)
						if (res)
							marca="S"
						else
							marca="N"
					}else{
						marca="S"
					}
				}  // FIN DE OPCION SELECIONADA
			} // FIN DE REVISAR TODAS LAS OPCIONES

		}// FIN DEL CASE
		if (marca=="S"){
			document.devolver.searchExpr.value=search
			document.devolver.submit()
		}else{
			alert("<?php echo $msgstr["markloan"]?>")
		}
}

function PagarMultas(Accion){
	Mfn=""
	switch (nMultas){
		case 1:
			if (document.ecta.pay.checked){
            	Mfn=document.ecta.pay.value
			}
			break
		default:
			for (i=0;i<nMultas;i++){
				if (document.ecta.pay[i].checked){
					Mfn+=document.ecta.pay[i].value+"|"
				}
			}
			break
	}
	if (Mfn==""){
		alert("<?php echo $msgstr["selfine"]?>")
		return
	}
	document.multas.Mfn.value=Mfn
	document.multas.Accion.value=Accion
	document.multas.submit()
}

function DeleteSuspentions(Accion){
	Mfn=""
	switch (nSusp){
		case 1:
			if (document.ecta.susp.checked){
            	Mfn=document.ecta.susp.value
			}
			break
		default:
			for (i=0;i<nSusp;i++){
				if (document.ecta.susp[i].checked){
					Mfn+=document.ecta.susp[i].value+"|"
				}
			}
			break
	}
	if (Mfn==""){
		alert("<?php echo $msgstr["selsusp"]?>")
		return
	}
	document.multas.Mfn.value=Mfn
	document.multas.Accion.value=Accion
	document.multas.submit()
}
</script>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["statment"]?>
	</div>
	<div class="actions">
		<?php include("submenu_prestamo.php");?>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<?php
echo "<a href=../documentacion/ayuda.php?help=". $_SESSION["lang"]."/circulation/loan.html target=_blank>". $msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/circulation/loan.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: usuarios_prestamos_presentar.php </font>
	</div>";
// prestar, reservar o renovar
?>
<div class="middle form">
	<div class="formContent">
<form name=ecta>
<?php
if ($xnum_p=="") $xnum_p=0;
$ec_output.= "</form>";
$ec_output.="<script>
		np=$xnum_p
		</script>\n";
$ec_output.= "<form name=devolver action=devolver_ex.php method=post>
<input type=hidden name=searchExpr>
<input type=hidden name=usuario value=".$arrHttp["usuario"].">
<input type=hidden name=vienede value=ecta>\n";
if (isset($arrHttp["reserve"])) $ec_output.= "<input type=hidden name=reserve value=".$arrHttp["reserve"].">\n";
$ec_output.= "</form>
<form name=multas action=multas_eliminar_ex.php method=post>
<input type=hidden name=Accion>
<input type=hidden name=usuario value=".$arrHttp["usuario"].">
<input type=hidden name=Mfn value=\"\">";
if (isset($arrHttp["reserve"])) $ec_output.= "<input type=hidden name=reserve value=".$arrHttp["reserve"].">\n";
$ec_output.= "</form>
<br>
";

echo $ec_output;
if ($reservas !=""){	echo "<P><font color=red><strong>".$msgstr["total_copies"].": ".count($copies_title).". ".$msgstr["item_reserved"]."</strong></font><br>";
	echo $reservas ;}
if (isset($arrHttp["prestado"]) and $arrHttp["prestado"]=="S"){
	if (isset($arrHttp["resultado"])){
		$inven=explode(';',$arrHttp["resultado"]);
		foreach ($inven as $inventario){			echo "<p><font color=red>". $inventario." ".$msgstr["item"].": ".$msgstr["loaned"]." </font>";
			if (isset($arrHttp["policy"])){				$p=explode('|',$arrHttp["policy"]);
				echo $msgstr["policy"].": " . $p[0] ." - ". $p[1];			}
		}
	}
}
if (isset($arrHttp["devuelto"]) and $arrHttp["devuelto"]=="S"){	if (isset($arrHttp["resultado"]) and isset($arrHttp["rec_dev"])){		$inven=explode(';',$arrHttp["rec_dev"]);
		foreach ($inven as $inventario){			if (trim($inventario)!=""){
				$Mfn=trim($inventario);
				echo "<p><font color=red>". $inventario." ".$msgstr["item"].": ".$msgstr["returned"]." </font>";
				$Formato="v10,' ',mdl,v100'<br>'";
				$Formato="&Pft=$Formato";
				$IsisScript=$xWxis."leer_mfnrange.xis";
				$query = "&base=trans&cipar=$db_path"."par/trans.par&from=$Mfn&to=$Mfn$Formato";
				include("../common/wxis_llamar.php");
				foreach ($contenido as $value){
					echo $value;
				}
			}
		}
	}
}

//SE VERIFICA SI ALGUNO DE LOS EJEMPLARES DEVUELTOS ESTÁ RESERVADO
if (isset($arrHttp["lista_control"])) {
	//include("../reserve/reserves_read.php");
	$rn=explode(";",$arrHttp["lista_control"]);
	$res=array();
	foreach ($rn as $value){
		if (trim($value)!=""){
			if (!isset($res[$value]))
				$res[$value]=1;
			else
				$res[$value]=$res[$value]+1;
		}
	}

	if (count($res)>0){
		$Expresion="";
		foreach ($res as $key=> $value){
			if ($Expresion==""){
				$Expresion=$key;
			}else{
				$Expresion.="+".$key;
			}
		}
		$reserves_title= ReservesRead($Expresion);
		if ($reserves_title!=""){
			echo "<p><hr><strong>".$msgstr["reserves"]." <font color=red>(title)</font></strong><br>";
			echo $reserves_title."<p>";
		}
	}
}


if (isset($arrHttp["renovado"]) and $arrHttp["renovado"]=="S"){	if (isset($arrHttp["resultado"])){		$inven=explode(';',$arrHttp["resultado"]);
		foreach ($inven as $inventario){
			if (trim($inventario)!="")
				echo "<p><font color=red>".$msgstr["item"]." ". $inventario." </font>";
		}	}
}else{}

//SE IMPRIMEN LOS RECIBOS DE PRÉSTAMOS
if (count($recibo_arr)>0) {
	ImprimirRecibo($recibo_arr);
}

//SE IMPRIMEN LOS RECIBOS DE DEVOLUCION
if (isset($arrHttp["rec_dev"])){	$Mfn_rec=$arrHttp["rec_dev"];
	$fs="r_return";	$r=explode(";",$Mfn_rec);
	$rec_salida=array();

	foreach ($r as $Mfn){
		if ($Mfn!=""){
			$Formato="";
			if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/$fs.pft")){
				$Formato=$db_path."trans/pfts/".$_SESSION["lang"]."/$fs";
			}else{
				if (file_exists($db_path."trans/pfts/".$lang_db."/$fs.pft")){
					$Formato=$db_path."trans/pfts/".$lang_db."/$fs";
				}
			}
			if ($Formato!="") {
                $Formato="&Formato=$Formato";
				$IsisScript=$xWxis."leer_mfnrange.xis";
				$query = "&base=trans&cipar=$db_path"."par/trans.par&from=$Mfn&to=$Mfn$Formato";
				include("../common/wxis_llamar.php");
				$recibo="";
				foreach ($contenido as $value){
					$recibo.=trim($value);
				}
				$rec_salida[]=$recibo;
			}
		}
	}
	if (count($rec_salida)>0) {
		ImprimirRecibo($rec_salida);
	}
}
?>
</div></div>
<?php include("../common/footer.php");?>
</body>
</html>

<?php
if (isset($arrHttp["error"])){
	echo "<script>
	alert('".$arrHttp["error"]."')
	</script>
	";
}
}  //END FUNCTION PRODUCEOUTPUT



function ImprimirRecibo($recibo_arr){	$salida="";
	foreach ($recibo_arr as $Recibo){		$salida=$salida.$Recibo;
	}
	$salida=str_replace('/','\/',$salida);
?>
<script>
	msgwin=window.open("","recibo","width=400, height=300, scrollbars, resizable")
	msgwin.document.write("<?php echo $salida?>")
	msgwin.focus()
	msgwin.print()
	msgwin.close()
</script>
<?php
}

?>
<form name=reservacion method=post action="../reserve/buscar.php">
<input type=hidden name=encabezado  value="s">
<input type=hidden name=usuario value=<?php echo $arrHttp["usuario"];?>>
<?php if (isset($arrHttp["reserve"])) echo "<input type=hidden name=reserve value=".$arrHttp["reserve"].">\n";?>
</form>
<form name=reservas method=post action=../reserve/delete_reserve.php>
<input type=hidden name=Mfn_reserve>
<input type=hidden name=Accion>
<input type=hidden name=usuario value=<?php echo $arrHttp["usuario"]?>>
<input type=hidden name=retorno value="../circulation/usuario_prestamos_presentar.php">
<?php if (isset($arrHttp["reserve"])) echo "<input type=hidden name=reserve value=".$arrHttp["reserve"].">\n";?>
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
<form name=busqueda action=../reserve/buscar.php method=post>
<input type=hidden name=base>
<input type=hidden name=desde value=reserva>
<input type=hidden name=count value=1>
<input type=hidden name=cipar>
<input type=hidden name=Opcion value=formab>
<input type=hidden name=copies value=<?php echo $copies ?>>
<input type=hidden name=usuario value=<?php echo $arrHttp["usuario"]?>>

</form>

