<?php
class ControllerAccountCommon extends Controller {
	public function index() {

		$this->load->language('account/catalog');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/catalog');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_newsletter'),
			'href' => $this->url->link('account/common', '', true)
		);

		$data['action'] = $this->url->link('account/common', '', true);

		$data['newsletter'] = $this->customer->getNewsletter();

		$data['back'] = $this->url->link('account/account', '', true);
                
                $data['price'] = $this->url->link('account/price');
                $data['brand'] = $this->url->link('account/brand');
                $data['catalog'] = $this->url->link('account/catalog');
                $data['export_all'] = $this->url->link('account/export_all');
                $data['export_special'] = $this->url->link('account/export_special');
                $data['api'] = $this->url->link('account/api');
                $data['upc'] = $this->url->link('account/upc');
                $data['account'] = $this->url->link('account/account');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['new'] = $this->url->link('account/new');
                $data['total'] = $this->url->link('account/total');
                $data['listed'] = $this->url->link('account/listed');
                $data['not_listed'] = $this->url->link('account/not_listed');
                $data['monitor'] = $this->url->link('account/monitor');
                $data['ship'] = $this->url->link('account/ship');
                $data['order_dashboard'] = $this->url->link('account/order_dashboard');
                $data['orders_total'] = $this->url->link('account/orders_total');
                $data['pending'] = $this->url->link('account/pending');
                $data['awaiting_payment'] = $this->url->link('account/awaiting_payment');
                $data['processing'] = $this->url->link('account/processing');
                $data['odr_shipped'] = $this->url->link('account/odr_shipped');
                $data['cancelled'] = $this->url->link('account/cancelled');
                
                
		return $this->load->view('account/common', $data);
	}
}