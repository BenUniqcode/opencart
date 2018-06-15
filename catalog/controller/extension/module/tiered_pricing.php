<?php
require_once(DIR_SYSTEM . 'library/equotix/tiered_pricing/equotix.php');
class ControllerExtensionModuleTieredPricing extends Equotix {
	protected $code = 'tiered_pricing';
	protected $extension_id = '78';
	
	public function eventPreViewProductProduct($route, &$data) {
		if (isset($this->request->get['product_id']) && $this->validated()) {
			$this->load->language('extension/module/tiered_pricing');
			
			$data['text_category_discount'] = $this->language->get('text_category_discount');
			$data['text_all_discount'] = $this->language->get('text_all_discount');
			
			$this->load->model('catalog/product');
			
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			
			if ($product_info) {
				$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

				$data['discounts'] = array();

				foreach ($discounts as $discount) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount_category WHERE product_discount_id = '" . (int)$discount['product_discount_id'] . "'");
					
					if ($query->num_rows) {
						$category = $this->model_catalog_category->getCategory($query->row['category_id']);
					}
					
					$data['discounts'][] = array(
						'quantity' => $discount['quantity'],
						'category' => ($query->num_rows && $query->row['category_id'] == -1) ? '-1' : ($query->num_rows && $query->row['category_id'] ? $category['name'] : ''),
						'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
					);
				}
			}
		}
	}
	
	public function eventPreViewProductCategory($route, &$data) {
		$this->load->language('extension/module/tiered_pricing');
		
		$this->load->model('catalog/product');
		
		$data['text_discount'] = $this->language->get('text_discount');
				
		foreach ($data['products'] as $key => $product) {
			$product_info = $this->model_catalog_product->getProduct($product['product_id']);
		
			$discounts = array();
			
			$discount = $this->model_catalog_product->getProductDiscounts($product['product_id']);
			
			foreach ($discount as $result_1) {
				$tiered_price = $this->currency->format($this->tax->calculate($result_1['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				
				$discounts[] = array(
					'quantity' => $result_1['quantity'],
					'price'    => $tiered_price
				);
			}
			
			$data['products'][$key]['discounts'] = $discounts;
		}
	}
}