

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div class="wrapper">
    
<div class="content">
<?php echo form_open($this->uri->uri_string(),array('id'=>'article-form'));?>
<div class="row">
         <div class="col-md-8 col-md-offset-2">
            <div class="panel_s">
               <div class="panel-body">
                  <h4 class="no-margin">
<div class="checkbox checkbox-primary">
                     <input type="checkbox" id="staff_article" name="staff_article" <?php if(isset($article) && $article->staff_article == 1){echo 'checked';} ?>>
                     <label for="staff_article"><?php echo _l('internal_article'); ?></label>
                  </div>
</h4>
</div>
</div>
</div>
<?php echo form_close(); ?>
</div>
</div>
