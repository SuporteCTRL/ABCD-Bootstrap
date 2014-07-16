<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      devolver_ex.php
 * @desc:      Returns a loan
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
// se determina si el préstamo está vencido
function compareDate ($FechaP){
global $locales;
	$dia=substr($FechaP,6,2);
	$mes=substr($FechaP,4,2);
	$year=substr($FechaP,0,4);
	$exp_date=$year."-".$mes."-".$dia;
	$todays_date = date("Y-m-d");
	$today = strtotime($todays_date);
	$expiration_date = strtotime($exp_date);
	$diff=$expiration_date-$today;
	return $diff;

}//end Compare Date

session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/get_post.php");
include("../config.php");
$lang=$_SESSION["lang"];
include("../lang/admin.php");
include("../lang/prestamo.php");
date_default_timezone_set('UTC');
foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
//die;

//Calendario de días feriados
include("../circulation/calendario_read.php");
//Horario de la biblioteca, unidades de multa, moneda
include("../circulation/locales_read.php");
include ("../circulation/fecha_de_devolucion.php");   //Para calcular si la reserva está vencida
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
if ($Formato!=""){
	$fp=file($Formato);
	foreach ($fp as $value){
		if (trim($value)!=""){
			$value=trim($value);
			$recibo_list[$value]=$value;
		}
	}
}


function AsignarReserva($Mfn,$asignacion,$anulacion){
global $db_path,$Wxis,$xWxis,$arrHttp;
	$ValorCapturado="00013\n";
	$ValorCapturado.="0040$anulacion\n";
	$ValorCapturado.="0060$asignacion\n";
	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."actualizar_registro.xis";
	$Formato="";
	$query = "&base=reserve&cipar=$db_path"."par/reserve.par&login=".$_SESSION["login"]."&Mfn=".$Mfn."&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");
	//var_dump($contenido);
}

function CancelReserve($Mfn){
global $db_path,$Wxis,$xWxis,$arrHttp;
	$fecha_dev=date("Ymd");
	$hora_dev=date("H:i:s");
	$ValorCapturado="00011\n";
	$ValorCapturado.="0130$fecha_dev\n";
	$ValorCapturado.="0131$hora_dev\n";
	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."actualizar_registro.xis";
	$Formato="";
	$query = "&base=reserve&cipar=$db_path"."par/reserve.par&login=".$_SESSION["login"]."&Mfn=".$Mfn."&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");
}

function ReservesAssign($key,$espera){
global $xWxis,$Wxis,$db_path,$msgstr,$arrHttp,$reservas_u_cn;
	$Expresion=$key." and ST_0";
	$IsisScript=$xWxis."cipres_usuario.xis";
	$Pft="f(mfn,1,0),'|',v10,'|'v60,'|',v40,'|',v130,'|',v200/";
	                                               //v10:  Código del usuario
	                                               //v60:  Fecha en la cual se asignó el objeto al usuario de la reserva
	                                               //v40:  Fecha hasta la cual es válida la reserve
	                                               //v130: Fecha en que el operador canceló una reserva
	                                               //v200: Fecha en la cual se dio el objeto en prestamo
	$query="&base=reserve&cipar=$db_path"."par/reserve.par&Expresion=$Expresion&Pft=$Pft";
	include("../common/wxis_llamar.php");
	$num_reservas=0;
	$reservas_u="";
	$cuenta=0;
	$reservas_u_cn="";
	$Usuario="";
	foreach ($contenido as $value) {
		$value=trim($value);
		//echo "$value<br>";
		//die;
		if (trim($value)!=""){			$r=explode('|',$value);
			$fecha_asignacion=$r[2];     //fecha en la cual se asignó el objeto a un usuario de reserva
			$fecha_cancelacion=$r[4];    //fecha en la cual un operador anuló una reserva
			$fecha_prestamo=$r[5];
			$Mfn=$r[0];
			$Usuario=$r[1];
			if ($fecha_asignacion!="" or $fecha_cancelacion!="" or $fecha_prestamo!=""){				continue;			}
            $f_asignacion=date('Ymd');

            $lapso=$espera;
            $unidad="D";           	$fecha_anulacion=FechaDevolucion($lapso,$unidad,$f_asignacion);
           	$fecha_anulacion=substr($fecha_anulacion,0,8);
           	AsignarReserva($Mfn,$f_asignacion,$fecha_anulacion);            return $Usuario;
		}
	}
	return $Usuario;
}

//Calculo de la sanción por atraso
include("sanctions_inc.php");

