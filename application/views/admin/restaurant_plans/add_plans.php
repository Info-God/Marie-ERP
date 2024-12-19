<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="plans-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('plans/create_plans'), array('id' => 'ticket-service-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo 'Edit Plan'; ?></span>
                    <span class="add-title"><?php echo 'New Plan'; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php $value = (isset($restaurant_details) ? $restaurant_details[0]['business_name'] : ''); ?>
                        <?php $attrs = (isset($restaurant_details) ? array() : array('autofocus' => true, 'placeholder' => 'Enter plan name')); ?>
                        <?php echo render_input('plan_name', 'Plan Name', $value, 'text', $attrs); ?>
                        <!-- <div id="additional"></div> -->
                        <?php $value = (isset($restaurant_details) ? $restaurant_details[0]['b_address'] : ''); ?>
                        <?php $attrs = (isset($restaurant_details) ? array() : array('autofocus' => true, 'placeholder' => 'Enter Cost')); ?>
                        <?php echo render_input('plan_cost', 'Cost', $value, 'text', $attrs); ?>
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
        appValidateForm($('#ticket-service-form'), {
            plan_name: 'required',
            plan_cost: 'required'
        }, manage_ticket_services);
        $('#plans-modal').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            console.log(group_id);
            $('#additional').html('');
            // $('#plans-modal input[plan="plan"]').val('');
            // $('.add-title').removeClass('hide');
            // $('.edit-title').removeClass('hide');

            $('#plans-modal .add-title').removeClass('hide');
            $('#plans-modal .edit-title').addClass('hide');

            if (typeof(group_id) !== 'undefined') {
                $('#plans-modal input[name="id"]').val(group_id);
                $('#plans-modal .add-title').addClass('hide');
                $('#plans-modal .edit-title').removeClass('hide');
                $('#plans-modal input[name="plan_name"]').val($(invoker).parents('tr').find('td').eq(1).text());
                $('#plans-modal input[name="plan_cost"]').val($(invoker).parents('tr').find('td').eq(2).text());
            }
        });
    });

    function manage_ticket_services(form) {
        var data = $(form).serialize();
        var url = form.action;
        var ticketArea = $('body').hasClass('ticket');
        if (ticketArea) {
            data += '&ticket_area=true';
        }
        $.post(url, data).done(function(response) {
            if (ticketArea) {
                response = JSON.parse(response);
                if (response.success == true && typeof(response.id) != 'undefined') {
                    var group = $('select#service');
                    group.find('option:first').after('<option value="' + response.id + '">' + response.name + '</option>');
                    group.selectpicker('val', response.id);
                    group.selectpicker('refresh');
                }
                $('#plans-modal').modal('hide');
            } else {
                window.location.reload();
            }
        });
        return false;
    }

    function new_service() {
        $('#plans-modal').modal('show');
        $('.edit-title').addClass('hide');
    }

    function edit_service(invoker, id) {
        var name = $(invoker).data('plan');
        $('#additional').append(hidden_input('id', id));
        $('#plans-modal input[plan="plan"]').val(name);
        $('#plans-modal').modal('show');
        $('.add-title').addClass('hide');
    }
</script>