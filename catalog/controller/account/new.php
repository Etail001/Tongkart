<?php
class ControllerAccountNew extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/newsletter');
                $this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/new');
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
                

		$data['action'] = $this->url->link('account/newsletter', '', true);

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
                $data['new'] = $this->url->link('account/new');
                $data['total'] = $this->url->link('account/total');
                $data['listed'] = $this->url->link('account/listed');
                $data['not_listed'] = $this->url->link('account/not_listed');
                $data['monitor'] = $this->url->link('account/monitor');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['url'] = $this->url->link('account/new',$url);
                $data['page'] = $page;
                $data['limit'] = $limit;
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['common'] = $this->load->controller('account/common');
                $data['new'] = $this->url->link('account/new');
                $listed_products = $this->model_account_new->getListedProducts($this->customer->getId());
                $array_products = array();
                foreach($listed_products as $list){
                    $array_products[] = $list['product_id'];
                }
                $totalproduct_new_arrivals = $this->model_account_new->getTotalNewProducts($array_products);
                $all_new_products = $this->model_account_new->getAllNewProducts($filter_data,$array_products);
                $data['products'] = array();
                foreach($all_new_products as $list){
                    $price = $this->currency->format($this->tax->calculate($list['price'], $list['tax_class_id'], $this->config->get('config_tax')), 'INR');
                    $data['products'][] = array(
					'product_id'  => $list['product_id'],
                                        'model'       => $list['model'],
					'thumb'       => $list['image'],
					'name'        => $list['name'],
					'price'       => $price,
                                        'quantity'       => $list['quantity'],
					//End
					//'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
                }
                $pagination = new Pagination();
                $pagination->total = $totalproduct_new_arrivals;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('account/new',$url.'&page={page}');
                $data['pagination'] = $pagination->render();
                
                $data['amazon'] = $this->url->link('account/new/ListAmazon');
                $data['results'] = sprintf($this->language->get('text_pagination'), ($totalproduct_new_arrivals) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalproduct_new_arrivals - $limit)) ? $totalproduct_new_arrivals : ((($page - 1) * $limit) + $limit), $totalproduct_new_arrivals, ceil($totalproduct_new_arrivals / $limit));
                $new_products = $this->model_account_new->getTotalProducts();
                $data['new_product'] = $new_products;
                $new_listed_products = $this->model_account_new->getTotalListedProducts($this->customer->getId());
                $data['new_listed_product'] = $new_listed_products;
                $new_restricted_products = $this->model_account_new->getTotalRestrictedProducts($this->customer->getId());
                $data['new_restricted_products'] = $new_restricted_products;
                $new_error_products = $this->model_account_new->getTotalErrorProducts($this->customer->getId());
                $data['new_error_products'] = $new_error_products;
                $data['new_not_listed_products'] = $new_products -$new_listed_products;
                if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
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
		$this->response->setOutput($this->load->view('account/new', $data));
	}
        public function ListAmazon() {

		$this->load->model('account/new');

		if (isset($this->request->post['selected'])) {
                        $upc_count = $this->model_account_new->upcCount($this->customer->getId());
                        $selected_product = count($this->request->post['selected']);
                        if ($selected_product <= $upc_count){
                            $this->model_account_new->ListAmazon($this->request->post['selected'],$this->customer->getId());
                            $this->session->data['success'] = 'We have Initiated listing of those products. It Usually takes 5-10 min to get them listed.';
                            $this->response->redirect($this->url->link('account/new'));
                        } else{
                            $this->session->data['error_warning'] = 'Not Enough UPC Available, please Import more UPC';
                            $this->response->redirect($this->url->link('account/new'));
                        }
			
                } else{
                    $this->session->data['error_warning'] = 'No Products Selected';
                    $this->response->redirect($this->url->link('account/new'));
                }
	}
        
}