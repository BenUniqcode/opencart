<?php
class ControllerExtensionModuleadmprdfilcatmanu extends Controller {	
	private $error = array();
	private $modpath = 'module/admprdfilcatmanu'; 
	private $modtpl = 'module/admprdfilcatmanu.tpl';
	private $modname = 'admprdfilcatmanu';
	private $modtext = 'Admin Product Filter By Category-Manufacturer';
	private $modid = '28131';
	private $modssl = 'SSL';
	private $modemail = 'opencarttools@gmail.com';
	private $token_str = '';
	private $modurl = 'extension/module';
	private $modurltext = '';

	public function __construct($registry) {
		parent::__construct($registry);
 		
		if(substr(VERSION,0,3)>='3.0' || substr(VERSION,0,3)=='2.3') { 
			$this->modtpl = 'extension/module/admprdfilcatmanu';
			$this->modpath = 'extension/module/admprdfilcatmanu';
		} else if(substr(VERSION,0,3)=='2.2') {
			$this->modtpl = 'module/admprdfilcatmanu';
		} 
		
		if(substr(VERSION,0,3)>='3.0') { 
			$this->modname = 'module_admprdfilcatmanu';
			$this->modurl = 'marketplace/extension'; 
			$this->token_str = 'user_token=' . $this->session->data['user_token'] . '&type=module';
		} else if(substr(VERSION,0,3)=='2.3') {
			$this->modurl = 'extension/extension';
			$this->token_str = 'token=' . $this->session->data['token'] . '&type=module';
		} else {
			$this->token_str = 'token=' . $this->session->data['token'];
		}
		
		if(substr(VERSION,0,3)>='3.0' || substr(VERSION,0,3)=='2.3' || substr(VERSION,0,3)=='2.2') { 
			$this->modssl = true;
		} 
 	} 
	
	public function index() {
		$data = $this->load->language($this->modpath);
		$this->modurltext = $this->language->get('text_extension');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting($this->modname, $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if(! (isset($this->request->post['svsty']) && $this->request->post['svsty'] == 1)) {
				$this->response->redirect($this->url->link($this->modurl, $this->token_str, $this->modssl));
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
 		$data['entry_status'] = $this->language->get('entry_status');
  		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_str, $this->modssl)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->modurltext,
			'href' => $this->url->link($this->modurl, $this->token_str, $this->modssl)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->modpath, $this->token_str, $this->modssl)
		);

		$data['action'] = $this->url->link($this->modpath, $this->token_str, $this->modssl);
		
		$data['cancel'] = $this->url->link($this->modurl, $this->token_str , $this->modssl); 
		
		if(substr(VERSION,0,3)>='3.0') { 
			$data['user_token'] = $this->session->data['user_token'];
		} else {
			$data['token'] = $this->session->data['token'];
		}
   
		$data[$this->modname.'_status'] = $this->setvalue($this->modname.'_status');	
 
  		$data['modname'] = $this->modname;
		$data['modemail'] = $this->modemail;
  		  
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->modtpl, $data));
	}
	
	protected function setvalue($postfield) {
		if (isset($this->request->post[$postfield])) {
			$postfield_value = $this->request->post[$postfield];
		} else {
			$postfield_value = $this->config->get($postfield);
		} 	
 		return $postfield_value;
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->modpath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	
	
	public function install() {
		@mail($this->modemail,
		"Extension Installed",
		"Hello!" . "\r\n" .  
		"Extension Name :  ".$this->modtext."" ."\r\n". 
		"Extension ID : ".$this->modid ."\r\n". 
		"Version : " . VERSION. "\r\n". 
		"Installed At : " .HTTP_CATALOG ."\r\n". 
		"Licence Start Date : " .date("Y-m-d") ."\r\n".  
		"Licence Expiry Date : " .date("Y-m-d", strtotime('+1 year'))."\r\n". 
		"From: ".$this->config->get('config_email'),
		"From: ".$this->config->get('config_email'));      
	}
	public function uninstall() { 
		@mail($this->modemail,
		"Extension Uninstalled",
		"Hello!" . "\r\n" .  
		"Extension Name :  ".$this->modtext."" ."\r\n". 
		"Extension ID : ".$this->modid ."\r\n". 
		"Version : " . VERSION. "\r\n". 
		"Installed At : " .HTTP_CATALOG ."\r\n". 
		"Licence Start Date : " .date("Y-m-d") ."\r\n".  
		"Licence Expiry Date : " .date("Y-m-d", strtotime('+1 year'))."\r\n". 
		"From: ".$this->config->get('config_email'),
		"From: ".$this->config->get('config_email'));        
	}
}