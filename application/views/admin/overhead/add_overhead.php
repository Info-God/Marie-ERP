<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="add-items" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('overhead/create_overheads'),array('overheads_id'=>'overheads_id')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                <span class="edit-title"><?php echo 'Edit overhead'; ?></span>
                    <span class="add-title"><?php echo 'New overhead'; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php $value = (isset($category) ? $category: ''); ?>
                        <?php $attrs = (isset($category) ? array() : array('autofocus' => true, 'placeholder' => 'Enter  name')); ?>
                        <?php echo render_input('category', 'category', $value, 'text', $attrs); ?>
                        <?php $value = (isset($predefined_items) ? $predefined_items: ''); ?>
                        <?php $attrs = (isset($predefined_items) ? array() : array('autofocus' => true, 'placeholder' => 'Enter description')); ?>
                        <?php echo render_input('predefined_items', 'predefined_items', $value, 'text', $attrs); ?>
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
            image: {
                required: true,
                accept: "image/*" // This ensures that only image files are accepted
            },
            category_name: 'required',
        }, manage_ingredients);
        $('#add-items').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        
        $('#add-items .add-title').removeClass('hide');
        $('#add-items .edit-title').addClass('hide');
        
        $('#add-items input[category="category"]').val('');
        $('#add-items input[predefined_items="predefined_items"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#add-items input[name="id"]').val(group_id);
            $('#add-items .add-title').addClass('hide');
            $('#add-items .edit-title').removeClass('hide');
            $('#add-items input[name="category"]').val($(invoker).parents('tr').find('td').eq(1).find('b').text());
            $('#add-items input[name="predefined_items"]').val($(invoker).parents('tr').find('td').eq(2).text());
        }
    });
    });
    function new_service(){
        $('#add-items').modal('show');
        $('.edit-title').addClass('hide');
    }

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
            $('#add-items').modal('hide');
            $('.edit-title').addClass('hide');
        });
        return false;
    }

</script>
