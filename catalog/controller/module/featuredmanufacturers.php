<?php
class ControllerModuleFeaturedmanufacturers extends Controller {
	protected function index($setting) {
		$this->language->load('module/featuredmanufacturers');
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');

		$this->data['manufacturers'] = array();

		if ( $this->config->has('featured_manufacturer') ) {
			$manufacturers = explode(",", $this->config->get('featured_manufacturer'));
			if ( count($manufacturers) > 0 && $this->config->get('featured_manufacturer') != '' ) {
				if (!empty($setting['limit'])) {
					$manufacturers = array_slice($manufacturers, 0, $setting['limit']);
				}

				$this->data['heading_title'] = $this->language->get('heading_title');


				foreach ($manufacturers as $manufacturer) {
					$result = $this->model_catalog_manufacturer->getManufacturer($manufacturer);

					if ( version_compare( VERSION, '1.5.3.1', '>') ) {
						$href = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']);
					} else {
						$href = $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $result['manufacturer_id']);
					}

					$this->data['manufacturers'][] = array(
						'manufacturer_id' => $result['manufacturer_id'],
						'name'            => $result['name'],
						'image'           => $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']),
						'href'            => $href,
					);
				}

				if ( $this->config->has('featured_manufacturer_has_link') ) {
					$this->data['featured_manufacturer_has_link'] = $this->config->get('featured_manufacturer_has_link');
				} else {
					$this->data['featured_manufacturer_has_link'] = 1;
				}

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/featuredmanufacturers.tpl')) {
					$this->template = $this->config->get('config_template') . '/template/module/featuredmanufacturers.tpl';
				} else {
					$this->template = 'default/template/module/featuredmanufacturers.tpl';
				}

				$this->render();
			}
		}
  	}
}
?>
