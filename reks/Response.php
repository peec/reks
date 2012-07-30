<?php
namespace reks;

/**
 * A response can be returned from a method in any controller.
 * @author peec
 *
 */
class Response{
	/**
	 * 
	 * @var reks\View
	 */
	private $view;
	
	private $build = array(
			'content' => null,
			'code' => self::HTTP_OK,
			'type' => Controller::C_HTML
			);
	
	
	public function __construct($content, $type = Controller::C_HTML, $code=self::HTTP_OK){
		$this->build['content'] = $content;
		$this->build['code'] = $code;
		$this->build['type'] = $type;
	}
	
	
	public function setCode($code){
		$this->build['code'] = $code;
		return $this;
	}
	
	public function asJson(){
		$this->build['type'] = Controller::C_JSON;
		return $this;
	}
	
	
	/**
	 * Sends a http status code to browser.
	 * @param string $httpCode A valid http code.
	 */
	public function sendStatus($httpCode){
		header(' ', true, $httpCode);
	}
	
	public function execute(){
		$this->sendStatus($this->build['code']);
		switch($this->build['type']){
			case Controller::C_JSON:
				$this->build['content'] = json_encode($this->build['content']);
				break;
			case Controller::C_HTML:
				$this->build['content'] = $this->view->fetch($this->build['content']);
				break;
		}
		echo $this->build['content'];
		
	}
	
	
	public function setView(View $view){
		$this->view = $view;
	}
	
	// [Informational 1xx]
	
	const HTTP_CONTINUE = 100;
	
	const HTTP_SWITCHING_PROTOCOLS = 101;
	
	// [Successful 2xx]
	
	const HTTP_OK = 200;
	
	const HTTP_CREATED = 201;
	
	const HTTP_ACCEPTED = 202;
	
	const HTTP_NONAUTHORITATIVE_INFORMATION = 203;
	
	const HTTP_NO_CONTENT = 204;
	
	const HTTP_RESET_CONTENT = 205;
	
	const HTTP_PARTIAL_CONTENT = 206;
	
	// [Redirection 3xx]
	
	const HTTP_MULTIPLE_CHOICES = 300;
	
	const HTTP_MOVED_PERMANENTLY = 301;
	
	const HTTP_FOUND = 302;
	
	const HTTP_SEE_OTHER = 303;
	
	const HTTP_NOT_MODIFIED = 304;
	
	const HTTP_USE_PROXY = 305;
	
	const HTTP_UNUSED= 306;
	
	const HTTP_TEMPORARY_REDIRECT = 307;
	
	// [Client Error 4xx]
	
	const errorCodesBeginAt = 400;
	
	const HTTP_BAD_REQUEST = 400;
	
	const HTTP_UNAUTHORIZED = 401;
	
	const HTTP_PAYMENT_REQUIRED = 402;
	
	const HTTP_FORBIDDEN = 403;
	
	const HTTP_NOT_FOUND = 404;
	
	const HTTP_METHOD_NOT_ALLOWED = 405;
	
	const HTTP_NOT_ACCEPTABLE = 406;
	
	const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
	
	const HTTP_REQUEST_TIMEOUT = 408;
	
	const HTTP_CONFLICT = 409;
	
	const HTTP_GONE = 410;
	
	const HTTP_LENGTH_REQUIRED = 411;
	
	const HTTP_PRECONDITION_FAILED = 412;
	
	const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
	
	const HTTP_REQUEST_URI_TOO_LONG = 414;
	
	const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
	
	const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	
	const HTTP_EXPECTATION_FAILED = 417;
	
	// [Server Error 5xx]
	
	const HTTP_INTERNAL_SERVER_ERROR = 500;
	
	const HTTP_NOT_IMPLEMENTED = 501;
	
	const HTTP_BAD_GATEWAY = 502;
	
	const HTTP_SERVICE_UNAVAILABLE = 503;
	
	const HTTP_GATEWAY_TIMEOUT = 504;
	
	const HTTP_VERSION_NOT_SUPPORTED = 505;
	
	
	
	public static function isError($code) {
		return is_numeric($code) && $code >= self::HTTP_BAD_REQUEST;
	}
	
	public static function canHaveBody($code){
	
		return
	
		// True if not in 100s
	
		($code < self::HTTP_CONTINUE || $code >= self::HTTP_OK)
	
		&& // and not 204 NO CONTENT
	
		$code != self::HTTP_NO_CONTENT
	
		&& // and not 304 NOT MODIFIED
	
		$code != self::HTTP_NOT_MODIFIED;
	
	}
	
	
	
}