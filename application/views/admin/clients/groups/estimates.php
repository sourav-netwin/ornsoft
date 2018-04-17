<?php if(isset($client)){ ?>
<h4 class="no-mtop bold"><?php echo _l('estimates'); ?></h4>
<hr />


<div id="estimates_total"></div>
<?php if(has_permission('estimates','','create')){ ?>
<a href="<?php echo admin_url('estimates/estimate?customer_id='.$client->userid); ?>" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('create_new_estimate'); ?></a>
<?php } ?>
<?php if(has_permission('estimates','','view') || has_permission('estimates','','view_own')){ ?>
<a href="#" class="btn btn-info mbot25" data-toggle="modal" data-target="#client_zip_estimates"><?php echo _l('zip_estimates'); ?></a>
<?php } ?>
<?php
$table_data = array(_l('estimate_dt_table_heading_number'),
   _l('estimate_dt_table_heading_amount'),
   _l('estimates_total_tax'),
   _l('invoice_estimate_year'),
   _l('estimate_dt_table_heading_client'),
   _l('project'),
   _l('tags'),
   _l('estimate_dt_table_heading_date'),
   _l('estimate_dt_table_heading_expirydate'),
   _l('reference_no'),
   _l('estimate_dt_table_heading_status'));

$custom_fields = get_custom_fields('estimate',array('show_on_table'=>1));
foreach($custom_fields as $field){
  array_push($table_data,$field['name']);
}
render_datatable($table_data, 'estimates-single-client');
include_once(APPPATH . 'views/admin/clients/modals/zip_estimates.php');
?>

<?php } ?>
