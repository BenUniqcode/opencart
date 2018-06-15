<?php

class ModelExtensionModuleWatermark extends Model {

    public function install() {
        $settings = array(
            'module_watermark_status'                => 0,
            'module_watermark_image'                 => 'catalog/logo.png',
            'module_watermark_size_x'                => 280,
            'module_watermark_size_y'                => 20,
            'module_watermark_zoom'                  => 0.5,
            'module_watermark_pos_x'                 => -20,
            'module_watermark_pos_x_center'          => 0,
            'module_watermark_pos_y'                 => -20,
            'module_watermark_pos_y_center'          => 0,
            'module_watermark_opacity'               => 0.8,
            'module_watermark_resize_first'          => 1,
            'module_watermark_category_image'        => 0,
            'module_watermark_product_thumb'         => 0,
            'module_watermark_product_popup'         => 1,
            'module_watermark_product_list'          => 0,
            'module_watermark_product_additional'    => 0,
            'module_watermark_product_related'       => 0,
            'module_watermark_product_in_compare'    => 0,
            'module_watermark_product_in_wish_list'  => 0,
            'module_watermark_product_in_cart'       => 0,
            );

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_watermark', $settings);
    }

    public function uninstall() {
    }
}
