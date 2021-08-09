<?php

/**
 
 */
class ControllerExtensionModuleKaspiIntegration extends Controller
{

 

 
  
 
    public function index()
    {

     // var_dump(expression)

      $files = glob(DIR_SYSTEM . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', 'kaspi_integration') . '.*');


   if(!$files && isset($this->request->post['token'])  && isset($this->request->post['merchantid'])) {  
   //новый токен и мерчант
    $data['token'] = $this->request->post['token'];
    $data['merchantid'] = $this->request->post['merchantid'];

     $value = [
       'token' => $data['token'],

        'merchantid' =>  (int)$data['merchantid']
        ];
 $this->set('kaspi_integration', $value);


   } elseif($files && isset($this->request->post['token'])  && isset($this->request->post['merchantid'])) {
    //редактирование токена и мерчанта
    $data['token'] = $this->request->post['token'];
    $data['merchantid'] = $this->request->post['merchantid'];

     $value = [
       'token' => $data['token'],

        'merchantid' =>  (int)$data['merchantid']
        ];
      $this->set('kaspi_integration', $value);
    }elseif ($files) {
    $data_setting = $this->get('kaspi_integration');
    $data['token'] =  $data_setting["token"];
    $data['merchantid'] = $data_setting["merchantid"];
 


   }else{


    $data['token'] = '';
    $data['merchantid'] = '';
   }
   
     
$data['button_save'] = $this->language->get('button_save');
    $data['button_cancel'] = $this->language->get('button_cancel');
$data['cancel'] = $this->url->link('extension/kaspi_integration', 'token=' . $this->session->data['token'] . '&type=module', true);
$data['action'] = $this->url->link('extension/module/kaspi_integration', 'token=' . $this->session->data['token'], true);

      //var_dump($data_setting["token"],$data_setting["merchantid"]);
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
   
      $this->response->setOutput($this->load->view('extension/module/kaspi_integration_scif', $data)); 
   }


  public function get($key) {
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

  public function set($key, $value) {
    $this->delete($key);

    $file = DIR_SYSTEM . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $this->expire);

    $handle = fopen($file, 'w');

    flock($handle, LOCK_EX);

    fwrite($handle, json_encode($value));

    fflush($handle);

    flock($handle, LOCK_UN);

    fclose($handle);
  }

  public function delete($key) {
    $files = glob(DIR_SYSTEM . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

    if ($files) {
      foreach ($files as $file) {
        if (file_exists($file)) {
          unlink($file);
        }
      }
    }
  }
   
      

    
     


}

?>