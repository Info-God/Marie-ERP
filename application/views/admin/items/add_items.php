<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="add-items" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('items/create_items'),array('id'=>'items-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                <span class="edit-title"><?php echo 'Edit Item'; ?></span>
                    <span class="add-title"><?php echo 'New Item'; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php $value = (isset($restaurant_details) ? $restaurant_details[0]['business_name']: ''); ?>
                        <?php $attrs = (isset($restaurant_details) ? array() : array('autofocus' => true, 'placeholder' => 'Enter existing or new category name')); ?>
                        <?php echo render_input('category_name', 'Category', $value, 'text', $attrs); ?>
                        <?php $value = (isset($restaurant_details) ? $restaurant_details[0]['business_name']: ''); ?>
                        <?php $attrs = (isset($restaurant_details) ? array() : array('autofocus' => true, 'placeholder' => 'Enter new item name')); ?>
                        <?php echo render_input('item_name', 'Item', $value, 'text', $attrs); ?>
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
    window.addEventListener('load',function(){
        appValidateForm($('#items-form'),{category_name:'required',item_name:'required'},manage_ticket_services);
        $('#add-items').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            console.log(group_id);
            $('#additional').html('');
            // $('#add-items input[plan="plan"]').val('');
            // $('.add-title').removeClass('hide');
            // $('.edit-title').removeClass('hide');

            $('#add-items .add-title').removeClass('hide');
            $('#add-items .edit-title').addClass('hide');

            if (typeof(group_id) !== 'undefined') {
                $('#add-items input[name="id"]').val(group_id);
                $('#add-items .add-title').addClass('hide');
                $('#add-items .edit-title').removeClass('hide');
                $('#add-items input[name="category_name"]').val($(invoker).parents('tr').find('td').eq(1).text());
                $('#add-items input[name="item_name"]').val($(invoker).parents('tr').find('td').eq(2).text());
            }
        });
    });
    function manage_ticket_services(form) {
        var data = $(form).serialize();
        var url = form.action;
        var ticketArea = $('body').hasClass('ticket');
        if(ticketArea) {
            data+='&ticket_area=true';
        }
        $.post(url, data).done(function(response) {
            if(ticketArea) {
               response = JSON.parse(response);
               if(response.success == true && typeof(response.id) != 'undefined'){
                var group = $('select#service');
                group.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                group.selectpicker('val',response.id);
                group.selectpicker('refresh');
            }
            $('#add-items').modal('hide');
        } else {
            window.location.reload();
        }
    });
        return false;
    }
    function new_service(){
        $('#add-items').modal('show');
        $('.edit-title').addClass('hide');
    }
    function edit_service(invoker,id){
        var name = $(invoker).data('category_name');
        $('#additional').append(hidden_input('id',id));
        $('#add-items input[category_name="category_name"]').val(name);
        $('#add-items').modal('show');
        $('.add-title').addClass('hide');
    }
</script>
