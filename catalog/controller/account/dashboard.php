<?php
class ControllerAccountDashboard extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/dashboard');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/dashboard');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('account/customer');

			$this->model_account_customer->editNewsletter($this->request->post['newsletter']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/account', '', true));
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

		$data['action'] = $this->url->link('account/dashboard', '', true);

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
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['common'] = $this->load->controller('account/common');
                $pending_order = $this->model_account_dashboard->getTotalPendingOrders($this->customer->getId());
                $data['pending_order'] = $pending_order;
                $cancel_order = $this->model_account_dashboard->getTotalCancelOrders($this->customer->getId());
                $data['cancel_order'] = $cancel_order;
                $shipped_order = $this->model_account_dashboard->getTotalShippedOrders($this->customer->getId());
                $data['shipped_order'] = $shipped_order;
                $total_order = $this->model_account_dashboard->getTotalOrders($this->customer->getId());
                $data['total_order'] = $total_order;
                $total_products = $this->model_account_dashboard->getTotalProducts();
                $data['total_product'] = $total_products;
                $total_new_products = $this->model_account_dashboard->getTotalNewProducts();
                $data['total_new_products'] = $total_new_products;
                $total_listed_products = $this->model_account_dashboard->getTotalListedProducts($this->customer->getId());
                $data['total_listed_product'] = $total_listed_products;
                $total_restricted_products = $this->model_account_dashboard->getTotalRestrictedProducts($this->customer->getId());
                $data['total_restricted_products'] = $total_restricted_products;
                $total_error_products = $this->model_account_dashboard->getTotalErrorProducts($this->customer->getId());
                $account_details = $this->model_account_dashboard->getAccountDetails($this->customer->getId());
                //echo "<pre>";print_r($account_details);echo "</pre>";die;
                $data['account_details'] = $account_details;
                $data['total_error_products'] = $total_error_products;
                $data['total_not_listed_products'] = $total_products -$total_listed_products;
                $wallet_amount = $this->model_account_dashboard->getWallet($this->customer->getId());
                $data['total_amount'] = $this->currency->format($this->tax->calculate($wallet_amount, 0, $this->config->get('config_tax')), 'INR');
                //echo "<pre>";print_r($data['total_product']);echo "</pre>-----";die;
		$this->response->setOutput($this->load->view('account/dashboard', $data));
	}
}