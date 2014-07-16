<?php
session_start();
$_SESSION=array();
include("../config.php");
include ("../common/header.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";

if (isset($_SESSION["lang"])){
	$arrHttp["lang"]=$_SESSION["lang"];
}else{
	$arrHttp["lang"]=$lang;
	$_SESSION["lang"]=$lang;
}
include ("../lang/admin.php");
include ("../lang/lang.php");
?>

<script src=../dataentry/js/lr_trim.js></script>
<script>

// Function to check letters and numbers
function alphanumeric(inputtxt) {
  var letters = /^[0-9a-z]+$/;
  if (letters.test(inputtxt)) {
    return true;
  } else {

    return false;
  }
}

function Enviar(){	login=Trim(document.administra.login.value)
	password=Trim(document.administra.password.value)
	new_password=Trim(document.administra.new_password.value)
	confirm_password=Trim(document.administra.confirm_password.value)

	if (login=="" || password=="" || new_password=="" || confirm_password==""){
		alert("<?php echo $msgstr["datosidentificacion"]?>")
		return
	}else{		if (new_password != confirm_password){			alert("<?php echo $msgstr["passconfirm"]?>")
			return		}
		txt=login+password+new_password+confirm_password
		if (alphanumeric(txt)){			document.administra.submit()		}else{			alert("<?php echo $msgstr["valchars"]?>")		}
	}}
</script>
</head>
<body>
<div class="jumbotron">

			<h1><?php echo $institution_name?></h1>

</div>
   <div class="container">
   
<div class="page-header">
  <h1><?php echo $msgstr["chgpass"]; ?></h1>
</div>   
   
  
   
<form role="form" name="administra" action="../common/inicio.php" method=post onsubmit="Javascript:Enviar();return false">
<input type="hidden" name="Opcion" value="chgpsw">
<input type="hidden" name="lang" value="<?php echo $arrHttp["lang"]?>">
<input type="hidden" name="db_path" value="<?php if (isset($arrHttp["db_path"])) echo $arrHttp["db_path"]?>">


  <div class="form-group">
				<label for="user"><?php echo $msgstr["userid"]?></label>
				<input type="text" name="login" id="user" value="" class="form-control" required >
 </div>
  <div class="form-group">
				<label for="pwd"><?php echo $msgstr["actualpass"]?></label>
				<input type="password" name="password" id="pwd" value="" class="form-control" required >
 </div>
  <div class="form-group">
				<label for="pwd"><?php echo $msgstr["newpass"]?></label>
				<input type="password" name="new_password" id="pwd" value="" class="form-control" required >

  <div class="form-group">
				<label for="pwd"><?php echo $msgstr["confirmpass"]?></label>
				<input type="password" name="confirm_password" id="pwd" value="" class="form-control" required >
 </div>

					<a href="javascript:Enviar()" class="btn btn-lg btn-primary btn-block">
						<span></span><?php echo $msgstr["chgpass"]; ?>
					</a>
					<a href="/" class="btn btn-lg btn-warning btn-block">
						<span></span><?php echo $msgstr["cancelar"]; ?>					
					</a>
			
					


</form>
    </div> <!-- /container -->
<?php include ("../common/footer.php");?>
	</body>
</html>



