var jsRoutes = {}; (function(_root){
	var _nS = function(c,f,b){var e=c.split(f||"."),g=b||_root,d,a;for(d=0,a=e.length;d<a;d++){g=g[e[d]]=g[e[d]]||{}}return g}
	var _qS = function(items){var qs = ''; for(var i=0;i<items.length;i++){if(items[i]) qs += (qs ? '&' : '') + items[i]}; return qs ? ('?' + qs) : ''}
	var _s = function(p,s){return p+((s===true||(s&&s.secure))?'s':'')+'://'}
	var _wA = function(r){
		return {
			ajax:function(c){
				c.url=r.url;
				c.type=r.method;
				$.ajax(c);
			}, 
			method:r.method,
			url:r.url,
			absoluteURL: function(s){
				
				return _s('http',s)+'<?php echo $url->fetchDomain()?>'+r.url
			}
		}
	}

	<?php foreach($jsRoutes as $route):
		list($controller, $method, $args) = $route->parseTo();
	
	
		// @todo Must not be dynamic controller... We don't really support it..
		// @todo Should this be possible? How?
		if (substr($controller,0, 1) != '@') {
			
			
		$dynamicMethod = substr($method,0, 1) == '@';
		
		$jsController = str_replace('/', '.', $controller) . (!$dynamicMethod  ? '.' . $method : '');
		if (substr($jsController,0,1) == '.')$jsController = substr($jsController, 1);
		
		$from = $route->getFrom();
		
		// Special case... Dynamic method!
		if ($dynamicMethod){
			$args[] = $method;
		}
		
		$params = array();
		
		
		foreach($args as $var){
			$from = str_replace("$var", '" + (function(k,v) {return v})("'.substr($var, 1).'", '.substr($var, 1).') + "', $from);
			$params[] = substr($var, 1);
		}
		
		

		
		
		

	?>
	

	
		_nS('<?php echo $jsController?>'); _root.<?php echo $jsController?> = 
			function(<?php echo implode(',', $params)?>){

				var t =  
					_wA(<?php 
						$obj = new \stdClass();
						$obj->method = ($route->getType() == '*' ? 'POST' : $route->getType());
						$obj->url = '"'.$from.'"';
							echo '{'  .  "method: '{$obj->method}', url: \"".substr($url->asset(''), 0, -1).(!$url->removeScriptpath ? '/index.php' : '' )."\" + {$obj->url}"   .  '}';
						?>)
				return t;
			}
	<?php } endforeach?>
	
})(jsRoutes)
