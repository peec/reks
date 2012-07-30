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
namespace reks\tester;

/**
 * Routes tester controller.
 * Add this to your route configuration and test all your routes files for syntax errors in one click.
 * 
 * Sample integration:
 * '/dev/testroutes' => '\reks\tester\Routes.index',
 * 
 * @author peec
 *
 */
class Routes extends \reks\Controller{
	
	public function index(){
		
		$router = \reks\RouterFactory::create($this->app);
		
		
		
		try{
			$router->testRoutes();
			
			$this->view->assign('message', 'All routes are OK :)!');
		}catch(\reks\RouteParseException $e){
			$this->view->assign('error', $e->getMessage());
		}
		
		$this->view->assign('title', 'REKS Routes tester');
		$this->view->render($this->app->BASE_REKS . '/reks/res/tester.php');
	}
	
}