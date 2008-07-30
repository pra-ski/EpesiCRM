<?php
/**
 * 
 * @author Arkadiusz Bisaga <abisaga@telaxus.com>
 * @copyright Arkadiusz Bisaga <abisaga@telaxus.com>
 * @license SPL
 * @version 0.1
 * @package utils-watchdog
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Utils_WatchdogInstall extends ModuleInstall {

	public function install() {
		$ret = true;
		$ret &= DB::CreateTable('utils_watchdog_category',
					'id I AUTO KEY,'.
					'name C(32),'.
					'callback C(128)',
			array('constraints'=>''));
		if(!$ret){
			print('Unable to create table utils_watchdog_category.<br>');
			return false;
		}
		$ret &= DB::CreateTable('utils_watchdog_event',
					'id I AUTO KEY,'.
					'category_id I,'.
					'internal_id I,'.
					'message C(64)',
			array('constraints'=>', FOREIGN KEY (category_id) REFERENCES utils_watchdog_category(id)'));
		if(!$ret){
			print('Unable to create table utils_watchdog_event.<br>');
			return false;
		}
		$ret &= DB::CreateTable('utils_watchdog_subscription',
					'category_id I,'.
					'internal_id I,'.
					'last_seen_event I,'.
					'user_id I',
			array('constraints'=>', FOREIGN KEY (user_id) REFERENCES user_login(id)'));
		if(!$ret){
			print('Unable to create table utils_watchdog_subscription.<br>');
			return false;
		}
		$ret &= DB::CreateTable('utils_watchdog_category_subscription',
					'category_id I,'.
					'user_id I',
			array('constraints'=>', FOREIGN KEY (user_id) REFERENCES user_login(id), FOREIGN KEY (category_id) REFERENCES utils_watchdog_category(id)'));
		if(!$ret){
			print('Unable to create table utils_watchdog_category_subscription.<br>');
			return false;
		}
		return $ret;
	}
	
	public function uninstall() {
		$ret = true;
		$ret &= DB::DropTable('utils_watchdog_category');
		$ret &= DB::DropTable('utils_watchdog_event');
		$ret &= DB::DropTable('utils_watchdog_subscription');
		$ret &= DB::DropTable('utils_watchdog_category_subscription');
		return $ret;
	}
	
	public function version() {
		return array("0.8");
	}
	
	public function requires($v) {
		return array(
			array('name'=>'Base/Lang','version'=>0),
			array('name'=>'Utils/GenericBrowser','version'=>0));
	}
	
	public static function info() {
		return array(
			'Description'=>'',
			'Author'=>'Arkadiusz Bisaga <abisaga@telaxus.com>',
			'License'=>'SPL');
	}
	
	public static function simple_setup() {
		return false;
	}
	
}

?>