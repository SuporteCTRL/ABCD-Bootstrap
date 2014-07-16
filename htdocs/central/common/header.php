<html>
	<head>
		<title><?php echo $def["LEGEND2"]; ?></title>
		<meta http-equiv="Expires" content="-1" />
		<meta http-equiv="pragma" content="no-cache" />
<?php
	if (isset($cisis_ver) and $cisis_ver=="unicode/"){		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";	}else{		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />";	}
?>
	<meta name="robots" CONTENT="NONE" />
	<meta http-equiv="keywords" content="" />
	<meta http-equiv="description" content="" />
	<link rel="icon" type="image/x-icon" href="/favicon.ico" />
	
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