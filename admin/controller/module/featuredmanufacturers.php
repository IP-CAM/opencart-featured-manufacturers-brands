<?php
class ControllerModuleFeaturedManufacturers extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/featuredmanufacturers');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('featured_manufacturers', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');

		$this->data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$this->data['entry_limit'] = $this->language->get('entry_limit');
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_link_to_page'] = $this->language->get('entry_link_to_page');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/featuredmanufacturers', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('module/featuredmanufacturers', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->post['featured_manufacturer'])) {
			$this->data['featured_manufacturer'] = $this->request->post['featured_manufacturer'];
		} else {
			$this->data['featured_manufacturer'] = $this->config->get('featured_manufacturer');
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['featured_manufacturer'])) {
			$manufacturers = explode(',', $this->request->post['featured_manufacturer']);
		} else if ( $this->config->has('featured_manufacturer') ) {
			$manufacturers = explode(',', $this->config->get('featured_manufacturer'));
		} else {
			$manufacturers = array();
		}

		if (isset($this->request->post['featured_manufacturer_has_link'])) {
			$this->data['featured_manufacturer_has_link'] = $this->request->post['featured_manufacturer_has_link'];
		} else if ( $this->config->has('featured_manufacturer_has_link') ) {
			$this->data['featured_manufacturer_has_link'] = $this->config->get('featured_manufacturer_has_link');
		} else {
			$this->data['featured_manufacturer_has_link'] = 1;
		}

		$this->data['manufacturers'] = array();

		foreach ($manufacturers as $manufacturer_id) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

			if ($manufacturer_info) {
				$this->data['manufacturers'][] = array(
					'manufacturer_id' => $manufacturer_info['manufacturer_id'],
					'name'       => $manufacturer_info['name']
				);
			}
		}

		$this->data['modules'] = array();

		if (isset($this->request->post['featuredmanufacturers_module'])) {
			$this->data['modules'] = $this->request->post['featuredmanufacturers_module'];
		} elseif ($this->config->get('featuredmanufacturers_module')) {
			$this->data['modules'] = $this->config->get('featuredmanufacturers_module');
		}

		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/featuredmanufacturers.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/featuredmanufacturers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['featuredmanufacturers_module'])) {
			foreach ($this->request->post['featuredmanufacturers_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function autocomplete() {
		$json = array();
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/featuredmanufacturers');

			$filter_name = $this->request->get['filter_name'];
			$limit = 20;

			$data = array(
				'filter_name'         => $filter_name,
				'start'               => 0,
				'limit'               => $limit,
			);

			$results = $this->model_catalog_featuredmanufacturers->getManufacturers($data);

			foreach ($results as $result) {
				$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'       => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
				);
			}
		}
		$this->response->setOutput(json_encode($json));
	}
}
?>
