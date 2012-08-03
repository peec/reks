<!DOCTYPE html>
<html>
<head>
	<!-- 
		This page is just a test page, so you can get started.
		Here you have examples of reverse routing of urls and some more goodies.
	 -->
	<?php 
		// Use the Scripts class to add many style sheets.
		$view->scripts->css
		->add('style.css')
		->display();
		
		$view->scripts->js
		->addJsRoutes();
		?>
		
		
		
	
	<?php 
		// Some magic here, when using head class methods this will output all the data
		// like <title></title> tags , description, and etc... See Head class in the reks\view package.
		echo $view->html?>
	
	
</head>
<body>
	<header>
		<h1><?php echo $header?></h1>
	</header>
	<div>
		<h2>Just some links:</h2>
		<p>
			<a href="<?php echo $url->reverse('Main.helloWorld')?>"><?php echo $lang->hello_world?> - this is a link to the main page (this one)</a>
		</p>
	
		<p>
			<a href="<?php echo $url->reverse('Main.hello', array('to' => 'world'))?>">Hello to  ( click me )...</a>
		</p>
	
		<h2>Cool Dev tools:</h2>
		
			<ul>
				<li><a href="<?php echo $url->reverse('/reks/controller/RouteTest.index')?>">Check if syntax of all routes is OK ...</a></li>
				<li><a href="<?php echo $url->reverse('/reks/tests/Main.index')?>">Unit test REKS framework</a></li>
				<li><a href="<?php echo $url->reverse('tests/Tests.index')?>">Unit test This application</a></li>
		
			</ul>
		
	</div>
	<footer>
		REKS group, <a href="http://reks.pkj.no">reks.pkj.no</a>
	</footer>

	<!-- Java scripts at the bottom. -->
	<?php $view->scripts->js->display();?>	
</body>
</html>