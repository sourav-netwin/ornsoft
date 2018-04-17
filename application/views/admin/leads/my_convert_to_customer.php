<style>
.my-dropdown{
	width:auto;
	margin-left:6px;
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
<div class="modal fade" id="convert_lead_to_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-lg" role="document">
      <?php echo form_open('admin/leads/convert_to_customer',array('id'=>'lead_to_client_form')); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
               <?php echo _l('lead_convert_to_client'); ?>
            </h4>
         </div>
         <div class="modal-body">
            <?php echo form_hidden('leadid',$lead->id); ?>
            <?php if(strpos($lead->name,' ') !== false){
               $_temp = explode(' ',$lead->name);
               $firstname = $_temp[0];
               if(isset($_temp[2])){
                 $lastname = $_temp[1] . ' ' . $_temp[2];
              } else {
                 $lastname = $_temp[1];
              }
           } else {
              $lastname = '';
              $firstname = $lead->name;
           }
           ?>
           <?php echo render_input('firstname','lead_convert_to_client_firstname',$firstname); ?>
           <?php echo render_input('lastname','lead_convert_to_client_lastname',$lastname); ?>
           <?php echo render_input('title','contact_position',$lead->title); ?>
           <?php echo render_input('email','lead_convert_to_email',$lead->email); ?>
           <?php echo render_input('company','lead_company',$lead->company); ?>
           <?php echo render_input('phonenumber','lead_convert_to_client_phone',$lead->phonenumber); ?>
           <?php echo render_input('address','client_address',$lead->address); ?>
           <?php echo render_input('city','client_city',$lead->city); ?>
           <?php echo render_input('state','client_state',$lead->state); ?>
           <?php
           $countries= get_all_countries();
           $customer_default_country = get_option('customer_default_country');
           $selected =($lead->country != 0 ? $lead->country : $customer_default_country);
           echo render_select( 'country',$countries,array( 'country_id',array( 'short_name')), 'clients_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
           ?>
           <?php echo render_input('zip','clients_zip',$lead->zip); ?>
           <?php
           $not_mergable_customer_fields  = array('userid','datecreated','leadid','default_language','default_currency','active');
           $not_mergable_contact_fields  = array('id','userid','datecreated','is_primary','password','new_pass_key','new_pass_key_requested','last_ip','last_login','last_password_change','active','profile_image','direction');
           $customer_fields = $this->db->list_fields('tblclients');
           $contact_fields = $this->db->list_fields('tblcontacts');
           $custom_fields = get_custom_fields('leads');
           $found_custom_fields = false;
           foreach ($custom_fields as $field) {
             $value = get_custom_field_value($lead->id, $field['id'], 'leads');
             if ($value == '') {
              continue;
           } else {
              $found_custom_fields = true;
           }
        }
        if($found_custom_fields == true){
         echo '<h4 class="bold text-center mtop30">'._l('copy_custom_fields_convert_to_customer').'</h4><hr />';
      }
      foreach ($custom_fields as $field) {
         $value = get_custom_field_value($lead->id, $field['id'], 'leads');
         if ($value == '') {
            continue;
         }
         ?>
         <p class="bold text-info"><?php echo $field['name']; ?> (<?php echo $value; ?>)</p>
         <hr />
         <p class="bold no-margin"><?php echo _l('leads_merge_customer'); ?></p>
         <div class="radio radio-primary">
            <input type="radio" data-field-id="<?php echo $field['id']; ?>" id="m_1_<?php echo $field['id']; ?>" class="include_leads_custom_fields" checked name="include_leads_custom_fields[<?php echo $field['id']; ?>]" value="1">
            <label for="m_1_<?php echo $field['id']; ?>" class="font-normal">
               <span data-toggle="tooltip" data-title="<?php echo _l('copy_custom_fields_convert_to_customer_help'); ?>"><i class="fa fa-info-circle"></i></span> <?php echo _l('lead_merge_custom_field'); ?>
            </label>
         </div>
         <div class="radio radio-primary">
            <input type="radio" data-field-id="<?php echo $field['id']; ?>" id="m_2_<?php echo $field['id']; ?>" class="include_leads_custom_fields" name="include_leads_custom_fields[<?php echo $field['id']; ?>]" value="2">
            <label for="m_2_<?php echo $field['id']; ?>" class="font-normal">
               <?php echo _l('lead_merge_custom_field_existing'); ?>
            </label>
         </div>
         <div class="hide" id="merge_db_field_<?php echo $field['id']; ?>">
            <hr />
            <select name="merge_db_fields[<?php echo $field['id']; ?>]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
               <option value=""></option>
               <?php foreach($customer_fields as $c_field){
                  if(!in_array($c_field, $not_mergable_customer_fields)){
                   echo '<option value="'.$c_field.'">'.str_replace('_',' ',ucfirst($c_field)).'</option>';
                }
             }
             ?>
          </select>
          <hr />
       </div>
       <p class="bold"><?php echo _l('leads_merge_contact'); ?></p>
       <div class="radio radio-primary">
         <input type="radio" data-field-id="<?php echo $field['id']; ?>" id="m_3_<?php echo $field['id']; ?>" class="include_leads_custom_fields" name="include_leads_custom_fields[<?php echo $field['id']; ?>]" value="3">
         <label for="m_3_<?php echo $field['id']; ?>" class="font-normal">
            <?php echo _l('leads_merge_as_contact_field'); ?>
         </label>
      </div>
      <div class="radio radio-primary">
         <input type="radio" data-field-id="<?php echo $field['id']; ?>" id="m_4_<?php echo $field['id']; ?>" class="include_leads_custom_fields" name="include_leads_custom_fields[<?php echo $field['id']; ?>]" value="4">
         <label for="m_4_<?php echo $field['id']; ?>" class="font-normal">
            <span data-toggle="tooltip" data-title="<?php echo _l('copy_custom_fields_convert_to_customer_help'); ?>"><i class="fa fa-info-circle"></i></span>
            <?php echo _l('lead_merge_custom_field'); ?>
         </label>
      </div>
      <div class="hide" id="merge_db_contact_field_<?php echo $field['id']; ?>">
         <hr />
         <select name="merge_db_contact_fields[<?php echo $field['id']; ?>]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
            <option value=""></option>
            <?php foreach($contact_fields as $c_field){
               if(!in_array($c_field, $not_mergable_contact_fields)){
                echo '<option value="'.$c_field.'">'.str_replace('_',' ',ucfirst($c_field)).'</option>';
             }
          }
          ?>
       </select>
    </div>
    <hr />
    <div class="radio radio-primary">
      <input type="radio" data-field-id="<?php echo $field['id']; ?>" id="m_5_<?php echo $field['id']; ?>" class="include_leads_custom_fields" name="include_leads_custom_fields[<?php echo $field['id']; ?>]" value="5">
      <label for="m_5_<?php echo $field['id']; ?>" class="font-normal">
         <?php echo _l('lead_dont_merge_custom_field'); ?>
      </label>
   </div>
   <hr />
   <?php } ?>
   <?php echo form_hidden('original_lead_email',$lead->email); ?>
   <div class="client_password_set_wrapper">
      <label for="password" class="control-label"><?php echo _l('client_password'); ?></label>
      <div class="input-group">
         <input type="password" class="form-control password" name="password" autocomplete="off">
         <span class="input-group-addon">
            <a href="#password" class="show_password" onclick="showPassword('password');return false;"><i class="fa fa-eye"></i></a>
         </span>
         <span class="input-group-addon">
            <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
         </span>
      </div>
   </div>
   <?php if(total_rows('tblemailtemplates',array('slug'=>'contact-set-password','active'=>0)) == 0){ ?>
   <div class="checkbox checkbox-primary">
      <input type="checkbox" name="send_set_password_email" id="send_set_password_email">
      <label for="send_set_password_email">
         <?php echo _l( 'client_send_set_password_email'); ?>
      </label>
   </div>
   <?php } ?>
   <?php if(total_rows('tblemailtemplates',array('slug'=>'new-client-created','active'=>0)) == 0){ ?>
   <div class="checkbox checkbox-primary">
      <input type="checkbox" name="donotsendwelcomeemail" id="donotsendwelcomeemail">
      <label for="donotsendwelcomeemail"><?php echo _l('client_do_not_send_welcome_email'); ?></label>
   </div>
   <?php } ?>
</div>
<div class="modal-footer">
   <button type="button" class="btn btn-default" onclick="init_lead(<?php echo $lead->id; ?>); return false;" data-dismiss="modal"><?php echo _l('back_to_lead'); ?></button>
   <?php if(has_permission('projects', '', 'create')){ ?>
   <div class="dropup my-dropdown pull-right">
	  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<?php echo _l('submit'); ?>
		<span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
		<li><a href="#" id="normal-submit" class="btn btn-primary"><?php echo _l('submit'); ?></a></li>
		<li><a href="#" id="create-submit" class="btn btn-primary"><?php echo _l('lead_save_and_new_project'); ?></a></li>
		<li><a href="#" id="copy-submit" class="btn btn-primary"><?php echo _l('lead_save_and_copy_project'); ?></a></li>
	  </ul>
	</div>
   <?php } else { ?>
	<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
	<?php } ?>
</div>
</div>
<?php echo form_close(); ?>
</div>
</div>
<script>
   var clientId = 0;
   my_validate_lead_convert_to_client_form();
   init_selectpicker();
   var create_type = 0;
   function my_validate_lead_convert_to_client_form() {
	   var validate_result = false;
		_validate_form($('#lead_to_client_form'), {
			company: 'required',
			firstname: 'required',
			lastname: 'required',
			password: {
				required: {
					depends: function(element) {
						var sent_set_password = $('input[name="send_set_password_email"]');
						if (sent_set_password.prop('checked') == false) {
							return true;
						}
					}
				}
			},
			email: {
				required: true,
				email: true,
				remote: {
					url: site_url + "admin/misc/contact_email_exists",
					type: 'post',
					data: {
						email: function() {
							return $('#lead_to_client_form input[name="email"]').val();
						},
						userid: ''
					}
				}
			}

		}, function(form){
			if(create_type == 0){
				form.submit();
			} else if(create_type == 1){
				$.post(admin_url + 'myleads/convert_to_customer', $('#lead_to_client_form').serialize(), function(data) {
					var client = JSON.parse(data);
					window.location.href = site_url + "admin/projects/project?customer_id=" + client.id;
			    });
			} else if(create_type == 2){
				$.post(admin_url + 'myleads/convert_to_customer', $('#lead_to_client_form').serialize(), function(data) {
					var client = JSON.parse(data);
					if(client && client.id && client.id > 0){
						clientId = client.id;
						$('#convert_lead_to_client_modal').modal('hide');
						var clientName = $('#lead_to_client_form input[name="firstname"]').val() + " " + $('#lead_to_client_form input[name="lastname"]').val();
						$.get(admin_url + 'myleads/copy_project', function(response) {
							$('#lead_convert_to_customer').html(response);
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
						});
					}
			    });
			}
		});
	}
	$('#normal-submit').click(function(){
		create_type = 0;
		$('#lead_to_client_form').submit();
	});
	$('#create-submit').click(function(){
		create_type = 1;
		$('#lead_to_client_form').submit();
	});
	$('#copy-submit').click(function(){
		create_type = 2;
		$('#lead_to_client_form').submit();
	});
	
</script>
