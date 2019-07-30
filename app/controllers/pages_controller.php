<?php
/* SVN FILE: $Id: pages_controller.php 5847 2007-10-22 03:39:01Z phpnut $ */
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.controller
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 5847 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-10-21 22:39:01 -0500 (Sun, 21 Oct 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller
 */
class PagesController extends AppController{
/**
 * Controller name
 *
 * @var string
 * @access public
 */
	var $name = 'Pages';
/**
 * Default helper
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', 'Javascript');
/**
 * This controller does not use a model
 *
 * @var array
 * @access public
 */
	var $uses = array();
/**
 * Displays a view
 *
 * @param mixed What page to display
 * @access public
 */
	function display() {
		$this->layout = REDESIGN_PATH . 'default';

		if (!func_num_args()) {
			$this->redirect('/');
		}
		$path = func_get_args();

		if (!count($path)) {
			$this->redirect('/');
		}
		$count = count($path);
		$page = null;
		$subpage = null;
		$title = null;
		$description = null;
		$keywords = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title = $_description = Inflector::humanize($path[$count - 1]);
		}
		$this->set('page', $page);
		$this->set('subpage', $subpage);
		$this->set('_title', $title);
		$this->set('_description', $description);
		$this->set('_keywords', $keywords);
		
		$this->render(join('/', $path));
	}
	
	function home() {
		$this->layout = REDESIGN_PATH . 'homepage';
		$title = 'Sportovní výživa, potřeby pro kulturisty a fitness';
		$description = 'Sportovní výživa a doplňky stravy pro fitness a kulturistiku levně. Kvalitní výživové poradenství od opravdových odborníků.';
		$keywords = 'Sportovní výživa, fitness výživa, doplňky stravy, výživa pro kulturisty, potřeby pro kulturisty';
		
		$this->set('_title', $title);
		$this->set('_description', $description);
		$this->set('_keywords', $keywords);
		
		App::import('Model', 'News');
		$this->News = new News;
		$hp_news = $this->News->hp_list();
		$this->set('hp_news', $hp_news);
		
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		App::import('Model', 'RecommendedProduct');
		$this->RecommendedProduct = new RecommendedProduct;
		$hp_recommended = $this->RecommendedProduct->hp_list($customer_type_id);
		$this->set('hp_recommended', $hp_recommended);
		
		App::import('Model', 'DiscountedProduct');
		$this->DiscountedProduct = new DiscountedProduct;
		$hp_discounted = $this->DiscountedProduct->hp_list($customer_type_id);
		$this->set('hp_discounted', $hp_discounted);
		
		App::import('Model', 'MostSoldProduct');
		$this->MostSoldProduct = new MostSoldProduct;
		$hp_most_sold = $this->MostSoldProduct->hp_list($customer_type_id);
		$this->set('hp_most_sold', $hp_most_sold);
		
		$opening_hours = array(
			1 => '8:00 - 17:00',
			2 => '8:00 - 17:00',
			3 => '8:00 - 17:00',
			4 => '8:00 - 17:00',
			5 => '8:00 - 16:00',
			6 => 'viz <a rel="noreferrer" href="https://www.facebook.com/SportNutritionCZ/" target="_blank">Facebook</a>',
			7 => 'Zavřeno'
		);
		
		$today = date('Y-m-d');
		
		// oteviraci doba behem vanoc 2014
		$before_christmas_start = '2014-12-15';
		$before_christmas_end = '2014-12-21';
		$before_christmas = ($today >= $before_christmas_start && $today <= $before_christmas_end);
		if ($before_christmas) {
			$opening_hours = array(
					1 => '8:oo - 18:oo',
					2 => '8:oo - 18:oo',
					3 => '8:oo - 18:oo',
					4 => '8:oo - 18:oo',
					5 => '8:oo - 18:oo',
					6 => '9:3o - 12:oo',
					7 => 'Zavřeno'
			);
		} else {
			$during_christmas_start = '2014-12-22';
			$during_christmas_end = '2014-12-28';
			$during_christmas = ($today >= $during_christmas_start && $today <= $during_christmas_end);
			if ($during_christmas) {
				$opening_hours = array(
						1 => '8:oo - 18:oo',
						2 => '8:oo - 18:oo',
						3 => 'Zavřeno',
						4 => 'Zavřeno',
						5 => 'Zavřeno',
						6 => 'Zavřeno',
						7 => 'Zavřeno'
				);
			} else {
				$after_christmas_start = '2014-12-29';
				$after_christmas_end = '2015-01-04';
				$after_christmas = ($today >= $after_christmas_start && $today <= $after_christmas_end);
				if ($after_christmas) {
					$opening_hours = array(
							1 => '8:oo - 17:oo',
							2 => '8:oo - 17:oo',
							3 => 'Zavřeno',
							4 => 'Zavřeno',
							5 => 'Zavřeno',
							6 => 'Zavřeno',
							7 => 'Zavřeno'
					);
				}
			}
		}
		
		$this->set('opening_hours', $opening_hours);
		
		App::import('Model', 'HomepageBanner');
		$this->HomepageBanner = &new HomepageBanner;
		$banners  = $this->HomepageBanner->find('all', array(
			'conditions' => array(),
			'contain' => array(),
			'fields' => array('*'),
			'order' => array('HomepageBanner.order' => 'asc')
		));
		$this->set('homepage_banners', $banners);
		$this->set('homepage_banner_folder', $this->HomepageBanner->folder);
		
	}
}
?>