<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" style="height: 1040px;" >
 <div class="pull-right">
        <button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>


   <form action="<?php echo $action; ?>" method="post"  id="form-setting-kaspi"  class="form-horizontal" >
  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name">token</label>
            <div class="col-sm-10">
              <input type="text" name="token" value="<?php echo $token; ?>" placeholder="<?php echo $token; ?>" id="input-name" class="form-control" />
              
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name">merchantid</label>
            <div class="col-sm-10">
              <input type="text" name="merchantid" value="<?php echo $merchantid; ?>" placeholder="<?php echo $merchantid; ?>" id="input-name" class="form-control" />
              
            </div>
          </div>
   </form>


   <div style="margin-left: 200px;" >
     
   
    

</div>
<?php echo $footer; ?>