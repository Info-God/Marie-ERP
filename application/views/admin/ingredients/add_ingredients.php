<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="add-ingredients" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('ingredients/create_ingredients'), array('class' => 'ingredients-form', 'autocomplete' => 'off', 'id' => 'ingredients-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo 'New Ingredient'; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php $value = (isset($subgroup_name) ? $subgroup_name : ''); ?>
                        <?php $attrs = (isset($subgroup_name) ? array() : array('autofocus' => true, 'placeholder' => 'Enter new category name')); ?>
                        <?php echo render_input('category_name', 'Category', $value, 'text', $attrs); ?>
                        <?php $value = (isset($ingredient_name) ? $ingredient_name : ''); ?>
                        <?php $attrs = (isset($ingredient_name) ? array() : array('autofocus' => true, 'placeholder' => 'Enter new Item name')); ?>
                        <?php echo render_input('ingredient_name', 'Ingredient', $value, 'text', $attrs); ?>
                        <?php $units=[0 => ["name"=> "kg"],1 => ["name"=> "litres"],2 => ["name"=> "units"],]; ?>
                        <?php echo render_select('unit', $units, array('name', 'name'), 'Unit of Measure', "kg"); ?>
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
            ingredient_name: 'required'
        });

        // Reset form when the modal is shown
        $('#add-ingredients').on('shown.bs.modal', function() {
            $('#additional').html('');
            $('#ingredients-form')[0].reset();
            $('.add-title').removeClass('hide');
        });

        // Handle form submission via AJAX
        $('#ingredients-form').on('submit', function(e) {
        location.reload();
        //     e.preventDefault();
        //     var form = $(this);
        //     $.ajax({
        //         type: form.attr('method'),
        //         url: form.attr('action'),
        //         data: form.serialize(),
        //         success: function(response) {
        //             if(response.success) {
        //                 console.log("1232");
        //                 location.reload();
        //             } else {
        //                 // Handle the error response
        //                 alert('An error occurred. Please try again.');
        //             }
        //         },
        //         error: function() {
        //             // Handle AJAX error
        //             alert('An error occurred. Please try again.');
        //         }
        //     });
        });
    });

    function new_service() {
        $('#add-ingredients').modal('show');
    }
</script>
