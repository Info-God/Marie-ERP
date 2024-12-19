<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="add-departments" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('restaurant_departments/create_department'),array('id'=>'department-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                <span class="edit-title"><?php echo 'Edit Department'; ?></span>
                    <span class="add-title"><?php echo 'New Department'; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                         <?php $value = (isset($restaurant_details) ? $restaurant_details[0]['business_name']: ''); ?>
                        <?php $attrs = (isset($restaurant_details) ? array() : array('autofocus' => true, 'placeholder' => 'Enter new department name')); ?>
                        <?php echo render_input('department_name', 'Department', $value, 'text', $attrs); ?>
                    </div>
                     <!-- Restaurant address -->
                     
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
        appValidateForm($('#department-form'),{department_name:'required'},manage_ticket_services);
        $('#add-departments').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#add-departments input[department_name="department_name"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
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
            $('#add-departments').modal('hide');
        } else {
            window.location.reload();
        }
    });
        return false;
    }
    function new_service(){
        $('#add-departments').modal('show');
        $('.edit-title').addClass('hide');
    }
    function edit_service(invoker,id){
        var name = $(invoker).data('department_name');
        $('#additional').append(hidden_input('id',id));
        $('#add-departments input[department_name="department_name"]').val(name);
        $('#add-departments').modal('show');
        $('.add-title').addClass('hide');
    }
</script>
