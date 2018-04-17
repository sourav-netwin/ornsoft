<style>
	.section-header a.btn {
		margin-top:-4px;
	}
	.totals-container {
		margin-top:-10px;
	}
	.totals-container .row {
		margin-left:-2px;
		margin-right:-2px;
	}
	.totals-container .total-column {
		padding-left:2px;
		padding-right:2px;
	}
	.dataTables_wrapper > div:first-child {
		display:none;
	}
	.totals-container .row .panel-body h3{
		font-size: 16px;
	}
	.money-format .row .panel-body h3{
		font-size: 11px;
	}
	.totals-container .row .panel-body span{
		font-size: 10px;
	}
	.section-header .header-link{
		color: #333 !important;
	}
</style>
<h4 class="no-mtop bold"><?php echo _l('overview'); ?></h4>
<hr class="no-mbot no-border" />
	<?php 
	$contact_id = '';
	$contact_fname = '';
	$contact_lname = '';
	$contact_email = '';
	$contact_phone = '';
	$this->db->where(array('userid'=> $client->userid, 'is_primary'=>'1'));
	$contact = $this->db->get('tblcontacts')->row();
	if($contact){
		$contact_id = $contact->id;
		$contact_fname = $contact->firstname;
		$contact_lname = $contact->lastname;
		$contact_email = $contact->email;
		$contact_phone = $contact->phonenumber;
	}
	?>
