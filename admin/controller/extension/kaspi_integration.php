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
	

    public function index()
    {
		
		//$this->load->model('extension/kaspi_integration');
           // $this->model_extension_kaspi_integration->getProductScfi();

		    $tomorrow  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));	
		    $tomorrow  =  $tomorrow * 1000 ;
		    $tomorrow  = (string)$tomorrow;
		  
 

         


         $url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=20&filter[orders][state]=NEW&filter[orders][creationDate][$ge]='.$tomorrow.'';
            	

      
            foreach ($this->transport($url)->data as $key => $value) {

               //  var_dump($this->transport($url)->data);
 //var_dump( $this->getRelatedScif($this->transport($value->relationships->entries->links->related)->data));
                     $data["orders"][] = [
                     "order_id" => $value->id,  
                     "order_code" => $value->attributes->code,	
                     "formattedAddress"	=>isset($value->attributes->deliveryAddress->formattedAddress) ? $value->attributes->deliveryAddress->formattedAddress : 'Самовывоз' ,

                     "deliveryMode"  => $value->attributes->deliveryMode,

                     

                      "cellPhone" =>  $this->phoneMask($value->attributes->customer->cellPhone),

                     "firstName" => $value->attributes->customer->firstName,

                     "lastName" => $value->attributes->customer->lastName,
                     

           //"products" =>  $this->getRelated($this->transport($value->relationships->entries->links->related)->data),
		    "products" =>  $this->getRelatedScif($this->transport($value->relationships->entries->links->related)->data),

                   ];
           
                   
     
            	
            	
            }



          //var_dump( $data["orders"]);die;
       $data['catalog'] = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_CATALOG : HTTP_CATALOG;
           $url=''; 
        //  $data['action'] = $this->url->link('extension/kaspi_integration/add','token=' . $this->session->data['token'] . $url, true);
       $data['state'] = 'NEW';
$data['settings']   = $this->url->link('extension/module/kaspi_integration', 'token=' . $this->session->data['token'], true);
         $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
   
   if(0){
	   $this->response->setOutput($this->load->view('extension/kaspi_integration', $data));
   }else{
	    $this->response->setOutput($this->load->view('extension/kaspi_integration_scif', $data));	
   }
   
		  

   	
     

    }

		public function getRelated( $arr_obj_order_sub_item)
		
		{

              
            $this->load->model('extension/kaspi_integration');
          //  $this->model_extension_kaspi_integration->getProductScfi();
			
			foreach ($arr_obj_order_sub_item as $key => $value) {
						                                              
         
				//var_dump($value);die;
				

				$url_merchant_product = $this->transport($value->relationships->product->links->related)
						                                                  ->data
						                                                 // ->attributes
						                                                  ->relationships
						                                                  ->merchantProduct
						                                                  ->links
						                                                  ->self ;
						                                                 
		     
//var_dump(str_replace( ''.$this->merchantid.'##', '',base64_decode($this->transport($url_merchant_product)->data->id)));//die;

                      $products[] = [
                       'product' => $this->model_extension_kaspi_integration->getProductBySku(str_replace( ''.$this->getSetting('kaspi_integration')['merchantid'].'##', '',base64_decode($this->transport($url_merchant_product)->data->id))),
                        'totalPrice' => $value->attributes->totalPrice,
                         'quantity' => $value->attributes->quantity
    
                      ];


                  

	        }

	
       
           return $products;
        }
		
		
		public function getRelatedScif( $arr_obj_order_sub_item)
		
		{

              
            $this->load->model('extension/kaspi_integration');
          //  $this->model_extension_kaspi_integration->getProductScfi();
			
			foreach ($arr_obj_order_sub_item as $key => $value) {
						                                              
         
				//var_dump($value);
				

				$url_merchant_product = $this->transport($value->relationships->product->links->related)
						                                                  ->data
						                                                 // ->attributes
						                                                  ->relationships
						                                                  ->merchantProduct
						                                                  ->links
						                                                  ->self ;
						                                                 
		     
//var_dump(str_replace( ''.$this->merchantid.'##', '',base64_decode($this->transport($url_merchant_product)->data->id)));

                      $products[] = [
                       'product' => $this->model_extension_kaspi_integration->getProductScfi(str_replace( ''.$this->getSetting('kaspi_integration')['merchantid'].'##', '',base64_decode($this->transport($url_merchant_product)->data->id))),
                        'totalPrice' => $value->attributes->totalPrice,
                         'quantity' => $value->attributes->quantity
    
                      ];


                  

	        }

	//var_dump($products,'getRelatedScif');die;
       
           return $products;
        }



 



    public function transport($url)
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