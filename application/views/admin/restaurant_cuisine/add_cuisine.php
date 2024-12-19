<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="add-cuisine" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('restaurant_cuisine/create_cuisine'), array('id' => 'add_cuisine')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo 'Edit Cuisine'; ?></span>
                    <span class="add-title"><?php echo 'New Cuisine'; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php $value = (isset($restaurant_details) ? $restaurant_details[0]['business_name'] : ''); ?>
                        <?php $attrs = (isset($restaurant_details) ? array() : array('autofocus' => true, 'placeholder' => 'Enter new cuisine')); ?>
                        <?php echo render_input('cuisine_type', 'Cuisine', $value, 'text', $attrs); ?>
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
        appValidateForm($('#add_cuisine'), {
            cuisine_type: 'required'
        }, manage_ticket_services);
        $('#add-cuisine').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            console.log(group_id);
            $('#additional').html('');
            // $('#add-cuisine input[plan="plan"]').val('');
            // $('.add-title').removeClass('hide');
            // $('.edit-title').removeClass('hide');

            $('#add-cuisine .add-title').removeClass('hide');
            $('#add-cuisine .edit-title').addClass('hide');

            if (typeof(group_id) !== 'undefined') {
                $('#add-cuisine input[name="id"]').val(group_id);
                $('#add-cuisine .add-title').addClass('hide');
                $('#add-cuisine .edit-title').removeClass('hide');
                $('#add-cuisine input[name="cuisine_type"]').val($(invoker).parents('tr').find('td').eq(1).text());
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
                $('#add-cuisine').modal('hide');
            } else {
                window.location.reload();
            }
        });
        return false;
    }

    function new_service() {
        $('#add-cuisine').modal('show');
        $('.edit-title').addClass('hide');
    }

    function edit_service(invoker, id) {
        var name = $(invoker).data('cuisine_type');
        $('#additional').append(hidden_input('id', id));
        $('#add-cuisine input[cuisine_type="cuisine_type"]').val(name);
        $('#add-cuisine').modal('show');
        $('.add-title').addClass('hide');
    }
</script>