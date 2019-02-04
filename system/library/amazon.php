<?php

include(dirname(dirname(dirname(__FILE__)))."/system/library/amazondb.php");
include(dirname(dirname(dirname(__FILE__)))."/system/library/signatureUtil.php");
include(dirname(dirname(dirname(__FILE__)))."/system/library/curl_request.php");
include(dirname(dirname(dirname(__FILE__)))."/system/library/handleFeedResponse.php");
class AmazonService extends AmazonDB {

    //public $AWSAccessKey = 'AKIAIVBYOWTWVB6NJZFQ';
    //public $SellerId = 'A1QYUNXXXLD5U2';
    //public $MarketplaceId = 'ATVPDKIKX0DER';
    //public $secretKey = 'LeBVRErO0H/bP3s+2c1pjfj5IrmpHub1GSrGthcV';
    public $algorithm = 'HmacSHA256';
    public $end_point = 'https://mws.amazonservices.in';
    public $path = '';
    public $UserAgent = '';
    public $host = 'mws.amazonservices.in';
    public $contentType;
    public $response_feed_file_size = 5242880; // 5 MB
    public $feed_ID = 0;
    public $feedType = '';
    public $fetchFeedFrom = '';

    /**
     * check store is valid or not
     * @param Object $data
     */
    public function checkSellerAccount($data,$state = 0) { 
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_ListMarketplaceParticipations>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/Sellers/2011-07-01';
        $this->contentType = 'application/x-www-form-urlencoded; charset=utf-8';
        if ($state == 1){
        $params = array(
            'AWSAccessKeyId' => $data['aws_key_id'],
            'Action' => 'ListMarketplaceParticipations',
            'SellerId' => $data['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2011-07-01',
            'SignatureMethod' => $this->algorithm,
        );
        } else{
           $params = array(
            'AWSAccessKeyId' => $data['aws_key_id'],
            'Action' => 'ListMarketplaceParticipations',
            'SellerId' => $data['merchant_id'],
	     'MWSAuthToken' => $data['auth_token'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2011-07-01',
            'SignatureMethod' => $this->algorithm,
        ); 
        }

        $signature = SignatureUtils::signParameters($params, $data['secret_key'], 'POST', $this->host, $this->path, $this->algorithm);
        
        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        
        $response = $this->SubmitRequest($this->end_point,'',$params, $signature);
        return $response;
    }

    /**
     * Check Product
     * @param string $ItemSku
     */
    public function ItemLookUp($ItemSku) {
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_GetMatchingProductForId>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/Products/2011-10-01';
        $this->contentType = 'application/x-www-form-urlencoded; charset=utf-8';
        $params = array(
            'AWSAccessKeyId' => $this->AWSAccessKey,
            'Action' => 'GetMatchingProductForId',
            'SellerId' => $this->SellerId,
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2011-10-01',
            'SignatureMethod' => $this->algorithm,
            'MarketplaceId' => $this->MarketplaceId,
            'IdType' => 'SellerSKU',
            'IdList.Id.1' => $ItemSku
        );

        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, $this->path, $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }

        $response = $this->SubmitRequest($this->end_point, $params, $signature);

        return $response;
    }

    /**
     * Function to submit passed inventory feed
     * on passed store
     * @param object $storeInfo
     * @param string $xmlFile
     */
    public function submitInventoryFeed($storeInfo, $filePath, $type,$state = 0) {

        $db = new AmazonDB();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_SubmitFeed>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/?';
        $this->contentType = 'text/xml';
        $this->secretKey = $storeInfo['secret_key'];
        $this->feedType = $type;
        $this->fetchFeedFrom = $filePath;

        if ($state == 0){
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
	     //'MWSAuthToken' => $storeInfo['auth_token']
        );
        } else{
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
	    'MWSAuthToken' => $storeInfo['auth_token']
        );
        }

        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $xml_data = file_get_contents($this->fetchFeedFrom);

