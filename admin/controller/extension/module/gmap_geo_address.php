<?php
class ControllerExtensionModuleGmapGeoAddress extends Controller {
    private $error = array(); 
 
    public function index() {
        $this->load->language('extension/module/gmap_geo_address');
     
        $this->document->setTitle($this->language->get('heading_title'));
        
        /* Add Neccessary Javascript and CSS files */
        $this->document->addStyle('view/javascript/jquery/gmap-geocomplete/jquery.geocomplete.css');
        $this->document->addScript('http://maps.googleapis.com/maps/api/js?key=AIzaSyDHO-IbrLAPBmBcEyKsPELyAiLKNhWU6j0&libraries=places'); // replace api key with yours, if you wish
        $this->document->addScript('view/javascript/jquery/gmap-geocomplete/jquery.geocomplete.js');
        
        $this->load->model('setting/setting');
     
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_gmap_geo_address', $this->request->post);
     
            $this->session->data['success'] = $this->language->get('text_success');
     
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }
     
        $data['heading_title'] = $this->language->get('heading_title');
     
        $data['text_edit']    = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
     
        $data['entry_map_address'] = $this->language->get('entry_map_address');
        $data['entry_map_latitude'] = $this->language->get('entry_map_latitude');
        $data['entry_map_longitude'] = $this->language->get('entry_map_longitude');
        $data['entry_status'] = $this->language->get('entry_status');
     
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
         
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        
        if (isset($this->error['map_address'])) {
            $data['error_map_address'] = $this->error['map_address'];
        } else {
            $data['error_map_address'] = '';
        } 
     
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
        );
        
        $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/module/gmap_geo_address', 'user_token=' . $this->session->data['user_token'], true)
        );
          
        $data['action'] = $this->url->link('extension/module/gmap_geo_address', 'user_token=' . $this->session->data['user_token'], true); 
     
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
          
        if (isset($this->request->post['module_gmap_geo_address_status'])) {
            $data['module_gmap_geo_address_status'] = $this->request->post['module_gmap_geo_address_status'];
        } else {
            $data['module_gmap_geo_address_status'] = $this->config->get('module_gmap_geo_address_status');
        }
        
        if (isset($this->request->post['module_gmap_geo_address_map_address'])) {
            $data['module_gmap_geo_address_map_address'] = $this->request->post['module_gmap_geo_address_map_address'];
        } else {
            $data['module_gmap_geo_address_map_address'] = $this->config->get('module_gmap_geo_address_map_address');
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/gmap_geo_address', $data));

    }

    protected function validate() {
 
        if (!$this->user->hasPermission('modify', 'extension/module/gmap_geo_address')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
 
        if (!$this->request->post['module_gmap_geo_address_map_address']) {
            $this->error['map_address'] = $this->language->get('error_map_address');
        }
 
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
