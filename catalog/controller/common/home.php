<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}
		$data['logged'] = $this->customer->isLogged();
		// Category
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$data['categories'] = $this->model_catalog_category->getCategories(0);

		//echo "<pre>"; print_r($data['categories']);die;

		/*$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}

				// Level 1
				$data['categories'] = array(
					'name'     => $category['name'],
					'category_id' => $category['category_id'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
			
		}*/
		$this->load->model('selleradvertise/advertise_front');

		$this->load->model('tool/image');

		if (isset($this->request->get['path'])) {
			$category_id = $this->request->get['path'];
		} else {
			$category_id = '';
		}

		if(isset($this->session->data['login_type'])){
			$data['login_type'] = $this->session->data['login_type'];
		} else {
			$data['login_type'] = '';
		}

		$data['advertisement_national'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(2, $category_id,4);
		$data['image_resize'] = $this->model_tool_image;//->resize($data['advertisement_national']['offer_image'], 1324, 182);
		$data['advertisement_state'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(3, $category_id,4);
		$data['advertisement_city'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(4, $category_id,4);
		$data['advertisement_local'] = $this->model_selleradvertise_advertise_front->getAdvertisesFrontLocal(5, $category_id,4);
		

		//echo "<pre>"; print_r($data['advertisement_national']); die;

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['left_menu'] = $this->load->controller('common/left_menu');

		// For seller advertise top banner
		$this->load->model('selleradvertise/advertise_front');

		$this->load->model('tool/image');
		$data['image_resize'] = $this->model_tool_image;//->resize($data['advertisement_national']['offer_image'], 1324, 182);
		$data['advertisement_top_banner'] = $this->model_selleradvertise_advertise_front->getAdvertisesHomeTopBanner();
		$data['search_pst'] = $this->url->link('seller/seller');

		//Home auto search
		//$this->load->model('seller/seller');
		//$data['auto_search_home'] = $this->model_seller_seller->getHomeautoseach();		

		$this->response->setOutput($this->load->view('common/home', $data));
	}

	public function getLoadMore() {	
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}
		$data['logged'] = $this->customer->isLogged();
		// Category
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$data['categories'] = $this->model_catalog_category->getCategories(0);

		
		$this->load->model('selleradvertise/advertise_front');

		$this->load->model('tool/image');

		if (isset($this->request->get['path'])) {
			$category_id = $this->request->get['path'];
		} else {
			$category_id = '';
		}
		$data['image_resize'] = $this->model_tool_image;//->resize($data['advertisement_national']['offer_image'], 1324, 182);
		$id = $this->request->get['id'];
		$data['count'] = $this->request->get['count'];
		if(isset($this->request->get['tab_id']) && $this->request->get['tab_id'] != '') {
			if (isset($this->request->get['tab_id']) && $this->request->get['tab_id'] == 'home') {
				$data['advertisement_national'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(2, $category_id, 6, $data['count']);
			}
			
			if (isset($this->request->get['tab_id']) && $this->request->get['tab_id'] == 'profile') {
				$data['advertisement_state'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(3, $category_id,6, $data['count']);
			}
			if (isset($this->request->get['tab_id']) && $this->request->get['tab_id'] == 'messages') {
				$data['advertisement_city'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(4, $category_id,6, $data['count']);
			}
			if (isset($this->request->get['tab_id']) && $this->request->get['tab_id'] == 'settings') {
				$data['advertisement_local'] = $this->model_selleradvertise_advertise_front->getAdvertisesFrontLocal(5, $category_id,6, $data['count']);
			}
		} else {
			$data['advertisement_national'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(2, $category_id,4);			
			$data['advertisement_state'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(3, $category_id,4);
			$data['advertisement_city'] = $this->model_selleradvertise_advertise_front->getAdvertisesFront(4, $category_id,4);
			$data['advertisement_local'] = $this->model_selleradvertise_advertise_front->getAdvertisesFrontLocal(5, $category_id,4);
		}
		
		$data['tab_id'] = $this->request->get['tab_id'];
		

		//$data['column_left'] = $this->load->controller('common/column_left');
		//$data['column_right'] = $this->load->controller('common/column_right');
		//$data['content_top'] = $this->load->controller('common/content_top');
		//$data['content_bottom'] = $this->load->controller('common/content_bottom');
		////$data['footer'] = $this->load->controller('common/footer');
		//$data['header'] = $this->load->controller('common/header');
		//$data['left_menu'] = $this->load->controller('common/left_menu');
		
		//$this->load->model('selleradvertise/advertise_front');

		//$this->load->model('tool/image');
		//$data['image_resize'] = $this->model_tool_image;//->resize($data['advertisement_national']['offer_image'], 1324, 182);
		//$data['advertisement_top_banner'] = $this->model_selleradvertise_advertise_front->getAdvertisesHomeTopBanner();
		//$data['search_pst'] = $this->url->link('seller/seller');

		$data['logged'] = $this->customer->isLogged();
		
		if(isset($this->session->data['login_type'])){
			$data['login_type'] = $this->session->data['login_type'];
		} else {
			$data['login_type'] = '';
		}

		$this->response->setOutput($this->load->view('common/home_load_more', $data));
	}

}
