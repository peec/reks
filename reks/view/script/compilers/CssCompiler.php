<?php
namespace reks\view\script\compilers;

class CssCompiler extends Compiler{
	
	public function setup(){
	
		$this->setExtension('css');
	
		$app = $this->view->app;
		$this->setCompiler(function($file, $content) use($app){
			if (file_exists($file)){
				$scriptDir = dirname($file);
					
				
				$content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
				$content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
				
				
				preg_match_all('#url\((.*?)\)#is', $content, $matches);
				
				
				foreach($matches[1] as $path){
					$path = trim($path,'\'"');
					// If this is a relative reference.
					if (
							substr($path, 0, 1) != '/' &&
							!strstr($path, '://')
					){
						
						$newPath = substr(
								realpath($scriptDir . '/' . $path), 
								strlen(realpath($app->PUBLIC_PATH))
						);
						
						
						if ($newPath){
							$newPath = '..'.str_replace(array(DIRECTORY_SEPARATOR, '\\'), '/', $newPath);
							$content = str_replace($path, $newPath, $content);
						}
						
					}
				}
			}
			return $content;
		});
	}
		
	
	public function show($src){
		return '<link rel="stylesheet" type="text/css" href="'.$src.'" />';
	}
}