<?php
/**
 * REKS framework is a very lightweight and small footprint PHP 5.3+ Framework.
 * It supports a limited set of features but fully MVC based and Objectoriented.
 * 
 * Copyright (c) 2012, REKS group ( Lars Martin Rørtveit, Andreas Elvatun, Petter Kjelkenes, Pål André Sundt )
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the REKS GROUP nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL "REKS Group" BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @license 3-clause BSD
 * @package reks
 * @author REKS group at Telemark University College
 */
namespace reks;


/**
 * Factory for initializing the Router.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class RouterFactory{
	
	
	
	/**
	 * Creates a new router object inserting $_SERVER  and logger instances aswell as the current time started.
	 * @param reks\App $app
	 * @return reks\Router
	 */
	static public function create(App $app, Router $superRouter = null){
		$router = new Router(
			$app,	
			microtime(true),
			isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/'
		);
		$router->app->module = new Module($router);
		
		// Create module object so configuration can add modules.
		$module = $app->module;
		
		
		$confFile = $app->APP_PATH . '/config.php';
		
		$config = array('route' => array());
		// Inheret config from super if any.
		if ($superRouter){
			$config = $superRouter->config;
			$configHandler = $superRouter->app->configHandler;
			if (file_exists($confFile))include $confFile;
		}else{
			// Include main config.
			require $confFile;
			$app->configHandler = isset($configHandler) ? $configHandler : null;
		}
		$router->setRoutes($config['route']);
		$router->setConfig($config);
		$router->setLog(new Log($config['log_level'], $config['log_dir']));
		
		return $router;
	}
	
}