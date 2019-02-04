<?php
class ControllerCatalogPrice extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/price');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/price');
                $this->load->model('setting/setting');
                if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
                    $data['price_filter'] = $this->request->post;
                    $this->model_setting_setting->editSetting('price_filter', $data,0);
		      $this->model_catalog_price->UpdateListingPrice();
                    $this->session->data['success'] = $this->language->get('text_success');
                }

		$this->getList();
	}

	

	

	protected function getList() {
		if (isset($this->request->get['filter_product'])) {
			$filter_product = $this->request->get['filter_product'];
		} else {
			$filter_product = '';
		}

		if (isset($this->request->get['filter_author'])) {
			$filter_author = $this->request->get['filter_author'];
		} else {
			$filter_author = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/price', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		

		$data['user_token'] = $this->session->data['user_token'];
                $categories = $this->model_catalog_price->getCategories();
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
                if (isset($this->error['value'])) {
			$data['error_value'] = $this->error['value'];
		} else {
			$data['error_value'] = '';
		}
                foreach($categories as $error_category){
                    if (isset($this->error['value'.$error_category['category_id']])) {
			$data['error_value'.$error_category['category_id']] = $this->error['value'.$error_category['category_id']];
                    } else {
                            $data['error_value'.$error_category['category_id']] = '';
                    }
                }
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
                $this->load->model('setting/setting');
                $result = $this->model_setting_setting->getSetting('price_filter',0);
                
                $data['categories'] = $categories;
                if(!empty($this->request->post)){
                   $data['price_filter'] = $this->request->post;
                } else{
                    if(!empty($result)){
                        $data['price_filter'] = $result['price_filter'];
                    }
                }
                //echo "<pre>";print_r($data['price_filter']);echo "</pre>";die;
                
		$data['action'] = $this->url->link('catalog/price', 'user_token=' . $this->session->data['user_token'] . $url, true);
		

		$data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'] . $url, true);
		

		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/price', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['review_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}
		if (isset($this->error['value'])) {
			$data['error_value'] = $this->error['value'];
		} else {
			$data['error_value'] = '';
		}

		if (isset($this->error['text'])) {
			$data['error_text'] = $this->error['text'];
		} else {
			$data['error_text'] = '';
		}

		if (isset($this->error['rating'])) {
			$data['error_rating'] = $this->error['rating'];
		} else {
			$data['error_rating'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['review_id'])) {
			$data['action'] = $this->url->link('catalog/review/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/review/edit', 'user_token=' . $this->session->data['user_token'] . '&review_id=' . $this->request->get['review_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['review_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$review_info = $this->model_catalog_price->getReview($this->request->get['review_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($review_info)) {
			$data['product_id'] = $review_info['product_id'];
		} else {
			$data['product_id'] = '';
		}

		if (isset($this->request->post['product'])) {
			$data['product'] = $this->request->post['product'];
		} elseif (!empty($review_info)) {
			$data['product'] = $review_info['product'];
		} else {
			$data['product'] = '';
		}

		if (isset($this->request->post['author'])) {
			$data['author'] = $this->request->post['author'];
		} elseif (!empty($review_info)) {
			$data['author'] = $review_info['author'];
		} else {
			$data['author'] = '';
		}

		if (isset($this->request->post['text'])) {
			$data['text'] = $this->request->post['text'];
		} elseif (!empty($review_info)) {
			$data['text'] = $review_info['text'];
		} else {
			$data['text'] = '';
		}

		if (isset($this->request->post['rating'])) {
			$data['rating'] = $this->request->post['rating'];
		} elseif (!empty($review_info)) {
			$data['rating'] = $review_info['rating'];
		} else {
			$data['rating'] = '';
		}

		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($review_info)) {
			$data['date_added'] = ($review_info['date_added'] != '0000-00-00 00:00' ? $review_info['date_added'] : '');
		} else {
			$data['date_added'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($review_info)) {
			$data['status'] = $review_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/review_form', $data));
	}

	protected function validateForm() {
                $this->load->model('catalog/price');
		if (!$this->user->hasPermission('modify', 'catalog/price')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                if(isset($this->request->post['price_type']) && $this->request->post['price_type'] == 1){
                if(isset($this->request->post['type']) && $this->request->post['type'] == 1){
                    if (!isset($this->request->post['value']) || $this->request->post['value']=='') {
			$this->error['value'] = $this->language->get('error_value_required');
                    } else if(!is_numeric($this->request->post['value'])){
                        $this->error['value'] = $this->language->get('error_value_integer');
                    } else if(is_numeric($this->request->post['value'])){
                    if($this->request->post['value']<0){
                            $this->error['value'] = $this->language->get('error_value_fixed');
                        }
                    }
                } else if(isset($this->request->post['type']) && $this->request->post['type'] == 0){
                    if (!isset($this->request->post['value']) || $this->request->post['value']=='') {
			$this->error['value'] = $this->language->get('error_value_required');
                    } else if(!is_numeric($this->request->post['value'])){
                        $this->error['value'] = $this->language->get('error_value_integer');
                    } else if(is_numeric($this->request->post['value'])){
                    if($this->request->post['value']<0 || $this->request->post['value']> 100){
                            $this->error['value'] = $this->language->get('error_value_percentage');
                        }
                    }
                }
                } elseif(isset($this->request->post['price_type']) && $this->request->post['price_type'] == 0){
                    $categories = $this->model_catalog_price->getCategories();
                    foreach($categories as $category){
                        if (isset($this->request->post['type'.$category['category_id']]) && $this->request->post['type'.$category['category_id']] == 1) {
                            if (!isset($this->request->post['value'.$category['category_id']]) || $this->request->post['value'.$category['category_id']] == '') {
                                $this->error['value'.$category['category_id']] = $this->language->get('error_value_required');
                            } else if (!is_numeric($this->request->post['value'.$category['category_id']])) {
                                $this->error['value'.$category['category_id']] = $this->language->get('error_value_integer');
                            } else if (is_numeric($this->request->post['value'.$category['category_id']])) {
                                if ($this->request->post['value'.$category['category_id']] < 0) {
                                    $this->error['value'.$category['category_id']] = $this->language->get('error_value_fixed');
                                }
                            }
                        }elseif(isset($this->request->post['type'.$category['category_id']]) && $this->request->post['type'.$category['category_id']] == 0){
                            if (!isset($this->request->post['value'.$category['category_id']]) || $this->request->post['value'.$category['category_id']]=='') {
                                $this->error['value'.$category['category_id']] = $this->language->get('error_value_required');
                            } else if(!is_numeric($this->request->post['value'.$category['category_id']])){
                                $this->error['value'.$category['category_id']] = $this->language->get('error_value_integer');
                            } else if(is_numeric($this->request->post['value'.$category['category_id']])){
                            if($this->request->post['value'.$category['category_id']]<0 || $this->request->post['value'.$category['category_id']]> 100){
                                    $this->error['value'.$category['category_id']] = $this->language->get('error_value_percentage');
                                }
                            }
                        }
                        
                    }
                }

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}