<div class="row">
    <div class="col-md-6">
		<div class="panel-heading project-info-bg no-radius"><?php echo _l('customer_profile'); ?></div>
		<div class="panel-body no-radius">
			<table class="table table-borded no-margin">
				<tbody>
					<tr>
						<td class="bold"><?php echo _l('client_company'); ?></td>
						<td><a href="<?php echo admin_url(); ?>clients/client/<?php echo $client->userid; ?>"><?php echo $client->company; ?></a>
						</td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('client_address'); ?></td>
						<td><?php echo $client->address; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('client_city'); ?></td>
						<td><?php echo $client->city; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('client_state'); ?></td>
						<td><?php echo $client->state; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('client_postal_code'); ?></td>
						<td><?php echo $client->zip; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('clients_country'); ?></td>
						<?php $country_name = ''; 
						$country = get_country($client->country);
						if($country){
							$country_name = $country->short_name;
						}
						?>
						<td><?php echo $country_name; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('client_phonenumber'); ?></td>
						<td><?php echo $client->phonenumber; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('contact_first_name'); ?></td>
						<td><?php echo $contact_fname; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('contact_last_name'); ?></td>
						<td><?php echo $contact_lname; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('contact_email'); ?></td>
						<td><?php echo $contact_email; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('contact_phonenumber'); ?></td>
						<td><?php echo $contact_phone; ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('customer_since'); ?></td>
						<td><?php echo _d($client->datecreated); ?></td>
					</tr>
					<tr>
						<td class="bold"><?php echo _l('assigned_admins'); ?></td>
						<?php
						$admin_names = '';
						$this->db->where(array('customer_id'=> $client->userid));
						$admins = $this->db->get('tblcustomeradmins')->result();
						foreach($admins as $admin){
							$full_name = get_staff_full_name($admin->staff_id);
							$admin_names .= '<a href="' . admin_url('profile/' . $admin->staff_id) . '">' . staff_profile_image($admin->staff_id, array(
								'staff-profile-image-small mright5'
							), 'small', array(
								'data-toggle' => 'tooltip',
								'data-title' => $full_name
							)) . '</a>';
						}
						?>
						<td><?php echo $admin_names; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
	<?php if(has_permission('invoices','','view') || has_permission('invoices','','view_own')) { ?>
		<h4 class="no-mtop bold section-header">
			<a class="header-link" href="<?php echo admin_url('clients/client/'.$client->userid.'?group=invoices'); ?>"><?php echo _l('invoices'); ?></a> 
			<?php if(has_permission('invoices','','edit') || has_permission('invoices','','create')){ ?>
            <a href="<?php echo admin_url('invoices/invoice?customer_id='.$client->userid); ?>" class="btn btn-info pull-right btn-sm"><?php echo _l('create_new_invoice'); ?></a>
            <?php } ?>
		</h4>
		<hr />
		<div id="invoices_total" class="totals-container money-format"></div>
	<?php } ?>

    <?php if(has_permission('estimates','','view') || has_permission('estimates','','view_own')) { ?>
		<h4 class="no-mtop bold section-header">
			<a class="header-link" href="<?php echo admin_url('clients/client/'.$client->userid.'?group=estimates'); ?>"><?php echo _l('estimates'); ?></a>
			<?php if(has_permission('estimates','','edit') || has_permission('estimates','','create')){ ?>
            <a href="<?php echo admin_url('estimates/estimate?customer_id='.$client->userid); ?>" class="btn btn-info pull-right btn-sm"><?php echo _l('create_new_estimate'); ?></a>
            <?php } ?>
		</h4>
		<hr />
		<div id="estimates_total" class="totals-container money-format"></div>
	<?php } ?>

    <?php if(has_permission('proposals','','view') || has_permission('proposals','','view_own')) { ?>
		<h4 class="no-mtop bold section-header">
			<a class="header-link" href="<?php echo admin_url('clients/client/'.$client->userid.'?group=proposals'); ?>"><?php echo _l('proposals'); ?></a>
		</h4>
		<hr />
		<div id="proposals_total" class="totals-container money-format"></div>
	<?php } ?>
	<?php if(has_permission('contracts','','view') || has_permission('contracts','','view_own')){ ?>	
		<h4 class="no-mtop bold section-header">
			<a class="header-link" href="<?php echo admin_url('clients/client/'.$client->userid.'?group=contracts'); ?>"><?php echo _l('contracts'); ?></a>
			<?php if(has_permission('contracts','','edit') || has_permission('contracts','','create')){ ?>
            <a href="<?php echo admin_url('contracts/contract?customer_id='.$client->userid); ?>" class="btn btn-info pull-right btn-sm"><?php echo _l('new_contract'); ?></a>
            <?php } ?>
		</h4>
		<hr />
		<div class="totals-container">
			<div class="row">
				<?php $minus_7_days = date('Y-m-d', strtotime("-7 days")); ?>
				<?php $plus_7_days = date('Y-m-d', strtotime("+7 days"));
				$where_own = array('client'=>$client->userid);
				if(!has_permission('contracts','','view')){
					$where_own['addedfrom'] = get_staff_user_id();
				}
				?>
				<div class="col-md-3 total-column">
					<div class="panel_s">
						<div class="panel-body">
							<h3 class="text-muted _total bold">
								<?php echo total_rows('tblcontracts',array_merge(array('DATE(dateend) >'=>date('Y-m-d'),'trash'=>0),$where_own)); ?>
							</h3>
							<span class="text-info"><?php echo _l('contract_summary_active'); ?></span>
						</div>
					</div>
				</div>
				<div class="col-md-3 total-column">
					<div class="panel_s">
						<div class="panel-body">
							<h3 class="text-muted _total bold"><?php echo total_rows('tblcontracts',array_merge(array('DATE(dateend) <'=>date('Y-m-d'),'trash'=>0),$where_own)); ?></h3>
                            <span class="text-danger"><?php echo _l('contract_summary_expired'); ?></span>
						</div>
					</div>
				</div>
				<div class="col-md-3 total-column">
					<div class="panel_s">
						<div class="panel-body">
							<h3 class="text-muted _total bold">
							<?php echo total_rows(
                                'tblcontracts','dateend BETWEEN "'.$minus_7_days.'" AND "'.$plus_7_days.'" AND trash=0 AND dateend is NOT NULL AND dateend >"'.date('Y-m-d').'"' . (count($where_own) > 0 ? ' AND addedfrom='.get_staff_user_id() : '')); ?></h3>
							<span class="text-warning"><?php echo _l('contract_summary_about_to_expire'); ?></span>
						</div>
					</div>
				</div>
				<div class="col-md-3 total-column">
					<div class="panel_s">
						<div class="panel-body">
							<h3 class="text-muted _total bold">
							<?php echo total_rows('tblcontracts','dateadded BETWEEN "'.$minus_7_days.'" AND "'.$plus_7_days.'" AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom='.get_staff_user_id() : '')); ?></h3>
							<span class="text-success"><?php echo _l('contract_summary_recently_added'); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<h4 class="mtop20 bold section-header">
			<a class="header-link" href="<?php echo admin_url('clients/client/'.$client->userid.'?group=projects'); ?>"><?php echo _l('projects'); ?></a>
			<?php if(has_permission('projects','','edit') || has_permission('projects','','create')){ ?>
            <a href="<?php echo admin_url('projects/project?customer_id='.$client->userid); ?>" class="btn btn-info pull-right btn-sm"><?php echo _l('new_project'); ?></a>
            <?php } ?>
		</h4>
		  <hr />
		  <div class="totals-container">
			  <div class="row">
			   <?php
			   $_where = '';
			   if(!has_permission('projects','','view')){
				$_where = 'id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id='.get_staff_user_id().')';
			  }
			  ?>
			  <?php foreach($project_statuses as $status){ ?>
			  <div class="col-md-3 total-column">
				<div class="panel_s">
				 <div class="panel-body">
				  <h3 class="text-muted _total">
					<?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['id']. ' AND clientid='.$client->userid; ?>
					<?php echo total_rows('tblprojects',$where); ?>
				  </h3>
				  <span class="text-<?php echo project_status_color_class($status['id'],true); ?>"><?php echo project_status_by_id($status['id']); ?></span>
				</div>
			  </div>
			</div>
			<?php } ?>
			</div>
		</div>
		<?php if(isset($client)){ 

		$table_data = array(
		  '#',
		  _l('project_name'),
		  _l('project_customer'),
		  _l('project_start_date'),
		  _l('project_deadline'),
		  _l('project_members'),
		  _l('project_status'),
		  );
		if(has_permission('projects','','create') || has_permission('projects','','edit')){
		 array_push($table_data,_l('project_billing_type'));
		}
		$custom_fields = get_custom_fields('projects',array('show_on_table'=>1));
		foreach($custom_fields as $field){
		  array_push($table_data,$field['name']);
		}
		array_push($table_data, _l('options'));

		render_datatable($table_data,'projects-single-client');
		}
		?>
	</div>	
	<div class="col-md-6">
	<?php if((get_option('access_tickets_to_none_staff_members') == 1 && !is_staff_member()) || is_staff_member()){ ?>
		<h4 class="mtop20 bold section-header">
			<a class="header-link" href="<?php echo admin_url('clients/client/'.$client->userid.'?group=tickets'); ?>"><?php echo _l('tickets'); ?></a>
			<?php if(has_permission('tickets','','edit') || has_permission('tickets','','create')){ ?>
            <a href="<?php echo admin_url('tickets/add/'.$client->userid); ?>" class="btn btn-info pull-right btn-sm"><?php echo _l('new_ticket'); ?></a>
            <?php } ?>
		</h4>
		  <hr />
		  <div class="totals-container">
			  <div class="row">
			   <?php
			   $this->db->order_by("ticketstatusid", "asc");
			   $ticket_statuses = $this->db->get('tblticketstatus')->result();
			   $_where = '';
			   if(!has_permission('tickets','','view')){
				$_where = 'admin='.get_staff_user_id();
			  }
			  ?>
			  <?php foreach($ticket_statuses as $status){ ?>
			  <div class="col-md-<?php echo count($ticket_statuses); ?>ths col-xs-12 total-column">
				<div class="panel_s">
				 <div class="panel-body">
				  <h3 class="text-muted _total bold" style="color:#d33333;">
					
					<?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status->ticketstatusid. ' AND userid='.$client->userid; ?>
					<?php echo total_rows('tbltickets',$where); ?>
				  </h3>
				  <span style="<?php echo 'color:'.$status->statuscolor; ?>"><?php echo ticket_status_translate($status->ticketstatusid); ?></span>
				</div>
			  </div>
			</div>
			<?php } ?>
			</div>
		</div>
		<?php
		if(isset($client)){
		 echo AdminTicketsTableStructure('table-tickets-single');
		} ?>
	<?php } ?>
	</div>
</div>
<script>	
function init_proposals_total() {
	if ($('#proposals_total').length == 0) {
		return;
	}
	var currency = $('body').find('select[name="total_currency"]').val();

	var customer_id = <?php echo $client->userid; ?>;

	$.post(admin_url + 'myclients/get_proposals_total', {
		currency: currency,
		init_total: true,
		customer_id: customer_id
	}).done(function(response) {
		$('#proposals_total').html(response);
	});
}
document.addEventListener("DOMContentLoaded", function() {
	$.post(admin_url + 'myclients/get_proposals_total', {
		customer_id: <?php echo $client->userid; ?>
	}, function(data) {
		if ($('#proposals_total').length == 0) {
			return;
		}
		$('#proposals_total').html(data);
	});
});	
</script>
