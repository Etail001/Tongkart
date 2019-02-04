<?php
class ControllerAccountApiEdit extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/api_edit');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/api_edit');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('account/api_edit');

			$this->model_account_api_edit->editSettings($this->request->post,$this->customer->getId());

			$this->session->data['success'] = 'Settings Succesfully Updated';

			$this->response->redirect($this->url->link('account/api', '', true));
		}

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
			'href' => $this->url->link('account/dashboard', '', true)
		);

		$data['action'] = $this->url->link('account/api_edit', '', true);

		$data['newsletter'] = $this->customer->getNewsletter();

		$data['back'] = $this->url->link('account/api', '', true);
                
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
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['common'] = $this->load->controller('account/common');
                $total_products = $this->model_account_api_edit->getTotalProducts();
                $data['total_product'] = $total_products;
                $total_new_products = $this->model_account_api_edit->getTotalNewProducts();
                $data['total_new_products'] = $total_new_products;
                $total_listed_products = $this->model_account_api_edit->getTotalListedProducts($this->customer->getId());
                $data['total_listed_product'] = $total_listed_products;
                $total_restricted_products = $this->model_account_api_edit->getTotalRestrictedProducts($this->customer->getId());
                $data['total_restricted_products'] = $total_restricted_products;
                $total_error_products = $this->model_account_api_edit->getTotalErrorProducts($this->customer->getId());
                $data['total_error_products'] = $total_error_products;
                $data['total_not_listed_products'] = $total_products -$total_listed_products;
                $settings = $this->model_account_api_edit->getSettings($this->customer->getId());
                if (!empty($settings)){
                    $data['meta_title'] = $settings[0]['seller_id'];
                    $data['meta_title_token'] = $settings[0]['auth_token'];
                }
                if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
                if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];

			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}
                //echo "<pre>";print_r($data['total_product']);echo "</pre>-----";die;
		$this->response->setOutput($this->load->view('account/api_edit', $data));
	}
}