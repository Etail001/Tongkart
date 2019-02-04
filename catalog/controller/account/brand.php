<?php
class ControllerAccountBrand extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/brand');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/brand');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('account/brand');

			$this->model_account_brand->editSettings($this->request->post,$this->customer->getId());

			$this->session->data['success'] = 'Brand Settings Succesfully Updated';

			$this->response->redirect($this->url->link('account/brand', '', true));
		}
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

		$data['action'] = $this->url->link('account/brand', '', true);

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
                $total_brand_set = $this->model_account_brand->getTotalBrandSet($this->customer->getId());
                $brand_set = $this->model_account_brand->getBrandSet($filter_data,$this->customer->getId());
                $data['brands'] = array();
                foreach($brand_set as $list){
                    if ($list['category_id'] == 0){
                        $list['name'] = 'All';
                    }
                    $data['brands'][] = array(
					'brand_id'  => $list['brand_id'],
                                        'name'       => $list['name'],
					'text'        => $list['text']
					
				);
                }
                $pagination = new Pagination();
                $pagination->total = $total_brand_set;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('account/brand',$url.'&page={page}');

                $data['pagination'] = $pagination->render();
                $data['amazon'] = $this->url->link('account/new/ListAmazon');
                $data['results'] = sprintf($this->language->get('text_pagination'), ($total_brand_set) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_brand_set - $limit)) ? $total_brand_set : ((($page - 1) * $limit) + $limit), $total_brand_set, ceil($total_brand_set / $limit));
                $settings = $this->model_account_brand->getSettings($this->customer->getId());
                $categories = $this->model_account_brand->getCategories();
                $new_data = array(
                    'category_id' => '0',
                    'name'        => 'All'
                );
                array_unshift($categories, $new_data);
                $data['categories'] = $categories;
                if (!empty($settings)){
                    $data['sp'] = $settings[0]['sp'];
                    $data['mrp'] = $settings[0]['mrp'];
                    $data['mirp'] = $settings[0]['mirp'];
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
		$this->response->setOutput($this->load->view('account/brand', $data));
	}
}