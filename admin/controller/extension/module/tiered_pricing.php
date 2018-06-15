<?php
require_once(DIR_SYSTEM . 'library/equotix/tiered_pricing/equotix.php');
class ControllerExtensionModuleTieredPricing extends Equotix {
	protected $version = '2.0.0';
	protected $code = 'tiered_pricing';
	protected $extension = 'Tiered Pricing';
	protected $extension_id = '78';
	protected $purchase_url = 'tiered-pricing';
	protected $purchase_id = '20568';
	protected $error = array();
	
	public function index() {
		$this->load->language('extension/module/tiered_pricing');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_message'] = $this->language->get('text_message');
		$data['text_congratulations'] = $this->language->get('text_congratulations');
		
		$data['tab_general'] = $this->language->get('tab_general');
		
		$data['button_cancel'] = $this->language->get('button_cancel');

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/tiered_pricing', 'user_token=' . $this->session->data['user_token'], true)
   		);
		
		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->generateOutput('extension/module/tiered_pricing', $data);
	}
	
	public function install() {
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			return;
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_discount_category` (
			  `product_discount_id` int(11) NOT NULL,
			  `product_id` int(11) NOT NULL,
			  `category_id` int(11) NOT NULL,
			  PRIMARY KEY (`product_discount_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;
		");
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->addEvent('module_tiered_pricing', 'admin/view/catalog/product_form/before', 'extension/module/tiered_pricing/eventPreViewCatalogProductForm');
		$this->model_setting_event->addEvent('module_tiered_pricing', 'catalog/view/product/product/before', 'extension/module/tiered_pricing/eventPreViewProductProduct');
		$this->model_setting_event->addEvent('module_tiered_pricing', 'catalog/view/product/category/before', 'extension/module/tiered_pricing/eventPreViewProductCategory');
		$this->model_setting_event->addEvent('module_tiered_pricing', 'catalog/view/product/search/before', 'extension/module/tiered_pricing/eventPreViewProductCategory');
		$this->model_setting_event->addEvent('module_tiered_pricing', 'catalog/view/product/manufacturer/before', 'extension/module/tiered_pricing/eventPreViewProductCategory');
		$this->model_setting_event->addEvent('module_tiered_pricing', 'catalog/view/product/special/before', 'extension/module/tiered_pricing/eventPreViewProductCategory');
	}
	
	public function uninstall() {
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			return;
		}
		
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "option_popup`");
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('module_tiered_pricing');
	}
	
	public function eventPreViewCatalogProductForm($route, &$data) {
		$this->load->language('extension/module/tiered_pricing');
	
		$this->load->model('catalog/product');
    $this->load->model('catalog/category');
    
    $categoriesData = array(
      'sort' => 'name'
    );
	
		$data['categories'] = $this->model_catalog_category->getCategories($categoriesData);
		
		$data['entry_applies_to'] = $this->language->get('entry_applies_to');
		$data['text_all_category'] = $this->language->get('text_all_category');
		$data['text_this_product_only'] = $this->language->get('text_this_product_only');
		
		if (isset($this->request->post['product_discount'])) {
			$product_discounts = $this->request->post['product_discount'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);
		} else {
			$product_discounts = array();
		}

		$data['product_discounts'] = array();

		foreach ($product_discounts as $product_discount) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount_category WHERE product_discount_id = '" . (int)$product_discount['product_discount_id'] . "'");
		
			$data['product_discounts'][] = array(
				'customer_group_id' => $product_discount['customer_group_id'],
				'category_id'		=> $query->num_rows ? $query->row['category_id'] : 0,
				'quantity'          => $product_discount['quantity'],
				'priority'          => $product_discount['priority'],
				'price'             => $product_discount['price'],
				'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
			);
		}
	}
}