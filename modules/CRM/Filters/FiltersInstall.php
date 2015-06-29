<?php
/**
 * @author Paul Bukowski <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2008, Telaxus LLC
 * @license MIT
 * @version 1.0
 * @package epesi-crm
 * @subpackage filters
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class CRM_FiltersInstall extends ModuleInstall {

	public function install() {
		$ret = true;
		$ret &= DB::CreateTable('crm_filters_group','
			id I4 AUTO KEY,
			name C(128) NOTNULL,
			description C(255),
			user_login_id I4 NOTNULL',
			array('constraints'=>', UNIQUE(name, user_login_id), FOREIGN KEY (user_login_id) REFERENCES user_login(ID)'));
		if(!$ret){
			print('Unable to create table crm_filters_group.<br>');
			return false;
		}
		$ret &= DB::CreateTable('crm_filters_contacts','
			group_id I4 NOTNULL,
			contact_id I4',
			array('constraints'=>', FOREIGN KEY (group_id) REFERENCES crm_filters_group(id)'));
		if(!$ret){
			print('Unable to create table crm_filters_contacts.<br>');
			return false;
		}
		Base_ThemeCommon::install_default_theme($this -> get_type());
		Base_AclCommon::add_permission(_M('Manage Perspective'),array('ACCESS:employee'));
		return $ret;
	}

	public function uninstall() {
		Base_AclCommon::delete_permission('Manage Perspective');
		$ret = true;
		$ret &= DB::DropTable('crm_filters_contacts');
		$ret &= DB::DropTable('crm_filters_group');
		Base_ThemeCommon::uninstall_default_theme($this -> get_type());
		return $ret;
	}

	public function version() {
		return array("1.0");
	}

	public function requires($v) {
		return array(
			array('name'=>Base_ActionBar::module_name(),'version'=>0),
			array('name'=>Base_Theme::module_name(),'version'=>0),
			array('name'=>Base_LangInstall::module_name(),'version'=>0),
			array('name'=>Base_User_Settings::module_name(),'version'=>0),
			array('name'=>Utils_GenericBrowser::module_name(),'version'=>0),
			array('name'=>CRM_Contacts::module_name(),'version'=>0),
			array('name'=>Libs_QuickForm::module_name(),'version'=>0));
	}

	public static function info() {
		return array(
			'Description'=>'CRM Filters',
			'Author'=>'pbukowski@telaxus.com',
			'License'=>'MIT');
	}

	public static function simple_setup() {
		return 'CRM';
	}

}

?>