        //$response = $this->SubmitFeedRequest($this->end_point, $params, $signature, $xml_data, $xmlFile);
        $response = $this->SubmitFeedRequest($this->end_point, $params, $signature, $xml_data, $this->fetchFeedFrom, $storeInfo['amazon_store_name']);
        return $response;
    }

    /**
     * Function to get the result 
     * of feed submission for passed feed ID
     * @param object $storeInfo
     * @param string $feedID
     */
    public function getInventoryFeedResponse($storeInfo, $Type = null, $feedID) {
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_SubmitFeed>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/?';
        $this->contentType = 'text/xml';
        $this->secretKey = $storeInfo['secret_key'];
        $this->feed_ID = $feedID;

        $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'GetFeedSubmissionResult',
            'FeedSubmissionId' => $this->feed_ID,
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureMethod' => $this->algorithm,
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01'
        );
        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        //print_r($params);die;
        // echo $this->end_point;
        $response = $this->SubmitRequest($this->end_point, $Type, $params, $signature, $storeInfo['amazon_store_name']);
        //print_r($response);die;
        return $response;
    }
    public function getFeedResponse($storeInfo, $Type = '', $feedID,$state = 0) {
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_SubmitFeed>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/?';
        $this->contentType = 'text/xml';
        $this->secretKey = $storeInfo['secret_key'];
        $this->feed_ID = $feedID;
	 if ($state == 0){
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'GetFeedSubmissionResult',
            'FeedSubmissionId' => $this->feed_ID,
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureMethod' => $this->algorithm,
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
        );
        } else{
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'GetFeedSubmissionResult',
            'FeedSubmissionId' => $this->feed_ID,
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureMethod' => $this->algorithm,
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
	     'MWSAuthToken' => $storeInfo['auth_token']
        );
        }
        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $response = $this->SubmitRequest($this->end_point, $Type, $params, $signature, $storeInfo['amazon_store_name']);
        return $response;
    }

    public function SubmitRequest($endPoint, $FeedType = '', $params, $signature, $sellercentral = '') {
        if ($params['Action'] == 'GetMatchingProductForId') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        } else if ($params['Action'] == 'ListMarketplaceParticipations') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        } else if ($params['Action'] == 'GetFeedSubmissionResult') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        } else if ($params['Action'] == 'GetCompetitivePricingForSKU') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        }else if ($params['Action'] == 'GetMyPriceForSKU') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        }else if ($params['Action'] == 'GetServiceStatus') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        }
        $curlrequest = new CurlRequest();
        $query = $curlrequest->_getParametersAsString($params);
        if ($params['Action'] == 'GetFeedSubmissionResult') {
            $url = $this->getUrl($query);
        } else {
            $url = $this->getUrl();
        }
        
        $headers = $this->getHeaders($query);
        $options = array(
            CURLOPT_URL => $url, //$this->end_point . $this->path,
            CURLOPT_PORT => '443',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_RETURNTRANSFER => true, // return web page 
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => 0,
            CURLOPT_USERAGENT => $this->UserAgent,
            CURLOPT_ENCODING => "UTF-8", // handle all encodings
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 50, // timeout on connect
            CURLOPT_TIMEOUT => 50, // timeout on response
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 0     // disable certificate checking
        );
        //echo "<pre>";print_r($options);echo "</pre>";die;
        $ch = curl_init($this->end_point . $this->path);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_PORT,'443');
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_USERAGENT,$this->UserAgent);
        curl_setopt($ch,CURLOPT_ENCODING,"UTF-8");
        curl_setopt($ch,CURLOPT_AUTOREFERER,true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,50);
        curl_setopt($ch,CURLOPT_TIMEOUT,50);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        
        //@curl_setopt_array($ch, $options);


        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        //Create a file to save the result of submiited inventory feed of passed feed id
        
        /*if ($this->feed_ID != 0 && !empty($this->feed_ID)) {
            $db = new AmazonDB();
            $file = $db->SaveFeedResponsePath('feed_id_result', $sellercentral);
            if (isset($FeedType)) {
                $feed_id_file = DIR_FS_AMAZON_FEED . $sellercentral . "/Response/" . $FeedType . "_" . $this->feed_ID . '.xml';
            } else {
                $feed_id_file = DIR_FS_AMAZON_FEED . $sellercentral . "/Response/" . $this->feed_ID . '.xml';
            }
            
            if (filesize($file) > $this->response_feed_file_size) {
                $fp = fopen($file, 'w+');
                $response_fp = fopen($feed_id_file, 'w+');
            } else {
                $response_fp = fopen($feed_id_file, 'w+'); //a
                $fp = fopen($file, 'a'); //a			
            }
            chmod($feed_id_file, 0775);

            fwrite($fp, '<FEED ID>' . $this->feed_ID . '</FEED>' . "\n");
            fwrite($fp, $content);
            fwrite($fp, "\n\n\n\n");
            fclose($fp);
            fwrite($response_fp, $content);
            fclose($response_fp);
            $createExcel = new handleFeedResponse();
            $createExcel->writeResponse($this->feed_ID, $content);
        }*/
        
        //Code to write competitive price detils on files
        if($params['Action'] == 'GetCompetitivePricingForSKU')
        {
            $competitivePriceResponseFile = "amazon_competitive_listings_".$params['FileIndex'].".xml";
            $competitivePriceResponseFilePath = DIR_FS_AMAZON_FEED . $sellercentral . "/CompetitivePrice/" . $competitivePriceResponseFile;
            $fp = fopen($competitivePriceResponseFilePath,'w+');
            fwrite($fp, $content);
            fclose($fp);
        }else if($params['Action'] == 'GetMyPriceForSKU')
        {
            $competitivePriceResponseFile = "amazon_myprice_listings_".$params['FileIndex'].".xml";
            $competitivePriceResponseFilePath = DIR_FS_AMAZON_FEED . $sellercentral . "/MyPrice/" . $competitivePriceResponseFile;
            $fp = fopen($competitivePriceResponseFilePath,'w+');
            fwrite($fp, $content);
            fclose($fp);
        }
        

        curl_close($ch);

        return $header;
    }

    public function SubmitFeedRequest($endPoint, $params, $signature, $xml_data, $xml_file, $sellerCentral) {
        if ($params['Action'] == 'SubmitFeed') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        }
        //echo $this->fetchFeedFrom;
        $db = new AmazonDB();
        $curlrequest = new CurlRequest();
        $query = $curlrequest->_getParametersAsString($params);
        if(preg_match("/shipping_feed.xml/", $this->fetchFeedFrom)){
            $headers = array (
                                'Expect: ',
                                'Accept: ',
                                'Transfer-Encoding: chunked',
                                'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
                                'Content-MD5: ' . base64_encode(md5_file($xml_file, true))
                            );
        }else{
            $headers = $this->getHeaders($this->fetchFeedFrom, 'feedSubmit');
        }
        
        //$fp = fopen($xml_file, 'w+');
        $url = $this->getUrl($query);
        $options = array(
            CURLOPT_URL => $this->getUrl($query), //$this->end_point . $this->path . $query,
            CURLOPT_PORT => '443',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $xml_data,
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_RETURNTRANSFER => true, // return web page 
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => 0,
            CURLOPT_USERAGENT => $this->UserAgent,
            CURLOPT_ENCODING => "UTF-8", // handle all encodings
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 15, // timeout on connect
            CURLOPT_TIMEOUT => 15, // timeout on response
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => false // disable certificate checking
        );
        $ch = curl_init($this->end_point . $this->path . $query);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_PORT,'443');
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_data);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_USERAGENT,$this->UserAgent);
        curl_setopt($ch,CURLOPT_ENCODING,"UTF-8");
        curl_setopt($ch,CURLOPT_AUTOREFERER,true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,50);
        curl_setopt($ch,CURLOPT_TIMEOUT,50);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        //curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);

        //Create a file to store the response from  Amazon
        //$file = $db->SaveFeedResponsePath($this->feedType, $sellerCentral);
        //echo $file;die;
        //if (filesize($file) > $this->response_feed_file_size) {
        //    $fp = fopen($file, 'w+');
        //} else {
        //    $fp = fopen($file, 'w+'); //a			
        //}
        //chmod($file, 0775);
        //fwrite($fp, $content);
        //fwrite($fp, "\n\n\n\n");
        //fclose($fp);


        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        curl_close($ch);
        return $header;
    }

    public function getHeaders($data, $action = NUll) {
        if ($action == 'feedSubmit') {
            $content_md5 = base64_encode(md5_file($data, true));
        } else {
            $content_md5 = base64_encode($data);
        }
        $headers = array(
            'Expect: ',
            'Accept: ',
            'HTTP/1.1',
            'Host: ' . $this->host,
            'Transfer-Encoding: chunked',
            'Content-Type: ' . $this->contentType,
            'Content-MD5: ' . $content_md5,
            'Connection: Keep-Alive'
        );
        return $headers;
    }

    public function getUrl($queryString = NUll) {
        if (!empty($queryString)) {
            return $this->end_point . $this->path . $queryString;
        } else {
            return $this->end_point . $this->path;
        }
    }

    /**
     * Function to submit the Amazon product feed
     * @param Object $storeInfo
     * @param String $filePath
     * @param String $type
     */
    public function submitProductFeed($storeInfo, $filePath, $type,$state = 0) {
        $db = new AmazonDB();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_SubmitFeed>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/?';
        $this->contentType = 'text/xml';
        $this->secretKey = $storeInfo['secret_key'];
        $this->feedType = $type;
        $this->fetchFeedFrom = $filePath;
        if ($state == 0){
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
	     //'MWSAuthToken' => $storeInfo['auth_token']
        );
        } else{
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
	    'MWSAuthToken' => $storeInfo['auth_token']
        );
        }
        

        //	print_r($params);die; 
        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $xml_data = file_get_contents($this->fetchFeedFrom);
        $response = $this->SubmitFeedRequest($this->end_point, $params, $signature, $xml_data, $this->fetchFeedFrom, $storeInfo['amazon_store_name']);
        //echo "<pre>";print_r($response);echo "</pre>111";die;
        return $response;
    } 

    /**
     * Function to submit the Amazon product feed
     * @param Object $storeInfo
     * @param String $filePath
     * @param String $type
     */
    public function submitPriceFeed($storeInfo, $filePath, $type, $state = 0) {

        $db = new AmazonDB();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_SubmitFeed>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/?';
        $this->contentType = 'text/xml';
        $this->secretKey = $storeInfo['secret_key'];
        $this->feedType = $type;
        $this->fetchFeedFrom = $filePath;

        if ($state == 0){
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
	     //'MWSAuthToken' => $storeInfo['auth_token']
        );
        } else{
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
	    'MWSAuthToken' => $storeInfo['auth_token']
        );
        } 

        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $xml_data = file_get_contents($this->fetchFeedFrom);

        $response = $this->SubmitFeedRequest($this->end_point, $params, $signature, $xml_data, $this->fetchFeedFrom, $storeInfo['amazon_store_name']);
        return $response;
    }

    /**
     * Function to submit the Amazon product feed
     * @param Object $storeInfo
     * @param String $filePath
     * @param String $type
     */
    public function submitImageFeed($storeInfo, $filePath, $type, $state = 0) {
        $db = new AmazonDB();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_SubmitFeed>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/?';
        $this->contentType = 'text/xml';
        $this->secretKey = $storeInfo['secret_key'];
        $this->feedType = $type;
        $this->fetchFeedFrom = $filePath;
	 if ($state == 0){
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
	     //'MWSAuthToken' => $storeInfo['auth_token']
        );
        } else{
            $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id'],
	    'MWSAuthToken' => $storeInfo['auth_token']
        );
        }
        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $xml_data = file_get_contents($this->fetchFeedFrom);

        $response = $this->SubmitFeedRequest($this->end_point, $params, $signature, $xml_data, $this->fetchFeedFrom, $storeInfo['amazon_store_name']);
        return $response;
    }

    /**
     * Function to submit the Amazon product feed
     * @param Object $storeInfo
     * @param String $filePath
     * @param String $type
     */
    public function submitRelationFeed($storeInfo, $filePath, $type) {
        $db = new AmazonDB();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_SubmitFeed>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/?';
        $this->contentType = 'text/xml';
        $this->secretKey = $storeInfo['secret_key'];
        $this->feedType = $type;
        $this->fetchFeedFrom = $filePath;

        $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id']
        );


        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $xml_data = file_get_contents($this->fetchFeedFrom);

        $response = $this->SubmitFeedRequest($this->end_point, $params, $signature, $xml_data, $this->fetchFeedFrom, $storeInfo['amazon_store_name']);
        return $response;
    }
    
    public function submitDeleteProductFeed($storeInfo, $filePath, $type) {
        $db = new AmazonDB();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_SubmitFeed>/<1.0> (Language=PHP/' . phpversion() . ')';
        $this->path = '/?';
        $this->contentType = 'text/xml';
        $this->secretKey = $storeInfo['secret_key'];
        $this->feedType = $type;
        $this->fetchFeedFrom = $filePath;

        $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'SubmitFeed',
            'Merchant' => $storeInfo['merchant_id'],
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SignatureMethod' => $this->algorithm,
            'FeedType' => $db->getFeedType($this->feedType),
            'MarketplaceIdList.Id.1' => $storeInfo['market_place_id']
        );

        //	print_r($params);die; 
        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $xml_data = file_get_contents($this->fetchFeedFrom);

        $response = $this->SubmitFeedRequest($this->end_point, $params, $signature, $xml_data, $this->fetchFeedFrom, $storeInfo['amazon_store_name']);
        return $response;
    }

    public function ListOrder($storeInfo,$state = 0) { 

        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $AWSAccessKeyId = $storeInfo['aws_key_id'];
        $MerchantId = $storeInfo['merchant_id'];
        $Marketplace = $storeInfo['market_place_id'];
        if ($state == 0){
          $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'ListOrders',
            'SellerId' => $MerchantId,
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2013-09-01',
            'SignatureMethod' => $this->algorithm,
            'LastUpdatedAfter' => $storeInfo['last_order_fetch'],
            'OrderStatus.Status.1' => 'Unshipped',
            'OrderStatus.Status.2' => 'PartiallyShipped',
            'OrderStatus.Status.3' => 'Shipped', 
            //'OrderStatus.Status.3' => 'Pending',
			'OrderStatus.Status.3' => 'Canceled',
            'MarketplaceId.Id.1' => $Marketplace,
	     //'MWSAuthToken' => $storeInfo['auth_token']
        );
        } else{
            $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'ListOrders',
            'SellerId' => $MerchantId,
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2013-09-01',
            'SignatureMethod' => $this->algorithm,
            'LastUpdatedAfter' => $storeInfo['last_order_fetch'],
            'OrderStatus.Status.1' => 'Unshipped',
            'OrderStatus.Status.2' => 'PartiallyShipped',
            'OrderStatus.Status.3' => 'Shipped', 
            //'OrderStatus.Status.3' => 'Pending',
			'OrderStatus.Status.3' => 'Canceled',
            'MarketplaceId.Id.1' => $Marketplace,
	     'MWSAuthToken' => $storeInfo['auth_token']
        );
        }
        
