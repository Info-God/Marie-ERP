<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <?php echo form_open_multipart(admin_url('sales/sales_channel/') . $id . '/' . $month, array('class' => 'sales-form', 'autocomplete' => 'off', 'id' => 'sales-form')); ?>
      <div class="row">
         <div class="col-md-<?php echo $result = !empty($sales_channel) ? '10 col-md-offset-1' : '8 col-md-offset-2'; ?>">
            <div class="panel_s">
               <div class="panel-body">
                  <h4 class="no-margin h4-panel">
                     <?php echo $title; ?>
                  </h4>
                  <div class="col-md-12"></div>
                  <div class="horizontal-scrollable-tabs" style="margin-top: 10px;">
                     <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                     <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>

                     <div class="horizontal-tabs">
                        <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
                           <li role="presentation" class="<?php if (!isset($menu_data)) {
                                                               if (!$this->input->get('tab')) {
                                                                  echo 'active';
                                                               }
                                                            } ?>" id="tab1">
                              <a href="#sales_channel" aria-controls="sales_channel" role="tab" data-toggle="tab">
                                 <?php echo 'Sales Channel' ?>
                              </a>
                           </li>
                           <?php if (!empty($sales_channel)) { ?>
                              <li role="presentation" class="<?php if ($this->input->get('tab') == 'sales_table1') {
                                                                  echo 'active';
                                                               }; ?>" id="tab2">
                                 <a href="#sales_table1" aria-controls="sales_table1" role="tab" data-toggle="tab">
                                    <?php echo 'Sales Data By Channel' ?>
                                 </a>
                              </li>

                              <?php if (!empty($food_values) || !empty($beverage_values)) { ?>

                                 <li role="presentation" class="<?php if (isset($menu_data)) {
                                                                     echo 'active';
                                                                  }; ?>" id="tab3">
                                    <a href="#sales_table2" aria-controls="sales_table2" role="tab" data-toggle="tab">
                                       <?php echo 'Sales By Menu Product' ?>
                                    </a>
                                 </li>
                              <?php } ?>
                           <?php } ?>
                        </ul>
                     </div>
                  </div>
                  <div class="clearfix"></div>
                  <div class="tab-content mtop15">
                     <!-- reach -->

                     <div role="tabpanel" class="tab-pane<?php if (!isset($menu_data)) {
                                                            if (!$this->input->get('tab')) {
                                                               echo ' active';
                                                            };
                                                         } ?>" id="sales_channel">
                        <?php if ($customer_custom_fields) { ?>
                           <div role="tabpanel" class="tab-pane <?php if ($this->input->get('tab') == 'custom_fields') {
                                                                     echo ' active';
                                                                  }; ?>" id="custom_fields">
                              <?php $rel_id = (isset($client) ? $client->userid : false); ?>
                              <?php echo render_custom_fields('customers', $rel_id); ?>
                           </div>
                        <?php } ?>
                        <div class="row">
                           <div class="col-md-12">
                              <?php $selected = array();
                              if (isset($sales_channel)) {

                                 foreach ($sales_channel as $channel) {
                                    array_push($selected, $channel);
                                 }
                              }
                              ?>
                              <?php $value = (isset($sales_channel) ? $sales_channel : ''); ?>
                              <?php if (empty($sales_channel)) {echo render_input('month', 'Month', $value, 'month', $attrs);} ?>

                              <?php echo render_select('reach[]', get_reach_types(), array('name', 'name'), 'Sales Type', $selected, array('multiple' => true, 'data-actions-box' => true, 'required'), array(), '', '', false); ?>

                              <?php if (!empty($sales_channel)) { ?>
                                 <ul>
                                    <a href="#sales_table1" aria-controls="sales_table1" role="tab" data-toggle="tab" data-target="#sales_table1" onclick="salesDataCall()"><button type="button" class="btn  btn-primary">Next</button></a>
                                 </ul>
                              <?php } ?>
                           </div>
                        </div>
                     </div>

                     <!-- Sales table -->
                     <?php if (!empty($sales_channel)) { ?>
                        <div role="tabpanel" class="tab-pane" id="sales_table1">
                           <div class="mtop15">
                              <?php $value = (isset($month) ? $month : '');
                              ?>
                              <?php echo render_select('month', get_months(), array('name', 'name'), 'Month', $value);
                              ?>
                              <table class="table dt-table scroll-responsive" data-order-col="2" data-order-type="desc">
                                 <thead>
                                    <tr>
                                       <th>Sales Types</th>

                                       <?php foreach ($sales_channel as $channel) { ?>

                                          <th>
                                             <?php echo $channel; ?>
                                          </th>

                                       <?php } ?>
                                       <th><?php #echo "Sum" 
                                             ?></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr>
                                       <th>Foods</th>
                                       <?php for ($i = 0; $i < count($sales_channel); $i++) { ?>
                                          <th>
                                             <?php foreach ($food_values as $key => $food_value) {
                                                if ($key == $i) {
                                                   break;
                                                }
                                             } ?>
                                             <input type="text" name="food_values[<?php echo $i ?>]" value="<?php echo $data = (isset($food_values) ? $food_value : ''); ?>">
                                          </th>
                                       <?php } ?>
                                       <th>
                                          <!-- <input type="text" name="food_values" value=""> -->
                                       </th>
                                    </tr>
                                    <tr>
                                       <th>Beverages</th>
                                       <?php for ($j = 0; $j < count($sales_channel); $j++) { ?>
                                          <th>
                                             <?php foreach ($beverages_values as $key => $beverage_value) {
                                                if ($key == $j) {
                                                   break;
                                                }
                                             } ?>
                                             <input type="text" name="beverages_values[<?php echo $j ?>]" value="<?php echo $data = (isset($beverages_values) ? $beverage_value : ''); ?>">
                                          </th>
                                       <?php } ?>
                                       <th>
                                          <!-- <input type="text" name="food_values" value=""> -->
                                       </th>
                                    </tr>
                                    <!-- <tr>
                                                   <th>Sum</th>
                                                   <?php #for ($j = 0; $j < count($sales_channel); $j++) { 
                                                   ?>
                                                      <th>
                                                            <input type="text" name="beverages_values[<?php #echo $j 
                                                                                                      ?>]" value="<?php #echo $data = (isset($beverages_values) ? $beverage_value : ''); 
                                                                                                                  ?>">
                                                      </th>
                                                   <?php #} 
                                                   ?>
                                                   th>
                                                            <input type="text" name="food_values" value="" disabled> 
                                                   </th>
                                                </tr> -->
                                 </tbody>
                              </table>
                              <div class="">
                                 <a href="#sales_channel" aria-controls="sales_channel" role="tab" data-toggle="tab" data-target="#sales_channel"><button type="button" class="btn  btn-primary" onclick="salesChannelCall()">Back</button></a>
                                 <a href="#sales_table2" aria-controls="sales_table2" role="tab" data-toggle="tab" data-target="#sales_table2"><button type="button" class="btn  btn-primary " onclick="salesMenuCall()">Next</button></a>
                              </div>

                           </div>
                        </div>
                     <?php } ?>
                     <!-- Sales table 2 -->
                     <?php if (!empty($food_value) || !empty($beverage_value)) { ?>
                        <div role="tabpanel" class="tab-pane<?php if (isset($menu_data)) {
                                                               echo 'active';
                                                            } ?>" id="sales_table2">
                           <div class="mtop15" style="margin-bottom: 10px;">
                              <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#import_sales" style="margin-right: 10px;"><?php echo _l('Upload File'); ?></a>
                              <a href='#' class="btn btn-info pull-left" data-toggle="modal" data-target="#new_row" style="margin-bottom: 10px;"><?php echo _l('New Row'); ?></a>
                              <table class="table dt-table scroll-responsive" data-order-col="0" data-order-type="desc">
                                 <thead>
                                    <tr>
                                       <th>Item No</th>
                                       <th>Category</th>
                                       <th>Item Name</th>
                                       <th>Total Unit Sales Volume</th>
                                       <th>Options</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php if ($menu_data) {
                                       $i = 0;
                                       while ($i < count($menu_data)) {
                                    ?>
                                          <tr>
                                             <td><?php echo $i + 1; ?></td>
                                             <td><?php echo $menu_data[$i]['Category']; ?></td>
                                             <td><?php echo $menu_data[$i]['Item_Name']; ?></td>
                                             <td><?php echo $menu_data[$i]['Total_Unit_Sales_Volume']; ?></td>
                                             <td><?php echo icon_btn('sales/delete_row/' . $id . '/' . $month . '/' . $row_id[$i] . '#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#edit_row', 'data-id' => $aRow['id']]);
                                                   echo icon_btn('sales/delete_row/' . $id . '/' . $month . '/' . $row_id[$i], 'remove', 'btn-danger _delete'); ?></td>
                                          </tr>
                                    <?php
                                          $i++;
                                       }
                                    } ?>
                                 </tbody>
                              </table>
                              <a href="#sales_table1" aria-controls="sales_table1" role="tab" data-toggle="tab" data-target="#sales_table1"><button type="button" class="btn  btn-primary" onclick="salesDataCall()">Back</button></a>

                           </div>
                        </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div>
         <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <button class="btn btn-info only-save customer-form-submiter">
               <?php echo _l('submit'); ?>
            </button>
         </div>
         <?php if (!empty($sales_channel)) {
            $this->load->view('admin/sales/sales_import');
            $this->load->view('admin/sales/sales_row');
            $this->load->view('admin/sales/sales_row_edit');
         } ?>
      </div>
      <?php echo form_close(); ?>
   </div>
</div>
<?php init_tail(); ?>
<script>
   $(function() {
      appValidateForm($('#sales-form'), {
         // 'reach[]': 'required',
         // food_values: 'required',
         // beverages_values: 'required',
         // month: 'required',
      });
   });


   var element1 = document.getElementById('tab1')
   var element2 = document.getElementById('tab2')
   var element3 = document.getElementById('tab3')



   function salesChannelCall() {
      element1.classList.add("active");
      element2.classList.remove("active");
      element3.classList.remove("active");
   }

   function salesDataCall() {
      element1.classList.remove("active");
      element2.classList.add("active");
      element3.classList.remove("active");
   }

   function salesMenuCall() {
      element1.classList.remove("active");
      element2.classList.remove("active");
      element3.classList.add("active");
   }
</script>

</body>

</html>