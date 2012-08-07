<!doctype html>
<html>
	<head>
		<title>Error: <?php $out($code)?></title>
		<style type="text/css">
		*{
			margin:0;
			padding:0 auto 0 auto;
		}
		body{
			font: 10pt monospace;
			
			background-color: #191007;
		}
		.container{
			width: 600px;
			text-align: left;
			margin: 0 auto 0 auto;
			padding: 0;
		}
		header{
			border-top: 5px solid #191007;
			background-color: #CC0000;
			
			padding: 10px 0 10px;
		}
		#wrap{
			color: white;
			padding: 30px 0 200px;
		}
		h4{
			color: #cc0000;
			border-bottom: 1px dotted #fff;
			margin: 20px 0 20px;
		}
		
		em{
			font-size: 14pt;
			padding: 10px 0 10px;
			display: block;
		}
		</style>
	</head>
	<body>
		<header>
			<div class="container">
				<h1><?php $out($code)?></h1>
			</div>
		</header>
		<div id="wrap">
			<div class="container">
				<?php if ($code == 500):?>
					<h2>Internal server error</h2>
					<em><?php $out($requesturi)?></em>
							
					<?php if ($view->app->inProduction()):?>
						<p>We are having some issues at the server side. Please contact the webmaster.</p>
					<?php else: ?>
						<p>
						Exception (<?php $out(get_class($ex))?>) caught from: <strong><?php $out($con . "::" . $me)?></strong>
						</p>
						
						<h4>Message:</h4>
						<p><?php $out($ex->getMessage())?></p>
						
						
						<h4>Stacktrace:</h4>
						<p><?php echo nl2br($ex->getTraceAsString())?></p>
					<?php endif?>
				<?php elseif($code == 404): ?>
					<h2>Page not found</h2>
					<em><?php $out($requesturi)?></em>
					<p>We could not find the above page on our servers.</p>
				<?php endif?>
			</div>
		</div>
	</body>
</html>