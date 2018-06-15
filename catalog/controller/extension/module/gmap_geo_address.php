<?php
class ControllerExtensionModuleGmapGeoAddress extends Controller {
    public function index() {
        $this->load->language('extension/module/gmap_geo_address');
         
        $data['heading_title'] = $this->language->get('heading_title'); // set the heading_title of the module
        
        $data['entry_map_address'] = $this->language->get('entry_map_address');
        $data['entry_map_geo_address'] = $this->language->get('entry_map_geo_address');
        
        /* Get Map Address Details */
        $data['gmap_geo_address'] = $this->config->get('module_gmap_geo_address_map_address');     
         
        /* Add Neccessary Javascript and CSS files */
        $this->document->addStyle('catalog/view/javascript/jquery/gmap-geocomplete/jquery.geocomplete.css');
        $this->document->addScript('http://maps.googleapis.com/maps/api/js?key=AIzaSyDHO-IbrLAPBmBcEyKsPELyAiLKNhWU6j0&libraries=places'); // replace api key with yours, if you wish
        $this->document->addScript('catalog/view/javascript/jquery/gmap-geocomplete/jquery.geocomplete.js');
        
        return $this->load->view('extension/module/gmap_geo_address', $data);
    }
    
}

