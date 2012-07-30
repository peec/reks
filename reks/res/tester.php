<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title?></title>
	<style type="text/css">
		html{
			font: 12pt "Times New Roman",Georgia,Serif;
		}
		.msg{
			padding: 5px 8px;
			margin: 10px 0;
		}
		.msg h5{
			margin: 0;
			font-size: 18pt;
		}
		.success{
			background-color: #CFC;
			border: solid 1px #6C6;
		}
		.error{
			background-color: #FCC;
			border: solid 1px #C66;
		}
	</style>
</head>
<body>
	<header>
		<h1><?php echo $title?></h1>
	</header>
	<div>
		<?php if (isset($error)):?>
			<div class="msg error">
			<h5>Error</h5>
			<p><?php echo $error?></p>
			</div>
		<?php else:?>
			<div class="msg success">
			<h5>Success</h5>
			<p><?php echo $message?></p>
			</div>
		<?php endif?>
	</div>
	<footer>
	
	</footer>
</body>
</html>