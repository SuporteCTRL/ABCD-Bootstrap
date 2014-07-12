<?PHP
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      pft_update.php
 * @desc:      Updates pft�s files
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
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
$lang=$_SESSION["lang"];
include("../common/get_post.php");
include("../config.php");

include("../lang/dbadmin.php");
include("../lang/admin.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
//die;
if (!isset($arrHttp["Modulo"])) $arrHttp["Modulo"]="";
include("../common/header.php");
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=S";
}else{
	$encabezado="";
}
if (!isset($arrHttp["Opcion"])) $arrHttp["Opcion"]="";
?>
<div class="sectionInfo">

			<div class="breadcrumb">
				<h5><?php echo $msgstr["pft"]." " .$msgstr["database"]?>: <?php echo $arrHttp["base"]?></h5>
			</div>

			<div class="actions">
<?php if ($arrHttp["Opcion"]=="new"){
				echo "<a href=\"../common/inicio.php?reinicio=s\" class=\"defaultButton backButton\">";
	}else{
		if ($arrHttp["Modulo"]=="dataentry")
		 	echo "<a href=\"pft.php?base=".$arrHttp["base"]."$encabezado&Modulo=dataentry\" class=\"defaultButton backButton\">";
		else
			echo "<a href=\"menu_modificardb.php?base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">";
	}
?>
					<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
					<span><strong><?php echo $msgstr["back"]?></strong></span>
				</a>
			</div>
			<div class="spacer">&#160;</div>
</div>
<div class="middle form">
			<div class="formContent">
<?php
if (!isset($arrHttp["pftname"])){
	if (isset($arrHttp["pft"]))
		$arrHttp["pft"]=stripslashes($arrHttp["pft"]);
	else
		$arrHttp["pft"]=stripslashes($arrHttp["pftedit"]);
	$arrHttp["nombre"]=trim(strtolower($arrHttp["nombre"]));
	$arrHttp["nombre"]=str_replace(".pft","",$arrHttp["nombre"]);
	$archivo=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".$arrHttp["nombre"].".pft";
	$fp=fopen($archivo,"w");
	if (!$fp){
		echo "<h2>$archivo ".$msgstr["revisarpermisos"]."</h2>";
		die;
	}
	fputs($fp, $arrHttp["pft"]);
	fclose($fp);
	unset($fp);
    if (isset($arrHttp["desde"]) and ($arrHttp["desde"]=="dataentry" or $arrHttp["desde"]=="recibos")){
	    echo "<script>
				self.close()
			 </script>
			 </body>
			 </html>";
	if (file_exists($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat")){
		$fp=file($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat");
	}else{
			$fp=file($db_path.$arrHttp["base"]."/pfts/".$lang_db."/formatos.dat");
		}
	$flag="";
	// IF THERE IS A HEADING FOR THE FORMAT
	$head="";
	$tipof="|";
	if (isset($arrHttp["headings"])) {
		$head="Y";
		$tipof="|".$arrHttp["tipof"];
	// DELETE THE FILE FORMAT NAME FROM THE DESCRIPTION OF THE FORMAT
	$desc=str_replace("(".$arrHttp["nombre"].")","",$arrHttp["descripcion"]);
	$dat=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat";
	$fp_out=fopen($dat,"w");
	if ($fp){
		foreach ($fp as $value){
				$f=explode('|',$value);
				if ($f[0]==$arrHttp["nombre"]) {
					$flag="S";
				}
		}
	}
	fclose($fp_out);
	if ($flag!="S"){
		fputs($fp_out,$arrHttp["nombre"]."|".$desc.$tipof."\n");
		fclose($fp_out);
	if ($head=="Y"){
		$fp=fopen($archivo_h,"w");
		$red=fwrite($fp,$arrHttp["headings"]);
		fclose($fp);
}else{
	if (file_exists($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat")){
		$fp=file($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat");
	}else{
		if (file_exists($db_path.$arrHttp["base"]."/pfts/".$lang_db."/formatos.dat")){
			$fp=file($db_path.$arrHttp["base"]."/pfts/".$lang_db."/formatos.dat");
		}
	}
	$p=explode('|',$arrHttp["pftname"]);
	$pname=$p[0];
	foreach ($fp as $value){
		if (!isset($l[2])) $l[2]="";
		if ($l[0]==$pname){
	$dat=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat";
	$fp_out=fopen($dat,"w");
	foreach ($salida as $value) {
	fclose($fp_out);

if (isset($archivo))
	echo "<center><h3>$archivo ".$msgstr["updated"]."</h3></center>";
else
	echo "<center><h3>".$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat ".$msgstr["updated"]."</h3></center>";

if (!isset($arrHttp["encabezado"])){
	$fp = file($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat");
	if ($fp) {
		echo "<script>

		var i;
		if (top.ModuloActivo==\"catalog\"){
			selectbox=top.menu.document.forma1.formato
			for(i=selectbox.options.length-1;i>=0;i--){
				selectbox.remove(i);
			}
			top.menu.document.forma1.formato.options[0]=new Option('','');
		";
		$i=0;
		$fp=file($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat");
		foreach($fp as $linea){
			if (trim($linea)!="") {
				$l=explode('|',$linea);
				$cod=trim($l[0]);
				$nom=trim($l[1]);
				if (trim($cod)!=""){
					$i=$i+1;
					echo "
					top.menu.document.forma1.formato.options[$i]=new Option('$nom','$cod');
					";
				}
			}
		}
		echo "}\n";
		$i=$i+1;
		echo "top.menu.document.forma1.formato.options[$i]=new Option('".$msgstr["noformat"]."','')\n";
        $i=$i+1;
		echo "top.menu.document.forma1.formato.options[$i]=new Option('".$msgstr["all"]."','ALL')\n";
		echo "top.menu.document.forma1.formato.selectedIndex=1
		     </script>";
	}
}
echo "</div></div>";
include("../common/footer.php");
if (isset($arrHttp['desde']) and ($arrHttp['desde']=="dataentry" or $arrHttp["desde"]=="recibos" )){
<script>
	self.close()
</script>
<?php
}
echo "</body></html>";
?>