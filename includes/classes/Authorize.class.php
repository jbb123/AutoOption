<?php

class Authorize
{
	public function __construct()
	{
		global $db;
		$this->db = $db;

		$this->postURLTest = "https://test.authorize.net/gateway/transact.dll";
		$this->postURL = "https://secure.authorize.net/gateway/transact.dll";
		$this->xLogin = "7a2j9TMZ";
		$this->xTranKey = "8m46P269XpXgQd6h";
		$this->xVersion = "3.1";
		$this->xDelimData = "TRUE";
		$this->xDelimChar = "|";
		$this->xRelayResponse = "FALSE";
		$this->xType = "AUTH_CAPTURE";
		$this->xMethod = "CC";
		
		
	}
	
	public function debit($cardNum, $expDate, $amount, $firstName, $lastName, $address, $state, $zip, $description)
	{
		
		$postString .= "&x_login=" . urlencode($this->xLogin) .
						"&x_tran_key=" . urlencode($this->xTranKey) .
						"&x_version=" . urlencode($this->xVersion) . 
						"&x_delim_data=" . urlencode($this->xDelimData) . 
						"&x_delim_char=" . urlencode($this->xDelimChar) . 
						"&x_relay_response=" . urlencode($this->xRelayResponse) . 
						"&x_type=" . urlencode($this->xType) . 
						"&x_method=" . urlencode($this->xMethod) .
						"&x_card_num=" . urlencode($cardNum) .
						"&x_exp_date=" . urlencode($expDate) .
						"&x_amount=" . urlencode($amount) .
						"&x_description=" . urlencode($description) .
						"&x_first_name=" . urlencode($firstName) .
						"&x_last_name=" . urlencode($lastName) .
						"&x_address=" . urlencode($address) .
						"&x_state=" . urlencode($state) .
						"&x_zip=" . urlencode($zip) .
						"&x_test_request=true";
						
						
		
		$request = curl_init($this->postURL); 
		curl_setopt($request, CURLOPT_HEADER, 0); 
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($request, CURLOPT_POSTFIELDS, $postString); 
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); 
		$response = curl_exec($request); 
		
		curl_close ($request); 
		
		$result = explode("|", $response);
		/*
		 * 0 = response code
		 * 1 = response subcode
		 * 2= response reason code
		 * 3 = response reason text
		 */
		
		
		return $result;
		
		//$response_array = explode($post_values["x_delim_char"],$post_response);					
		
		
		
	}
	
}

?>	