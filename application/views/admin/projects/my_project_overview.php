<div class="row">
   <div class="col-md-6 border-right">
      <div class="row">
         <div class="col-md-<?php echo ($project->deadline ? 6 : 12); ?> project-progress-bars">
            <?php $tasks_not_completed_progress = round($tasks_not_completed_progress,2); ?>
            <?php $project_time_left_percent = round($project_time_left_percent,2); ?>
            <div class="row">
               <div class="col-md-9">
                  <p class="text-uppercase bold text-dark font-medium">
                     <?php echo $tasks_not_completed; ?> / <?php echo $total_tasks; ?> <?php echo _l('project_open_tasks'); ?>
                  </p>
                  <p class="text-muted bold"><?php echo $tasks_not_completed_progress; ?>%</p>
               </div>
               <div class="col-md-3 text-right">
                  <i class="fa fa-check-circle<?php if($tasks_not_completed_progress >= 100){echo ' text-success';} ?>" aria-hidden="true"></i>
               </div>
               <div class="col-md-12 mtop5">
                  <div class="progress no-margin progress-bar-mini">
                     <div class="progress-bar light-green-bg no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $tasks_not_completed_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $tasks_not_completed_progress; ?>">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php if($project->deadline){ ?>
         <div class="col-md-6 project-progress-bars">
            <div class="row">
               <div class="col-md-9">
                  <p class="text-uppercase bold text-dark font-medium">
                     <?php echo $project_days_left; ?> / <?php echo $project_total_days; ?> <?php echo _l('project_days_left'); ?>
                  </p>
                  <p class="text-muted bold"><?php echo $project_time_left_percent; ?>%</p>
               </div>
               <div class="col-md-3 text-right">
                  <i class="fa fa-calendar-check-o<?php if($project_time_left_percent >= 100){echo ' text-success';} ?>" aria-hidden="true"></i>
               </div>
               <div class="col-md-12 mtop5">
                  <div class="progress no-margin progress-bar-mini">
                     <div class="progress-bar<?php if($project_time_left_percent == 0){echo ' progress-bar-warning ';} else { echo ' progress-bar-success ';} ?>no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $project_time_left_percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $project_time_left_percent; ?>">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
      </div>
      <hr />
      <div class="row">
      <div class="col-md-7">
            <table class="table no-margin project-overview-table">
               <tbody>
                  <?php if(has_permission('customers','','view') || is_customer_admin($project->clientid)){ ?>
                  <tr>
                     <td class="bold"><?php echo _l('project_customer'); ?></td>
                     <td><a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>"><?php echo $project->client_data->company; ?></a>
                     </td>
                  </tr>
                  <?php } ?>
                  <?php if(has_permission('projects','','create') || has_permission('projects','','edit')){ ?>
                  <tr>
                     <td class="bold"><?php echo _l('project_billing_type'); ?></td>
                     <td>
                        <?php
                        if($project->billing_type == 1){
                          $type_name = 'project_billing_type_fixed_cost';
                       } else if($project->billing_type == 2){
                          $type_name = 'project_billing_type_project_hours';
                       } else {
                          $type_name = 'project_billing_type_project_task_hours';
                       }
                       echo _l($type_name);
                       ?>
                    </td>
                    <?php if($project->billing_type == 1 || $project->billing_type == 2){
                     echo '<tr>';
                     if($project->billing_type == 1){
                       echo '<td class="bold">'._l('project_total_cost').'</td>';
                       echo '<td>'.format_money($project->project_cost,$currency->symbol).'</td>';
                    } else {
                       echo '<td class="bold">'._l('project_rate_per_hour').'</td>';
                       echo '<td>'.format_money($project->project_rate_per_hour,$currency->symbol).'</td>';
                    }
                    echo '<tr>';
                 }
              }
              ?>
              <tr>
               <td class="bold"><?php echo _l('project_status'); ?></td>
               <td><?php echo $project_status['name']; ?></td>
            </tr>
            <tr>
               <td class="bold"><?php echo _l('project_datecreated'); ?></td>
               <td><?php echo _d($project->project_created); ?></td>
            </tr>
            <tr>
               <td class="bold"><?php echo _l('project_start_date'); ?></td>
               <td><?php echo _d($project->start_date); ?></td>
            </tr>
            <?php if($project->deadline){ ?>
            <tr>
               <td class="bold"><?php echo _l('project_deadline'); ?></td>
               <?php $value = (isset($project) ? _dt($project->deadline) : ''); if(strlen($value) > 16){ $value = substr($value, 0, -3);} ?>
			   <td><?php echo $value; ?></td>
            </tr>
            <?php } ?>
            <?php if($project->date_finished){ ?>
            <tr class="text-success">
               <td class="bold"><?php echo _l('project_completed_date'); ?></td>
               <td><?php echo _d($project->date_finished); ?></td>
            </tr>
            <?php } ?>
            <?php if($project->billing_type == 1 && (has_permission('projects','','create'))){ ?>
            <tr>
               <td class="bold"><?php echo _l('project_overview_total_logged_hours'); ?></td>
               <td><?php echo seconds_to_time_format($this->projects_model->total_logged_time($project->id)); ?></td>
            </tr>
            <?php } ?>
            <?php $custom_fields = get_custom_fields('projects');
            if(count($custom_fields) > 0){ ?>
            <?php foreach($custom_fields as $field){ ?>
            <?php $value = get_custom_field_value($project->id,$field['id'],'projects');
            if($value == ''){continue;} ?>
            <tr>
               <td class="bold"><?php echo ucfirst($field['name']); ?></td>
               <td><?php echo $value; ?></td>
            </tr>
            <?php } ?>
            <?php } ?>
         </tbody>
      </table>
   </div>
   <div class="col-md-5 text-center project-percent-col mtop20">
      <p class="bold"><?php echo _l('project'). ' ' . _l('project_progress'); ?></p>
      <div class="project-progress relative mtop15" data-value="<?php echo $percent_circle; ?>" data-size="150" data-thickness="22" data-reverse="true">
         <strong class="project-percent"></strong>
      </div>
   </div>
</div>
<?php $tags = get_tags_in($project->id,'project'); ?>
<?php if(count($tags) > 0){ ?>
<div class="clearfix"></div>
<div class="tags-read-only-custom">
   <hr />
   <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
   <input type="text" class="tagsinput read-only" id="tags" name="tags" value="<?php echo prep_tags_input($tags); ?>" data-role="tagsinput">
</div>
<?php } ?>
<div class="tc-content">
   <hr />
   <p class="bold"><?php echo _l('project_description'); ?></p>
   <?php if(empty($project->description)){
      echo '<p class="text-muted no-mbot">' . _l('no_description_project') . '</p>';
   }
   echo check_for_links($project->description); ?>
</div>
<div class="tc-content">
	<hr />
	<p class="bold"><?php echo _l('task_checklist_items'); ?></p>
	<table class="table table-borded no-margin">
		<tbody>
		<?php $tasks = $this->projects_model->get_tasks($project->id);
		$found = false;
		foreach($tasks as $task) {
			$this->db->where('taskid', $task['id']);
			$this->db->order_by('list_order', 'asc');
			$task_items = $this->db->get('tbltaskchecklists')->result_array();
			foreach($task_items as $task_item) {
				if(isset($task_item['show_on_project']) && $task_item['show_on_project'] == 1){
				$item_value = $task_item['value'];
				if(!empty($task_item['type']) && $task_item['type'] == 3) {
					if(!empty($task_item['value'])) {
						$this->db->where('id', intval($task_item['value']));
						$attachment = $this->db->get('tblfiles')->row();
						if($attachment){
							$href_url = site_url('download/file/taskattachment/'. $attachment->id);
							$mime_class = get_mime_class($attachment->filetype);
							$file_name = $attachment->file_name;
							$item_value = '<a href="'.$href_url.'" class="pull-left select-file-item" target="_blank"><i class="'.$mime_class.'"></i>'.$file_name.'</a>';
						} 
					}
				} else if(!empty($task_item['type']) && $task_item['type'] == 4){
					$item_value = '';
					if(isset($task_item['value']) && !empty($task_item['value'])) {
						$options = @json_decode($task_item['value']);
						if(!is_null($options)){
							foreach($options as $option){
								if($option->s == 1){
									$item_value = $option->n;
								}
							}
						}
					}
				}
				$found = true; ?>
				<tr>
					<td class="bold"><?php echo $task_item['description']; ?></td>
					<td><?php echo $item_value; ?></td>
				</tr>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<?php if(!$found) { ?>
			<p class="text-muted no-margin"><?php echo _l('project_no_checklist_items_found'); ?></p>
		<?php } ?>
		</tbody>
	</table>
</div>
<div class="team-members">
   <hr />
   <?php if(has_permission('projects','','edit') || has_permission('projects','','create')){ ?>
   <div class="inline-block pull-left mright5" data-toggle="tooltip" data-title="<?php echo _l('add_edit_members'); ?>">
      <a href="#" data-toggle="modal" class="pull-right" data-target="#add-edit-members"><i class="fa fa-cog"></i></a>
   </div>
   <?php } ?>
   <p class="pull-left bold">
      <?php echo _l('project_members'); ?>
   </p>
   <div class="clearfix"></div>
   <?php
   if(count($members) == 0){
      echo '<p class="text-muted">'._l('no_project_members').'</p>';
   }
   foreach($members as $member){ ?>
   <div class="media">
      <div class="media-left">
         <a href="<?php echo admin_url('profile/'.$member["staff_id"]); ?>">
            <?php echo staff_profile_image($member['staff_id'],array('staff-profile-image-small','media-object')); ?>
         </a>
      </div>
      <div class="media-body">
         <?php if(has_permission('projects','','edit') || has_permission('projects','','create')){ ?>
         <a href="<?php echo admin_url('projects/remove_team_member/'.$project->id.'/'.$member['staff_id']); ?>" class="pull-right text-danger _delete"><i class="fa fa fa-times"></i></a>
         <?php } ?>
         <h5 class="media-heading mtop5"><a href="<?php echo admin_url('profile/'.$member["staff_id"]); ?>"><?php echo get_staff_full_name($member['staff_id']); ?></a>
            <?php if(has_permission('projects','','create') || $member['staff_id'] == get_staff_user_id()){ ?>
            <br /><small class="text-muted"><?php echo _l('total_logged_hours_by_staff') .': '.seconds_to_time_format($member['total_logged_time']); ?></small>
            <?php } ?>
         </h5>
      </div>
   </div>
   <?php } ?>
</div>
</div>
<div class="col-md-6">
	<div class="row milestone-progress-bar">
		<?php
			$milestones = $this->projects_model->get_milestones($project->id);
		?>
		<?php
		$m = 1;
		foreach($milestones as $milestone){
			$total_project_tasks  = total_rows('tblstafftasks', array(
			 'rel_type' => 'project',
			 'rel_id' => $project->id,
			 'milestone'=>$milestone['id'],
			 ));
			$total_finished_tasks = total_rows('tblstafftasks', array(
			 'rel_type' => 'project',
			 'rel_id' => $project->id,
			 'status' => 5,
			 'milestone'=>$milestone['id'],
			 ));
			$total_not_started_tasks = total_rows('tblstafftasks', array(
			 'rel_type' => 'project',
			 'rel_id' => $project->id,
			 'status' => 1,
			 'milestone'=>$milestone['id'],
			 ));
			$m_percent              = 0;
			$milestone_status = 'in_progress';
			if ($total_finished_tasks >= floatval($total_project_tasks)) {
			 $m_percent = 100;
			 $milestone_status = 'completed';
			} else {
			 if ($total_project_tasks !== 0) {
			   $m_percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
			   if ($total_not_started_tasks >= floatval($total_project_tasks)) {
				   $milestone_status = 'not_started';
			   }
			 } else {
				$milestone_status = 'not_started';
			 }
			}
			$milestone_color = '';
			$milestone_color_cp = '';
			$milestone_color_ns = '';
			$milestone_color_ip = '';
			$m_color = '';
			if(!empty($milestone["color"]) && !is_null($milestone['color']) && has_permission('projects','','create')){
				
				$colors = @json_decode($milestone["color"]);
				if(!is_null($colors)){
					if(isset($colors->cp)) $milestone_color_cp = $colors->cp;
					if(isset($colors->ns)) $milestone_color_ns = $colors->ns;
					if(isset($colors->ip)) $milestone_color_ip = $colors->ip;
					if($milestone_status == 'completed'){						
						$m_color = $milestone_color_cp;
					} else if($milestone_status == 'in_progress'){
						$m_color = $milestone_color_ip;
					} else {
						$m_color = $milestone_color_ns;
					}
				} else {
					$m_color = $milestone["color"];
				}
				if($m_color != ''){
					$milestone_color = ' style="background:'.$m_color.';border:1px solid '.$m_color.'"';
				}
			}
			?>
		<div class="col-md-4 mtop25 milestone-column" data-milestone-id="<?php echo $milestone['id']; ?>">
			<div class="panel-heading panel-heading-bg <?php if($milestone_color != ''){echo 'color-not-auto-adjusted color-white ';} ?><?php if($milestone['id'] != 0){echo 'task-phase';}else{echo 'info-bg';} ?>"<?php echo $milestone_color; ?>>
			 <?php if($milestone['id'] != 0){ ?>
			<i class="fa fa-file-text pointer" aria-hidden="true" data-toggle="popover" data-title="<?php echo _l('milestone_description'); ?>" data-html="true" data-content="<?php echo preg_replace('/"/','\'',$milestone['description']); ?>"></i>&nbsp;
			<?php } ?>
				<a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_milestones'); ?>" class="edit-milestone-phase <?php if($m_color != ''){echo 'color-white';} ?>">
				<span class="bold"><?php echo $milestone['name']; ?></span>
				</a>
				<?php echo '<br /><small>' . _l('milestone_'.$milestone_status). '</small>'; ?>
			</div>
			<div class="panel-footer">
				<div class="progress no-margin progress-bar-mini">
					<div class="progress-bar not-dynamic progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $m_percent; ?>">
					</div>
				</div>
			</div>
		</div>
		<?php if($m %= 3 == 0){echo '<div class="clearfix"></div>';} ?>
		<?php $m++;} ?>
	</div>
	<hr />
  <div class="col-md-12">
   <div class="pull-left">
      <?php if(has_permission('projects','','create')){ ?>
      <p class="pull-left no-mbot"><a href="<?php echo admin_url('projects/export_project_data/'.$project->id); ?>" target="_blank"><i class="fa fa-file-pdf-o"></i> <?php echo _l('export_project_data'); ?></a>&nbsp;<span class="text-muted">|</span>&nbsp;</p>
      <?php } ?>
      <?php if(is_admin()){ ?>
      <a href="<?php echo admin_url('projects/view_project_as_client/'.$project->id .'/'.$project->clientid); ?>" target="_blank"><?php echo _l('project_view_as_client'); ?></a>
      <?php } ?>
   </div>
   <div class="dropdown pull-right">
      <a href="#" class="dropdown-toggle" type="button" id="dropdownMenuProjectLoggedTime" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
         <?php if(!$this->input->get('overview_chart')){
            echo _l('this_week');
         } else {
            echo _l($this->input->get('overview_chart'));
         }
         ?>
         <span class="caret"></span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="dropdownMenuProjectLoggedTime">
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=this_week'); ?>"><?php echo _l('this_week'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=last_week'); ?>"><?php echo _l('last_week'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=this_month'); ?>"><?php echo _l('this_month'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=last_month'); ?>"><?php echo _l('last_month'); ?></a></li>
      </ul>
   </div>
   <div class="clearfix"></div>
   <hr class="hr-10" />
</div>
<canvas id="timesheetsChart" style="max-height:300px;" width="300" height="300"></canvas>
<?php if(has_permission('projects','','create')) { ?>

<div class="row mtop40">
   <?php if($project->billing_type == 3 || $project->billing_type == 2){ ?>
   <div class="col-md-12">
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->total_logged_time_by_billing_type($project->id);
         ?>
         <p class="text-uppercase text-muted"><?php echo _l('project_overview_logged_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo format_money($data['total_money'],$currency->symbol); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_billable_time($project->id);
         ?>
         <p class="text-uppercase text-info"><?php echo _l('project_overview_billable_hours'); ?> <span class="bold"><?php echo $data['logged_time'] ?></span></p>
         <p class="bold font-medium"><?php echo format_money($data['total_money'],$currency->symbol); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_billed_time($project->id);
         ?>
         <p class="text-uppercase text-success"><?php echo _l('project_overview_billed_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo format_money($data['total_money'],$currency->symbol); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_unbilled_time($project->id);
         ?>
         <p class="text-uppercase text-danger"><?php echo _l('project_overview_unbilled_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo format_money($data['total_money'],$currency->symbol); ?></p>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12">
        <hr />
     </div>
  </div>
  <?php } ?>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="col-md-3">
         <p class="text-uppercase text-muted"><?php echo _l('project_overview_expenses'); ?></p>
         <p class="bold font-medium"><?php echo format_money(sum_from_table('tblexpenses',array('where'=>array('project_id'=>$project->id),'field'=>'amount')),$currency->symbol); ?></p>
      </div>
      <div class="col-md-3">
         <p class="text-uppercase text-info"><?php echo _l('project_overview_expenses_billable'); ?></p>
         <p class="bold font-medium"><?php echo format_money(sum_from_table('tblexpenses',array('where'=>array('project_id'=>$project->id,'billable'=>1),'field'=>'amount')),$currency->symbol); ?></p>
      </div>
      <div class="col-md-3">
         <p class="text-uppercase text-success"><?php echo _l('project_overview_expenses_billed'); ?></p>
         <p class="bold font-medium"><?php echo format_money(sum_from_table('tblexpenses',array('where'=>array('project_id'=>$project->id,'invoiceid !='=>'NULL','billable'=>1),'field'=>'amount')),$currency->symbol); ?></p>
      </div>
      <div class="col-md-3">
         <p class="text-uppercase text-danger"><?php echo _l('project_overview_expenses_unbilled'); ?></p>
         <p class="bold font-medium"><?php echo format_money(sum_from_table('tblexpenses',array('where'=>array('project_id'=>$project->id,'invoiceid IS NULL','billable'=>1),'field'=>'amount')),$currency->symbol); ?></p>
      </div>
   </div>
</div>
<?php } ?>
</div>
</div>
<div class="modal fade" id="add-edit-members" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('projects/add_edit_members/'.$project->id)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('project_members'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
            $selected = array();
            foreach($members as $member){
              array_push($selected,$member['staff_id']);
           }
           echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true));
           ?>
        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <button type="submit" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
      </div>
   </div>
   <!-- /.modal-content -->
   <?php echo form_close(); ?>
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php if(isset($project_overview_chart)){ ?>
<script>
   var project_overview_chart = <?php echo json_encode($project_overview_chart); ?>;
</script>
<?php } ?>