<?php

class CurlRequest {

    public function _queryStringV1($params) {
        $size_of_params = sizeof($params);
        $last_index = $size_of_params - 1;

        $urlString = '/?';
        $j = 0;
        foreach ($params as $key => $value) {
            $params_value = str_replace('%7E', '~', rawurlencode($value));
            $urlString .= $j != $last_index ? $key . '=' . $params_value . '&' : $key . '=' . $params_value;
            $j++;
        }
        rtrim($urlString, '&');
        return $urlString;
    }

    public function _queryStringV2($params) {
        //Sort the URL parameters
        $url_parts = array();
        foreach (array_keys($params) as $key) {
            $url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
            sort($url_parts);
        }
        //Construct the string to sign
        if ($params['Action'] == 'GetMatchingProductForId') {
            $urlString = '/Products/2011-10-01?';
        } else if ($params['Action'] == 'GetServiceStatus') {
            $urlString = '/Sellers/2011-07-01?';
        } else {
            $urlString = '/?';
        }
        $urlString .= implode("&", $url_parts);
        return $urlString;
    }

    /**
     * Convert paremeters to Url encoded query string
     */
    public function _getParametersAsString(array $parameters) {
        $queryParameters = array();
        foreach ($parameters as $key => $value) {
            $queryParameters[] = $key . '=' . self::_urlencode($value);
        }
        return implode('&', $queryParameters);
    }

    public function _urlencode($value) {
        return str_replace('%7E', '~', rawurlencode($value));
    }

    public function _calculateSignatureV1($stringToSign, $urlString, $key) {
        //Sign the request //Base64 encode the signature and make it URL safe
        $signature = urlencode(base64_encode(hash_hmac("sha256", $stringToSign . $urlString, $key, TRUE)));

        $signature = str_replace("%7E", "~", rawurlencode($signature));

        return $signature;
    }

}

?>