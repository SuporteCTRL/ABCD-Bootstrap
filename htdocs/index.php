<?php

session_start();

$_SESSION=array();

	include("central/config.php");
	include ("$app_path/common/header.php");
	include("$app_path/common/get_post.php");

if (isset($_SESSION["lang"])){	$arrHttp["lang"]=$_SESSION["lang"];}else{	$arrHttp["lang"]=$lang;
	$_SESSION["lang"]=$lang;}
	include ("$app_path/lang/admin.php");
	include ("$app_path/lang/lang.php");
?>

<script src="<?php echo $app_path?>/dataentry/js/lr_trim.js"></script>
<script>
document.onkeypress =
	function (evt) {
			var c = document.layers ? evt.which
	       		: document.all ? event.keyCode
	       		: evt.keyCode;
			if (c==13) Enviar()
			return true;
	}

function UsuarioNoAutorizado(){
	alert("<?php echo $msgstr["menu_noau"]?>")
}

function CambiarClave(){
	document.cambiarPass.login.value=Trim(document.administra.login.value)
	document.cambiarPass.password.value=Trim(document.administra.password.value)
	ix=document.administra.lang.selectedIndex
	document.cambiarPass.lang.value=document.administra.lang.options[ix].value
	ix=document.administra.db_path.selectedIndex
	document.cambiarPass.db_path.value=document.administra.db_path.options[ix].value	document.cambiarPass.submit()}

function Enviar(){

	login=Trim(document.administra.login.value)
	password=Trim(document.administra.password.value)
	if (login=="" || password==""){
		alert("<?php echo $msgstr["datosidentificacion"]?>")
		return
	}else{
		if (document.administra.newindow.checked){
			ancho=screen.availWidth-15
			alto=(screen.availHeight||screen.height) -50
			document.administra.target="ABCD"
			msgwin=window.open("","ABCD","menubar=no, toolbar=no, location=no, scrollbars=yes, status=yes, resizable=yes, top=0, left=0, width="+ancho+", height="+alto)
			msgwin.focus()		} else{			document.administra.target=""		}
		document.administra.submit()
	}
}
</script>

	<style>
		html {
			padding-top: 50px;
			padding-bottom: 20px;
				}
	</style>	

</head>

<body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
			<a class="navbar-brand" style="color: #fff;" href="#"><i class="fa fa-university"></i> <?php echo $institution_name?></a>
        </div>
			
			<div class="navbar-collapse collapse">
 
				<form class="navbar-form navbar-right" role="form" name="administra" onsubmit="javascript:return false" method="post" action="<?php echo $app_path?>/common/inicio.php">
					<input type="hidden" name="Opcion" value="admin">
					<input type="hidden" name="cipar" value="acces.par">
					<input type="hidden" name="window_id">
	
	<?php
		if (isset($arrHttp["login"]) and $arrHttp["login"]=="N"){
	?>
	 <div class="form-group">
		<input type="text" name="login" id="user" value="" class="form-control"  required >
 	</div>
	<?php
		}else{
	?>
	<div class="form-group">
		<input type="text" name="login" id="user" value="" class="form-control" placeholder="<?php echo $msgstr["userid"];?>" >
	</div>
	<?php
		}
	?>
		<div class="form-group">
		<input type="password" name="password" id="pwd" value="" class="form-control" placeholder="<?php echo $msgstr["password"];?>" required >		</div>

	<?php
 		$a=$msg_path."/lang/".$_SESSION["lang"]."/lang.tab";
 			if (file_exists($a)){
 	?> 				<select class="form-control" name="lang" >';
	<?php			
			$fp=file($a);
			$selected="";
				foreach ($fp as $value){
			$value=trim($value);
			if ($value!=""){
				$l=explode('=',$value);
				if ($l[0]!="lang"){
					if ($l[0]==$_SESSION["lang"]) $selected=" selected";
	?>				
			<option value="<?php echo $l[0];?>" selected><?php echo $msgstr[$l[0]]; ?></option>

	<?php
					$selected="";
				}
			}
		}
	}else{
		echo $msgstr["flang"].$a;
		die;
	}
?>
			</select>

<?php
if (file_exists("dbpath.dat")){
	$fp=file("dbpath.dat");
	echo "<select class=form-control  name=db_path>\n";
	foreach ($fp as $value){
		if (trim($value)!=""){
			$v=explode('|',$value);
			$v[0]=trim($v[0]);
			echo "<Option value=".trim($v[0]).">".$msgstr['database_dir']." ".$v[1]."\n";
		}

	}
	echo "</select>";
}
?>
  <div class="checkbox">
    <label for="setCookie">
      <input type="checkbox" name="newindow" value="
<?php
if (isset($open_new_window) and $open_new_window=="Y")
	echo "Y checked";
else
	echo "N";
?>" /> <?php echo $msgstr["openwindow"]?>
    </label>
  </div>

	<a href="javascript:Enviar()" class="btn btn-success">
	<span></span><?php echo $msgstr["entrar"]?>
	</a>
	
	<?php 
	 if (isset($change_password) and $change_password=="Y") 
		 echo "<a class=\"btn btn-warning\" href=javascript:CambiarClave()>". $msgstr["chgpass"]."</a>\n";
	 ?>
</form>

<form class="form-signin" name="cambiarPass" action="<?php echo $app_path?>/dataentry/change_password.php" method="post">
	<input type="hidden" name="login">
	<input type="hidden" name="password">
	<input type="hidden" name="db_path">
	<input type="hidden" name="Opcion" value="chgpsw">
</form>

        </div><!--/.navbar-collapse -->
      </div>
    </div>
    
	 <div class="col-sl-12 col-sm-12 col-md-12">
	 
	<?php
		if (isset($arrHttp["login"]) and $arrHttp["login"]=="N"){
	?>
					<div  class="alert alert-warning" role="alert"><?php echo $msgstr["menu_noau"];?></div>

	<?php
		}
		if (isset($arrHttp["login"]) and $arrHttp["login"]=="P"){
	?>	
				<div class="helper alert"><?php echo $msgstr["pswchanged"];?></div>
	<?php		
		}
	?>	 
	 
		<?php //include ('site/php/index.php'); ?>
		<iframe  width="100%" height="100%" src="/site" frameborder="0" ></iframe>
	</div>   

<?php include ("$app_path/common/footer.php");?>
	</body>
</html>