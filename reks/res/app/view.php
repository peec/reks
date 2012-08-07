<!doctype html>
<html lang="<?php echo $view->config['language']?>">
	<head>
		<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta name="description" content="">
    	<meta name="author" content="Petter Kjelkenes">
		
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    	<![endif]-->
		
		
		<!-- Display title tag and other app specific tags. -->
		<?php echo $view->html?>
		
		
		
		
	</head>
	<body>	
		<!-- Content -->
		<div id="content">
			<p>Welcome to your first application!</p>
		</div>
		
		
		
		<!-- Footer -->
		<footer>
			
		</footer>
		<!-- Render added javascripts / inline javascripts at the bottom -->
		<?php $view->scripts->js->display()?>
	</body>
</html>