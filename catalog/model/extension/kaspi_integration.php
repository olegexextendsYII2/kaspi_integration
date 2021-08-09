<?php

/**
 *
 */
class ModelExtensionKaspiIntegration extends Model
{



   public function getProductById($product_id ) {
        
   $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" .  (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

  
         return $query->row;
         
    }


     public function getProductBySku($sku ) {

$product = $this->getProductScfi($sku);

//if($product["shop_option_id"]){
	
//}
 //var_dump($product["shop_option_id"]); die;
$option_data  = $this->getOptionsForOrder($product);
$product_id = $product["shop_id"];
$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" .  (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
        
  // $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.sku = '" .  $sku . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

       //  var_dump(array_merge($query->row,$option_data[0])); die;
        
  
         return array_merge($query->row,$option_data[0]);
         
    }

public function getProductScfi($sku ) {
		//$sku = 1519;
	//var_dump($sku); die;
   
  $sql='SELECT * FROM wn_scif1_spr_noms WHERE id = '.(int)$sku.'';
        
   
  

$result = $this->db->query($sql);
     $product =  $result->row;
       //var_dump($product["name"],'getProductScfi');  
       
  
        // return $product["shop_id"];
		  return $product;
 
   
}

public function getOptionsForOrder( $product ) {
	
	$ids = $this->getOptionIdEndOptionValueId($product["shop_id"],$product["shop_option_id"]);
       $name = $this->getOptionName($ids["option_value_id"]);
	   $value	= $this->getOptionValue($ids["option_id"]);
	   $type =  $this->getOptionType($ids["option_id"]);
	   $option_data = array();

					
						$option_data[] = array(
							'product_option_id'       => $ids['product_option_id'],
							'product_option_value_id' => $ids['product_option_value_id'],
							'option_id'               => $ids['option_id'],
							'option_value_id'         => $ids['option_value_id'],
							'name_option'                    => $name['name'],
							'value_option'                   => $value['name'],
							'type_option'                    => $type['type']
						);
						
					//var_dump($option_data);die;	
					
	   
	
	return $option_data;
	
}
public function getOptionIdEndOptionValueId( $shop_id , $shop_option_id ) {
	//var_dump($shop_id , $shop_option_id);die;
	$query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$shop_option_id . "'
                                                                                         AND product_id = '" . (int)$shop_id . "'");
	
	 //var_dump($query->row);die;
	
	return $query->row;
	
}

public function getOptionName($option_value_id) {
		

		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "option_value_description  WHERE option_value_id = '" . (int)$option_value_id . "'");
       //var_dump($query->row);die;
		return $query->row;

		
	}
	
	public function getOptionType($option_id) {
		

		$query = $this->db->query("SELECT type FROM " . DB_PREFIX . "option  WHERE option_id = '" . (int)$option_id . "'");
      // var_dump($query->row);die;
		return $query->row;

		
	}
	public function getOptionValue($option_id) {
		

		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "option_description  WHERE option_id = '" . (int)$option_id . "'");
       //var_dump($query->row);die;
		return $query->row;

		
	}



   

}