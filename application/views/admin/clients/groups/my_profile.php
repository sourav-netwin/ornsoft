<style>
.my-dropdown{
	width:auto;
}
.dropdown-menu .btn{
	color: #ffffff;
	white-space: nowrap;
}
.dropdown-menu .btn:hover{
	color: #ffffff;
    background-color: #1e95b1;
}
.dropdown-menu li{
	padding:2px 4px;
}
</style>  
  <h4 class="no-mtop bold"><?php echo _l('client_add_edit_profile'); ?></h4>
  <hr class="no-mbot no-border" />
<div class="row">
    <?php echo form_open($this->uri->uri_string(),array('class'=>'client-form','autocomplete'=>'off', 'id'=>'client_fom')); ?>
    <div class="additional"></div>
    <div class="col-md-12">
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                    <?php echo _l( 'customer_profile_details'); ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                    <?php echo _l( 'billing_shipping'); ?>
                </a>
            </li>
            <?php if(isset($client)){ ?>
            <li role="presentation">
                <a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">
                    <?php echo _l( 'customer_contacts'); ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#customer_admins" aria-controls=customer_admins" role="tab" data-toggle="tab">
                    <?php echo _l( 'customer_admins'); ?>
                </a>
            </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane ptop10 active" id="contact_info">
                <div class="row">
                    <div class="col-md-6">
                        <?php 
						$value=( isset($client) ? $client->company : ''); ?>
                        <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                        <?php echo render_input( 'company', 'client_company',$value,'text',$attrs); ?>
                        <?php $value=( isset($client) ? $client->phonenumber : ''); ?>
                        <?php echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>
                        <?php 
						if(get_option('company_requires_vat_number_field') == 1){
                            $value=( isset($client) ? $client->vat : '');
                            echo render_input( 'vat', 'client_vat_number',$value);
                        }
                        $s_attrs = array('data-none-selected-text'=>_l('system_default_string'));
                        $selected = '';
                        if(isset($client) && client_have_transactions($client->userid)){
                          $s_attrs['disabled'] = true;
                      }
                      foreach($currencies as $currency){
                        if(isset($client)){
                          if($currency['id'] == $client->default_currency){
                            $selected = $currency['id'];
                        }
                    }
                }
                ?>
                <?php if(!isset($client)){ ?>
                <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_currency_change_notice'); ?>"></i>
                <?php } ?>
                <?php echo render_select('default_currency',$currencies,array('id','name','symbol'),'invoice_add_edit_currency',$selected,$s_attrs); ?>
                <div class="form-group">
                    <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
                    </label>
                    <select name="default_language" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""><?php echo _l('system_default_string'); ?></option>
                        <?php foreach(list_folders(APPPATH .'language') as $language){
                            $selected = '';
                            if(isset($client)){
                             if($client->default_language == $language){
                              $selected = 'selected';
                          }
						  }
						  ?>
						  <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
						  <?php } ?>
					  </select>
				  </div>
				  <?php $value=( isset($client) ? $client->latitude : ''); ?>
				  <?php echo render_input( 'latitude', 'customer_latitude',$value); ?>
				  <?php $value=( isset($client) ? $client->longitude : ''); ?>
				  <?php echo render_input( 'longitude', 'customer_longitude',$value); ?>
			  </div>
			  <div class="col-md-6">
				<?php $value=( isset($client) ? $client->address : ''); ?>
				<?php echo render_input( 'address', 'client_address',$value); ?>
				<?php $value=( isset($client) ? $client->city : ''); ?>
				<?php echo render_input( 'city', 'client_city',$value); ?>
				<?php $value=( isset($client) ? $client->state : ''); ?>
				<?php echo render_input( 'state', 'client_state',$value); ?>
				<?php $value=( isset($client) ? $client->zip : ''); ?>
				<?php echo render_input( 'zip', 'client_postal_code',$value); ?>
				<?php $countries= get_all_countries();
				$customer_default_country = get_option('customer_default_country');
				$selected =( isset($client) ? $client->country : $customer_default_country);
				echo render_select( 'country',$countries,array( 'country_id',array( 'short_name')), 'clients_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
				?>
				<?php $value=( isset($client) ? $client->website : ''); ?>
                <?php echo render_input( 'website', 'client_website',$value); ?>
				<?php
				$selected = array();
				if(isset($customer_groups)){
					foreach($customer_groups as $group){
						 array_push($selected,$group['groupid']);
					 }
				 }
				 echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',$selected,array('multiple'=>true),array(),'','',false);
			 ?>
		 </div>
     <div class="col-md-12">
        <?php $rel_id=( isset($client) ? $client->userid : false); ?>
        <?php echo render_custom_fields( 'customers',$rel_id); ?>
    </div>
</div>
</div>
<?php if(isset($client)){ ?>
<div role="tabpanel" class="tab-pane" id="contacts">
    <?php if(has_permission('customers','','create') || is_customer_admin($client->userid)){
        $disable_new_contacts = false;
        if(is_empty_customer_company($client->userid) && total_rows('tblcontacts',array('userid'=>$client->userid)) == 1){
           $disable_new_contacts = true;
        }
        ?>
    <div class="inline-block"<?php if($disable_new_contacts){ ?> data-toggle="tooltip" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>"<?php } ?>>
    <a href="#" onclick="contact(<?php echo $client->userid; ?>); return false;" class="btn btn-info mbot25<?php if($disable_new_contacts){echo ' disabled';} ?>"><?php echo _l('new_contact'); ?></a>
    </div>
    <?php } ?>
    <?php
    $table_data = array(_l('client_firstname'),_l('client_lastname'),_l('client_email'),_l('contact_position'),_l('client_phonenumber'),_l('contact_active'),_l('clients_list_last_login'));
    $custom_fields = get_custom_fields('contacts',array('show_on_table'=>1));
    foreach($custom_fields as $field){
       array_push($table_data,$field['name']);
   }
   array_push($table_data,_l('options'));
   echo render_datatable($table_data,'contacts'); ?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="customer_admins">
    <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
    <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
    <?php } ?>
    <table class="table dt-table">
        <thead>
            <tr>
                <th><?php echo _l('staff_member'); ?></th>
                <th><?php echo _l('customer_admin_date_assigned'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customer_admins as $c_admin){ ?>
            <tr>
                <td><a href="<?php echo admin_url('profile/'.$c_admin['staff_id']); ?>">
                    <?php echo staff_profile_image($c_admin['staff_id'], array(
                        'staff-profile-image-small',
                        'mright5'
                        ));
                        echo get_staff_full_name($c_admin['staff_id']); ?></a>
                    </td>
                    <td data-order="<?php echo $c_admin['date_assigned']; ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
    <div role="tabpanel" class="tab-pane ptop10" id="billing_and_shipping">
        <div class="row">
            <div class="col-md-12">
                <div class="row">

                    <div class="col-md-6">
                        <h4><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="text-info font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->billing_street : ''); ?>
                        <?php echo render_input( 'billing_street', 'billing_street',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_city : ''); ?>
                        <?php echo render_input( 'billing_city', 'billing_city',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_state : ''); ?>
                        <?php echo render_input( 'billing_state', 'billing_state',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
                        <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
                        <?php $selected=( isset($client) ? $client->billing_country : '' ); ?>
                        <?php echo render_select( 'billing_country',$countries,array( 'country_id',array( 'short_name')), 'billing_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                    </div>

                    <div class="col-md-6">
                        <h4>
                            <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                            <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="text-info font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a></h4>
                            <hr />
                            <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
                            <?php echo render_input( 'shipping_street', 'shipping_street',$value); ?>
                            <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
                            <?php echo render_input( 'shipping_city', 'shipping_city',$value); ?>
                            <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
                            <?php echo render_input( 'shipping_state', 'shipping_state',$value); ?>
                            <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
                            <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
                            <?php $selected=( isset($client) ? $client->shipping_country : $customer_default_country ); ?>
                            <?php echo render_select( 'shipping_country',$countries,array( 'country_id',array( 'short_name')), 'shipping_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                        </div>
                        <?php if(isset($client) &&
                        (total_rows('tblinvoices',array('clientid'=>$client->userid)) > 0 || total_rows('tblestimates',array('clientid'=>$client->userid)) > 0)){ ?>
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="update_all_other_transactions" id="update_all_other_transactions">
                                    <label for="update_all_other_transactions">
                                        <?php echo _l('customer_update_address_info_on_invoices'); ?><br />
                                    </label>
                                </div>
                                <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
		<?php if(!isset($client) && (has_permission('projects', '', 'create') || has_permission('estimates', '', 'create'))){ ?>
		<div class="dropup my-dropdown mtop20">
		  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<?php echo _l('submit'); ?>
			<span class="caret"></span>
		  </button>
		  <button class="btn btn-info save-and-add-contact my-customer-form-submiter">
            <?php echo _l( 'save_customer_and_add_contact'); ?>
		  </button>
		  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
			<li><a href="javascript:void(0)" id="normal-submit" class="btn btn-primary"><?php echo _l('submit'); ?></a></li>
			<?php if(has_permission('estimates', '', 'create')){ ?>
			<li><a href="javascript:void(0)" id="create-estimate" class="btn btn-primary"><?php echo _l('client_save_and_new_estimate'); ?></a></li>
			<?php } ?>
			<?php if(has_permission('projects', '', 'create')){ ?>
			<li><a href="javascript:void(0)" id="create-submit" class="btn btn-primary"><?php echo _l('lead_save_and_new_project'); ?></a></li>
			<li><a href="javascript:void(0)" id="copy-submit" class="btn btn-primary"><?php echo _l('lead_save_and_copy_project'); ?></a></li>
			<?php } ?>
		  </ul>
		</div>
        <?php } else { ?>
        <button class="btn btn-info mtop20 only-save my-customer-form-submiter">
            <?php echo _l( 'submit'); ?>
        </button>
		<?php } ?>
    </div>
</div>
<?php echo form_close(); ?>
</div>
<div id="contact_data"></div>
<?php if(isset($client)){ ?>
<?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
<div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('clients/assign_admins/'.$client->userid)); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
            </div>
            <div class="modal-body">
                <?php
                $selected = array();
                foreach($customer_admins as $c_admin){
                 array_push($selected,$c_admin['staff_id']);
             }
             echo render_select('customer_admins[]',$staff,array('staffid',array('firstname','lastname')),'',$selected,array('multiple'=>true),array(),'','',false); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        </div>
    </div>
    <!-- /.modal-content -->
    <?php echo form_close(); ?>
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php } ?>
<?php } ?>
<script>
var clientId = 0;
document.addEventListener("DOMContentLoaded", function() {
    my_validate_lead_convert_to_client_form();
    var create_type = 0;
    function my_validate_lead_convert_to_client_form() {
	   var validate_result = false;
		_validate_form($('#client_fom'), {
			company: 'required'
		}, function(form){
			if(create_type == 0){
				form.submit();
			} else if(create_type == 1){
				$.post(admin_url + 'myclients/client', $('#client_fom').serialize(), function(data) {
					var client = JSON.parse(data);
					window.location.href = site_url + "admin/projects/project?customer_id=" + client.id;
			    });
			} else if(create_type == 3){
				$.post(admin_url + 'myclients/client', $('#client_fom').serialize(), function(data) {
					var client = JSON.parse(data);
					window.location.href = site_url + "admin/estimates/estimate?customer_id=" + client.id;
			    });
			} else if(create_type == 2){
				$.post(admin_url + 'myclients/client', $('#client_fom').serialize(), function(data) {
					var client = JSON.parse(data);
					if(client && client.id && client.id > 0){
						clientId = client.id;
						var clientName = $('#client_fom input[name="company"]').val();
						$.get(admin_url + 'myleads/copy_project', function(response) {
							$('#copy_project').remove();
							$('body').append(response);
							$('#copy_project').modal({
								show: true,
								backdrop: 'static',
								keyboard: false
							});						
							$('#copy_project input[name="project_name"]').val(clientName);
							$('#copy_form input[name="client_id"]').remove();
							$('#copy_form').append($('<input>').attr({
								type: 'hidden',
								name: 'client_id',
								value: clientId
							}));
							$('#copy_project').on('hidden.bs.modal', function () {
								window.location.href = admin_url + 'clients/client/' + clientId;
							})
						});
					}
			    });
			}
		});
	}
	$('#normal-submit').click(function(){
		create_type = 0;
		$('#client_fom').submit();
		//return false;
	});
	$('#create-submit').click(function(){
		create_type = 1;
		$('#client_fom').submit();
		//return false;
	});
	$('#copy-submit').click(function(){
		create_type = 2;
		$('#client_fom').submit();
		//return false;
	});
	$('#create-estimate').click(function(){
		create_type = 3;
		$('#client_fom').submit();
		//return false;
	});
	$('.my-customer-form-submiter').on('click', function() {
		create_type = 0;
         var form = $('.client-form');
         if (form.valid()) {
             if ($(this).hasClass('save-and-add-contact')) {
                 form.find('.additional').html(hidden_input('save_and_add_contact', 'true'));
             } else {
                 form.find('.additional').html('');
             }
         }
     });
});	
</script>