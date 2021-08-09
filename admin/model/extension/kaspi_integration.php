<?php


/**
 * Class Model ControllerExtensionForDealerImport
 
 */
class ModelExtensionKaspiIntegration extends Model
{



   public function getProductBySku($sku ) {



   
  
        
   $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.sku = '" .  $sku . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

         
       
  
         return $query->row;
         
    }
	
	public function getProductScfi($sku) {
		
		//var_dump((int)$sku);
	//	define('WEBNICE','CRON');
//define('WN_PATH',dirname(__FILE__));
//require '/var/www/vhosts/butuz.kz/httpdocs/wn/wn_settings.php';
//$settings=$scif_actions[9900]['settings'];
//var_dump(array_keys($settings['stores']));die;
   
  $sql='SELECT * FROM wn_scif1_spr_noms WHERE id = '.(int)$sku.'';
        
   
   /*$sql='SELECT n.id, n.name,
n.price'.$settings['price'].' AS price, v.name AS vendor_name,
store'.implode(',store',array_keys($settings['stores']))
.' FROM '.SCIF_CATALOG_PREFIX.'spr_noms n
LEFT JOIN '.SCIF_CATALOG_PREFIX.'spr_noms_gr g1 ON g1.id=n.parent
LEFT JOIN '.SCIF_CATALOG_PREFIX.'spr_values v ON n.property'.$settings['property'].'=v.id
WHERE n.id = '.(int)$sku.'';
*/

//.$settings['where'];
//var_dump($sql);die;

$result = $this->db->query($sql);

       //var_dump($result->row,'getProductScfi6');die;  
       
  
         return $result->row;
         
    }
	
	


 
   


   

}