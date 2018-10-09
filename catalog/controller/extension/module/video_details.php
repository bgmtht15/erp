<?php
class ControllerExtensionModuleVideoDetails extends Controller {
	public function index($setting) {
		$module = rand();
		$this->load->language('extension/module/video_details');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();
		$data['module'] = $module++;
		$data['moduleName'] = 'Tin tức - Sự kiện khác';

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$filter_data = array(
			'filter_category_id' => $setting['parent_id'],
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'product_id' => $product_id
		);

		$results = $this->model_catalog_product->getNewsTitle($filter_data);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'name'        => $result['name'],
					'thumb'       => $image,
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					'date_added' => $result['date_added']
				);
			}

			return $this->load->view('extension/module/video_details', $data);
		}
	}
}
