<?php
class ControllerExtensionModuleTintuc extends Controller {
	public function index($setting) {
		$module = rand();
		$this->load->language('extension/module/tintuc');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		$categoryId = $setting['parent_id'];
		if (isset($parts[0])) {
			$categoryId = $parts[0];
		}

		$data['products'] = array();
		$data['module'] = $module++;
		$data['moduleName'] = $setting['name'];
		
		$filter_data = array(
			'filter_category_id' => $setting['parent_id'],
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
		);

		$results = $this->model_catalog_product->getNews($filter_data);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_new_description_length')) . '..',
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			


			return $this->load->view('extension/module/tintuc', $data);
		}
	}
}
