<?php
class ControllerTrackTrack extends Controller {
	private $error = array();

	public function index() {
		
	}
	
	public function trackShipments() {
		$this->load->model('track/track');
		$tracking_numbers = $this->model_track_track->getTrackingNumbers();
		$credentials = $this->model_track_track->getCredentials();
		foreach ($tracking_numbers as $result) {
			$opts = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
			$context = stream_context_create($opts);
			// Here I call my external function
			$user = $credentials['user'];
			$password = $credentials['password'];
			$awb = $result['tracking_number'];
			
			$result = "http://icms.grandslamexpress.in/V2/ClientAPI.asmx/GetAwbPacketStatus?UserId=$user&Password=$password&AWBNo=$awb";
			$result = @file_get_contents($result, FALSE, $context);
			
			echo '<pre>';
			print_r($response);die;
			
			$page = file_put_contents('img/GetAwbPacketStatus.xml',$result);
			$page_xml = 'http://store.tongkart.com/tngkart/img/GetAwbPacketStatus.xml';
			$data = file_get_contents($page_xml);
			$xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $data);
			$xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			//print_r($array);die;
			$dataResult = array(); 
			if(!empty($array['GetPacketStatus']['AWBNo']))
			{
				$dataResult['AWBNo'] = $array['GetPacketStatus']['AWBNo'];
				$dataResult['Msg'] = $array['GetPacketStatus']['Msg'];
				$dataResult['Status'] = $array['GetPacketStatus']['Status'];
				$dataResult['Time'] = $array['GetPacketStatus']['Time'];
				$dataResult['Memo'] = $array['GetPacketStatus']['Memo'];
				$dataResult['Location'] = $array['GetPacketStatus']['Location'];
				$this->model_track_track->insertPacketStatus($dataResult);
			}
			
		}
		
	}
	
}
