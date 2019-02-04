<?php
class ControllerAccountPrice extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/price');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/price');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('account/price');

			$this->model_account_price->editSettings($this->request->post,$this->customer->getId());

			$this->session->data['success'] = 'Price Settings Succesfully Updated';

			$this->response->redirect($this->url->link('account/price', '', true));
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
			'href' => $this->url->link('account/newsletter', '', true)
		);
                $url='';
                if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
                        $url .= '&limit='.$limit;
		} else {
			$limit = 10;
		}
                $filter_data = array(
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit
		);

		$data['action'] = $this->url->link('account/price', '', true);

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
                $total_products = $this->model_account_price->getTotalProducts();
                $data['total_product'] = $total_products;
                $total_new_products = $this->model_account_price->getTotalNewProducts();
                $data['total_new_products'] = $total_new_products;
                $total_listed_products = $this->model_account_price->getTotalListedProducts($this->customer->getId());
                $data['total_listed_product'] = $total_listed_products;
                $total_restricted_products = $this->model_account_price->getTotalRestrictedProducts($this->customer->getId());
                $data['total_restricted_products'] = $total_restricted_products;
                $total_error_products = $this->model_account_price->getTotalErrorProducts($this->customer->getId());
                $data['total_error_products'] = $total_error_products;
                $data['total_not_listed_products'] = $total_products -$total_listed_products;
                $settings = $this->model_account_price->getSettings($this->customer->getId());
                if (!empty($settings)){
                    $data['sp'] = $settings[0]['sp'];
                    $data['mrp'] = $settings[0]['mrp'];
                    $data['mirp'] = $settings[0]['mirp'];
                }
                $total_price_set = $this->model_account_price->getTotalPriceSet($this->customer->getId());
                $price_set = $this->model_account_price->getBrandSet($filter_data,$this->customer->getId());
                $data['prices'] = array();
                foreach($price_set as $list){
                    if ($list['category_id'] == 0){
                        $list['name'] = 'All';
                    }
                    $data['prices'][] = array(
					'price_id'  => $list['price_id'],
                                        'name'       => $list['name'],
					'sp'        => $list['sp'],
                                        'mrp'        => $list['mrp'],
                                        'mirp'        => $list['mirp']
					
				);
                }
                $pagination = new Pagination();
                $pagination->total = $total_price_set;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('account/price',$url.'&page={page}');

                $data['pagination'] = $pagination->render();
                $data['amazon'] = $this->url->link('account/new/ListAmazon');
                $data['results'] = sprintf($this->language->get('text_pagination'), ($total_price_set) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_price_set - $limit)) ? $total_price_set : ((($page - 1) * $limit) + $limit), $total_price_set, ceil($total_price_set / $limit));
                $categories = $this->model_account_price->getCategories();
                $new_data = array(
                    'category_id' => '0',
                    'name'        => 'All'
                );
                array_unshift($categories, $new_data);
                $data['categories'] = $categories;
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
		$this->response->setOutput($this->load->view('account/price', $data));
	}
}