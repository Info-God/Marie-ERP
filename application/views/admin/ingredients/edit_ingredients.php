<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="edit-ingredients" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('ingredients/create_ingredients'), array('class' => 'ingredients-form', 'autocomplete' => 'off', 'id' => 'ingredients-forms')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                <span class="edit-title"><?php echo 'Edit Ingredient'; ?></span>
                    <span class="add-title"><?php echo 'New Ingredient'; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php $value = (isset($category_name) ? $category_name : ''); ?>
                        <?php $attrs = (isset($category_name) ? array() : array('autofocus' => true, 'placeholder' => 'Enter existing or new category name')); ?>
                        <?php echo render_input('category_name', 'Category', $value, 'text', $attrs); ?>
                        <?php echo form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    window.addEventListener('load', function() {
        appValidateForm($('#ingredients-form'), {
            category_name: 'required',
        }, manage_ingredients);
        $('#edit-ingredients').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        console.log(group_id);
        $('#edit-ingredients .add-title').removeClass('hide');
        $('#edit-ingredients .edit-title').addClass('hide');
        $('#edit-ingredients input[name="id"]').val('');
        $('#edit-ingredients input[name="name"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#edit-ingredients input[name="id"]').val(group_id);
            $('#edit-ingredients .add-title').addClass('hide');
            $('#edit-ingredients .edit-title').removeClass('hide');
            $('#edit-ingredients input[name="category_name"]').val($(invoker).parents('tr').find('td').eq(1).text());
        }
    });
    });

    function manage_ingredients(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-customer-groups')){
                    $('.table-customer-groups').DataTable().ajax.reload();
                }
                if($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
                    var groups = $('select[name="groups_in[]"]');
                    groups.prepend('<option value="'+response.id+'">'+response.name+'</option>');
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
            }
            $('#edit-ingredients').modal('hide');
        });
        return false;
    }

</script>