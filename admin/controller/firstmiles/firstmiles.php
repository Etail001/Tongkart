<?php
class ControllerFirstmilesFirstmiles extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('firstmiles/firstmiles');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('firstmiles/firstmiles');

		$this->getList();
	}
	
	protected function getList() {
		//echo "==============";die;
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = '';
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = '';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'href' => $this->url->link('firstmiles/firstmiles', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['action'] = $this->url->link('firstmiles/firstmiles/generateLabel', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action_p'] = $this->url->link('firstmiles/firstmiles/packetStatus', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action_s'] = $this->url->link('firstmiles/firstmiles/scanDocumentList', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = str_replace('&amp;', '&', $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'] . $url, true));

		$data['orders'] = array();

		$filter_data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_customer'	     => $filter_customer,
			'filter_order_status'    => $filter_order_status,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_firstmiles_firstmiles->getTotalOrders($filter_data);

		$results = $this->model_firstmiles_firstmiles->getOrders($filter_data);

		foreach ($results as $result) {
			if(!empty($result['awb']))
			{
				$awb = $result['awb'];
			}
			else
			{
				$awb = '';
			}
			$data['orders'][] = array(
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'order_status'  => $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'shipping_code' => $result['shipping_code'],
				'traking_number' => $awb,
				'view'          => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
				'edit'          => $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true)
			);
		}
	
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_status'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_order_status_id'] = $filter_order_status_id;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// API login
		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
		
		// API login
		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);
			
			$session->start();
					
			$this->model_user_api->deleteApiSessionBySessonId($session->getId());
			
			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
			
			$session->data['api_id'] = $api_info['api_id'];

			$data['api_token'] = $session->getId();
		} else {
			$data['api_token'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('firstmiles/order_list', $data));
	}
	
	function generateLabel()
	{
		$this->load->model('sale/order');

		$this->load->model('setting/setting');
		
		$this->load->model('logistics/logistics');
		
		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}
		
		foreach ($orders as $order_id) {
				$order_info = $this->model_sale_order->getOrder($order_id);

				if ($order_info) {
					$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

					if ($store_info) {
						$store_address = $store_info['config_address'];
						$store_email = $store_info['config_email'];
						$store_telephone = $store_info['config_telephone'];
						$store_fax = $store_info['config_fax'];
					} else {
						$store_address = $this->config->get('config_address');
						$store_email = $this->config->get('config_email');
						$store_telephone = $this->config->get('config_telephone');
						$store_fax = $this->config->get('config_fax');
					}

					if ($order_info['invoice_no']) {
						$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
					} else {
						$invoice_no = '';
					}

					if ($order_info['payment_address_format']) {
						$format = $order_info['payment_address_format'];
					} else {
						$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
					}

					$find = array(
						'{firstname}',
						'{lastname}',
						'{company}',
						'{address_1}',
						'{address_2}',
						'{city}',
						'{postcode}',
						'{zone}',
						'{zone_code}',
						'{country}'
					);

					$replace = array(
						'firstname' => $order_info['payment_firstname'],
						'lastname'  => $order_info['payment_lastname'],
						'company'   => $order_info['payment_company'],
						'address_1' => $order_info['payment_address_1'],
						'address_2' => $order_info['payment_address_2'],
						'city'      => $order_info['payment_city'],
						'postcode'  => $order_info['payment_postcode'],
						'zone'      => $order_info['payment_zone'],
						'zone_code' => $order_info['payment_zone_code'],
						'country'   => $order_info['payment_country']
					);

					$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

					if ($order_info['shipping_address_format']) {
						$format = $order_info['shipping_address_format'];
					} else {
						$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
					}

					$find = array(
						'{firstname}',
						'{lastname}',
						'{company}',
						'{address_1}',
						'{address_2}',
						'{city}',
						'{postcode}',
						'{zone}',
						'{zone_code}',
						'{country}'
					);

					$replace = array(
						'firstname' => $order_info['shipping_firstname'],
						'lastname'  => $order_info['shipping_lastname'],
						'company'   => $order_info['shipping_company'],
						'address_1' => $order_info['shipping_address_1'],
						'address_2' => $order_info['shipping_address_2'],
						'city'      => $order_info['shipping_city'],
						'postcode'  => $order_info['shipping_postcode'],
						'zone'      => $order_info['shipping_zone'],
						'zone_code' => $order_info['shipping_zone_code'],
						'country'   => $order_info['shipping_country']
					);

					$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

					$this->load->model('tool/upload');

					$product_data = array();

					$products = $this->model_sale_order->getOrderProducts($order_id);

					foreach ($products as $product) {
						$option_data = array();

						$options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

						foreach ($options as $option) {
							if ($option['type'] != 'file') {
								$value = $option['value'];
							} else {
								$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

								if ($upload_info) {
									$value = $upload_info['name'];
								} else {
									$value = '';
								}
							}

							$option_data[] = array(
								'name'  => $option['name'],
								'value' => $value
							);
						}

						$product_data[] = array(
							'name'     => $product['name'],
							'model'    => $product['model'],
							'option'   => $option_data,
							'quantity' => $product['quantity'],
							'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
							'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
						);
					}

					$voucher_data = array();

					$vouchers = $this->model_sale_order->getOrderVouchers($order_id);

					foreach ($vouchers as $voucher) {
						$voucher_data[] = array(
							'description' => $voucher['description'],
							'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
						);
					}

					$total_data = array();

					$totals = $this->model_sale_order->getOrderTotals($order_id);

					foreach ($totals as $total) {
						$total_data[] = array(
							'title' => $total['title'],
							'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
						);
					}

					$data['orders'][] = array(
						'order_id'	       => $order_id,
						'invoice_no'       => $invoice_no,
						'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
						'store_name'       => $order_info['store_name'],
						'store_url'        => rtrim($order_info['store_url'], '/'),
						'store_address'    => nl2br($store_address),
						'store_email'      => $store_email,
						'store_telephone'  => $store_telephone,
						'store_fax'        => $store_fax,
						'email'            => $order_info['email'],
						'telephone'        => $order_info['telephone'],
						'shipping_address' => $shipping_address,
						'shipping_method'  => $order_info['shipping_method'],
						'payment_address'  => $payment_address,
						'payment_method'   => $order_info['payment_method'],
						'product'          => $product_data,
						'voucher'          => $voucher_data,
						'total'            => $total_data,
						'comment'          => nl2br($order_info['comment'])
					);
				}
			}
			$credentials = $this->model_logistics_logistics->getCredentials();
			$soapUrl = "http://icms.grandslamexpress.in/V2/ClientAPI.asmx";
			$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			  <soap:Body>
				<PickUpEntry xmlns="http://grandslamexpress.in/">
				  <UserId>'.$credentials['user'].'</UserId>
				  <Password>'.$credentials['password'].'</Password>
				  <CCode>'.$credentials['user'].'</CCode>
				  <AWBNo></AWBNo>
				  <OrderNo>'.$order_id.'</OrderNo>
				  <Origin>HK</Origin>
				  <Destination>IN</Destination>
				  <Shipper_Name>China</Shipper_Name>
				  <Shipper_Add1>Hongkong</Shipper_Add1>
				  <Shipper_Add2>sanghai</Shipper_Add2>
				  <Shipper_Add3></Shipper_Add3>
				  <Shipper_State>Hongkong</Shipper_State>
				  <Shipper_Pin>200010</Shipper_Pin>
				  <Shipper_Mobile>8521478965</Shipper_Mobile>
				  <Shipper_Email>hx@gmail.com</Shipper_Email>
				  <Consignee_Name>Fada</Consignee_Name>
				  <Consignee_Contact>7895478595</Consignee_Contact>
				  <Consignee_Add1>New Delhi</Consignee_Add1>
				  <Consignee_Add2>Delhi</Consignee_Add2>
				  <Consignee_Add3></Consignee_Add3>
				  <Consignee_City>Delhi</Consignee_City>
				  <Consignee_State>Delhi</Consignee_State>
				  <Consignee_Pin>110037</Consignee_Pin>
				  <Consignee_Mobile>7895478595</Consignee_Mobile>
				  <Consignee_Email>fd@gmail.com</Consignee_Email>
				  <Pcs>1</Pcs>
				  <Weight>.100</Weight>
				  <Dox_Spx>ND</Dox_Spx>
				  <Shipment_Value>100</Shipment_Value>
				  <Shipment_Currency>INR</Shipment_Currency>
				  <COD_Amount>0</COD_Amount>
				  <PickupDate>04-Sep-2018</PickupDate>
				  <Instruction>Electronics Items</Instruction>
				  <Mode>TEST</Mode>
				  <ClientSource>GS</ClientSource>
				  <PaymentMode>Prepaid</PaymentMode>
				</PickUpEntry>
			  </soap:Body>
			</soap:Envelope>';

			$headers = array(
			"POST /V2/ClientAPI.asmx HTTP/1.1",
			"Host: icms.grandslamexpress.in",
			"Content-Type: text/xml; charset=utf-8",
			"Content-Length: ".strlen($xml_post_string)
			); 

			$url = $soapUrl;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($ch);
			$page = file_put_contents('img/PickupEntryResponse.xml',$response);
			$page_xml = 'http://store.tongkart.com/tngkart/admin/img/PickupEntryResponse.xml';
			$data = file_get_contents($page_xml);
			$xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $data);
			$xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			
			$dataResponse = $array['soapBody']['PickUpEntryResponse'];
			
			
			$dataResult = array();
			if(empty($dataResponse['PickUpEntryResult']['Error']))
			{
				$dataResult['AWBNo'] = $dataResponse['PickUpEntryResult']['AWBNo'];
				$dataResult['Msg'] = $dataResponse['PickUpEntryResult']['Msg'];
				$dataResult['Status'] = $dataResponse['PickUpEntryResult']['Status'];
				$dataResult['OrderId'] = $order_id;
				$this->model_logistics_logistics->insertPickupEntry($dataResult);
			}
			else
			{
				
			}
			$url='';
			$this->response->redirect($this->url->link('firstmiles/firstmiles', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
	
	public function packetStatus()
	{
		$this->load->model('sale/order');

		$this->load->model('logistics/logistics');
		
		$this->load->model('logistics/logistics');
		
		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}
		$credentials = $this->model_logistics_logistics->getCredentials();
		foreach ($orders as $order_id) {
			$order_info = $this->model_logistics_logistics->getOrder($order_id);
			
			$opts = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
			$context = stream_context_create($opts);
			// Here I call my external function
			$user = $credentials['user'];
			$password = $credentials['password'];
			$awb = $order_info['awb'];
			
			$result = "http://icms.grandslamexpress.in/V2/ClientAPI.asmx/GetAwbPacketStatus?UserId=$user&Password=$password&AWBNo=$awb";
			$result = @file_get_contents($result, FALSE, $context);
		
			
			$page = file_put_contents('img/GetAwbPacketStatus.xml',$result);
			$page_xml = 'http://store.tongkart.com/tngkart/admin/img/GetAwbPacketStatus.xml';
			$data = file_get_contents($page_xml);
			$xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $data);
			$xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			
			$dataResult = array();
			if(!empty($array['GetPacketStatus']['AWBNo']))
			{
				$dataResult['AWBNo'] = $array['GetPacketStatus']['AWBNo'];
				$dataResult['Msg'] = $array['GetPacketStatus']['Msg'];
				$dataResult['Status'] = $array['GetPacketStatus']['Status'];
				$dataResult['Time'] = $array['GetPacketStatus']['Time'];
				$dataResult['Memo'] = $array['GetPacketStatus']['Memo'];
				$dataResult['Location'] = $array['GetPacketStatus']['Location'];
				$dataResult['OrderId'] = $order_id;
				$this->model_logistics_logistics->insertPacketStatus($dataResult);
			}
		}
		$url='';
		$this->response->redirect($this->url->link('firstmiles/packetStatus', 'user_token=' . $this->session->data['user_token'] . $url, true));
		
		
	}
	
	public function scanDocumentList()
	{
		$this->load->model('sale/order');

		$this->load->model('logistics/logistics');
		
		$this->load->model('logistics/logistics');
		
		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}
		$credentials = $this->model_logistics_logistics->getCredentials();
		foreach ($orders as $order_id) {
			$order_info = $this->model_logistics_logistics->getOrder($order_id);
			
			$soapUrl = "http://icms.grandslamexpress.in/V2/ClientAPI.asmx";
			$xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
									<soap:Body>
										<ScanDocumentList xmlns="http://grandslamexpress.in/">
											<UserId>'.$credentials['user'].'</UserId>
											<Password>'.$credentials['password'].'</Password>
											<AWBNo>'.$order_info['awb'].'</AWBNo>
											<DocumentType>string</DocumentType>
											<Document>string</Document>
										</ScanDocumentList>
									</soap:Body>
								</soap:Envelope>';

			$headers = array(
			"POST /V2/ClientAPI.asmx HTTP/1.1",
			"Host: icms.grandslamexpress.in",
			"Content-Type: text/xml; charset=utf-8",
			"Content-Length: ".strlen($xml_post_string)
			); 

			$url = $soapUrl;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($ch);
			
			$page = file_put_contents('img/ScanDocumentList.xml',$response);
			$page_xml = 'http://store.tongkart.com/tngkart/admin/img/ScanDocumentList.xml';
			$data = file_get_contents($page_xml);
			$xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $data);
			$xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			
			$dataResponse = $array['soapBody']['ScanDocumentListResponse'];
			$dataResult = array();
			if(!empty($dataResponse['ScanDocumentListResult']['AWBNo']))
			{
				$dataResult['AWBNo'] = $dataResponse['ScanDocumentListResult']['AWBNo'];
				$dataResult['Msg'] = $dataResponse['ScanDocumentListResult']['Msg'];
				$dataResult['Status'] = $dataResponse['ScanDocumentListResult']['Status'];
				$dataResult['OrderId'] = $order_info['order_id'];
				$this->model_logistics_logistics->insertScanDocumentList($dataResult);
			}
		}
		$url='';
		$this->response->redirect($this->url->link('firstmiles/scanDocument', 'user_token=' . $this->session->data['user_token'] . $url, true));
		
		
	}
}
