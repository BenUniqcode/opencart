<?php

/*
    Module: Watermark for OpenCart 3.x.x.x
    Author: 
    Version: 3.0
    Date: Y.2017 M.09 D.17
    Filelist:

        /admin/controller/module/watermark.php
        /admin/language/english/module/watermark.php
        /admin/model/module/watermark.php
        /admin/view/template/module/watermark.twig

        /image/catalog/watermark_default.png

        /catalog/model/module/watermark/image.watermark.php

        /system/watermark.ocmod.xml

    Modif:

        /catalog/model/tool/image.php

        /module/featured.php        
        /module/bestseller.php      
        /module/ebay_listing.php    
        /module/carousel.php        
        /module/latest.php          
        /module/special.php         
        /module/slideshow.php       
        /module/banner.php          

        /feed/google_sitemap.php    
        /feed/google_base.php       

        /product/product.php        
        /product/special.php        
        /product/manufacturer.php   
        /product/search.php         
        /product/compare.php        
        /product/category.php       

        /common/cart.php            
        /account/wishlist.php       
        /payment/pp_express.php     
        /checkout/cart.php          
*/

class ControllerExtensionModuleWatermark extends Controller {

    private $error = array();

    public function index() {

        $this->load->language('extension/module/watermark');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/watermark');
        $this->load->model('setting/setting');

        if( $this->request->post )
        {
            //We need image size
            if (true || is_file(DIR_IMAGE . $this->request->post['module_watermark_image'])){
                $info = getimagesize(DIR_IMAGE . $this->request->post['module_watermark_image']);
            } else {
                $info = [0,0];
                $this->request->post['module_watermark_image'] = '';
            }
            $this->request->post['module_watermark_size_x'] = $info[0];
            $this->request->post['module_watermark_size_y'] = $info[1];

            $this->model_setting_setting->editSetting('module_watermark', $this->request->post);
            $this->error['success'] = $this->language->get('text_success');
            // reset cache
            $dir = DIR_IMAGE . 'cache/catalog';
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                         RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if (!$file->isDir()){
                    unlink($file->getRealPath());
                }
            }
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
       }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['success'])) {
            $data['error_success'] = $this->error['success'];
        } else {
            $data['error_success'] = '';
        }

        $data['heading_title'] = $this->language->get('heading_title_for_opencart');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/watermark', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );
        
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data += $this->model_setting_setting->getSetting('module_watermark');

        $this->load->model('tool/image');
        $data['thumb'] = $this->model_tool_image->resize($data['module_watermark_image'], 100, 100);
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        if (!$data['thumb'])
            $data['thumb'] = $data['placeholder'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/watermark', $data));
    }

    public function install() {

        $this->load->model('extension/module/watermark');
        $this->model_extension_module_watermark->install();

    }

    public function uninstall() {

        $this->load->model('extension/module/watermark');
        $this->model_extension_module_watermark->uninstall();

    }

}
