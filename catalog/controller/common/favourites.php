<?php
class ControllerCommonFavourites extends Controller {
	public function index() {
		$this->load->language('common/cart');

		$data['cus_logged'] = $this->customer->isLogged();

		$this->load->model('seller/seller');

		$this->load->model('tool/image');
		$data['image_resize'] = $this->model_tool_image;

		$data['store_favourites_front'] = $this->model_seller_seller->getstore_favourites_front($this->customer->getID());
		//print_r(count($data['store_favourites_front']));

		return $this->load->view('common/store_favourites', $data);
	}

	public function info() {
		$this->response->setOutput($this->index());
	}
}
