<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="new_row" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
    <?php #echo form_open(admin_url('sales/row'),array('id'=>'sales_row_form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo "File Upload" ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $value = (isset($categories) ? $categories : '');
                        echo render_select('categories', get_categories(), array('category', 'category'), 'Category', $value);
                        $value = (isset($items) ? $items : '');
                        echo render_select('items', get_items(), array('item', 'item'), 'Item', $value);
                        ?>
                        <?php $value = (isset($sales_unit) ? $sales_unit : ''); ?>
                        <?php $attrs = (isset($sales_unit) ? array() : array('autofocus' => true, 'placeholder' => 'Enter Sales Volume for the item selected')); ?>
                        <?php echo render_input('sales_unit', 'Sales Volume', $value, 'text', $attrs); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <a href="<?php echo admin_url('sales/sales_import/' . $id); ?>">
                    <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button></a>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load', function() {
        appValidateForm($('#sales_row_form'), {
            // categories: 'required',
            // items: 'required',
            // sales_unit: 'required'
        }, manage_customer_groups);

        $('#customer_group_modal').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            $('#customer_group_modal .add-title').removeClass('hide');
            $('#customer_group_modal .edit-title').addClass('hide');
            $('#customer_group_modal input[name="id"]').val('');
            $('#customer_group_modal input[name="name"]').val('');
            // is from the edit button
            if (typeof(group_id) !== 'undefined') {
                $('#customer_group_modal input[name="id"]').val(group_id);
                $('#customer_group_modal .add-title').addClass('hide');
                $('#customer_group_modal .edit-title').removeClass('hide');
                $('#customer_group_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
            }
        });
    });

    function manage_customer_groups(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if ($.fn.DataTable.isDataTable('.table-customer-groups')) {
                    $('.table-customer-groups').DataTable().ajax.reload();
                }
                if ($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
                    var groups = $('select[name="groups_in[]"]');
                    groups.prepend('<option value="' + response.id + '">' + response.name + '</option>');
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
            }
            $('#customer_group_modal').modal('hide');
        });
        return false;
    }
</script>