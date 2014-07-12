<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      users administration
 * @desc:
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
global $arrHttp;
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/header.php");
include("../common/get_post.php");
include("../config.php");
$lang=$_SESSION["lang"];
include("../lang/dbadmin.php");
include("../lang/profile.php");
//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";
echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	//include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}else{
	$encabezado="";
}
?>



<h2><?php echo $msgstr["usuarios"]?></h2>





<ol class="breadcrumb" >
	<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/profiles.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
 	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/profiles.html target=_blank>".$msgstr["edhlp"]."</a>";
 	?>&nbsp; &nbsp; Script: users_adm.php
	</ol>

<ul class="nav nav-tabs nav-justified" role="tablist">
	<li class="active"><a target="perfil" href="../dataentry/browse.php?showdeleted=Y&encabezado=s&base=acces&cipar=acces.par&return=../dbadmin/users_adm.php"><?php echo $msgstr["usuarios"]; ?></a></li>
	<li><a target="perfil" href="profile_edit.php?encabezado=s"><?php echo $msgstr["profiles"]; ?></a></li>
</ul>

<div class="col-sm-12" style="height: 1000px;">

<iframe name="perfil" width="100%" scrolling="auto" frameborder="0" height="100%" src="../dataentry/browse.php?showdeleted=Y&encabezado=s&base=acces&cipar=acces.par&return=../dbadmin/users_adm.php"></iframe>

</div>

<?php include("../common/footer.php");
echo "</body></html>\n";

?>
