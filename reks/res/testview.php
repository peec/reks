<!DOCTYPE html>
<html>
<head>
<title><?php echo $testName ?>
</title>
<style type="text/css">
body {
	text-align: center;
}

.box {
	text-align: left;
	margin: 0 auto 0 auto;
	width: 500px;
	border: 1px grey solid;
	padding: 15px;
}

.error h3 {
	color: red;
}

.success h3 {
	color: green;
}

.summary {
	padding: 15px;
}

.error .summary {
	background-color: red;
	color: white;
}

.success .summary {
	background-color: green;
	color: white;
}

.controller, .method{
	font: 10pt "Consolas",monospace;
}


pre{
overflow: auto;
font-family: "Consolas",monospace;
font-size: 9pt;
text-align:left;
background-color: #FCF7EC;
overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not
white-space: pre-wrap; /* css-3 */
white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
word-wrap: break-word; /* Internet Explorer 5.5+ */
margin: 0px 0px 0px 0px;
padding:5px 5px 3px 5px;
white-space : normal; /* crucial for IE 6, maybe 7? */
}
</style>
</head>
<body>
	<div id="container">
		<h1>
			<?php echo $testName ?>
		</h1>
		<div class="box">
			<div class="<?php echo $result->hasErrors() ? 'error' : 'success'; ?>">
				<h3>
					<?php echo $result->hasErrors() ? 'Error' : 'Success'; ?>
				</h3>
				<?php if ($result->hasErrors()):?>
					<ul>
						<?php foreach($result->getFailedCases() as $case):?>
							<li>
							<?php if ($case instanceof \reks\tester\TestResultExceptionItem):?>
							Unexpected Exception: 
							<?php endif?>
							
							<strong><?php echo $case->getMethod()?></strong>
							 in <span class="controller"><?php echo $case->getClass()?></span>.<span class="method"><?php echo $case->getTestName()?>()</span>
							 
							 
							 <?php if ($case instanceof \reks\tester\TestResultExceptionExpectedItem):?>
							 	(Expected one of the following exceptions):
							 	<ul>
							 		<?php foreach($case->getExceptions() as $e):?>
							 			<li><?php echo $e?></li>
							 		<?php endforeach?>
							 	</ul>
							 <?php endif?>
							 
							 
							 <?php if ($case instanceof \reks\tester\OutputExpectedItem):?>
							 	(Expected one of the following outputs):
							 	<?php $output = $case->getOutput();?>
							 	<?php if (is_array($output)):?>
							 		<ul>
							 	
							 			<?php foreach($output as $e):?>
							 				<li><?php echo $e?></li>
							 			<?php endforeach?>
							 		</ul>
							 	<?php else:?>
							 		<p><?php echo $output?></p>
							 	<?php endif?>
							 <?php endif?>
							 
							 
							 <?php if ($case instanceof \reks\tester\OutputNotExpectedItem):?>
							 	(Got unexpected output):
							 	<pre>
							 		<?php echo $case->getOutput()?>
							 	</pre>
							 <?php endif?>
							 <?php if ($case instanceof \reks\tester\TestResultExceptionItem):?>
							 
							 <ul>
							 	
							 	<li><strong>Message:</strong><?php echo $case->getException()->getMessage()?></li>
							 	<li><strong>Exception Code:</strong><?php echo $case->getException()->getCode()?></li>
							 	
							 	<li><strong>Trace:</strong>
							 	<pre><?php echo htmlentities($case->getException()->getTraceAsString())?></pre>
							 	</li>
							 </ul>
							 
							 <?php endif?>
							</li>
						<?php endforeach?>
					</ul>
				<?php endif?>
				
				
				
				<div class="summary">
					<?php echo $result->getTestMethodCount(); ?>
					/
					<?php echo $result->getTestMethodCount(); ?>
					test cases complete. <strong><?php echo $result->getSuccessCount() ?>
					</strong> passes, <strong><?php echo $result->getFailCount() ?>
					</strong> fails and <strong><?php echo $result->getExceptionCount(); ?>
					</strong> exceptions.
				</div>
				<h6>Test controllers used:</h6>
				<ul>
					<?php foreach($result->getTestControllers() as $controller):?>
					<li><?php echo $controller?></li>
					<?php endforeach?>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>
