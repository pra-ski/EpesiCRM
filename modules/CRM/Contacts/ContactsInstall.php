<?php
/**
 * CRMHRInstall class.
 *
 * This class provides initialization data for CRMHR module.
 *
 * @author Kuba SĹawiĹski <ruud@o2.pl>, Arkadiusz Bisaga <abisaga@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @version 0.9
 * @package tcms-extra
 */
defined("_VALID_ACCESS") || die();

/**
 * This class provides initialization data for Test module.
 * @package tcms-extra
 * @subpackage test
 */
class CRM_ContactsInstall extends ModuleInstall {
	public function install() {
// ************ contacts ************** //
		Base_ThemeCommon::install_default_theme('CRM/Contacts');
		$fields = array(
			array('name'=>'Login', 'type'=>'integer', 'required'=>false, 'param'=>'64', 'extra'=>false, 'display_callback'=>array('CRM_ContactsCommon', 'display_login'), 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_login')),
			array('name'=>'First Name', 'type'=>'text', 'required'=>true, 'param'=>'64', 'extra'=>false, 'visible'=>true),
			array('name'=>'Last Name', 'type'=>'text', 'required'=>true, 'param'=>'64', 'extra'=>false, 'visible'=>true),
			array('name'=>'Address 1', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false),
			array('name'=>'Address 2', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false),
			array('name'=>'Email', 'type'=>'text', 'required'=>false, 'param'=>'128', 'extra'=>false, 'visible'=>false, 'display_callback'=>array('CRM_ContactsCommon', 'display_email'), 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_email')),
			array('name'=>'City', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false, 'visible'=>true),
			array('name'=>'Country', 'type'=>'commondata', 'required'=>true, 'param'=>array('Countries'), 'extra'=>false, 'visible'=>true, 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_country')),
			array('name'=>'Zone', 'type'=>'commondata', 'required'=>false, 'param'=>array('Countries','Country'), 'extra'=>false, 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_zone')),
			array('name'=>'Postal Code', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false),
			array('name'=>'Phone', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false, 'visible'=>true),
			array('name'=>'Fax', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false),
			array('name'=>'Web address', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false, 'display_callback'=>array('CRM_ContactsCommon', 'display_webaddress'), 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_webaddress')),
			array('name'=>'Company Name', 'type'=>'multiselect', 'required'=>false, 'param'=>array('company'=>'Company Name'), 'extra'=>false, 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_company')),
			array('name'=>'Group', 'type'=>'multiselect', 'required'=>false, 'param'=>'Contacts_groups', 'extra'=>false)
		);
		Utils_RecordBrowserCommon::install_new_recordset('contact', $fields);
		Utils_RecordBrowserCommon::set_tpl('contact', Base_ThemeCommon::get_template_filename('CRM/Contacts', 'Contact'));
		Utils_RecordBrowserCommon::set_processing_method('contact', array('CRM_ContactsCommon', 'submit_contact'));
		Utils_RecordBrowserCommon::new_filter('contact', 'Company');
		Utils_RecordBrowserCommon::set_quickjump('contact', 'Last Name');
		Utils_RecordBrowserCommon::set_favorites('contact', true);
		Utils_RecordBrowserCommon::set_recent('contact', 15);
		Utils_RecordBrowserCommon::set_caption('contact', 'Contacts');
//		Utils_RecordBrowserCommon::set_tpl('contact', Base_ThemeCommon::get_template_filename('CRM/Contacts', 'View_entry'));
// ************ companies ************** //
		$fields = array(
			array('name'=>'Company Name', 'type'=>'text', 'required'=>true, 'param'=>'64', 'extra'=>false, 'visible'=>true),
			array('name'=>'Short Name', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false, 'visible'=>false),
			array('name'=>'Address 1', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false),
			array('name'=>'Address 2', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false),
			array('name'=>'City', 'type'=>'text', 'required'=>true, 'param'=>'64', 'extra'=>false, 'visible'=>true),
			array('name'=>'Country', 'type'=>'commondata', 'required'=>true, 'param'=>array('Countries'), 'extra'=>false, 'visible'=>true, 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_country')),
			array('name'=>'Zone', 'type'=>'commondata', 'required'=>false, 'param'=>array('Countries','Country'), 'extra'=>false, 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_zone')),
			array('name'=>'Postal Code', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false),
			array('name'=>'Phone', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false, 'visible'=>true),
			array('name'=>'Fax', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false),
			array('name'=>'Web address', 'type'=>'text', 'required'=>false, 'param'=>'64', 'extra'=>false, 'display_callback'=>array('CRM_ContactsCommon', 'display_webaddress'), 'QFfield_callback'=>array('CRM_ContactsCommon', 'QFfield_webaddress'), 'visible'=>true),
			array('name'=>'Group', 'type'=>'multiselect', 'required'=>false, 'param'=>'Companies_groups', 'extra'=>false)
		);
//		Utils_RecordBrowserCommon::set_tpl('company', Base_ThemeCommon::get_template_filename('CRM/Contacts', 'Company'));
		Utils_RecordBrowserCommon::install_new_recordset('company', $fields);
		Utils_RecordBrowserCommon::set_quickjump('company', 'Name');
		Utils_RecordBrowserCommon::set_favorites('company', true);
		Utils_RecordBrowserCommon::set_recent('company', 15);
		Utils_RecordBrowserCommon::set_caption('company', 'Companies');
// ************ addons ************** //
		Utils_RecordBrowserCommon::new_addon('company', 'CRM/Contacts', 'company_addon', 'Contacts');
		Utils_RecordBrowserCommon::new_addon('company', 'CRM/Contacts', 'company_attachment_addon', 'Notes');
		Utils_RecordBrowserCommon::new_addon('contact', 'CRM/Contacts', 'contact_attachment_addon', 'Notes');
// ************ other ************** //
		Utils_CommonDataCommon::new_array('Companies_Groups',array('Customer','Vendor','Other'));
		Utils_CommonDataCommon::new_array('Contacts_Groups',array('Public','Private','Other'));

		$this->add_aco('view deleted attachments','Employee Manager');
		$this->add_aco('view attachments','Employee');
		$this->add_aco('edit attachments','Employee');
		$this->add_aco('download attachments','Employee');
		return true;
	}

	public function uninstall() {
		Base_ThemeCommon::uninstall_default_theme('CRM/Contacts');
		Utils_RecordBrowserCommon::delete_addon('company', 'CRM/Contacts', 'company_addon');
		Utils_RecordBrowserCommon::delete_addon('company', 'CRM/Contacts', 'company_attachment_addon');
		Utils_RecordBrowserCommon::delete_addon('contact', 'CRM/Contacts', 'contact_attachment_addon');
		Utils_RecordBrowserCommon::uninstall_recordset('company');
		Utils_RecordBrowserCommon::uninstall_recordset('contact');
		Utils_CommonDataCommon::remove('Contacts_Groups');
		$this->del_aco('view deleted attachments');
		$this->del_aco('view attachments');
		$this->del_aco('edit attachments');
		$this->del_aco('download attachments');
		return true;
	}

	public function requires($v) {
		return array(
			array('name'=>'Utils/RecordBrowser', 'version'=>0),
			array('name'=>'Utils/Attachment', 'version'=>0),
			array('name'=>'CRM/Acl', 'version'=>0),
			array('name'=>'Base/Lang', 'version'=>0),
			array('name'=>'Base/Acl', 'version'=>0),
			array('name'=>'Data/Countries', 'version'=>0)
		);
	}

	public function provides($v) {
		return array();
	}

	public static function info() {
		return array('Author'=>'<a href="mailto:kslawinski@telaxus.com">Kuba Sławiński</a> and <a href="mailto:abisaga@telaxus.com">Arkadiusz Bisaga</a> (<a href="http://www.telaxus.com">Telaxus LLC</a>)', 'License'=>'TL', 'Description'=>'Module for organising Your contacts.');
	}

	public static function simple_setup() {
		return true;
	}

	public function version() {
		return array('0.9');
	}

	public static function post_install() {
		$loc = Base_RegionalSettingsCommon::get_default_location();
		$count = DB::GetOne('SELECT count(ul.id) FROM user_login ul');
		$ret = array(array('type'=>'text','name'=>'cname','label'=>'Company name','default'=>'','param'=>array('maxlength'=>64),'rule'=>array(array('type'=>'required','message'=>'Field required'))),
			     array('type'=>'text','name'=>'sname','label'=>'Short company name','default'=>'','param'=>array('maxlength'=>64)),
			);
		if($count==1) {
			$ret[] = array('type'=>'text','name'=>'fname','label'=>'Your first name','default'=>'','param'=>array('maxlength'=>64), 'rule'=>array(array('type'=>'required','message'=>'Field required')));
			$ret[] = array('type'=>'text','name'=>'lname','label'=>'Your last name','default'=>'','param'=>array('maxlength'=>64), 'rule'=>array(array('type'=>'required','message'=>'Field required')));
		}
		return array_merge($ret,array(
			     array('type'=>'text','name'=>'address1','label'=>'Address 1','default'=>'','param'=>array('maxlength'=>64)),
			     array('type'=>'text','name'=>'address2','label'=>'Address 2','default'=>'','param'=>array('maxlength'=>64)),
			     array('type'=>'callback','name'=>'country','func'=>array('CRM_ContactsInstall','country_element'),'default'=>$loc['country']),
			     array('type'=>'callback','name'=>'state','func'=>array('CRM_ContactsInstall','state_element'),'default'=>$loc['state']),
			     array('type'=>'text','name'=>'city','label'=>'City','default'=>'','param'=>array('maxlength'=>64)),
			     array('type'=>'text','name'=>'postal','label'=>'Postal Code','default'=>'','param'=>array('maxlength'=>64)),
			     array('type'=>'text','name'=>'phone','label'=>'Phone','default'=>'','param'=>array('maxlength'=>64)),
			     array('type'=>'text','name'=>'fax','label'=>'Fax','default'=>'','param'=>array('maxlength'=>64)),
			     array('type'=>'text','name'=>'web','label'=>'Web address','default'=>'','param'=>array('maxlength'=>64))
			     ));
	}

	private static $country_elem_name;
	public static function country_element($name, $args, & $def_js) {
		self::$country_elem_name = $name;
		return HTML_QuickForm::createElement('commondata',$name,'Country','Countries');
	}

	public static function state_element($name, $args, & $def_js) {
		return HTML_QuickForm::createElement('commondata',$name,'State',array('Countries',self::$country_elem_name),array('empty_option'=>true));
	}

	public static function post_install_process($val) {
		$comp_id = Utils_RecordBrowserCommon::new_record('company',
			array('company_name'=>$val['cname'],
				'short_name'=>isset($val['sname'])?$val['sname']:'',
				'address_1'=>isset($val['address1'])?$val['address1']:'',
				'address_2'=>isset($val['address2'])?$val['address2']:'',
				'country'=>isset($val['country'])?$val['country']:'',
				'zone'=>isset($val['state'])?$val['state']:'',
				'city'=>isset($val['city'])?$val['city']:'',
				'postal_code'=>isset($val['postal'])?$val['postal']:'',
				'phone'=>isset($val['phone'])?$val['phone']:'',
				'fax'=>isset($val['fax'])?$val['fax']:'',
				'web_address'=>isset($val['web'])?$val['web']:''
				));
		Variable::set('main_company',$comp_id);
		$count = DB::GetOne('SELECT count(ul.id) FROM user_login ul');
		if($count==1) {
			$user = DB::GetRow('SELECT ul.id,up.mail,ul.login FROM user_login ul INNER JOIN user_password up ON up.user_login_id=ul.id');
			$uid = Base_AclCommon::get_acl_user_id($user['login']);
			if($uid !== false) {
				$groups_old = Base_AclCommon::get_user_groups($uid);
				Base_AclCommon::change_privileges($user['login'], array_merge($groups_old,array(Base_AclCommon::get_group_id('Employee Administrator'),Base_AclCommon::get_group_id('Customer Administrator'))));
			}

			Utils_RecordBrowserCommon::new_record('contact',
				array('first_name'=>$val['fname'],
					'last_name'=>$val['lname'],
					'address_1'=>isset($val['address1'])?$val['address1']:'',
					'address_2'=>isset($val['address2'])?$val['address2']:'',
					'country'=>isset($val['country'])?$val['country']:'',
					'zone'=>isset($val['state'])?$val['state']:'',
					'city'=>isset($val['city'])?$val['city']:'',
					'postal_code'=>isset($val['postal'])?$val['postal']:'',
					'phone'=>isset($val['phone'])?$val['phone']:'',
					'fax'=>isset($val['fax'])?$val['fax']:'',
					'web_address'=>isset($val['web'])?$val['web']:'',
					'company_name'=>array($comp_id),
					'login'=>$user['id'],
					'email'=>$user['mail']
					));
		}
	}
}

?>
