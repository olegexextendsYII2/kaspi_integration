<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  
  <div class="container-fluid">
    
    <div class="panel panel-default">
       <div class="panel-heading"  style="height: 51px;" >
        <div class="pull-right">
        
        <a href="<?php echo $settings; ?>" data-toggle="tooltip" title="Настройки Модуля Kaspi" class="btn btn-default"> Настройки Модуля</a></div>
        <h3 class="panel-title"><i class="fa fa-list"></i> Заказы из Kaspi (<?php echo $state; ?>)</h3>
      </div>

        

          

          <div class="table-responsive">

          
            <table class="table table-bordered table-hover">
              <thead>

              </thead>
              <tbody>
             
                <?php if (isset($orders)) { ?>
                
<?php foreach ($orders as $key => $order) { ?>


            
                <tr> 
 
                

                  
                    

                    
                  <td class="text-right"><?php echo $order['order_code']; ?></td>
                  <td class="text-left"><?php echo $order['firstName']; ?></br>
                                        <?php echo $order['lastName']; ?>

                  </td>
                  <td class="text-left"><?php echo $order['cellPhone']; ?></td>
                  <td class="text-left"><?php echo $order['formattedAddress']; ?></td>
                 <td class="text-left"><?php echo $order['deliveryMode']; ?></td>

                  <td class="text-left">
                <?php foreach ($order['products'] as $product) { ?>

                 <?php $product_quantity_difference_store = $product['product']['store1']+
                                                             $product['product']['store6']+
                                                             $product['product']['store3'];

                 $product_quantity_difference_store_result = $product_quantity_difference_store-$product['quantity'];
                                                              ?>
                 
                    
                   </br></br>
                  <?php echo  $product['product']['name']; ?></br>
                  

                   <?php if ($product_quantity_difference_store == 0) { ?>
                   
               ВНИМАНИЕ!!!  (Нет в наличии, ни на одном складе!)
                  <?php } else { ?>

                  <?php if ( ($product_quantity_difference_store != 0) && ( $product_quantity_difference_store_result < 0) ){ 


                                       echo ' ВНИМАНИЕ!!! не хватает('.$product_quantity_difference_store_result.')  Кас.зак.'.$product['quantity'].'шт -  Мустафина 15 ('.(int)$product['product']['store1'].'шт) / Кошкарбаева 56 (Butuz 2) ('.(int)$product['product']['store6'].'шт)/ виртуальный склад ('.(int)$product['product']['store3'].'шт))';
                            }elseif( 0) {
                                        echo ' ВНИМАНИЕ!!! Кас.зак.'.$product['quantity'].'шт -  Кошкарбаева 56 (Butuz 2)'.(int)$product['product']['store1'].'шт) Не хватает'.$product_quantity_difference.'товара(ов)';
                         
                             }else {  echo '(в наличии!,  Кас.зак.'.$product['quantity'].'шт -  Мустафина 15 ('.(int)$product['product']['store1'].'шт) / Кошкарбаева 56 (Butuz 2) ('.(int)$product['product']['store6'].'шт)/ виртуальный склад ('.(int)$product['product']['store3'].'шт))'; 

                              } ?>

                  <?php } ?>
   
                  <input type="hidden" name="order[]['product_id']" value="<?php echo $product['product']['shop_id']; ?>" >
                  <?php } ?>
                  </td>
                 <td class="text-left"> 
                  <form method="post" action="<?php echo $catalog ?>index.php?route=extension/kaspi_integration/add" enctype="multipart/form-data"   > 

                        <input type="hidden" name="comment" value="<?php echo $order['order_code']; ?>" >
                       <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>" >


                    <input type="submit"   return false;" value="Принять"> 
             
                   </form>
                </td>
                </tr>

                <?php } ?>
              


                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8">Нет новых заказов</td>
                </tr>
               <?php } ?> 

                
              </tbody>
            </table>

          </div>
        
        
      </div>
    </div>
  </div>



<?php echo $footer; ?>