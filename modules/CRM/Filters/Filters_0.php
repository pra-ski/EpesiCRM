<?php
/**
 *
 * @author pbukowski@telaxus.com
 * @copyright pbukowski@telaxus.com
 * @license SPL
 * @version 0.1
 * @package crm-filters
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class CRM_Filters extends Module {
	private $lang;

	public function construct() {
		$this->lang = $this->init_module('Base/Lang');
	}

	public function body() {
		$th = $this->init_module('Base/Theme');
		$display_settings = Base_User_SettingsCommon::get('Base/ActionBar','display');
		$display_icon = ($display_settings == 'both' || $display_settings == 'icons only');
		$display_text = ($display_settings == 'both' || $display_settings == 'text only');
		$th->assign('display_icon',$display_icon);
		$th->assign('display_text',$display_text);

		eval_js_once('crm_filters_deactivate = function(){leightbox_deactivate(\'crm_filters\');}');

		$th->assign('my','<a '.$this->create_callback_href(array($this,'set_profile'),'my').' id="crm_filters_my">'.$this->lang->t('My records').'</a>');
		eval_js('Event.observe(\'crm_filters_my\',\'click\', crm_filters_deactivate)');

		$th->assign('all','<a '.$this->create_callback_href(array($this,'set_profile'),'all').' id="crm_filters_all">'.$this->lang->t('All records').'</a>');
		eval_js('Event.observe(\'crm_filters_all\',\'click\', crm_filters_deactivate)');

		$th->assign('manage','<a '.$this->create_callback_href(array($this,'manage_filters')).' id="crm_filters_manage">'.$this->lang->t('Manage filters').'</a>');
		eval_js('Event.observe(\'crm_filters_manage\',\'click\', crm_filters_deactivate)');

		$ret = DB::Execute('SELECT id,name,description FROM crm_filters_group WHERE user_login_id=%d',array(Acl::get_user()));
		$filters = array();
		while($row = $ret->FetchRow()) {
			$filters[] = array('title'=>$row['name'],'description'=>$row['description'],'open'=>'<a '.$this->create_callback_href(array($this,'set_profile'),$row['id']).' id="crm_filters_'.$row['id'].'">','close'=>'</a>');
			eval_js('Event.observe(\'crm_filters_'.$row['id'].'\',\'click\', crm_filters_deactivate)');
		}
		$th->assign('filters',$filters);

		$qf = $this->init_module('Libs/QuickForm');
		$contacts = CRM_ContactsCommon::get_contacts(array('company_name'=>CRM_ContactsCommon::get_main_company()));
		$contacts_select = array();
		foreach($contacts as $v)
			$contacts_select[$v['id']] = $v['first_name'].' '.$v['last_name'];
		$qf->addElement('select','contact',$this->lang->t('Records of'),$contacts_select,array('onChange'=>$qf->get_submit_form_js().'crm_filters_deactivate()'));
		if($qf->validate()) {
			$c = $qf->exportValue('contact');
			$this->set_module_variable('profile',$c);
			$this->set_module_variable('profile_desc',$contacts_select[$c]);
		}
		$th->assign('contacts',$qf->toHtml());

		ob_start();
		$th->display();
		$profiles_out = ob_get_clean();

		Libs_LeightboxCommon::display('crm_filters',$profiles_out,$this->lang->t('Filters'));
		Base_ActionBarCommon::add('folder','Filters','class="lbOn" rel="crm_filters"',$this->get_module_variable('profile_desc',$this->lang->t('My records')));
	}
	
	public function manage_filters() {
		$x = ModuleManager::get_instance('/Base_Box|0');
		if(!$x) trigger_error('There is no base box module instance',E_USER_ERROR);
		$x->push_main($this->get_type(),'edit');
	}

	public function set_profile($prof) {
		if(is_numeric($prof)) {
			DB::Execute('DELETE FROM crm_filters_contacts WHERE (SELECT cd.value FROM contact_data cd WHERE cd.contact_id=contact_id AND cd.field=\'Company Name\')!=%d',CRM_ContactsCommon::get_main_company());
			$c = DB::GetCol('SELECT p.contact_id FROM crm_filters_contacts p WHERE p.group_id=%d',array($prof));
			if($c)
				$ret = implode(',',$c);
			else
				$ret = '-1';
			$this->set_module_variable('profile_desc',DB::GetOne('SELECT name FROM crm_filters_group WHERE id=%d',array($prof)));
		} elseif($prof=='my') {
			$this->set_module_variable('profile',CRM_FiltersCommon::get_my_profile());
			$this->set_module_variable('profile_desc',$this->lang->t('My records'));
		} else {//all and undefined
			$contacts = Utils_RecordBrowserCommon::get_records('contact', array('company_name'=>CRM_ContactsCommon::get_main_company()));
			$contacts_select = array();
			foreach($contacts as $v)
				$contacts_select[] = $v['id'];
			if($contacts_select)
				$ret = implode(',',$contacts_select);
			else
				$ret = '-1';

			$this->set_module_variable('profile_desc',$this->lang->t('All records'));
		}
		$this->set_module_variable('profile',$ret);
	}
	
	public function get() {
		if(!$this->isset_module_variable('profile'))
			$this->set_module_variable('profile',CRM_FiltersCommon::get_my_profile());
		$ret = $this->get_module_variable('profile');
		return '('.$ret.')';
	}

	public function edit() {
		Base_ActionBarCommon::add('add',$this->lang->ht('Add group'),$this->create_callback_href(array($this,'edit_group')));

		$gb = $this->init_module('Utils/GenericBrowser',null,'edit');

		$gb->set_table_columns(array(
				array('name'=>$this->lang->t('Name'), 'width'=>20, 'order'=>'g.name'),
				array('name'=>$this->lang->t('Description'), 'width'=>30, 'order'=>'g.description'),
				array('name'=>$this->lang->t('Users in category'), 'width'=>50, 'order'=>'')
				));

		$ret = $gb->query_order_limit('SELECT g.name,g.id,g.description FROM crm_filters_group g WHERE g.user_login_id='.Acl::get_user(),'SELECT count(g.id) FROM crm_filters_group g WHERE g.user_login_id='.Acl::get_user());
		while($row = $ret->FetchRow()) {
			$gb_row = & $gb->get_new_row();
			$gb_row->add_action($this->create_confirm_callback_href($this->lang->ht('Delete this group?'),array('CRM_Filters','delete_group'), $row['id']),'Delete');
			$gb_row->add_action($this->create_callback_href(array($this,'edit_group'),$row['id']),'Edit');
			$users = DB::GetCol('SELECT '.DB::Concat('(SELECT aa.value FROM contact_data aa WHERE aa.contact_id=c.contact_id AND aa.field=\'First Name\')','\' \'','(SELECT aa.value FROM contact_data aa WHERE aa.contact_id=c.contact_id AND aa.field=\'Last Name\')').' FROM crm_filters_contacts c WHERE c.group_id=%d',array($row['id']));
			$gb_row->add_data($row['name'], $row['description'], implode(', ',$users));
		}

		$this->display_module($gb);
	}

	public function edit_group($id=null) {
		if($this->is_back()) return false;

		$form = $this->init_module('Libs/QuickForm', null, 'edit_group');
		if(isset($id)) {
			$name = DB::GetOne('SELECT name FROM crm_filters_group WHERE id=%d',array($id));
			$form->addElement('header',null,$this->lang->t('Edit group "%s"',array($name)));

			$contacts_def = DB::GetCol('SELECT contact_id FROM crm_filters_contacts WHERE group_id=%d',array($id));

			$form->setDefaults(array('name'=>$name,'contacts'=>$contacts_def));
		} else
			$form->addElement('header',null,$this->lang->t('New group'));
		$form->addElement('text','name',$this->lang->t('Name'));
		$form->addElement('text','description',$this->lang->t('Description'));
		$form->addRule('name',$this->lang->t('Max length of field exceeded'),'maxlength',128);
		$form->addRule('description',$this->lang->t('Max length of field exceeded'),'maxlength',256);
		$form->addRule('name',$this->lang->t('Field required'),'required');
		$form->registerRule('unique','callback','check_group_name_exists', 'CRM_Filters');
		$form->addRule(array('name','description'),$this->lang->t('Group with this name and description already exists'),'unique',$id);
		$contacts = CRM_ContactsCommon::get_contacts(array('company_name'=>CRM_ContactsCommon::get_main_company()));
		$contacts_select = array();
		foreach($contacts as $v)
			$contacts_select[$v['id']] = $v['first_name'].' '.$v['last_name'];
		$form->addElement('multiselect', 'contacts', $this->lang->t('People'), $contacts_select);
		if ($form->validate()) {
			$v = $form->exportValues();
			if(isset($id)) {
				if($v['name']!=$name)
					DB::Execute('UPDATE crm_filters_group SET name=%s,description=%s WHERE id=%d',array($v['name'],$v['description'],$id));
				DB::Execute('DELETE FROM crm_filters_contacts WHERE group_id=%d',array($id));
			} else {
				DB::Execute('INSERT INTO crm_filters_group(name,description,user_login_id) VALUES(%s,%s,%d)',array($v['name'],$v['description'],Acl::get_user()));
				$id = DB::Insert_ID('crm_filters_group','id');
			}

			foreach($v['contacts'] as $p)
				DB::Execute('INSERT INTO crm_filters_contacts(group_id,contact_id) VALUES(%d,%d)',array($id,$p));

			return false;
		} else {
			Base_ActionBarCommon::add('save','Save',$form->get_submit_form_href());
			Base_ActionBarCommon::add('back','Cancel',$this->create_back_href());

			$rb1 = $this->pack_module('Utils/RecordBrowser/RecordPicker', array('contact' ,'contacts',array('CRM_Filters','edit_group_sel'), array('company_name'=>CRM_ContactsCommon::get_main_company())));
			Base_ActionBarCommon::add('folder','Detailed selection',$rb1->create_open_href(false));

			$form->display();
		}

		return true;
	}

	public static function edit_group_sel($id) {
		return $id;
	}

	public static function delete_group($id) {
		DB::Execute('DELETE FROM crm_filters_contacts WHERE group_id=%d',array($id));
		DB::Execute('DELETE FROM crm_filters_group WHERE id=%d',array($id));
	}

	public static function check_group_name_exists($name,$id) {
		if(isset($id))
			return (DB::GetOne('SELECT id FROM crm_filters_group WHERE id!=%d AND name=%s AND description=%s',array($id,$name[0],$description[1]))===false);
		else
			return (DB::GetOne('SELECT id FROM crm_filters_group WHERE name=%s AND description=%s',array($name[0],$name[1]))===false);
	}

}

?>
