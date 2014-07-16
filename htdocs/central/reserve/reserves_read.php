<?php
function MostrarRegistroCatalografico($dbname,$CN){
global $msgstr,$arrHttp,$db_path,$xWxis,$tagisis,$Wxis,$wxisUrl,$lang_db;
	$pref_cn="";
	$archivo=$db_path.$dbname."/loans/".$_SESSION["lang"]."/loans_conf.tab";
	if (!file_exists($archivo)) $archivo=$db_path.$dbname."/loans/".$lang_db."/loans_conf.tab";
	$fp=file_exists($archivo);
	if ($fp){
		$fp=file($archivo);
		foreach ($fp as $value){
			$t=explode(" ",trim($value));
			if ($t[0]=="NC")
				$pref_cn=$t[1];
		}
	}
	if ($pref_cn=="") $pref_cn="CN_";
	$Expresion=$pref_cn.$CN;
	$formato_obj=$db_path.$dbname."/loans/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path.$dbname."/loans/".$lang_db."/loans_display.pft";
	$arrHttp["count"]="";
	$Formato=$formato_obj;
	$IsisScript=$xWxis."buscar_ingreso.xis";
	$query = "&base=$dbname&cipar=$db_path"."par/".$dbname.".par&Expresion=".$Expresion."&Formato=$formato_obj";
	include("../common/wxis_llamar.php");
	$salida="";
	foreach ($contenido as $value){
		if (substr($value,0,8)!="[TOTAL:]")
			$salida.=$value;
	}
	$salida="(".$CN.") ".$salida;
	return $salida;
}

function ColocarTitulos($base){
global $db_path,$lang_db;
	$salida= "\n<table bgcolor=#cccccc width=100%>\n";
	// se lee la tabla con los títulos de las columnas
	$archivo=$db_path.$base."/pfts/".$_SESSION["lang"]."/tit_reserve.tab";
	if (!file_exists($archivo)) $archivo=$db_path.$base."/pfts/".$lang_db."/tit_reserve.tab";
	if (file_exists($archivo)){
		$fp=file($archivo);
		foreach ($fp as $value){
			$value=trim($value);
			if (trim($value)!=""){
				$t=explode('|',$value);
				foreach ($t as $rot) $salida.= "<td><strong>$rot</strong></td>";
			}
		}
	}
	$salida.= "<td class=\"action\" bgcolor=white>&nbsp;</td></tr>\n";
	return $salida;
}

function ReservesRead($usuario){
global $xWxis,$Wxis,$db_path,$msgstr,$arrHttp,$reservas_u_cn;
	$Expresion=$usuario." and ST_0";
	$IsisScript=$xWxis."cipres_usuario.xis";
	$Formato=$db_path."reserve/pfts/".$_SESSION["lang"]."/tbreserve.pft";
	$query="&base=reserve&cipar=$db_path"."par/reserve.par&Expresion=$Expresion&Formato=$Formato";
	include("../common/wxis_llamar.php");
	$num_reservas=0;
	$reservas_u="";
	$cuenta=0;
	$reservas_u_cn="";
	foreach ($contenido as $value) {		$value=trim($value);
		if (trim($value)!=""){
			$r=explode('|',$value);			if (trim($r[13])=="" ){
				$cuenta=$cuenta+1;
				if ($cuenta==1){
		    		$reservas_u=ColocarTitulos("reserve");
				}
				if (trim($r[12])=="")
					$num_reservas=$num_reservas+1;
				$reservas_u.= "<tr>\n";
				$reservas_u.= "<td  bgcolor=white valign=top width=80>".$r[4]."</td>"; //codigo usuario
				$reservas_u.= "<td  bgcolor=white valign=top width=20>".$r[5]."</td>"; //tipo usuario
				$reservas_u.= "<td  bgcolor=white valign=top width=100>".$r[6]."</td>";//nombre
				$reservas_u.= "<td  bgcolor=white valign=top width=50>".$r[7]."</td>"; //base de datos
				$reservas_u.= "<td  bgcolor=white valign=top width=50>".$r[8]."</td>"; //número de control
				$reservas_u.="<td  bgcolor=white valign=top width=300> ";              //referencia
		        $reservas_u.=MostrarRegistroCatalografico($r[7],$r[8]);
		        $reservas_u.="</td>";
			    $reservas_u.="<td  bgcolor=white valign=top>"; //fecha reserva
			    $reservas_u.= "<td  bgcolor=white valign=top>".$r[10]."</td>"; //hora reserva
			    $reservas_u.= "<td  bgcolor=white valign=top>".$r[11]."</td>"; //operador
			    $reservas_u.= "<td  bgcolor=white valign=top>";
			    $reservas_u.=substr($r[12],6,2)."-".substr($r[12],4,2)."-".substr($r[12],0,4)."</td>";  //fecha asignación
			    $reservas_u.= "<td  bgcolor=white valign=top>".$r[13]."</td>"; //Fecha cancelación
			    $reservas_u.= "<td  bgcolor=white valign=top>".$r[14]."</td>"; //Fecha de préstamo
			    $reservas_u.="<td  bgcolor=white valign=top nowrap width=50>";

			    if (isset($r[12]) and trim($r[12])=="" or isset($r[13]) and trim($r[13])==""){
			    	$reservas_u.= "<a href=javascript:DeleteReserve(".$r[0].")><img src=../dataentry/img/toolbarDelete.png alt='".$msgstr["delete"]."' title='".$msgstr["delete"]."'></a>";
			    	$reservas_u.="&nbsp;<a href=javascript:CancelReserve(".$r[0].")><img src=../dataentry/img/toolbarCancelEdit.png alt='".$msgstr["cancel"]."' title='".$msgstr["cancel"]."'></a>";
			 		//if (isset($arrHttp["lista_control"])) $reservas_u.= "<BR>".$r[0]." Asignar";
			  }else{			    	$reservas_u.=$msgstr["reserve_canceled"];			    }
			  	$reservas_u.= "</td>\n";
			  	$reservas_u_cn.="|".$r[7].$r[8]." ".$r[4];
			}
		}
	}
	if ($reservas_u!="")  $reservas_u.="</table>\n";
	return $reservas_u;
}

?>