///////////
if (isset($arrHttp["vienede"])){   // viene del estado de cuenta	$items=explode('$$',trim(urldecode($arrHttp["searchExpr"])));}else{
	$items=explode("\n",trim(urldecode($arrHttp["searchExpr"])));
}
$resultado="";
$recibo="";
$Mfn_rec="";
$errores="";
$devueltos="";
$cn_l="";
$reservas_activadas="";
foreach ($items as $num_inv){
//Se ubica el ejemplar prestado en la base de datos de transacciones
	$num_inv=trim($num_inv);
	$inven=$num_inv;
	if ($num_inv!=""){
		$num_inv="TR_P_".$num_inv;
		if (!isset($arrHttp["base"])) $arrHttp["base"]="trans";
		$Formato="v10'|$'v20'|$'v30'|$'v35'|$'v40'|$'v45'|$'v70'|$'v80'|$'v100,'|$',v40,'|$'v400,'|$'v500,'|$',v95,'|$',v98/";
		$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&count=1&Expresion=".$num_inv."&Pft=$Formato";
		$contenido="";
		$IsisScript=$xWxis."buscar_ingreso.xis";
		include("../common/wxis_llamar.php");
		$Total=0;
		foreach ($contenido as $linea){			$linea=trim($linea);
			if ($linea!="") {
				$l=explode('|$',$linea);
				if (substr($linea,0,6)=="[MFN:]"){					$Mfn=trim(substr($linea,6));				}else{					if (substr($linea,0,8)=="[TOTAL:]"){						$Total=trim(substr($linea,8));					}else{						$prestamo=$linea;					}
				}
			}
		}
		$error="";
		if ($Total==0){
			$errores.=";".$inven;
		}
// se extrae la información del ejemplar a devolver
		if ($Total>0){
			$p=explode('|$',$prestamo);
			$cod_usuario=$p[1];
			$arrHttp["usuario"]=$cod_usuario;
			$inventario=$p[0];
			$fecha_p=$p[2];
			$hora_p=$p[3];
			$fecha_d=$p[9];   //fecha de devolución en formato ISO
			$hora_d=$p[5];
			$tipo_usuario=$p[6];
			$tipo_objeto=$p[7];
			$referencia=$p[8];
			$ppres=$p[10];
			$ncontrol=$p[12];
			$bd=$p[13];
			// se lee la política de préstamos
			include_once("loanobjects_read.php");
			// se lee el calendario
			include_once("calendario_read.php");
			// se lee la configuración local
			include_once("locales_read.php");

			//se determina la política a aplicar
			if ($ppres==""){				if (isset($politica[$tipo_objeto][$tipo_usuario])){
	    			$ppres=$politica[$tipo_objeto][$tipo_usuario];
				}
				if (trim($ppres)==""){
					if (isset($politica[0][$tipo_usuario])) {
						$ppres=$politica[0][$tipo_usuario];
					}
				}
				if (trim($ppres)==""){
					if (isset($politica[$tipo_usuario][0])){
	    				$ppres=$politica[$tipo_usuario][0];
	  				}
				}
				if (trim($ppres)==""){
					if (isset($politica["0"]["0"])){
						$ppres=$politica["0"]["0"];
					}
				}
			}
			//echo $ppres;
			$p=explode('|',$ppres);
			$espera_renovacion="";
			if (isset($p18))
				$espera_renovacion=$p[18];
			if (trim($espera_renovacion)=="") $espera_renovacion=2;
			$lapso=$p[3];
			$unidad=$p[5];
			$u_multa= $p[7];      //unidades de multa
			$u_multa_r= $p[8];    //unidades de multa si el libro está reservado
			$u_suspension=$p[9];  //unidades de suspensión
			$u_suspension=$p[10];  //unidades de suspensión si el libro está reservado
		    $devolucion=date("Ymd");
			$ValorCapturado="0001X\n0500$devolucion\n";
			$ValorCapturado.="0130^a".$_SESSION["login"]."^b".date("Ymd H:i:s");
			$ValorCapturado=urlencode($ValorCapturado);
			$IsisScript=$xWxis."actualizar_registro.xis";
			$Formato="";

			if (isset($recibo_list["pr_return"])){
				if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/r_return.pft")){
					$Formato=$db_path."trans/pfts/".$_SESSION["lang"]."/r_return";
				}else{
					if (file_exists($db_path."trans/pfts/".$lang_db."/r_return.pft")){
						$Formato=$db_path."trans/pfts/".$lang_db."/r_return";
					}
				}
				if ($Formato!="") {	                $Formato="&Formato=$Formato";
				}
			}
			$query = "&base=trans&cipar=$db_path"."par/trans.par&login=".$_SESSION["login"]."&Mfn=".$Mfn."&ValorCapturado=".$ValorCapturado."$Formato";
			include("../common/wxis_llamar.php");
            if ($Formato!=""){
            	$Mfn_rec.=";".$Mfn;
            }
            $resultado.=";".$Mfn;

			// si está atrasado se procesan las multas y suspensiones
			$atraso=compareDate ($fecha_d,$lapso);
			if ($politica==""){				$error="&error=".$msgstr["nopolicy"]." $tipo_usuario / $tipo_objeto";			}else{
				if ($atraso<0){
					$atraso=abs($atraso);
					Sanciones($fecha_d,$atraso,$arrHttp["usuario"],$inventario,$ppres);
					$resultado.=" ".$msgstr["overdue"];
				}
			}

			//SE LEEN LAS RESERVAS Y AL PRIMER USUARIO DE LA COLA  QUE NO TENGA
			//FECHA DE ASIGNACIÓN SE LE COLOCA LA FECHA DEL DÍA
			$cn_l.=";CN_".$bd."_".$ncontrol;
			$user_reserved=ReservesAssign("CN_".$bd."_".$ncontrol,$espera_renovacion);
			$reservas_activadas.=$user_reserved.";";

		}
	}
}
//die;
$cu="";
$recibo="";


if (isset($arrHttp["usuario"]))
	$cu="&usuario=".$arrHttp["usuario"];
else
	$cu="&usuario=$cod_usuario";
if (isset($arrHttp["reserve"])){
	$reserve="&reserve=\"S\"";
}else{
	$reserve="";
}
if (isset($arrHttp["vienede"]) or isset($arrHtp["reserve"])){
	header("Location: usuario_prestamos_presentar.php?devuelto=S&encabezado=s&resultado=".urlencode($resultado)."$cu&rec_dev=$Mfn_rec"."&inventario=".$arrHttp["searchExpr"]."&lista_control=".$cn_l.$reserve);}else{
	header("Location: devolver.php?devuelto=S&encabezado=s$error$cu&rec_dev=$Mfn_rec&resultado=$resultado&errores=$errores"."&lista_control=".$cn_l."&reservas=".$reservas_activadas);
}
die;

function ImprimirRecibo($recibo_arr){
	$salida="";
	foreach ($recibo_arr as $Recibo){
		$salida=$salida.$Recibo;
	}
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
?>