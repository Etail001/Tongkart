<?php
class ControllerQuarkQuark extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('quark/quark');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('quark/quark');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_download->addDownload($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';  

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('quark/quark', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
	
	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['download_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['filename'])) {
			$data['error_filename'] = $this->error['filename'];
		} else {
			$data['error_filename'] = '';
		}

		if (isset($this->error['mask'])) {
			$data['error_mask'] = $this->error['mask'];
		} else {
			$data['error_mask'] = '';
		}

		$url = '';

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
			'href' => $this->url->link('quark/quark', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['download_id'])) {
			$data['action'] = $this->url->link('quark/quark/upload', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('quark/quark/upload', 'user_token=' . $this->session->data['user_token'] . '&download_id=' . $this->request->get['download_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('quark/quark', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->get['download_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$download_info = $this->model_catalog_download->getDownload($this->request->get['download_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['download_id'])) {
			$data['download_id'] = $this->request->get['download_id'];
		} else {
			$data['download_id'] = 0;
		}

		if (isset($this->request->post['download_description'])) {
			$data['download_description'] = $this->request->post['download_description'];
		} elseif (isset($this->request->get['download_id'])) {
			$data['download_description'] = $this->model_catalog_download->getDownloadDescriptions($this->request->get['download_id']);
		} else {
			$data['download_description'] = array();
		}

		if (isset($this->request->post['filename'])) {
			$data['filename'] = $this->request->post['filename'];
		} elseif (!empty($download_info)) {
			$data['filename'] = $download_info['filename'];
		} else {
			$data['filename'] = '';
		}

		if (isset($this->request->post['mask'])) {
			$data['mask'] = $this->request->post['mask'];
		} elseif (!empty($download_info)) {
			$data['mask'] = $download_info['mask'];
		} else {
			$data['mask'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('quark/quark_form', $data));
	}
	public function upload() {
		$this->load->language('marketplace/installer');
		$json = array();
		require_once './PHPExcel/Classes/PHPExcel.php';
		$tmpfname = $_FILES['file']['tmp_name'];
		$inputFileType = PHPExcel_IOFactory::identify($tmpfname);
		$objPHPExcel = new PHPExcel();
		$objReader= PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel=$objReader->load($tmpfname);
		$objWorksheet=$objPHPExcel->setActiveSheetIndex(0);
		$lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
		$count	= 0;
		$alreadycount	=	0;
		$countbarcode	=	0;
		$ProSCMDataArray = array();
		for($i=8;$i<=$lastRow;$i++) 
		{ 
			  
			
			echo $productdata['Sku']									=	trim($objWorksheet->getCellByColumnAndRow(0,$i)->getValue());die;
			$productdata['Spu']									=	trim($objWorksheet->getCellByColumnAndRow(1,$i)->getValue());
			$productdata['Warehouse']							=	trim($objWorksheet->getCellByColumnAndRow(2,$i)->getValue());
			$productdata['Stock']								=	trim($objWorksheet->getCellByColumnAndRow(3,$i)->getValue());
			$productdata['Dispatch in days']					=	trim($objWorksheet->getCellByColumnAndRow(4,$i)->getValue());
			$productdata['Marketing']							=	trim($objWorksheet->getCellByColumnAndRow(5,$i)->getValue());
			$productdata['Shipping to Country'] 				=	trim($objWorksheet->getCellByColumnAndRow(6,$i)->getValue());
			$productdata['Shipping Method']						=	trim($objWorksheet->getCellByColumnAndRow(7,$i)->getValue());
			$productdata['Shipping Code']						=	trim($objWorksheet->getCellByColumnAndRow(8,$i)->getValue());
			$productdata['Market shipping']						=	trim($objWorksheet->getCellByColumnAndRow(9,$i)->getValue());
			$productdata['Delivery Time']						=	trim($objWorksheet->getCellByColumnAndRow(10,$i)->getValue());
			$productdata['Market fee(%)']						=	trim($objWorksheet->getCellByColumnAndRow(11,$i)->getValue());
			$productdata['Insurance(%)']						=	trim($objWorksheet->getCellByColumnAndRow(12,$i)->getValue());
			$productdata['Reseller Profit(%)']					=	trim($objWorksheet->getCellByColumnAndRow(13,$i)->getValue());
			$productdata['Reseller Discount(%)']				=	trim($objWorksheet->getCellByColumnAndRow(14,$i)->getValue());
			$productdata['Item Cost(USD)']						=	trim($objWorksheet->getCellByColumnAndRow(15,$i)->getValue());
			$productdata['Item Cost(USD)']						=	trim($objWorksheet->getCellByColumnAndRow(16,$i)->getValue());
			$productdata['Handing fee(USD)']					=	trim($objWorksheet->getCellByColumnAndRow(17,$i)->getValue());
			$productdata['Quarkscm Price(USD)']					=	trim($objWorksheet->getCellByColumnAndRow(18,$i)->getValue());
			$productdata['Market fee(USD)']						=	trim($objWorksheet->getCellByColumnAndRow(19,$i)->getValue());
			$productdata['Insurance(USD)']						=	trim($objWorksheet->getCellByColumnAndRow(20,$i)->getValue());
			$productdata['VIP Saving(USD)']						=	trim($objWorksheet->getCellByColumnAndRow(21,$i)->getValue());
			$productdata['Reseller Profit(USD)']				=	trim($objWorksheet->getCellByColumnAndRow(22,$i)->getValue());
			$productdata['Reseller Price(USD)']				    =	trim($objWorksheet->getCellByColumnAndRow(23,$i)->getValue());
			$productdata['Video']								=	trim($objWorksheet->getCellByColumnAndRow(24,$i)->getValue());
			$productdata['Brand']								=	trim($objWorksheet->getCellByColumnAndRow(25,$i)->getValue());
			$productdata['SKU Special']							=	trim($objWorksheet->getCellByColumnAndRow(26,$i)->getValue());
			$productdata['Category1']							=	trim($objWorksheet->getCellByColumnAndRow(27,$i)->getValue());
			$productdata['Category1ID']							=	trim($objWorksheet->getCellByColumnAndRow(28,$i)->getValue());
			$productdata['Category2']							=	trim($objWorksheet->getCellByColumnAndRow(29,$i)->getValue());
			$productdata['Category2ID']							=	trim($objWorksheet->getCellByColumnAndRow(30,$i)->getValue());
			$productdata['Category3']							=	trim($objWorksheet->getCellByColumnAndRow(31,$i)->getValue());
			$productdata['Category3ID']							=	trim($objWorksheet->getCellByColumnAndRow(32,$i)->getValue());
			$productdata['Category4']							=	trim($objWorksheet->getCellByColumnAndRow(33,$i)->getValue());
			$productdata['Category4ID']							=	trim($objWorksheet->getCellByColumnAndRow(34,$i)->getValue());
			$productdata['Product Name']						=	trim($objWorksheet->getCellByColumnAndRow(35,$i)->getValue());
			$productdata['Product Description']					=	trim($objWorksheet->getCellByColumnAndRow(36,$i)->getValue());
			$productdata['Quarkscm Listing URL']				=	$objWorksheet->getCellByColumnAndRow(37,$i)->getValue();
			$productdata['Image Main']							=	trim($objWorksheet->getCellByColumnAndRow(38,$i)->getValue());
			$productdataImage['Image1']							=	trim($objWorksheet->getCellByColumnAndRow(39,$i)->getValue());
			$productdataImage['Image2']							=	trim($objWorksheet->getCellByColumnAndRow(40,$i)->getValue());
			$productdataImage['Image3']							=	trim($objWorksheet->getCellByColumnAndRow(41,$i)->getValue());
			$productdataImage['Image4']							=	trim($objWorksheet->getCellByColumnAndRow(42,$i)->getValue());
			$productdataImage['Image5']							=	trim($objWorksheet->getCellByColumnAndRow(43,$i)->getValue());
			$productdataImage['Image6']							=	trim($objWorksheet->getCellByColumnAndRow(44,$i)->getValue());
			$productdataImage['Image7']							=	trim($objWorksheet->getCellByColumnAndRow(45,$i)->getValue());
			$productdataImage['Image8']							=	trim($objWorksheet->getCellByColumnAndRow(46,$i)->getValue());
			$productdataImage['Image9']							=	trim($objWorksheet->getCellByColumnAndRow(47,$i)->getValue());
			$productdataImage['Image10']						=	trim($objWorksheet->getCellByColumnAndRow(48,$i)->getValue());
			$productdataSpuVarient['SPU Variant1name']			=	trim($objWorksheet->getCellByColumnAndRow(49,$i)->getValue());
			$productdataSpuVarient['SPU Variant1value']			=	trim($objWorksheet->getCellByColumnAndRow(50,$i)->getValue());
			$productdataSpuVarient['SPU Variant2name']			=	trim($objWorksheet->getCellByColumnAndRow(51,$i)->getValue());
			$productdataSpuVarient['SPU Variant2value']			=	trim($objWorksheet->getCellByColumnAndRow(52,$i)->getValue());
			$productdataSpuVarient['SPU Variant3name']			=	trim($objWorksheet->getCellByColumnAndRow(53,$i)->getValue());
			$productdataSpuVarient['SPU Variant3value']			=	trim($objWorksheet->getCellByColumnAndRow(54,$i)->getValue());
			$productdataSpuVarient['SPU Variant4name']			=	trim($objWorksheet->getCellByColumnAndRow(55,$i)->getValue());
			$productdataSpuVarient['SPU Variant4value']			=	trim($objWorksheet->getCellByColumnAndRow(56,$i)->getValue());
			$productdataSpuVarient['SPU Variant5name']			=	trim($objWorksheet->getCellByColumnAndRow(57,$i)->getValue());
			$productdataSpuVarient['SPU Variant5value']			=	trim($objWorksheet->getCellByColumnAndRow(58,$i)->getValue());
			$productdata['Package Length(cm)']					=	trim($objWorksheet->getCellByColumnAndRow(59,$i)->getValue());
			$productdata['Package Width(cm)']					=	trim($objWorksheet->getCellByColumnAndRow(60,$i)->getValue());
			$productdata['Package Height(cm)']					=	trim($objWorksheet->getCellByColumnAndRow(61,$i)->getValue());
			$productdata['Package Weight(g)']					=	trim($objWorksheet->getCellByColumnAndRow(62,$i)->getValue());
			$productdataAttribute['Attribute1name']				=	trim($objWorksheet->getCellByColumnAndRow(63,$i)->getValue());
			$productdataAttribute['Attribute1value']			=	trim($objWorksheet->getCellByColumnAndRow(64,$i)->getValue());
			$productdataAttribute['Attribute2name']				=	trim($objWorksheet->getCellByColumnAndRow(65,$i)->getValue());
			$productdataAttribute['Attribute2value']			=	trim($objWorksheet->getCellByColumnAndRow(66,$i)->getValue());
			$productdataAttribute['Attribute3name']				=	trim($objWorksheet->getCellByColumnAndRow(67,$i)->getValue());
			$productdataAttribute['Attribute3value']			=	trim($objWorksheet->getCellByColumnAndRow(68,$i)->getValue());
			$productdataAttribute['Attribute4name']				=	trim($objWorksheet->getCellByColumnAndRow(69,$i)->getValue());
			$productdataAttribute['Attribute4value']			=	trim($objWorksheet->getCellByColumnAndRow(70,$i)->getValue());
			$productdataAttribute['Attribute5name']				=	trim($objWorksheet->getCellByColumnAndRow(71,$i)->getValue());
			$productdataAttribute['Attribute5value']			=	trim($objWorksheet->getCellByColumnAndRow(72,$i)->getValue());
			$productdataAttribute['Attribute6name']				=	trim($objWorksheet->getCellByColumnAndRow(73,$i)->getValue());
			$productdataAttribute['Attribute6value']			=	trim($objWorksheet->getCellByColumnAndRow(74,$i)->getValue());
			$productdataAttribute['Attribute7name']				=	trim($objWorksheet->getCellByColumnAndRow(75,$i)->getValue());
			$productdataAttribute['Attribute7value']			=	trim($objWorksheet->getCellByColumnAndRow(76,$i)->getValue());
			$productdataAttribute['Attribute8name']				=	trim($objWorksheet->getCellByColumnAndRow(77,$i)->getValue());
			$productdataAttribute['Attribute8value']			=	trim($objWorksheet->getCellByColumnAndRow(78,$i)->getValue());
			$productdataAttribute['Attribute9name']				=	trim($objWorksheet->getCellByColumnAndRow(79,$i)->getValue());
			$productdataAttribute['Attribute9value']			=	trim($objWorksheet->getCellByColumnAndRow(80,$i)->getValue());
			$productdataAttribute['Attribute10name']			=	trim($objWorksheet->getCellByColumnAndRow(81,$i)->getValue());
			$productdataAttribute['Attribute10value']			=	trim($objWorksheet->getCellByColumnAndRow(82,$i)->getValue());
			$productdataAttribute['Attribute11name']			=	trim($objWorksheet->getCellByColumnAndRow(83,$i)->getValue());
			$productdataAttribute['Attribute11value']			=	trim($objWorksheet->getCellByColumnAndRow(84,$i)->getValue());
			$productdataAttribute['Attribute12name']			=	trim($objWorksheet->getCellByColumnAndRow(85,$i)->getValue());
			$productdataAttribute['Attribute12value']			=	trim($objWorksheet->getCellByColumnAndRow(86,$i)->getValue());
			$productdataAttribute['Attribute13name']			=	trim($objWorksheet->getCellByColumnAndRow(87,$i)->getValue());
			$productdataAttribute['Attribute13value']			=	trim($objWorksheet->getCellByColumnAndRow(88,$i)->getValue());
			$productdataAttribute['Attribute14name']			=	trim($objWorksheet->getCellByColumnAndRow(89,$i)->getValue());
			$productdataAttribute['Attribute14value']			=	trim($objWorksheet->getCellByColumnAndRow(90,$i)->getValue());
			$productdataAttribute['Attribute15name']			=	trim($objWorksheet->getCellByColumnAndRow(91,$i)->getValue());
			$productdataAttribute['Attribute15value']			=	trim($objWorksheet->getCellByColumnAndRow(92,$i)->getValue());
			
			
			$ProSCMDataArray = array(
								'ProductData'=> $productdata,
								'productdataImage'=> $productdataImage,
								'productdataSpuVarient'=> $productdataSpuVarient,
								'productdataAttribute'=> $productdataAttribute,
							);
							
			$this->load->model('quark/quark');
			//$this->model_quark_quark->insertProductList($ProSCMDataArray);
			echo '<pre>';
			print_r($ProSCMDataArray);die;
			
		}

	}
}
