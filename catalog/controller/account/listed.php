<?php
class ControllerAccountListed extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/listed');
                $this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/listed');

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
                $querry = 0;
                if (isset($this->request->get['product_category'])) {
			$categories = $this->request->get['product_category'];
                        $categories = explode(",",$categories);
                        $url .= '&product_category=' . urlencode(html_entity_decode($this->request->get['product_category'], ENT_QUOTES, 'UTF-8'));
		} else {
			$categories = array();
		}
                $this->load->model('catalog/category');
                $data['product_categories'] = array();

		foreach ($categories as $category_id) {
                        
			$category_info = $this->model_catalog_category->getCategory($category_id);
                        //echo "<pre>";print_r($category_info);echo "</pre>";die;
			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}
                if (isset($this->request->get['filter_search'])) {
			$filter_search = $this->request->get['filter_search'];
                        $data['filter_search'] = $filter_search;
                        $querry = 1;
                        $url .= '&filter_search=' . urlencode(html_entity_decode($this->request->get['filter_search'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_search = '';
		}
                if (isset($this->request->get['filter_stock_start'])) {
			$filter_stock_start = $this->request->get['filter_stock_start'];
                        $data['filter_stock_start'] = $filter_stock_start;
                        $querry = 1;
                        $url .= '&filter_stock_start=' . urlencode(html_entity_decode($this->request->get['filter_stock_start'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_stock_start = '';
		}
                if (isset($this->request->get['filter_stock_end'])) {
			$filter_stock_end = $this->request->get['filter_stock_end'];
                        $data['filter_stock_end'] = $filter_stock_end;
                        $url .= '&filter_stock_end=' . urlencode(html_entity_decode($this->request->get['filter_stock_end'], ENT_QUOTES, 'UTF-8'));
                        $querry = 1;
		} else {
			$filter_stock_end = '';
		}
                if (isset($this->request->get['product_category'])) {
			$categories = $this->request->get['product_category'];
                        //$data['product_category'] = $filter_stock_end;
                        $url .= '&product_category=' . urlencode(html_entity_decode($this->request->get['product_category'], ENT_QUOTES, 'UTF-8'));
                        $querry = 1;
		} else {
			$categories = '';
		}
                if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
                        $url .= '&limit='.$limit;
		} else {
			$limit = 10;
		}
                $filter_data = array(
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit,
                        'filter_search' => $filter_search,
                        'filter_stock_start' => $filter_stock_start,
                        'filter_stock_end' => $filter_stock_end,
                        'category_id' => $categories
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
                $data['listed'] = $this->url->link('account/listed');
                $data['new'] = $this->url->link('account/new');
                $data['total'] = $this->url->link('account/total');
                $data['listed'] = $this->url->link('account/listed');
                $data['not_listed'] = $this->url->link('account/not_listed');
                $data['monitor'] = $this->url->link('account/monitor');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['url'] = $this->url->link('account/listed',$url);
                $data['page'] = $page;
                $data['limit'] = $limit;
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['common'] = $this->load->controller('account/common');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['listed'] = $this->url->link('account/listed');
                $data['customer_id'] = $this->customer->getId();
                $total_listed_products = $this->model_account_listed->getTotalListedProducts($filter_data,$this->customer->getId());
                $listed_products = $this->model_account_listed->getListedProducts($filter_data,$this->customer->getId());
                
                $data['products'] = array();
                foreach($listed_products as $list){
                    $price = $this->currency->format($this->tax->calculate($list['tongkart_price'], $list['tax_class_id'], $this->config->get('config_tax')), 'INR');
                    $category_details = $this->model_account_listed->getProductCategory($list['product_id']);
                    $data['products'][] = array(
					'product_id'  => $list['product_id'],
                                        'model'       => $list['model'],
					'thumb'       => $list['image'],
					'name'        => $list['name'],
					'price'       => $price,
                                        'category'    => $category_details,
                                        'quantity'       => $list['quantity'],
                                        'link'        => $this->url->link('product/product', 'product_id='.$list['product_id'])
					//End
					//'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
                }
                $pagination = new Pagination();
                $pagination->total = $total_listed_products;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('account/listed',$url.'&page={page}');

                $data['pagination'] = $pagination->render();
                $data['amazon'] = $this->url->link('account/new/ListAmazon');
                $data['results'] = sprintf($this->language->get('text_pagination'), ($total_listed_products) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_listed_products - $limit)) ? $total_listed_products : ((($page - 1) * $limit) + $limit), $total_listed_products, ceil($total_listed_products / $limit));
                
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
		$this->response->setOutput($this->load->view('account/listed', $data));
	}
        public function ListAmazon() {

		$this->load->model('account/listed');

		if (isset($this->request->post['selected'])) {
                        $this->model_account_listed->ListAmazon($this->request->post['selected'],$this->customer->getId());
                        $this->session->data['success'] = 'We have Initiated listing of those products. It Usually takes 5-10 min to get them listed.';
                        $this->response->redirect($this->url->link('account/listed'));
			
                } else{
                    $this->session->data['error_warning'] = 'No Products Selected';
                    $this->response->redirect($this->url->link('account/listed'));
                }
	}
}