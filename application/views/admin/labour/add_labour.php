<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .checkbox-wrapper-center {
        display: flex;
        /* Use flexbox to align items */
        align-items: center;
        /* Vertically center the items */
        margin-bottom: 10px;
        /* Add some space between the pairs */
    }
</style>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open($this->uri->uri_string(), array('id' => 'labour-form')); ?>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php $value = (isset($employee_name) ? $employee_name : ''); ?>
                        <?php $attrs = (isset($employee_name) ? array() : array('autofocus' => true)); ?>
                        <?php echo render_input('employee_name', 'Name', $value, 'text', $attrs); ?>

                        <?php $value = (isset($article) ? $article->subject : ''); ?>
                        <?php $attrs = (isset($article) ? array() : array('autofocus' => true)); ?>
                        <?php echo render_input('salary', 'Salary', $article->articleNumber, 'text'); ?>

                        <?php $value = (isset($article) ? $article->subject : ''); ?>
                        <?php $attrs = (isset($article) ? array() : array('autofocus' => true)); ?>
                        <?php echo render_input('productivity', 'Productivity', $article->articleNumber, 'hours'); ?>

                        <div class="checkbox checkbox-primary">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="staff_article_fulltime" name="staff_article" <?php if (isset($article) && $article->staff_article == 1) {
                                                                                                            echo 'checked';
                                                                                                        } ?>>
                                <label for="staff_article_fulltime"><?php echo _l('FullTime'); ?></label>
                            </div>

                            <div class="checkbox-wrapper-center">
                                <input type="checkbox" id="staff_article_foreigner" name="staff_article" <?php if (isset($article) && $article->staff_article == 1) {
                                                                                                                echo 'checked';
                                                                                                            } ?>>
                                <label for="staff_article_foreigner"><?php echo _l('Foreigner'); ?></label>
                            </div>

                            <?php #foreach ($currencies as $currency) {
                                #if (isset($client)) {
                                    #if ($currency['id'] == $client->default_currency) {
                                       # $selected = $currency['id'];
                                    #}
                                #}
                            #}
                            // Do not remove the currency field from the customer profile!
                            #echo render_select('default_currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $s_attrs); ?>

                        </div>

                    </div>
                </div>
            </div>
            <?php if ((has_permission('knowledge_base', '', 'create') && !isset($article)) || has_permission('knowledge_base', '', 'edit') && isset($article)) { ?>
                <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                    <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                </div>
            <?php } ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        appValidateForm($('#article-form'), {
            subject: 'required',
            articlegroup: 'required'
        });
    });
</script>
</body>

</html>