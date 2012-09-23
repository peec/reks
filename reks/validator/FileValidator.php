<?php
namespace reks\validator;

use reks\http\File;
/**
 * Used to validate file objects.
 * 
 * @author Petter Kjelkenes<kjelkenes@gmail.com>
 */
class FileValidator extends Validator{
	
	/**
	 * Sets the max file size.
	 * @param int $size Size in bytes.
	 * @return reks\validator\FileValidator
	 */
	public function maxSize($size){
		$this->add(function(File $self) use ($size){
			if ($size < $self->getSize())return "File exceeds the maximum size ({$self->getSize()} bytes) of $size bytes. ";
		});
		
		return $this;
	}
	
	/**
	 * Sets a white list of mime types.
	 * @param array $whitelist Array of mime types ( i.e image/gif, text/html ) etc.
	 * @return reks\validator\FileValidator
	 */
	public function mime(array $whitelist){
		
		$this->add(function(File $self) use ($whitelist){
			$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
			$mime = finfo_file($finfo, $self->getTmpName());
			finfo_close($finfo);
			
			if (!in_array($mime, $whitelist))return "Mime type '$mime' is a invalid mime type, valid types are "  . implode(', ', $whitelist);
		});
		
		return $this;
	}
	
	/**
	 * Sets a white list of file extensions.
	 * @param array $whitelist Array of file extensions, bmp, png, gif etc.
	 * @return reks\validator\FileValidator
	 */
	public function extensions(array $whitelist){
		
		$this->add(function(File $self) use ($whitelist){
			$ext = $self->getExtension();
			if (!in_array($ext, $whitelist))return "File extension {$ext} is not a valid file extension, valid extensions are "  . implode(', ', $whitelist);
		});
		return $this;
	}
	
	/**
	 * Adds a validation rule telling the file has to be a valid image.
	 * @return reks\validator\FileValidator
	 */
	public function isImage(){
		$this->add(function(File $self){
			if(!getimagesize($self->getTmpName()))return "File is not a valid image.";
		});
		return $this;
	}
}