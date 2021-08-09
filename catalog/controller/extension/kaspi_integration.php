<?php

/**
 *
 */
class ControllerExtensionKaspiIntegration extends Controller
{






public function phoneMask($value='')
{
    $value = '7'.$value;
                          $telephone = sprintf("%s (%s) %s-%s-%s",
                          substr($value, 0, 1),
                          substr($value, 1, 3),
                          substr($value, 4, 3),
                          substr($value, 7, 2),
                          substr($value, 9)
                        );

                     return '+'.$telephone;
}


 public function transportGet($url)
    {
    	    //var_dump($url);die;         
           $ch = curl_init($url);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [

                'Content-Type:application/vnd.api+json',
                'X-Auth-Token:' .$this->getSetting('kaspi_integration')["token"],
                
            ]);

            $result = curl_exec($ch);
           
            curl_close($ch);
            $result = json_decode($result);
            return $result;
    }

    public function transportPost($order_code,$order_id)
    {
     $url = 'https://kaspi.kz/shop/api/v2/orders';

   


		 $params =[
		        "data" =>[
		        	"type"=> "orders",
		        	"id"=> $order_id,
                    "attributes" =>[
                    	"code" => $order_code,
                    	"status" => "ACCEPTED_BY_MERCHANT"

                      ]
		            ]
               ];	
		$dataString =	json_encode( $params);

			//var_dump($params);die;  


			 $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                 'Content-Type:application/vnd.api+json',
                'X-Auth-Token:' .$this->getSetting('kaspi_integration')["token"],
                 
            ]);
    	//          
     
            $result = curl_exec($ch);
           
            curl_close($ch);
            $result = json_decode($result);
//var_dump($result);die;
          
            return $result;
    }





