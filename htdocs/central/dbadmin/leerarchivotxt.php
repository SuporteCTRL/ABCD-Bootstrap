<?php
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/get_post.php");
include ("../config.php");
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");;
if (!isset($arrHttp["archivo"])) die;
$archivo=str_replace("\\","/",$arrHttp["archivo"]);
$ix=strpos($archivo,'/');
$archivo=substr($archivo,$ix+1);
include("../common/header.php");
?>
<body>
<?php
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
?>
<div class="sectionInfo">

			<div class="breadcrumb">
				<?php echo "<h5>"." " .$msgstr["database"].": ".$arrHttp["base"]."</h5>"?>
			</div>

			<div class="actions">
<?php echo "<a href=\"menu_modificardb.php?base=".$arrHttp["base"]."&encabezado=s\" class=\"defaultButton backButton\">";
?>
					<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
					<span><strong><?php echo $msgstr["back"]?></strong></span>
				</a>
			</div>
			<div class="spacer">&#160;</div>
</div>
<?php }?>
<div class="middle form">
			<div class="formContent">
<br><br>
<?
if (!file_exists($db_path.$archivo)){	echo "$archivo ".$msgstr["ne"];
}else{
	$fp=file($db_path.$archivo);
	echo "<h5>".$arrHttp["archivo"]." &nbsp;
	<a href=editararchivotxt.php?archivo=".$archivo.">".$msgstr["edit"]."</a> &nbsp;
	<a href=javascript:self.close()>".$msgstr["close"]."</a>
	</h4>
	<xmp>";

	foreach ($fp as $value) echo $value;
	echo "</xmp>";
}
echo "</div></div>";
include("../common/footer.php");
echo "
</body>
</html>";

?>