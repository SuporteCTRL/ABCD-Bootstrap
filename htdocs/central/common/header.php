<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?php echo $def["LEGEND2"]; ?></title>
		<meta http-equiv="Expires" content="-1" />
		<meta http-equiv="pragma" content="no-cache" />
<?php
	if (isset($cisis_ver) and $cisis_ver=="unicode/"){		//echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1256\" />";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";	}else{		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />";	}
?>
		<meta name="robots" CONTENT="NONE" />
		<meta http-equiv="keywords" content="" />
		<meta http-equiv="description" content="" />
		<!-- Stylesheets -->

 
		
        <link rel="stylesheet" href="/central/tema/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="/central/tema/css/bootstrap-theme.min.css">-->
        <link rel="stylesheet" href="/central/tema/css/main.css">		
        <link rel="stylesheet" href="/central/tema/css/font-awesome.css">		
        

	  <!--	<link rel="stylesheet" rev="stylesheet" href="../css/template.css" type="text/css" media="screen"/>-->
		<!--[if IE]>
			<link rel="stylesheet" rev="stylesheet" href="../css/bugfixes_ie.css" type="text/css" media="screen"/>
		<![endif]-->
		<!--[if IE 6]>
			<link rel="stylesheet" rev="stylesheet" href="../css/bugfixes_ie6.css" type="text/css" media="screen"/>
		<![endif]-->
<?php if (isset($context_menu) and $context_menu=="N"){ }?>
</head>