public function getRelated( $arr_obj_order_sub_item)
		
		{

              
            $this->load->model('extension/kaspi_integration');
			
			foreach ($arr_obj_order_sub_item as $key => $value) {
						                                              
           
				
                    $url_merchant_product =   $value->relationships->product->links->related;    
				
                       
		     //var_dump( $this->transportGet($value->relationships->deliveryPointOfService->links->related)->data->attributes->displayName);


                      $products[] = [
                       'product' => $this->model_extension_kaspi_integration->getProductBySku(str_replace( ''.$this->getSetting('kaspi_integration')['merchantid'].'##', '',base64_decode(($this->transportGet($url_merchant_product)->data)->relationships->merchantProduct->data->id))),
                        'totalPrice' => $value->attributes->totalPrice,
                         'quantity' => $value->attributes->quantity,
						  /// комутация доставки
                   'delivery_display_name' => $this->transportGet($value->relationships->deliveryPointOfService->links->related)->data->attributes->displayName
    
                      ];


                  

	        }


       
          return $products;
        }



  public function add()
    {

 $this->transportPost($this->request->post["comment"],$this->request->post["order_id"]);



    	$url = 'https://kaspi.kz/shop/api/v2/orders?filter[orders][code]='.$this->request->post["comment"].'';

          if (isset($this->request->post['comment'])) {
					$order_data['comment'] = $this->request->post['comment'];
				} else {
					$order_data['comment'] = '';
				}

    	 $this->load->model('extension/kaspi_integration');



    	
			
			foreach ($this->transportGet($url)->data as $key =>$value ) {



				$url_merchant_product = $value->relationships->entries->links->related;		                                              
            //$order_data['products'] = array();
		foreach ($this->getRelated( $this->transportGet($url_merchant_product)->data) as $product) {


			 /// комутация доставки
	               $delivery_display_name = $product['delivery_display_name'];
			
                    $option_data = array();
            // foreach ($option_data as $option) {
				//var_dump($value->attributes->deliveryAddress->formattedAddress);die;
				
				if($product['product']['option_id']){
					$option_data[] = array(
							'product_option_id'       => $product['product']['product_option_id'],
							'product_option_value_id' => $product['product']['product_option_value_id'],
							'option_id'               => $product['product']['option_id'],
							'option_value_id'         => $product['product']['option_value_id'],
							'name'                    => $product['product']['name_option'],
							'value'                   => $product['product']['value_option'],
							'type'                    => $product['product']['type_option']
						);
					
				}
					
						
				//}
					//foreach ($option_data as $option) {
						//foreach ($option_data as $option) {
                 //var_dump($option); 
						//}die;

					$order_data['products'][] = array(
						'product_id' => $product['product']['product_id'],
						'name'       => $product['product']['name'],
						'model'      => $product['product']['model'],
						'option'     => $option_data,
						'download'   => '',
						'quantity'   => $product['quantity'],
						'subtract'   => '',
						'price'      => $product['product']['price'],
						'total'      => $product['totalPrice'],
						'tax'        => $this->tax->getTax($product['product']['price'], $product['product']['tax_class_id']),
						'reward'     => ''
					);
				}

				//var_dump($delivery_display_name); die;

				$order_data['totals'] = [


					   0    => [
							  
							    "code"=> "sub_total",

							    "title"=> "Предварительная стоимость",
							   
							    "value" => $value->attributes->totalPrice,
							   
							    "sort_order"=> "1"
							  ],

							  1 => [
							   
							    "code"=> "total",
							    "title"=> "Итого",
							    "value"=> $value->attributes->totalPrice,
							    
							    "sort_order" =>  "9"
							]

                     


				];


				

				

				$order_data['payment_firstname'] = $value->attributes->customer->firstName;
				$order_data['payment_lastname'] = $value->attributes->customer->lastName;
				$order_data['payment_address_1'] = isset($value->attributes->deliveryAddress->formattedAddress) ? $value->attributes->deliveryAddress->formattedAddress : 'Kaspi магазин' ;
				$order_data['total'] = $value->attributes->totalPrice;	                                                
			    $order_data['firstname'] = $value->attributes->customer->firstName;
				$order_data['lastname'] = $value->attributes->customer->lastName;
				$order_data['email'] = 'butuz.kz@yandex.ru';

              

				$order_data['telephone'] = $this->phoneMask($value->attributes->customer->cellPhone) ;  
                //$order_data['comment']
					

					$order_data['shipping_firstname'] = $value->attributes->customer->firstName;
					$order_data['shipping_lastname'] = $value->attributes->customer->lastName;
$order_data['shipping_address_1'] = isset($value->attributes->deliveryAddress->formattedAddress) ? $value->attributes->deliveryAddress->formattedAddress : 'Самовывоз' ;
$order_data['shipping_address_format'] = isset($value->attributes->deliveryAddress->formattedAddress) ? $value->attributes->deliveryAddress->formattedAddress : 'Самовывоз' ;


 /// комутация доставки



if(isset($value->attributes->deliveryAddress->formattedAddress)) {
							                  $shipping_method = 'Доставка по г. Нур-Султан';
							                   }elseif($delivery_display_name == 'PP1'){
												   $shipping_method = 'г. Нур-Султан, ул. Кошкарбаева, д. 56';
							                  
							                   }else{
							                   $shipping_method =	'г. Нур-Султан, ул. Мустафина, д. 15';
							                   }
 $order_data['shipping_method'] = $shipping_method ; 
                   
                   




                   
 if(isset($value->attributes->deliveryAddress->formattedAddress)) {
							                   $shipping_code =	'xshipping.xshipping1';
							                   }elseif($delivery_display_name == 'PP1'){
												   
												   $shipping_code = 'xshipping.xshipping4';
							                   
							                   }else{
							                    $shipping_code =	'xshipping.xshipping2';
							                   }

$order_data['shipping_code'] =  $shipping_code;
					

// комутация доставки			

                  

//var_dump($order_data['totals']);die;



        /// из api

$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id'] = $this->config->get('config_store_id');
				$order_data['store_name'] = $this->config->get('config_name');
				$order_data['store_url'] = $this->config->get('config_url');

				// Customer Details
				$order_data['customer_id'] = 1755;
				$order_data['customer_group_id'] = 6;
				
				$order_data['fax'] = '';
				$order_data['custom_field'] = '';

				

				// Payment Details
				
				$order_data['payment_company'] = '';
				
				$order_data['payment_address_2'] = '';
				$order_data['payment_city'] = 'Нур-Султан';
				$order_data['payment_postcode'] = ''; //Нур-Султан  - город республ-го значения
				$order_data['payment_zone'] = '';
				$order_data['payment_zone_id'] = 1720;
				$order_data['payment_country'] = '';
				$order_data['payment_country_id'] = 109;
				$order_data['payment_address_format'] = '';
				$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

				//if (isset($this->session->data['payment_method']['title'])) {
					//$order_data['payment_method'] = $this->session->data['payment_method']['title'];
				//} else {
					$order_data['payment_method'] = 'Kaspi магазин';
				//}

				//if (isset($this->session->data['payment_method']['code'])) {
					//$order_data['payment_code'] = $this->session->data['payment_method']['code'];
				//} else {
					$order_data['payment_code'] = 'cod4';
				//}

				
					
					$order_data['shipping_company'] = '';
					
					$order_data['shipping_address_2'] = '';
					$order_data['shipping_city'] = 'Нур-Султан';
					$order_data['shipping_postcode'] = '';
					$order_data['shipping_zone'] = '';
					$order_data['shipping_zone_id'] = 1720;
					$order_data['shipping_country'] = '';
					$order_data['shipping_country_id'] = 109;
				
					$order_data['shipping_custom_field'] = array();
					
				
				// Gift Voucher
				$order_data['vouchers'] = array();

				if (!empty($this->session->data['vouchers'])) {
					foreach ($this->session->data['vouchers'] as $voucher) {
						$order_data['vouchers'][] = array(
							'description'      => $voucher['description'],
							'code'             => token(10),
							'to_name'          => $voucher['to_name'],
							'to_email'         => $voucher['to_email'],
							'from_name'        => $voucher['from_name'],
							'from_email'       => $voucher['from_email'],
							'voucher_theme_id' => $voucher['voucher_theme_id'],
							'message'          => $voucher['message'],
							'amount'           => $voucher['amount']
						);
					}
				}


				// $this->load->model('extension/extension');

				// $totals = array();
				// $taxes = $this->cart->getTaxes();
				// $total = 0;

				// Because __call can not keep var references so we put them into an array.
				// $total_data = array(
				// 	'totals' => &$totals,
				// 	'taxes'  => &$taxes,
				// 	'total'  => &$total
				// );
			
				// $sort_order = array();

				// $results = $this->model_extension_extension->getExtensions('total');

				// foreach ($results as $key => $value) {
				// 	$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				// }

				// array_multisort($sort_order, SORT_ASC, $results);

				// foreach ($results as $result) {
				// 	if ($this->config->get($result['code'] . '_status')) {
				// 		$this->load->model('extension/total/' . $result['code']);
						
				// 		// We have to put the totals in an array so that they pass by reference.
				// 		$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				// 	}
				// }

				// $sort_order = array();

				// foreach ($total_data['totals'] as $key => $value) {
				// 	$sort_order[$key] = $value['sort_order'];
				// }

				// array_multisort($sort_order, SORT_ASC, $total_data['totals']);

				// $order_data = array_merge($order_data, $total_data);

            

				if (isset($this->request->post['affiliate_id'])) {
					$subtotal = $this->cart->getSubTotal();

					// Affiliate
					$this->load->model('affiliate/affiliate');

					$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->post['affiliate_id']);

					if ($affiliate_info) {
						$order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
						$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					// Marketing
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				}

				$order_data['language_id'] = $this->config->get('config_language_id');
				$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
				$order_data['currency_code'] = $this->session->data['currency'];
				$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
				$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
				} else {
					$order_data['forwarded_ip'] = '';
				}

				if (isset($this->request->server['HTTP_USER_AGENT'])) {
					$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
				} else {
					$order_data['user_agent'] = '';
				}

				if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
					$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$order_data['accept_language'] = '';
				}
 
}
    
//var_dump($order_data);die;

                 $this->load->model('checkout/order');
    	        

			$order_id = $this->model_checkout_order->addOrder($order_data);

				$this->model_checkout_order->addOrderHistory($order_id, $order_status_id = 15);

				// Set the order history
				
$admin = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_SERVER.'admin/' : HTTP_SERVER.'admin/';
				


      $this->response->redirect( $admin.'index.php?route=extension/kaspi_integration&token=' . $this->session->data['token'] . '');  

    }
	
	
     public function getSetting($key) {
          $files = glob(DIR_SYSTEM . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

          if ($files) {
            $handle = fopen($files[0], 'r');

            flock($handle, LOCK_SH);

            $data = fread($handle, filesize($files[0]));

            flock($handle, LOCK_UN);

            fclose($handle);

            return json_decode($data, true);
          }

          return false;
        }

   

   

}

?>