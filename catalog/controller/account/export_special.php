<?php
class ControllerAccountExportSpecial extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/export_special');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/export_special');

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
                        $data['page'] = $page;
		} else {
			$page = 1;
		}
                if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
                        $url .= '&limit='.$limit;
		} else {
			$limit = 10;
		}

		$data['action'] = $this->url->link('account/export_special', '', true);
                $data['action_import'] = $this->url->link('account/export_special/import', '', true);
                $data['action_export'] = $this->url->link('account/export_special/export', '', true);

		$data['newsletter'] = $this->customer->getNewsletter();

		$data['back'] = $this->url->link('account/account', '', true);
                
                $data['price'] = $this->url->link('account/price');
                $data['brand'] = $this->url->link('account/brand');
                $data['export_all'] = $this->url->link('account/export_all');
                $data['export_special'] = $this->url->link('account/export_special');
                
                
                $data['api'] = $this->url->link('account/api');
                $data['upc'] = $this->url->link('account/upc');
                $data['account'] = $this->url->link('account/account');
                $data['catalog'] = $this->url->link('account/catalog');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['new'] = $this->url->link('account/new');
                $data['total'] = $this->url->link('account/total');
                $data['listed'] = $this->url->link('account/listed');
                $data['not_listed'] = $this->url->link('account/not_listed');
                $data['monitor'] = $this->url->link('account/monitor');
                $data['sample_xls'] = HTTP_SERVER.'image/export_import/Upc.xlsx';
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['common'] = $this->load->controller('account/common');
                $total_export = $this->model_account_export_special->getTotalUpc($this->customer->getId());
                $data['total_export'] = $total_export;
                $total_used_export = $this->model_account_export_special->getTotalUsedUpc($this->customer->getId());
                $data['total_used_export'] = $total_used_export;
                $total_un_used_export = $this->model_account_export_special->getTotalUnUsedUpc($this->customer->getId());
                $data['total_un_used_export'] = $total_un_used_export;
                $settings = $this->model_account_export_special->getSettings($this->customer->getId());
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
                $querry = 0;
                if (isset($this->request->get['filter_product_id'])) {
			$filter_product_id = $this->request->get['filter_product_id'];
                        $data['filter_product_id'] = $filter_product_id;
                        $querry = 1;
                        $url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_product_id = '';
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
                $filter_data = array(
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit,
                        'filter_product_id' => $filter_product_id,
                        'filter_stock_start' => $filter_stock_start,
                        'filter_stock_end' => $filter_stock_end,
                        'category_id' => $categories
		);
                if ($querry == 0){
                    $total_export_product = 0;
                    $export_product = array();
                } else{
                    $total_export_product = $this->model_account_export_special->getTotalProductsSpecial($filter_data);
                    $export_product = $this->model_account_export_special->getProductsSpecial($filter_data);
                }
                $data['products'] = array();
                foreach($export_product as $list){
                    $category_details = $this->model_account_export_special->getProductCategory($list['product_id']);
                    $data['products'][] = array(
					'product_id'  => $list['product_id'],
                                        'model'       => $list['model'],
                                        'sku'       => $list['sku'],
                                        'quantity'       => $list['quantity'],
                                        'price'       => $list['price'],
                                        'category'           => $category_details
					
				);
                }
                $pagination = new Pagination();
                $pagination->total = $total_export_product;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('account/export_special',$url.'&page={page}');

                $data['pagination'] = $pagination->render();
                $data['results'] = sprintf($this->language->get('text_pagination'), ($total_export_product) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_export_product - $limit)) ? $total_export_product : ((($page - 1) * $limit) + $limit), $total_export_product, ceil($total_export_product / $limit));
		$this->response->setOutput($this->load->view('account/export_special', $data));
	}
        public function export() {
            ini_set('max_execution_time', 300000);
            ini_set('memory_limit', '1024M');
            $this->load->model('account/export_special');
            $querry = 0;
            if (isset($this->request->get['filter_product_id'])) {
                    $filter_product_id = $this->request->get['filter_product_id'];
                    $data['filter_product_id'] = $filter_product_id;
                    $querry = 1;
            } else {
                    $filter_product_id = '';
            }
            if (isset($this->request->get['filter_stock_start'])) {
                    $filter_stock_start = $this->request->get['filter_stock_start'];
                    $data['filter_stock_start'] = $filter_stock_start;
                    $querry = 1;
            } else {
                    $filter_stock_start = '';
            }
            if (isset($this->request->get['filter_stock_end'])) {
                    $filter_stock_end = $this->request->get['filter_stock_end'];
                    $data['filter_stock_end'] = $filter_stock_end;
                    $querry = 1;
            } else {
                    $filter_stock_end = '';
            }
            if (isset($this->request->get['product_category'])) {
                    $categories = $this->request->get['product_category'];
                    $querry = 1;
            } else {
                    $categories = '';
            }
            $filter_data = array(
                    'filter_product_id' => $filter_product_id,
                    'filter_stock_start' => $filter_stock_start,
                    'filter_stock_end' => $filter_stock_end,
                    'category_id' => $categories
            );
            if ($querry == 0){
                $export_product = array();
            } else{
                $export_product = $this->model_account_export_special->getAllProductsSpecial($filter_data);
            }
            require_once DIR_SYSTEM.'library/PHPExcel/Classes/PHPExcel.php';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("fd");
            $objPHPExcel->getProperties()->setLastModifiedBy("dsf");
            $objPHPExcel->getProperties()->setTitle("fds");
            $objPHPExcel->getProperties()->setSubject("fds");
            $objPHPExcel->getProperties()->setDescription("dfs");

            // Add some data
            $objPHPExcel->setActiveSheetIndex(0);

            $objPHPExcel->getActiveSheet()->SetCellValue("A1","Product Id"); 
            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Product Name");
            $objPHPExcel->getActiveSheet()->SetCellValue("C1","Listing SKU");
            $objPHPExcel->getActiveSheet()->SetCellValue("D1","Original SKU");
            $objPHPExcel->getActiveSheet()->SetCellValue("E1","Weight");
            $objPHPExcel->getActiveSheet()->SetCellValue("F1","Length");
            $objPHPExcel->getActiveSheet()->SetCellValue("G1","Height");
            $objPHPExcel->getActiveSheet()->SetCellValue("H1","Color");
            $objPHPExcel->getActiveSheet()->SetCellValue("I1","Size");
            $objPHPExcel->getActiveSheet()->SetCellValue("J1","Price");
            $objPHPExcel->getActiveSheet()->SetCellValue("K1","Category");
            $objPHPExcel->getActiveSheet()->SetCellValue("L1","Description");
            $objPHPExcel->getActiveSheet()->SetCellValue("M1","Amazon Description");
            $objPHPExcel->getActiveSheet()->SetCellValue("N1","Main Image");
            $objPHPExcel->getActiveSheet()->SetCellValue("O1","Additional Image");
            $objPHPExcel->getActiveSheet()->SetCellValue("P1","Additional Image");
            $objPHPExcel->getActiveSheet()->SetCellValue("Q1","Additional Image");
            $objPHPExcel->getActiveSheet()->SetCellValue("R1","Additional Image");
            $objPHPExcel->getActiveSheet()->SetCellValue("S1","Additional Image");
            $objPHPExcel->getActiveSheet()->SetCellValue("T1","Additional Image");
            $objPHPExcel->getActiveSheet()->SetCellValue("U1","Additional Image");
            $objPHPExcel->getActiveSheet()->SetCellValue("V1","Additional Image");
            $i=1;    
            $SrNo=1;
            foreach($export_product as $detail){
                $image_details = $this->model_account_export_special->getProductImage($detail['product_id']);
                $category_details = $this->model_account_export_special->getProductCategory($detail['product_id']);
                $i++;
                $detail['description'] = html_entity_decode($detail['description']);
                $objPHPExcel->getActiveSheet()->SetCellValue("A$i","{$detail['product_id']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("B$i","{$detail['name']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("C$i","{$detail['model']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("D$i","{$detail['sku']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("E$i","{$detail['weight']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("F$i","{$detail['length']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("G$i","{$detail['width']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("H$i","{$detail['color']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("I$i","{$detail['size']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("J$i","{$detail['price']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("K$i","{$category_details}");
                $objPHPExcel->getActiveSheet()->SetCellValue("L$i","{$detail['description']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("M$i","{$detail['amazon_description']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("N$i","{$detail['image']}");
                if(isset($image_details[0]['image']) && $image_details[0]['image'] != ''){
                    $objPHPExcel->getActiveSheet()->SetCellValue("O$i","{$image_details[0]['image']}");
                }
                if(isset($image_details[1]['image']) && $image_details[1]['image'] != ''){
                    $objPHPExcel->getActiveSheet()->SetCellValue("P$i","{$image_details[1]['image']}");
                }
                if(isset($image_details[2]['image']) && $image_details[2]['image'] != ''){
                    $objPHPExcel->getActiveSheet()->SetCellValue("Q$i","{$image_details[2]['image']}");
                }
                if(isset($image_details[3]['image']) && $image_details[3]['image'] != ''){
                    $objPHPExcel->getActiveSheet()->SetCellValue("R$i","{$image_details[3]['image']}");
                }
                if(isset($image_details[4]['image']) && $image_details[4]['image'] != ''){
                    $objPHPExcel->getActiveSheet()->SetCellValue("S$i","{$image_details[4]['image']}");
                }
                if(isset($image_details[5]['image']) && $image_details[5]['image'] != ''){
                    $objPHPExcel->getActiveSheet()->SetCellValue("T$i","{$image_details[5]['image']}");
                }
                if(isset($image_details[6]['image']) && $image_details[6]['image'] != ''){
                    $objPHPExcel->getActiveSheet()->SetCellValue("U$i","{$image_details[6]['image']}");
                }
                if(isset($image_details[7]['image']) && $image_details[7]['image'] != ''){
                    $objPHPExcel->getActiveSheet()->SetCellValue("V$i","{$image_details[7]['image']}");
                }
                $SrNo++;

            }
            $objPHPExcel->getActiveSheet()->setTitle('Simple');

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(55);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
            $filename = strtotime("now").'export_product.xls';
            $filepath = DIR_IMAGE.'export_export/'.$filename;
            $temp_filepath = 'export_export/'.$filename;
            //$this->model_account_export_special->insertUpcExportMonitor($temp_filepath,$this->customer->getId());
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            ob_clean();
            ob_flush();

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        public function test() {
            //die;
            $this->load->model('account/export_special');
            $string_category = '12001,12002,12003,12004,12005,12006,12007,12008,12009,12010,12011,12013,12014,12015,12016,12017,12018,12019,12020,12021,12022,12023,12024,12025,12026,12029,12030,12031,12032,12033,12034,12035,12036,12037,12038,12039,12040,12041,12042,12043,12044,12045,12046,12047,12048,12049,12050,12051,12052,12053,12054,12055,12056,12057,12058,12059,12060,12061,12062,12063,12064,12065,12066,12067,12068,12069,12070,12071,12072,12073,12074,12075,12076,12077,12078,12079,12080,12081,12082,12083,12084,12085,12086,12087,12088,12089,12090,12091,12092,12093,12094,12095,12096,12097,12098,12099,12100,12101,12102,12103,12104,12105,12106,12107,12108,12109,12110,12111,12112,12113,12114,12115,12116,12117,12118,12119,12120,12121,12122,12123,12124,12126,12127,12128,12129,12130,12131,12132,12133,12134,12137,12138,12139,12140,12141,12142,12143,12144,12145,12147,12148,12149,12150,12152,12154,12156,12157,12158,12159,12160,12161,12162,12163,12165,12166,12167,12168,12169,12170,12171,12172,12173,12174,12175,12178,12179,12180,12181,12182,12183,12184,12185,12186,12187,12188,12189,12190,12191,12192,12194,12195,12196,12197,12198,12199,12201,12202,12204,12207,12209,12210,12211,12212,12213,12214,12215,12217,12218,12219,12220,12223,12224,12225,12226,12227,12228,12229,12230,12231,12232,12233,12234,12236,12237,12238,12239,12241,12242,12243,12244,12245,12246,12247,12248,12249,12250,12251,12252,12253,12254,12255,12256,12257,12258,12259,12260,12261,12262,12263,12264,12265,12266,12267,12268,12269,12270,12271,12272,12273,12274,12275,12276,12277,12278,12279,12280,12281,12282,12283,12284,12285,12286,12287,12289,12290,12291,12292,12293,12294,12295,12296,12297,12298,12300,12301,12302,12303,12304,12305,12306,12307,12308,12309,12310,12311,12312,12313,12314,12315,12317,12318,12319,12320,12321,12323,12324,12325,12326,12327,12328,12329,12330,12331,12332,12333,12335,12336,12337,12338,12339,12340,12341,12342,12343,12344,12345,12346,12347,12349,12350,12351,12353,12354,12355,12356,12357,12358,12359,12360,12361,12362,12363,12365,12366,12367,12368,12369,12370,12371,12372,12373,12374,12375,12376,12377,12378,12379,12380,12381,12382,12383,12384,12385,12386,12387,12388,12389,12390,12391,12392,12393,12394,12395,12396,12397,12398,12399,12400,12401,12402,12403,12404,12405,12406,12407,12408,12409,12410,12411,12412,12414,12415,12416,12417,12418,12419,12420,12421,12422,12423,12424,12426,12427,12428,12429,12430,12431,12432,12433,12434,12435,12436,12437,12439,12440,12441,12442,12443,12444,12445,12446,12447,12448,12449,12450,12451,12452,7497,7499,12455,12456,12457,12458,12459,12460,12461,12462,12463,12464,13126,13127,13128,13129,13130,13131,12465,13135,13136,13137,13139,13140,12466,12467,12469,12470,12471,12472,12474,12475,12476,12477,12478,12479,12480,12481,12482,12483,12484,12485,13211,13212,13213,13214,13215,13216,12486,12487,12488,12489,12490,12492,12493,12494,12495,12496,12497,13132,13133,13134,13142,13143,13144,13145,13146,13147,13149,13150,13151,13152,13153,13154,13155,13156,13157,13159,13169,13175,13180,13184,13187,13188,13192,13193,13199,12500,12501,12502,12503,12504,12505,12506,12507,12508,12510,12511,12512,12513,12514,12516,12517,12518,12519,12520,12521,12522,12524,12525,12526,12527,12528,12529,12530,12531,12532,12533,12534,12535,12536,12538,12539,12541,12542,12543,12544,12545,12546,12547,12548,12549,12550,12551,12552,12553,12554,12555,12556,12557,12558,12560,12561,12562,12563,12564,12565,12566,12567,12568,12569,12570,12571,12572,12573,12574,12575,12576,12577,12578,12579,12580,12581,12582,12583,12584,12585,12586,12587,12588,12589,12590,12591,12592,12593,12594,12595,12596,12597,12598,12599,12600,12601,12602,12603,12604,12606,12607,12608,12609,12610,12611,12612,7495,12613,12614,12615,12616,12617,12618,12619,12620,12621,12622,12623,12624,12625,12626,12627,12628,12629,12630,12631,12632,12633,12634,12636,12637,12638,12639,12640,12641,12642,8106,12643,12644,12645,12646,8113,12713,12714,12715,12716,12717,12718,12719,12720,12721,12722,12723,12724,12725,12726,12727,12728,12729,12730,12731,12732,12733,12734,12735,12736,12737,12738,12739,12740,12741,12742,12743,12744,12745,12746,12747,12748,12749,12750,12751,12752,12753,12754,12755,12756,12757,12758,12759,12760,12761,12762,12763,12764,12765,12766,12767,12768,12769,12770,12771,12772,12773,12774,12775,12776,12777,12778,12779,12780,12781,12782,12783,12784,12785,12786,12787,12788,12789,12790,12791,12792,12793,12794,12795,12796,12797,12798,12799,12801,12802,12803,12804,12805,12806,12807,12808,12809,12810,12811,12812,12813,12814,12815,12816,12817,12818,12819,12820,12821,12822,12823,12824,12825,12826,12827,12828,12829,12830,12831,12833,12834,12835,12836,12837,12838,12839,12840,12842,12843,12844,12845,12846,12848,12849,12850,12851,12852,12853,12854,12856,12857,12858,12859,12860,12861,12862,12863,12864,12865,12866,12867,12868,12869,12870,12871,12872,12873,12874,12875,12877,12878,12879,12880,12881,12882,12883,12884,12885,12886,12887,12888,12889,12890,12891,12892,12893,12894,12895,12896,12897,12898,12899,12900,12901,12902,12903,12904,12905,12906,12907,12908,12909,12910,12911,12912,12913,12914,12915,12916,12917,12918,12919,12920,12921,12922,12923,12924,12925,12926,12927,12928,12929,12930,12931,12932,12933,12934,12935,12936,12937,12938,12939,12940,12941,12942,12943,12944,12945,12946,12947,12948,12949,12950,12951,12952,12953,12954,12955,12956,12957,12958,12959,12960,12961,12962,12963,12964,12965,12966,12967,12968,12969,12970,12971,12972,12973,12974,12975,12976,12977,12978,12979,13075,13076,13077,13078,13079,13080,13081,13082,13083,13084,13086,13087,13088,13089,13090,13091,13092,13093,13094,13095,13096,13097,13098,13099,13101,13102,13103,13105,13106,13107,13108,13109,13110,13111,13112,13113,13114,13115,13116,13117,13118,13119,13120,13121,13122,13123,13124,8108,12648,12649,12650,12651,12652,12653,12654,12655,12656,12657,12658,12659,12660,12661,12662,12663,12664,12665,12666,12667,12668,12669,12670,12671,12672,12673,12674,12675,12676,12677,12678,12679,12680,12681,12682,12684,12685,12686,12687,12688,12689,12690,12691,12692,12693,12694,12695,12696,12697,12698,12699,12700,12701,12702,12703,12704,12705,12706,12707,12708,12709,12710,12711,12712,13035,13036,13037,13038,13039,13040,13041,13042,13043,13044,13045,13046,13048,13049,13050,13051,13052,13053,13054,13055,13057,13058,13059,13060,13061,13062,13063,13064,13065,13066,13067,13069,13070,13071,13072,13073,12981,12982,12983,12984,12985,12986,12987,12988,12989,12990,12991,12992,12993,12994,12995,12996,12997,12998,12999,13000,13001,13002,13003,13004,13005,13006,13007,13008,13009,13010,13011,13012,13014,13015,13016,13017,13018,13019,13020,13021,13022,13023,13024,13025,13026,13027,13030,13031,13032,13033,6,7,8,9,10,11,12,13,15,16,17,18,19,20,22,23,24,25,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,131,135,143,145,146,148,149,150,151,152,153,154,155,156,157,158,159,163,242,243,249,250,251,253,254,255,256,257,259,260,261,262,263,264,265,266,267,268,269,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,291,329,331,332,333,334,335,336,337,338,340,341,342,343,344,345,348,350,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,429,430,431,433,434,440,445,447,448,450,451,452,458,459,460,461,462,463,464,465,466,467,472,474,476,477,479,480,484,486,487,489,490,491,494,495,498,499,500,501,502,505,507,509,522,536,540,543,544,545,546,549,551,554,555,556,558,559,561,562,563,564,566,568,576,577,579,584,586,588,590,591,592,596,597,598,600,604,609,613,614,615,616,618,619,620,621,622,623,625,626,627,628,629,630,631,632,633,634,635,636,638,639,642,644,648,649,650,651,652,653,655,657,658,663,673,675,676,677,678,679,681,682,685,686,687,690,696,697,699,700,701,702,703,704,706,709,718,719,720,721,723,724,726,728,729,730,731,736,739,740,741,742,745,746,764,770,772,775,787,790,794,798,803,805,806,808,811,818,819,820,821,822,823,827,838,840,843,844,845,847,848,849,850,851,852,854,857,858,859,860,861,862,863,864,866,867,868,869,870,871,872,873,874,875,876,877,878,880,881,886,887,890,893,894,896,897,898,899,900,902,903,905,908,909,910,912,913,914,916,917,918,919,920,921,923,924,926,927,929,931,932,933,934,935,936,937,939,941,942,943,945,949,951,953,954,955,956,958,959,960,961,963,966,968,969,972,977,979,980,981,982,983,984,985,986,987,988,989,990,991,992,993,994,996,1001,1002,1003,1004,1005,1006,1007,1008,1009,1014,1015,1018,1021,1022,1027,1034,1036,1041,1042,1045,1052,1055,1057,1060,1061,1063,1064,1065,1066,1067,1068,1069,1070,1071,1072,1073,1077,1079,1081,1082,1083,1084,1085,1086,1087,1088,1090,1095,1096,1098,1100,1101,1102,1105,1107,1108,1109,1110,1111,1112,1113,1114,1115,1116,1117,1118,1119,1120,1121,1123,1125,1128,1130,1131,1132,1134,1137,1142,1143,1147,1150,1154,1156,1159,1160,1161,1166,1171,1175,1177,1179,1181,1183,1184,1185,1186,1187,1188,1189,1190,1192,1193,1198,1200,1202,1203,1204,1210,1211,1214,1216,1217,1218,1219,1230,1231,1232,1233,1234,1236,1246,1248,1250,1252,1253,1254,1255,1256,1260,1261,1263,1267,1268,1269,1270,1271,1273,1274,1275,1277,1278,1283,1288,1290,1292,1293,1294,1295,1296,1299,1302,1305,1307,1308,1310,1312,1314,1315,1317,1319,1321,1323,1324,1325,1327,1328,1329,1330,1333,1334,1335,1336,1337,1338,1339,1341,1343,1348,1350,1355,1356,1357,1358,1359,1361,1363,1364,1365,1367,1369,1371,1373,1375,1377,1379,1380,1381,1382,1383,1384,1385,1386,1387,1388,1389,1390,1391,1392,1393,1394,1408,1409,1410,1414,1416,1417,1418,1421,1426,1427,1431,1434,1435,1436,1437,1438,1442,1447,1452,1453,1454,1455,1456,1461,1463,1464,1465,1468,1469,1470,1472,1473,1476,1477,1478,1483,1484,1485,1486,1488,1489,1491,1492,1494,1497,1498,1499,1500,1501,1502,1503,1504,1505,1506,1508,1509,1511,1512,1515,1517,1518,1519,1520,1521,1522,1523,1524,1526,1528,1529,1531,1533,1536,1537,1538,1539,1540,1541,1542,1543,1544,1546,1548,1550,1551,1553,1564,1565,1566,1567,1568,1570,1571,1586,1588,1590,1595,1598,1599,1600,1603,1605,1608,1610,1612,1614,1615,1617,1619,1621,1624,1630,1632,1637,1638,1639,1640,1641,1644,1645,1647,1648,1649,1650,1651,1652,1653,1654,1655,1660,1663,1666,1670,1671,1672,1673,1674,1676,1677,1678,1684,1686,1691,1695,1697,1700,1707,1710,1711,1712,1713,1714,1718,1720,1723,1725,1726,1730,1737,1738,1739,1740,1741,1742,1744,1746,1749,1752,1759,1760,1763,1764,1765,1770,1771,1773,1774,1775,1779,1787,1788,1791,1792,1793,1795,1796,1798,1799,1801,1802,1803,1804,1805,1806,1807,1809,1810,1811,1814,1816,1826,1827,1828,1830,1831,1834,1836,1837,1844,1847,1856,1861,1866,1877,1878,1879,1880,1881,1882,1890,1891,1892,1893,1894,1895,1896,1897,1898,1899,1900,1901,1903,1904,1905,1908,1909,1910,1913,1915,1916,1917,1918,1919,1920,1921,1922,1924,1933,1934,1936,1940,1941,1942,1946,1949,1950,1951,1954,1955,1956,1958,1959,1960,1961,1962,1963,1973,1974,1975,1978,1979,1981,1982,1983,1984,1985,1986,1987,1988,1989,1990,1991,1992,1993,1994,1995,1996,1997,1998,1999,2000,2001,2002,2004,2005,2006,2007,2010,2011,2012,2014,2015,2016,2017,2018,2019,2020,2021,2022,2023,2024,2025,2029,2030,2031,2033,2048,2051,2052,2053,2054,2055,2056,2057,2060,2061,2062,2063,2064,2065,2069,2070,2072,2073,2075,2076,2082,2083,2084,2085,2086,2087,2088,2089,2090,2091,2092,2093,2094,2096,2098,2099,2100,2123,2124,2125,2126,2127,2129,2130,2131,2132,2133,2146,2148,2169,2177,2178,2179,2180,2187,2188,2189,2190,2191,2192,2193,2194,2195,2196,2197,2198,2199,2200,2201,2204,2205,2206,2209,2210,2221,2222,2223,2225,2226,2227,2228,2229,2230,2231,2232,2233,2234,2235,2236,2237,2238,2239,2240,2241,2242,2243,2244,2245,2246,2247,2248,2249,2250,2251,2252,2253,2254,2255,2256,2257,2258,2259,2260,2261,2262,2263,2264,2265,2266,2267,2268,2269,2270,2271,2272,2273,2274,2275,2276,2277,2278,2279,2280,2281,2282,2283,2284,2285,2286,2287,2288,2289,2290,2291,2292,2293,2294,2295,2296,2297,2298,2299,2300,2301,2302,2303,2304,2305,2306,2307,2308,2309,2310,2311,2312,2313,2314,2315,2316,2317,2318,2319,2320,2321,2322,2323,2324,2325,2326,2327,2328,2329,2330,2331,2332,2333,2334,2335,2336,2337,2338,2339,2340,2341,2342,2343,2344,2345,2346,2347,2348,2349,2350,2351,2352,2353,2355,2356,2357,2358,2359,2360,2362,2363,2365,2366,2367,2368,2369,2370,2371,2372,2373,2374,2375,2376,2377,2378,2379,2380,2381,2382,2383,2384,2385,2386,2387,2388,2389,2390,2391,2392,2393,2394,2395,2396,2398,2399,2400,2401,2402,2403,2404,2405,2406,2407,2408,2409,2410,2411,2412,2413,2414,2415,2416,2417,2418,2419,2420,2421,2422,2423,2424,2425,2426,2427,2428,2429,2430,2431,2432,2433,2434,2435,2437,2438,2439,2440,2441,2442,2443,2444,2445,2446,2447,2448,2449,2450,2451,2452,2453,2454,2455,2456,2457,2458,2459,2460,2461,2462,2463,2464,2465,2466,2467,2468,2469,2470,2471,2472,2473,2474,2475,2476,2477,2478,2479,2480,2481,2482,2483,2484,2485,2486,2487,2488,2489,2490,2491,2492,2493,2494,2495,2496,2498,2499,2500,2501,2502,2503,2504,2505,2506,2507,2508,2509,2510,2511,2512,2514,2515,2516,2517,2518,2519,2520,2521,2522,2523,2524,2525,2526,2527,2528,2529,2530,2531,2532,2533,2535,2536,2537,2539,2540,2541,2542,2543,2544,2545,2546,2547,2548,2549,2550,2551,2553,2554,2555,2556,2557,2558,2559,2560,2561,2562,2563,2564,2565,2566,2567,2568,2569,2570,2571,2572,2573,2574,2575,2576,2577,2578,2579,2580,2581,2582,2583,2584,2585,2586,2588,2589,2590,2591,2592,2594,2597,2598,2599,2600,2601,2602,2603,2604,2605,2606,2607,2608,2609,2610,2611,2612,2613,2614,2615,2616,2617,2618,2619,2620,2621,2622,2623,2624,2625,2626,2627,2628,2629,2630,2631,2632,2633,2634,2635,2636,2637,2638,2639,2640,2641,2642,2643,2644,2645,2646,2647,2648,2649,2650,2651,2652,2653,2654,2655,2656,2657,2658,2659,2660,2661,2662,2663,2664,2665,2666,2667,2668,2669,2670,2671,2672,2673,2674,2675,2676,2677,2678,2679,2680,2681,2682,2684,2685,2686,2687,2689,2691,2692,2693,2694,2695,2701,2702,2705,2706,2707,2708,2709,2710,2712,2713,2714,2715,2716,2717,2718,2719';
            $array_category = explode(",",$string_category);
	     //echo "<pre>";print_r($array_category);echo "</pre>";die;
            $this->model_account_export_special->insertPath($array_category);
            
        }
        public function priceUpdate() {
            //die;
            $this->load->model('account/export_special');
            $this->model_account_export_special->priceUpdate();
            
        }
        public function autocomplete() {
                $this->load->model('account/export_special');
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_account_export_special->getCategories($filter_data);
			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}