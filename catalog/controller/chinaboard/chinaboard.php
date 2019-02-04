<?php
class ControllerChinaboardChinaboard extends Controller {
	public function index() {
		
	}

	public function GetAccessToken() 
	{
		$api_url = 'https://gloapi.chinabrands.com/v2/user/login';
		$client_secret = '57f0b43f3b939caa8ff569b4bfb27bf1';
		$data = array(
			'email' => 'globalpacts@gmail.com',
			'password' => 'Tongkart123',
			'client_id' => '1217456132'
			);
		$json_data = json_encode($data);
		$signature_string = md5($json_data.$client_secret); //ç­¾å??æ•°æ?®
		$post_data = 'signature='.$signature_string.'&data='.urlencode($json_data);
		$result = $this->VitaCurlPostMethod($api_url,$post_data);
		$token = $result['msg']['token'];
		return $token;
	}
	
	public function GetCategoryList()
	{
		$this->load->model('chinaboard/chinaboard');
		$token = $this->GetAccessToken();
		$api_url = 'https://gloapi.chinabrands.com/v2/category/index';
		$post_data = array( 'token' => $token,
				);
		$result = $this->VitaCurlPostMethod($api_url,$post_data);
		$this->model_chinaboard_chinaboard->insertCategoryList($result);
		//echo '<pre>';
		//print_r($result);die;
		
		/*$total_records = $result['msg']['total_records'];
		$total_pages = $result['msg']['total_pages'];
		$page_number = $result['msg']['page_number'];
		$return_count = $result['msg']['return_count'];
		$page_number = 1;
		do {
			if($page_number == $total_pages)
			{
				$result = $this->VitaCurlPostMethod($api_url,$post_data);
				$total_records = $result['msg']['total_records'];
				$total_pages = $result['msg']['total_pages'];
				$page_number = $result['msg']['page_number'];
				$return_count = $result['msg']['return_count'];
			}
			$this->model_chinaboard_chinaboard->insertDownloadList($result);
			$x++;
		} while ($page_number <= $total_pages);*/

		//echo '<pre>';
		//print_r($result['msg']['total_records']);die;
		
		//$this->model_chinaboard_chinaboard->insertCategoryList($result);
		//echo '<pre>';
		//print_r($result);die;
	}
	
	public function GetDownloadList()
	{
		$this->load->model('chinaboard/chinaboard');
		$token = $this->GetAccessToken();
		$api_url = 'https://gloapi.chinabrands.com/v2/user/inventory';
		$post_data = array( 'token' => $token, 'type' => 0, 'per_page' => 200, 'page_number' => 1, );
				
		$result = $this->VitaCurlPostMethod($api_url,$post_data);
		$total_records = $result['msg']['total_records'];
		$total_pages = $result['msg']['total_pages'];
		$page_number = $result['msg']['page_number'];
		$return_count = $result['msg']['return_count'];
		$page_number = 1;
		do {
			
			$this->model_chinaboard_chinaboard->insertDownloadList($result);
			if($page_number != $total_pages && $page_number != 1)
			{ 	
				$post_data = array( 'token' => $token, 'type' => 0, 'per_page' => 200, 'page_number' => $page_number, );
				$result = $this->VitaCurlPostMethod($api_url,$post_data);
				$total_records = $result['msg']['total_records'];
				$total_pages = $result['msg']['total_pages'];
				$page_number = $result['msg']['page_number'];
				$return_count = $result['msg']['return_count'];
				$this->model_chinaboard_chinaboard->insertDownloadList($result);
			}
			
			$page_number++;
		} while ($page_number <= $total_pages);
	}
	
