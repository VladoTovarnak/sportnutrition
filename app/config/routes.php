<?php
/* SVN FILE: $Id: routes.php 7296 2008-06-27 09:09:03Z gwoo $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 7296 $
 * @modifiedby		$LastChangedBy: gwoo $
 * @lastmodified	$Date: 2008-06-27 02:09:03 -0700 (Fri, 27 Jun 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */

//	Router::connect('/', array('controller' => 'contents', 'action' => 'view', 'index'));
	Router::connect('/', array('controller' => 'pages', 'action' => 'home'));
	Router::connect('/kosik', array('controller' => 'carts_products', 'action' => 'index'));
	
	Router::connect('/objednavka-osobni-udaje', array('controller' => 'customers', 'action' => 'order_personal_info'));
	Router::connect('/doprava-a-zpusob-platby', array('controller' => 'orders', 'action' => 'set_payment_and_shipping'));
	Router::connect('/vysypat-kosik', array('controller' => 'carts', 'action' => 'dump'));
	Router::connect('/rekapitulace-objednavky', array('controller' => 'orders', 'action' => 'recapitulation'));
	Router::connect('/dokonceni-objednavky', array('controller' => 'orders', 'action' => 'finalize'));
	
	Router::connect('/vyhledavani-produktu', array('controller' => 'searches', 'action' => 'do_search'));
	Router::connect('/registrace', array('controller' => 'customers', 'action' => 'add'));
	Router::connect('/prihlaseni', array('controller' => 'customers', 'action' => 'login'));
	Router::connect('/obnova-hesla', array('controller' => 'customers', 'action' => 'password'));
	
	Router::connect('/aktuality', array('controller' => 'news', 'action' => 'index'));
	
	// routovani url ze stareho nutrishopu. v AppController/beforeFilter presmerovavam stare urls na nove. akce 'tools/asd', na kterou je to routovany, je tam uplne zbytecne, protoze
	// stara url se presmeruje stejne a do te akce se to vubec nedostane
	Router::connect('/:old_sn_key/:sth', array('controller' => 'tools', 'action' => 'asd'), array('old_sn_key' => 'website|category|product|manufacturer', 'sth' => '.*'));
	
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

	// routovani obsahovych stranek
	Router::connect('/:path.htm', array('controller' => 'contents', 'action' => 'view'), array('pass' => array('path')));

	// routovani kategorii
	Router::connect(
		'/:slug:category_id',
		array('controller' => 'categories_products', 'action' => 'view'),
		array('slug' => '.*\-c', 'category_id' => '\d+', 'pass' => array('category_id'))
	);

	
	// routovani produktu
	Router::connect(
		'/:slug:product_id',
	 	array('controller' => 'products', 'action' => 'view'),
	 	array('slug' => '.*\-p', 'product_id' => '\d+', 'pass' => array('product_id'))
	);

	// routovani vyrobcu
	Router::connect(
		'/:slug:manufacturer_id',
		array('controller' => 'manufacturers', 'action' => 'view'),
		array('slug' => '.*\-v', 'manufacturer_id' => '\d+', 'pass' => array('manufacturer_id'))
	);

/**
 * Then we connect url '/test' to our test controller. This is helpfull in
 * developement.
 */
	Router::connect('/tests', array('controller' => 'tests', 'action' => 'index'));
?>