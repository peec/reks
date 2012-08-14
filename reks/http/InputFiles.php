<?php
namespace reks\http;
use \reks\core\App;

class InputFiles extends Input{

	private $app;
	
	public function __construct(App $app, array $data){
		$this->app = $app;
		$data = $this->bindFileObjects($data);
		parent::__construct($data);
	}
	
	/**
	 * Binds file objects to a better normal structure and binds them to \reks\http\File objects.
	 * 
	 * @param array $data Original data contents.
	 */
	private function bindFileObjects(array $files){
		$ret = array();
		if(isset($files['tmp_name'])){
			if (is_array($files['tmp_name'])){
				foreach($files['name'] as $idx => $name){
					if ($files['size'][$idx] > 0){
						$ret[$idx] = new File( $this->app, array(
								'name' => $name,
								'tmp_name' => $files['tmp_name'][$idx],
								'size' => $files['size'][$idx],
								'type' => $files['type'][$idx],
								'error' => $files['error'][$idx]
						));
					}
				}
			}else{
				if ($files['size'] > 0)
					$ret = new File( $this->app, $files);
				else $ret = null;
			}
		}
		else{
			foreach ($files as $key => $value){
				$ret[$key] = $this->bindFileObjects($value);
			}
		}
			
		return $ret;
	}
	
	/**
	 * @return reks\http\File
	 */
	public function file($name){
		return $this->get($name);
	}
}