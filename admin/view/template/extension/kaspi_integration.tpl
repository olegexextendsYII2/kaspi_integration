<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  
  <div class="container-fluid">
    
    <div class="panel panel-default">
      <div class="panel-heading">
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

                 <?php $product_quantity_difference = $product['product']['quantity']-$product['quantity']; ?>
                    
                    
                  

                   <?php if ( $product['product']['stock_status_id']  ==  5) { ?>
                 (Нет в наличии)
                  <?php } else { ?>

                  <?php if ( $product_quantity_difference <  0){ 
                                       echo ' ВНИМАНИЕ!!! Кас.зак.'.$product['quantity'].'шт -  Маг. '.(int)$product['product']['quantity'].'шт) Не хватает'.$product_quantity_difference.'товара(ов)';  
                             }else {  echo '(в наличии,  Кас.зак.'.$product['quantity'].'шт -  Маг. '.(int)$product['product']['quantity'].'шт)'; }?>

                  <?php } ?>
                  <?php echo  $product['product']['name']; ?></br>
                  <input type="hidden" name="order[]['product_id']" value="<?php echo $product['product']['product_id']; ?>" >
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