	public function GetProductInfo()
	{
		$this->load->model('chinaboard/chinaboard');
		$goods = $this->model_chinaboard_chinaboard->getGoods();
		$i=0;
		$goodsSN = array();
		foreach($goods as $key=>$goods)
		{	
			$goodsSN[$i] = $goods['goods_sn'];
			$i++;
		}
		$array_sn = array_chunk($goodsSN,100);
		
		$data_goods = "'".implode(",",$goodsSN)."'";
		$token = $this->GetAccessToken();
		$api_url = 'https://gloapi.chinabrands.com/v2/product/index';
		
	
		foreach($array_sn as $sn){
			$data_goods = "'".implode(",",$sn)."'";
			$post_data = array( 'token' => $token, 'goods_sn' => json_encode($data_goods) );
			$result = $this->VitaCurlPostMethod($api_url,$post_data);
			$this->model_chinaboard_chinaboard->insertProductInfo($result); 
									
		}
	}
	public function GetProductStock()
	{
		$this->load->model('chinaboard/chinaboard');
		$goods = $this->model_chinaboard_chinaboard->getProductSku();
		$i=0;
		$goodsSN = array();
		foreach($goods as $key=>$goods)
		{	
			$goodsSN[$i] = $goods['sku'];
			$i++;
		}
		$array_sn = array_chunk($goodsSN,50);
		
		$data_goods = "'".implode(",",$goodsSN)."'";
		$token = $this->GetAccessToken();
		$api_url = 'https://gloapi.chinabrands.com/v2/product/stock';
	
		foreach($array_sn as $sn){
			$data_goods = implode(",",$sn);
			$post_data = array( 'token' => $token, 'goods_sn' => json_encode($data_goods), 'warehouse' => 'YB' );
			$result = $this->VitaCurlPostMethod($api_url,$post_data);
			//echo '<pre>';
			//print_r($result);die;
			$this->model_chinaboard_chinaboard->updateProductStock($result);
									
		}
	}
	public function ShippingMethod()
	{	$token = $this->GetAccessToken();
		$api_url = 'https://cnapi.chinabrands.com/v2/shipping/index';
		$post_data = array( 'token' => $token );
		$result = $this->VitaCurlPostMethod($api_url,$post_data);
		echo "<pre>";print_r($result);echo "</pre>12345";die; 
	}
	public function ProcessOrder()
	{	
		header('Content-Type: text/html; charset=utf-8');
		$this->load->model('chinaboard/chinaboard');
		$goods = $this->model_chinaboard_chinaboard->getOrders();
		$token = $this->GetAccessToken(); 
		$api_url = 'https://gloapi.chinabrands.com/v2/order/create';
                $client_secret = '57f0b43f3b939caa8ff569b4bfb27bf1';
		//echo "<pre>";print_r($goods);echo "</pre>12345";die;
		foreach($goods as $sn){
                        $amount = $this->model_chinaboard_chinaboard->getOrdersAmount($sn['order_id']);
                        $goods_info = $this->model_chinaboard_chinaboard->getGoodsInfo($sn['order_id']);
                        $order = array(
                            '0' => array(
                                'user_order_sn' => $sn['temp_id'],
                                'country' => 'CN',
                                'warehouse' => 'YB',
                                'firstname' => 'Leyo',
                                'lastname' => '',
                                'addressline1' => '深南東路3020號百貨廣場東座2603室',
                                'addressline2' => '深圳市羅湖區',
                                'shipping_method' => 'GN', 
                                'tel' => '13410153396',
                                'state' => '广东省',
                                'city' => '深圳市',
                                'zip' => '518001',
                                'order_remark' => 'Ship',
                                'original_order_amount' => $amount,
                                'goods_info' => $goods_info
                            ),
                        );
			   //echo "<pre>";print_r($order);echo "</pre>";die;
                        $post_data = array( 'token' => $token, 'signature' => md5($client_secret.json_encode($order)), 'order' => json_encode($order) );
			   //echo "<pre>";print_r($post_data);echo "</pre>";die;
			$result = $this->VitaCurlPostMethod($api_url,$post_data);
			//echo "<pre>";print_r($result);echo "</pre>";die;
                     $this->model_chinaboard_chinaboard->updateOrder($result,$sn['temp_id']);
									
		}
	}
	
	public function GetOrderDetails()
	{
		header('Content-Type: text/html; charset=utf-8');
		$this->load->model('chinaboard/chinaboard');
		$token = $this->GetAccessToken(); 
		$api_url = 'https://gloapi.chinabrands.com/v2/order/index ';
		
		$post_data = array( 'token' => $token,'per_page' => 100, 'page_number' => 1 ); 
		$result = $this->VitaCurlPostMethod($api_url,$post_data);
		
		$total_records = $result['msg']['total_records'];
		$total_pages = $result['msg']['total_pages'];
		$page_number = $result['msg']['page_number'];
		$return_count = $result['msg']['return_count'];
		$page_number = 1;
		do {
			
			$this->model_chinaboard_chinaboard->getOrderDetails($result);
			if($page_number != $total_pages && $page_number != 1)
			{ 	
				$post_data = array( 'token' => $token, 'per_page' => 100, 'page_number' => $page_number, );
				$result = $this->VitaCurlPostMethod($api_url,$post_data);
				$total_records = $result['msg']['total_records'];
				$total_pages = $result['msg']['total_pages'];
				$page_number = $result['msg']['page_number'];
				$return_count = $result['msg']['return_count'];
				$this->model_chinaboard_chinaboard->getOrderDetails($result);
			}
			
			$page_number++;
		} while ($page_number <= $total_pages);
		
	}
	
	public function VitaCurlPostMethod($api_url,$post_data)
	{
		$curl = curl_init($api_url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		$result = curl_exec($curl); //è¿”å›žç»“æžœ
		$data = json_decode($result,true);
		curl_close($curl);
				
	return $data;
	}
}