//        print_r($params); die();
        //$this->path = ORDER_REQUEST_URI;
	 $this->path = 'https://mws.amazonservices.in/Orders/2013-09-01';
        $signature = SignatureUtils::signParameters($params, $storeInfo['secret_key'], 'POST', $this->host, '/Orders/2013-09-01', $this->algorithm);
	 
        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $SellerCentral = $storeInfo['amazon_store_name'];
        //echo ORDER_REQUEST_URI;
	 //echo "<pre>";print_r($signature);echo "</pre>12345";die;
        $this->get_data = $this->RequestReport($SellerCentral, $this->end_point, $params, $signature);
	 
//        print_r($this->get_data);die;
        if ($this->get_data) {
            return $this->get_data;
        } else {
            return false;
        }
    }

    public function RequestReport($SellerCentral, $endPoint, array $params, $signature, $i = 0) {
        $this->end_point = $endPoint;
        //print_r($params);
        if ($params['Action'] == 'ListOrders') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        } else if ($params['Action'] == 'ListOrdersByNextToken') {
            $params = array_slice($params, 0, 4, true) + array("Signature" => $signature) + array_slice($params, 4, count($params) - 1, true);
        } else if ($params['Action'] == 'ListOrderItems') {
            $params = array_slice($params, 0, 4, true) + array("Signature" => $signature) + array_slice($params, 4, count($params) - 1, true);
        }
        //print_r($params);
        if ($params['Action'] == 'ListOrders') {
            $this->UserAgent = '<MWS_ListOrders>/<1.0> (Language=PHP/' . phpversion() . ')';
        } else if ($params['Action'] == 'ListOrdersByNextToken') {
            $this->UserAgent = '<MWS_ListOrders>/<1.0> (Language=PHP/' . phpversion() . ')';
        } else if ($params['Action'] == 'ListOrderItems') {
            $this->UserAgent = '<MWS_ListOrderItems>/<1.0> (Language=PHP/' . phpversion() . ')';
        } else {
            $this->UserAgent = '<MWS_RequestReport>/<1.0> (Language=PHP/' . phpversion() . ')';
        }
        $curlrequest = new CurlRequest();
        //print_r($params);
        $query = $curlrequest->_getParametersAsString($params);
        if ($params['Action'] == 'GetFeedSubmissionResult') {
            $url = $this->getUrl($query);
        } else if($params['Action'] == 'ListOrders') {
            $url = 'https://mws.amazonservices.in/Orders/2013-09-01';
        } else if($params['Action'] == 'ListOrderItems') {
            $url = 'https://mws.amazonservices.in/Orders/2013-09-01';
        }else {
            $url = $this->getUrl();
        }
        //echo $query;
        //echo $url;die;
        // $headers = $this->getHeaders($query);
        $headers = array(
            'Expect: ',
            'Accept: ',
            'HTTP/1.1',
            'Host:' . $this->host,
            'Transfer-Encoding: chunked',
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Content-MD5: ' . base64_encode($query),
            'Connection: Keep-Alive'
        );

        $options = array(
            CURLOPT_URL => $url, //$this->end_point . $this->path,
            CURLOPT_PORT => '443',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_RETURNTRANSFER => true, // return web page 
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => 0,
            CURLOPT_USERAGENT => $this->UserAgent,
            CURLOPT_ENCODING => "UTF-8", // handle all encodings
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 50, // timeout on connect
            CURLOPT_TIMEOUT => 50, // timeout on response
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => false, // disable certificate checking
        );

        $ch = curl_init($this->end_point . $this->path);
        //echo $ch;
        //print_r($options);die;

        if ($params['Action'] == 'ListOrders') {
            $feed_response_file = "amazon_get_list_orders_response.xml";
        } else if ($params['Action'] == 'ListOrdersByNextToken') {
            $feed_response_file = "amazon_get_list_orders_response_" . $i . ".xml";
        } else if ($params['Action'] == 'ListOrderItems') {
            $feed_response_file = $params['AmazonOrderId'] . ".xml";
        } else if ($params['ReportType'] == '_GET_FLAT_FILE_ORDERS_DATA_') {
            $feed_response_file = "amazon_get_orders_listings_report_response.xml";
        } else if ($params['ReportType'] == '_GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_') {
            $feed_response_file = "amazon_get_orders_listings_report_response.xml";
        } else if ($params['ReportType'] == '_GET_FLAT_FILE_PENDING_ORDERS_DATA_') {
            $feed_response_file = "amazon_get_orders_listings_report_response.xml";
        } else {
            $feed_response_file = "amazon_get_merchant_listings_report_response.xml";
        }

        

        @curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);

        curl_close($ch);

        
	 
        //echo '<pre>';print_r($header);echo '</pre>';exit;

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        return $header;
    }

    public function ListOrdersByNextToken($storeInfo, $parsed_result, $SellerCentral, $i = 0) {
         //echo'<pre>'; print_r($parsed_result); echo '</pre>';
sleep(10);
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $AWSAccessKeyId = $storeInfo['aws_key_id'];
        $MerchantId = $storeInfo['merchant_id'];

        if ($i == 0) {
            $ListOrders = $parsed_result['ListOrdersResponse']['ListOrdersResult'];
        } else {
            $ListOrders = $parsed_result['ListOrdersByNextTokenResponse']['ListOrdersByNextTokenResult'];
        }
        //print_r($ListOrders);
        if (is_array($ListOrders) && array_key_exists('NextToken', $ListOrders)) {
            
            $NextToken = $ListOrders['NextToken'];

            $params = array(
                'AWSAccessKeyId' => $AWSAccessKeyId,
                'Action' => 'ListOrdersByNextToken',
                'SellerId' => $MerchantId,
                'SignatureVersion' => 2,
                'Timestamp' => $timestamp,
                'Version' => '2013-09-01',
                'SignatureMethod' => $this->algorithm,
                'NextToken' => $NextToken
            );

            //echo '<pre>'; print_r($params); echo '</pre>'; exit;
            $signature = SignatureUtils::signParameters($params, $storeInfo['secret_key'], 'POST', $this->host, $this->path, $this->algorithm);

            if (preg_match('/%7E/', rawurlencode($signature))) {
                $signature = str_replace("%7E", "~", rawurlencode($signature));
            }

            $this->get_data = $this->RequestReport($SellerCentral, $this->end_point, $params, $signature,  $i);

            if ($this->get_data) {
                $feed_list_orders_file = "amazon_get_list_orders_response_" . $i . ".xml";
                $feed_list_orders_file_path = DIR_FS_AMAZON_FEED . $SellerCentral . '/Orders/ListOrders/' . $feed_list_orders_file;

                $file_content = @file_get_contents($feed_list_orders_file_path);
                $parsed_result = $this->xml2array($file_content);
//echo "Parsed result"; print_R($parsed_result);
		$i++;
                $this->ListOrdersByNextToken($storeInfo, $parsed_result,$SellerCentral,  $i);
            } else {
                $parsed_result = '';
            }
        } else {
            return $this->get_data;
        }
    }

    /* BOF - code to for fetch all the orders files from the folder by Ashwani Gupta on 15-Sep-2014 */

    public function amazon_list_orders_files($SellerCentral) {

        $AmazonFeedDir = DIR_FS_AMAZON_FEED . $SellerCentral . '/Orders/ListOrders';

        $PossibleListOrdersFiles = array();

        for ($i = 0; $i <= 100; $i++) {
            $PossibleListOrdersFiles[] = "amazon_get_list_orders_response_" . $i . ".xml";
        }

        //echo '<pre>'; print_r($PossibleListOrdersFiles); echo '</pre>'; exit;

        if (is_dir($AmazonFeedDir)) {
            $OpenDirectory = opendir($AmazonFeedDir);
            if ($OpenDirectory) {
                $ListOrdersFiles = array();
                while (($file = readdir($OpenDirectory)) !== false) {
                    if ($file == 'amazon_get_list_orders_response.xml' || in_array($file, $PossibleListOrdersFiles)) {
                        $ListOrdersFiles[] = array('filename' => $file, 'filetype' => filetype($AmazonFeedDir . '/' . $file));
                    }
                }
                closedir($OpenDirectory);
            }

            if (!empty($ListOrdersFiles)) {
                return $ListOrdersFiles;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /* EOF - code to for fetch all the orders files from the folder by Ashwani Gupta on 15-Sep-2014 */

    public function ListOrderItems($storeInfo, $SellerCentral, $AmazonOrderId, $state = 0) { 

        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $AWSAccessKeyId = $storeInfo['aws_key_id'];
        $MerchantId = $storeInfo['merchant_id'];
        if ($state == 0){
            $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'ListOrderItems',
            'SellerId' => $MerchantId,
            'AmazonOrderId' => $AmazonOrderId,
            'SignatureVersion' => '2',
            'SignatureMethod' => $this->algorithm,
            'Timestamp' => $timestamp,
            'Version' => '2013-09-01'
        );
        } else{
            $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'ListOrderItems',
            'SellerId' => $MerchantId,
            'AmazonOrderId' => $AmazonOrderId,
            'SignatureVersion' => '2',
            'SignatureMethod' => $this->algorithm,
            'Timestamp' => $timestamp,
            'Version' => '2013-09-01',
	    'MWSAuthToken' => $storeInfo['auth_token']
        );
        }
        

        //echo '<pre>'; print_r($params); echo '</pre>'; exit;
        //$this->path = ORDER_REQUEST_URI;
	 $this->path = 'https://mws.amazonservices.in/Orders/2013-09-01';
        $signature = SignatureUtils::signParameters($params, $storeInfo['secret_key'], 'POST', $this->host, '/Orders/2013-09-01', $this->algorithm);
        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $SellerCentral = $storeInfo['amazon_store_name'];
        //echo ORDER_REQUEST_URI;
        $this->get_data = $this->RequestReport($SellerCentral, $this->end_point, $params, $signature);
        //echo'<pre>';print_r($this->get_data);echo'</pre>';die;
        if ($this->get_data) {
            return $this->get_data;
        } else {
            return false;
        }
    }

    /* BOF - code to covert xml response to array by Ashwani Gupta on 01-Sep-2014 */

    public function ShippingConfirmation($storeInfo, $xml_file,$state = 0) {
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $AWSAccessKeyId = $storeInfo['aws_key_id'];
        $MerchantId = $storeInfo['merchant_id'];
        $Marketplace = $storeInfo['market_place_id'];
        $this->secretKey = $storeInfo['secret_key'];
        $this->fetchFeedFrom = $xml_file;
        $this->feedType = 'shipment_feed_id_result';
        $this->end_point = $this->end_point."/?";
        if ($state == 0){
            $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'SubmitFeed',
            'Merchant' => $MerchantId,
            'SignatureVersion' => '2',
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',                        
            'SignatureMethod' => $this->algorithm,
            'FeedType' => '_POST_ORDER_FULFILLMENT_DATA_',
            //'Marketplace' => $Marketplace,
            'MarketplaceIdList.Id.1' => $Marketplace,
            'PurgeAndReplace' => 'false'
        );
        } else{
            $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'SubmitFeed',
            'Merchant' => $MerchantId,
            'SignatureVersion' => '2',
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',                        
            'SignatureMethod' => $this->algorithm,
            'FeedType' => '_POST_ORDER_FULFILLMENT_DATA_',
            //'Marketplace' => $Marketplace,
            'MarketplaceIdList.Id.1' => $Marketplace,
            'PurgeAndReplace' => 'false',
            'MWSAuthToken' => $storeInfo['auth_token']
        );
        }
        
        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, '/', $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        $xml_data = file_get_contents($this->fetchFeedFrom);

        $response = $this->SubmitFeedRequest($this->end_point, $params, $signature, $xml_data, $this->fetchFeedFrom, $storeInfo['amazon_store_name']);
        //print_r($response);die;
        return $response;
    }

    public static function xml2array($contents, $get_attributes = 1, $priority = 'tag') {
        if (!$contents)
            return array();

        if (!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        //print_r($xml_values);die;
        if (!$xml_values)
            return; //Hmm...           
//Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference
        //Go through the tags.
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
        foreach ($xml_values as $data) {
            unset($attributes, $value); //Remove existing values, or there will be trouble
            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data); //We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if (isset($value)) {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if ($type == "open") {//The starting of the tag '<tag>'
                $parent[$level - 1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];
                } else { //There was another element with the same tag name
                    if (isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag . '_' . $level] = 2;

                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if (!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                } else { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            } elseif ($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level - 1];
            }
        }
        return($xml_array);
    }

    /* BOF - code to covert xml response to array by Ashwani Gupta on 01-Sept-2014 */
    
    //Begin - Function definition to get Competitive price of SKUs from Amazon - added by Harsh Agarwal on 27-January-2016
    public function getCompetitivePriceForSKU($storeInfo, $index, $skuList) {
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $this->UserAgent = '<MWS_GetCompetitivePricingForSKU>/<1.0> (Language=PHP/' . phpversion().')';
        $this->path = PRODUCT_REQUEST_URI;
        $this->contentType = 'application/x-www-form-urlencoded; charset=utf-8';
        $this->secretKey = $storeInfo['secret_key'];

        $params = array(
            'AWSAccessKeyId' => $storeInfo['aws_key_id'],
            'Action' => 'GetCompetitivePricingForSKU',
            'MarketplaceId' => $storeInfo['market_place_id'],
            'SellerId' => $storeInfo['merchant_id'],
            'SignatureMethod' => $this->algorithm,
            'SignatureVersion' => 2,
            'Timestamp' => $timestamp,
            'Version' => '2011-10-01',
            'FileIndex' => $index
        );
        $params = array_merge($params, $skuList);

        $signature = SignatureUtils::signParameters($params, $this->secretKey, 'POST', $this->host, PRODUCT_REQUEST_URI, $this->algorithm);

        if (preg_match('/%7E/', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        
        $response = $this->SubmitRequest($this->end_point, '', $params, $signature, $storeInfo['amazon_store_name']);
        
        return $response;
    }
    //End - Function definition to get Competitive price of SKUs from Amazon - added by Harsh Agarwal on 27-January-2016
    
    //BOC:@SakshiGopal 21-March-2017 fundtions to get reports from Amazon
    public function requestAmazonReport($storeInfo) {

        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $AWSAccessKeyId = $storeInfo['aws_key_id'];
        $MerchantId = $storeInfo['merchant_id'];
        $Marketplace = $storeInfo['market_place_id'];
        $this->path = AMAZON_REPORT_REQUEST_URI;
        $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'RequestReport',
            'EndDate'	=>  END_DATE,
            'MarketplaceId' => $Marketplace,
            'SellerId' => $MerchantId,
            'ReportType'=>'_GET_MERCHANT_LISTINGS_DATA_',
            'SignatureVersion' => 2,
            'SignatureMethod' => $this->algorithm,
            'StartDate'     =>  START_DATE,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SecretKey' => $storeInfo['secret_key']
        );        
        
        $signature = SignatureUtils::signParameters($params, $storeInfo['secret_key'], 'POST', $this->host, $this->path, $this->algorithm);

        if (eregi('%7E', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }        
        $SellerCentral = $storeInfo['amazon_store_name'];
        
        $this->get_data = $this->AmazonRequestReport($SellerCentral, $this->end_point, $params, $signature);

        if ($this->get_data) {
            return $this->get_data;
        } else {
            return false;
        }
    }    
    public function AmazonRequestReport($SellerCentral, $endPoint, array $params, $signature) {
        $this->end_point = $endPoint;
        $this->path = AMAZON_REPORT_REQUEST_URI;
        
        if ($params['Action'] == 'RequestReport') {
            $params = array_slice($params, 0, 7, true) + array("Signature" => $signature) + array_slice($params, 7, count($params) - 1, true);
        }

        $this->UserAgent = '<MWS_RequestReport>/<1.0> (Language=PHP/' . phpversion() . ')';
        $curlrequest = new CurlRequest();
        $query = $curlrequest->_getParametersAsString($params);
        $url = $this->getUrl();
        
        $headers = array(
            'Expect: ',
            'Accept: ',
            'HTTP/1.1',
            'Host:' . $this->host,
            'Transfer-Encoding: chunked',
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Content-MD5: ' . base64_encode($query),
            'Connection: Keep-Alive',
            'User-Agent: '.$this->UserAgent
        );

        $options = array(
            CURLOPT_URL => $url, //$this->end_point . $this->path,
            CURLOPT_PORT => '443',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_RETURNTRANSFER => true, // return web page 
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => 0,
            CURLOPT_USERAGENT => $this->UserAgent,
            CURLOPT_ENCODING => "UTF-8", // handle all encodings
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 50, // timeout on connect
            CURLOPT_TIMEOUT => 50, // timeout on response
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => false // disable certificate checking
        );

        $ch = curl_init($this->end_point . $this->path);

        if ($params['ReportType'] == '_GET_MERCHANT_LISTINGS_DATA_') {
            $feed_response_file = "amazon_listings_report_response.xml";
        }

        

        @curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);

       
//        chmod($file, 0775);

        curl_close($ch);

        //echo '<pre>';print_r($header);echo '</pre>';exit;

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        return $header;
    }
    public function requestReportStatus($storeInfo) {
        $SellerCentral = $storeInfo['amazon_store_name'];
        $feed_response_file = "amazon_listings_report_response.xml";
        $feed_response_file_path = DIR_FS_AMAZON_FEED . $SellerCentral . '/AmazonReports/ListedItems/' . $feed_response_file;
        $file_content = @file_get_contents($feed_response_file_path);
        $parsed_result = $this->xml2array($file_content);
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $AWSAccessKeyId = $storeInfo['aws_key_id'];
        $MerchantId = $storeInfo['merchant_id'];
        $Marketplace = $storeInfo['market_place_id'];
        $this->path = AMAZON_REPORT_REQUEST_URI;
        
        $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'GetReportRequestList',
            'MarketplaceId' => $Marketplace,
            'SellerId' => $MerchantId,
            'ReportType'=>'_GET_MERCHANT_LISTINGS_DATA_',
            'SignatureVersion' => 2,
            'SignatureMethod' => $this->algorithm,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SecretKey' => $storeInfo['secret_key'],
            'ReportRequestIdList.Id.1'=>$parsed_result['RequestReportResponse']['RequestReportResult']['ReportRequestInfo']['ReportRequestId'],
            'ReportProcessingStatusList.Status.1'=>'_DONE_'
        );        
        
        $signature = SignatureUtils::signParameters($params, $storeInfo['secret_key'], 'POST', $this->host, $this->path, $this->algorithm);

        if (eregi('%7E', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        
        $this->get_data = $this->getAmazonRequestReportStatus($SellerCentral, $this->end_point, $params, $signature);

        if ($this->get_data) {
            return $this->get_data;
        } else {
            return false;
        }       
    }
    public function getAmazonRequestReportStatus($SellerCentral, $endPoint, array $params, $signature) {
        $this->end_point = $endPoint;
        $this->path = AMAZON_REPORT_REQUEST_URI;
        if ($params['Action'] == 'GetReportRequestList') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        }

        $this->UserAgent = '<MWS_GetReportRequestList>/<1.0> (Language=PHP/' . phpversion() . ')';
        
        $curlrequest = new CurlRequest();

        $query = $curlrequest->_getParametersAsString($params);

        $url = $this->getUrl();
        
        $headers = array(
            'Expect: ',
            'Accept: ',
            'HTTP/1.1',
            'Host:' . $this->host,
            'Transfer-Encoding: chunked',
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Content-MD5: ' . base64_encode($query),
            'Connection: Keep-Alive',
            'User-Agent: '.$this->UserAgent
        );

        $options = array(
            CURLOPT_URL => $url, //$this->end_point . $this->path,
            CURLOPT_PORT => '443',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_RETURNTRANSFER => true, // return web page 
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => 0,
            CURLOPT_USERAGENT => $this->UserAgent,
            CURLOPT_ENCODING => "UTF-8", // handle all encodings
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 50, // timeout on connect
            CURLOPT_TIMEOUT => 50, // timeout on response
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => false // disable certificate checking
        );

        $ch = curl_init($this->end_point . $this->path);

        if ($params['ReportType'] == '_GET_MERCHANT_LISTINGS_DATA_') {
            $feed_response_file = "amazon_listings_report_request_response.xml";
        }

        if ($params['Action'] == 'GetReportRequestList') {
            $file = DIR_FS_AMAZON_FEED . $SellerCentral . '/AmazonReports/ListedItems/' . $feed_response_file;
        }

        $fp = fopen($file, 'w+');

        @curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);

        fwrite($fp, $content);
        fclose($fp);
        chmod($file, 0775);

        curl_close($ch);

        //echo '<pre>';print_r($header);echo '</pre>';exit;

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        return $header;
    }    
    public function getAmazonReport($storeInfo) {
        $SellerCentral = $storeInfo['amazon_store_name'];
        $feed_response_file = "amazon_listings_report_request_response.xml";
        $feed_response_file_path = DIR_FS_AMAZON_FEED . $SellerCentral . '/AmazonReports/ListedItems/' . $feed_response_file;
        $file_content = @file_get_contents($feed_response_file_path);
        $parsed_result = $this->xml2array($file_content);
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $AWSAccessKeyId = $storeInfo['aws_key_id'];
        $MerchantId = $storeInfo['merchant_id'];
        $Marketplace = $storeInfo['market_place_id'];
        $this->path = AMAZON_REPORT_REQUEST_URI;
        
        $params = array(
            'AWSAccessKeyId' => $AWSAccessKeyId,
            'Action' => 'GetReport',
            'MarketplaceId' => $Marketplace,
            'SellerId' => $MerchantId,
            'ReportId'=>$parsed_result['GetReportRequestListResponse']['GetReportRequestListResult']['ReportRequestInfo']['GeneratedReportId'],
            'SignatureVersion' => 2,
            'SignatureMethod' => $this->algorithm,
            'Timestamp' => $timestamp,
            'Version' => '2009-01-01',
            'SecretKey' => $storeInfo['secret_key']
        );        
        
        $signature = SignatureUtils::signParameters($params, $storeInfo['secret_key'], 'POST', $this->host, $this->path, $this->algorithm);

        if (eregi('%7E', rawurlencode($signature))) {
            $signature = str_replace("%7E", "~", rawurlencode($signature));
        }
        
        $this->get_data = $this->getAmazonRequestReport($SellerCentral, $this->end_point, $params, $signature);

        if ($this->get_data) {
            return $this->get_data;
        } else {
            return false;
        }       
    }
    public function getAmazonRequestReport($SellerCentral, $endPoint, array $params, $signature) {
        $this->end_point = $endPoint;
        $this->path = AMAZON_REPORT_REQUEST_URI;
        if ($params['Action'] == 'GetReport') {
            $params = array_slice($params, 0, 6, true) + array("Signature" => $signature) + array_slice($params, 6, count($params) - 1, true);
        }

        $this->UserAgent = '<MWS_GetReport>/<1.0> (Language=PHP/' . phpversion() . ')';
        
        $curlrequest = new CurlRequest();

        $query = $curlrequest->_getParametersAsString($params);

        $url = $this->getUrl();
        
        $headers = array(
            'Expect: ',
            'Accept: ',
            'HTTP/1.1',
            'Host:' . $this->host,
            'Transfer-Encoding: chunked',
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Content-MD5: ' . base64_encode($query),
            'Connection: Keep-Alive',
            'User-Agent: '.$this->UserAgent
        );

        $options = array(
            CURLOPT_URL => $url, //$this->end_point . $this->path,
            CURLOPT_PORT => '443',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_RETURNTRANSFER => true, // return web page 
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => 0,
            CURLOPT_USERAGENT => $this->UserAgent,
            CURLOPT_ENCODING => "UTF-8", // handle all encodings
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 500, // timeout on connect
            CURLOPT_TIMEOUT => 500, // timeout on response
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => false // disable certificate checking
        );

        $ch = curl_init($this->end_point . $this->path);

        if ($params['ReportId']) {
            $feed_response_file = "amazon_all_listings_report.xml";
        }

        if ($params['Action'] == 'GetReport') {
            $file = DIR_FS_AMAZON_FEED . $SellerCentral . '/AmazonReports/ListedItems/' . $feed_response_file;
        }

        $fp = fopen($file, 'w+');

        @curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);

        fwrite($fp, $content);
        fclose($fp);
        chmod($file, 0775);

        curl_close($ch);

        //echo '<pre>';print_r($header);echo '</pre>';exit;

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        return $header;
    }        
    public function text2array($file) {
        if ($file != '') {
            $lines = file($file);
            $count = count($lines);
               if($count > 0){
                   $data = array();
                   $i = 0;
                   foreach ($lines as $value) {
                      $data[$i] = explode("\t", $value);
                      $i++;
                   }
                   return($data);
               }
               else{
                   return false;
               }
        }
    }
//EOC:@SakshiGopal 21-March-2017 fundtions to get reports from Amazon       
}

?>