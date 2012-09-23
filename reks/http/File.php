<?php
namespace reks\http;
use reks\validator\FileValidator;

use \reks\core\App;
/**
 * Represents a single file.
 * @author peec
 *
 */
class File{
	private $name;
	private $type;
	private $size;
	private $tmp_name;
	private $error;
	
	private $rules = array();
	
	
	/**
	 * @var reks\validator\FileValidator The validator object.
	 */
	private $validator;
	
	/**
	 * Constructs the file object
	 * @param App $app App stub
	 * @param array $file Array of file properties ( $_FILES['test'] )
	 */
	public function __construct(App $app, array $file){
		$this->name = $file['name'];
		$this->type = $file['type'];
		$this->size = $file['size'];
		$this->tmp_name = $file['tmp_name'];
		$this->error = $file['error'];

		$this->validator = new FileValidator($this);
	}
	
	/**
	 * 
	 * Returns false on error.
	 * @param string $path Full path to where to put the file.
	 * @param boolean $overwrite Overwrite the file if it exists? This defaults to false.
	 * @throws reks\validator\ValidationException on validation errors.
	 * @throws \Exception on PHP errors or overwrite if overwrite not enabled and file exists.
	 */
	public function upload($path, $overwrite=false){
		// Throw exception on errors.
		switch($this->error){
			case UPLOAD_ERR_INI_SIZE: throw new \Exception("The uploaded file exceeds the upload_max_filesize directive in php.ini.", UPLOAD_ERR_INI_SIZE);  break;
			case UPLOAD_ERR_FORM_SIZE: throw new \Exception("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. ", UPLOAD_ERR_FORM_SIZE);  break;
			case UPLOAD_ERR_PARTIAL: throw new \Exception("The uploaded file was only partially uploaded.", UPLOAD_ERR_PARTIAL);  break;
			case UPLOAD_ERR_NO_FILE: throw new \Exception("No file was uploaded. .", UPLOAD_ERR_NO_FILE);  break;
			case UPLOAD_ERR_NO_TMP_DIR: throw new \Exception("Missing a temporary folder.", UPLOAD_ERR_NO_TMP_DIR);  break;
			case UPLOAD_ERR_CANT_WRITE: throw new \Exception("Failed to write file to disk.", UPLOAD_ERR_CANT_WRITE);  break;
			case UPLOAD_ERR_EXTENSION: throw new \Exception("A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.", UPLOAD_ERR_EXTENSION);  break;
		}
		if (file_exists($path) && !$overwrite)throw new \Exception("File already exists.");
		
		$this->validator->validate($this);
		
		
		return move_uploaded_file($this->tmp_name, $path);
	}
	
	/**
	 * Returns the filename.
	 */
	public function getName(){
		return $this->name;
	}
	
	/**
	 * Returns the size of the file in bytes.
	 */
	public function getSize(){
		return $this->size;
	}
	
	/**
	 * Gets the file path of where the file is temporary located.
	 */
	public function getTmpName(){
		return $this->tmp_name;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function getErrorCode(){
		return $this->error;
	}
	
	/**
	 * Gets the file extension.
	 */
	public function getExtension(){
		return pathinfo($this->name, PATHINFO_EXTENSION);
	}
	
	/**
	 * Returns the validator object.
	 * @return reks\validator\FileValidator
	 */
	public function validator(){
		return $this->validator;
	}
	
}