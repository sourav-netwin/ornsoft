<style>
.cpicker-title{
	color:#111;
	text-align:left;
	display:block;
}
.cpicker-top{
	margin-bottom:8px;
}
.kan-ban-settings{
	padding-top:10px;
	padding-bottom:5px;
}
.my-cpicker {
    float: left;
    margin-right: 15px;
    border-radius: 1px;
    cursor: pointer;
}
</style>
<?php if(has_permission('projects','','create')){ ?>
<a href="#" class="btn btn-info" onclick="new_milestone();return false;"><?php echo _l('new_milestone'); ?></a>
<?php } ?>
<?php if(has_permission('tasks','','create')){ ?>
<a href="#" class="btn btn-info new-task-phase" onclick="new_task_from_relation('.table-rel-tasks','project',<?php echo $project->id; ?>); return false;"><?php echo _l('new_task'); ?></a>
<?php } ?>
<a href="#" class="btn btn-default" onclick="milestones_switch_view(); return false;"><i class="fa fa-th-list"></i></a>
<div class="tasks-phases">
    <?php
        $milestones = array();
        $milestones[] = array(
            'name'=>_l('milestones_uncategorized'),
            'id'=>0,
            'total_logged_time'=>$this->projects_model->calc_milestone_logged_time($project->id,0),
            'color'=>NULL,
            );
        $_milestones = $this->projects_model->get_milestones($project->id);
        foreach($_milestones as $m){
            $milestones[] = $m;
        }
    ?>
    <div class="row">
        <?php
        $m = 1;
        foreach($milestones as $milestone){

            $cpicker = '';
            if(has_permission('projects','','create') && $milestone['id'] != 0){
              foreach(get_system_favourite_colors() as $color){
                  $color_selected_class = 'cpicker-small';
                  $cpicker .= "<div class='kanban-cpicker my-cpicker ".$color_selected_class."' data-color='".$color."' style='background:".$color.";border:1px solid ".$color."'></div>";
              }
            }
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
            $percent              = 0;
			$milestone_status = 'in_progress';
            if ($total_finished_tasks >= floatval($total_project_tasks)) {
             $percent = 100;
			 $milestone_status = 'completed';
            } else {
             if ($total_project_tasks !== 0) {
               $percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
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
			$colors = '{"cp":"'.$milestone_color_cp.'","ip":"'.$milestone_color_ip.'","ns":"'.$milestone_color_ns.'"}';
            ?>
        <div data-colors='<?php echo $colors; ?>' class="col-md-3 mtop25 milestone-column <?php echo $milestone_status; ?>" data-milestone-id="<?php echo $milestone['id']; ?>">
            <div class="panel-heading panel-heading-bg <?php if($milestone_color != ''){echo 'color-not-auto-adjusted color-white ';} ?><?php if($milestone['id'] != 0){echo 'task-phase';}else{echo 'info-bg';} ?>"<?php echo $milestone_color; ?>>
             <?php if($milestone['id'] != 0){ ?>
            <i class="fa fa-file-text pointer" aria-hidden="true" data-toggle="popover" data-title="<?php echo _l('milestone_description'); ?>" data-html="true" data-content="<?php echo preg_replace('/"/','\'',$milestone['description']); ?>"></i>&nbsp;
            <?php } ?>
                <?php if($milestone['id'] != 0 && has_permission('projects','','edit')){ ?>
                  <a href="#" data-description-visible-to-customer="<?php echo $milestone['description_visible_to_customer']; ?>" data-description="<?php echo clear_textarea_breaks($milestone['description']); ?>" data-name="<?php echo $milestone['name']; ?>" data-due_date="<?php echo _d($milestone['due_date']); ?>" data-order="<?php echo $milestone['milestone_order']; ?>" onclick="edit_milestone(this,<?php echo $milestone['id']; ?>); return false;" class="edit-milestone-phase <?php if($m_color != ''){echo 'color-white';} ?>">
                <?php } ?>
                <span class="bold"><?php echo $milestone['name']; ?></span>
                 <?php if($milestone['id'] != 0 && has_permission('projects','','edit')){ ?>
                </a>
                <?php } ?>
                <?php if($milestone['id'] != 0 && (has_permission('tasks','','create') || has_permission('projects','','create'))){  ?>
                  <a href="#" onclick="return false;" class="pull-right text-dark" data-placement="bottom" data-toggle="popover" data-content="
                  <div class='text-center'><?php if(has_permission('tasks','','create')){ ?><button type='button' return false;' class='btn btn-success btn-block mtop10 new-task-to-milestone'>
                  <?php echo _l('new_task'); ?>
                  </button>
                  <?php } ?>
                  </div>
				  
				  
                  <?php if($cpicker != ''){echo '<hr class=\'cpicker-top\' />';}; ?>
				  <span class='cpicker-title'><?php echo _l('color_for_not_started'); ?></span>
                  <div data-status='not_started' class='kan-ban-settings cpicker-wrapper not_started'>
                  <?php echo $cpicker; ?>
                  </div>
                  <a href='#' class='reset_milestone_color_ns <?php if($milestone_color_ns == ''){echo 'hide';} ?>' data-color=''>
                  <?php echo _l('reset_to_default_color'); ?>
                  </a>				  
				  <?php if($cpicker != ''){echo '<hr class=\'cpicker-top\' />';}; ?>
				  <span class='cpicker-title'><?php echo _l('color_for_in_progress'); ?></span>
                  <div data-status='in_progress' class='kan-ban-settings cpicker-wrapper in_progress'>
                  <?php echo $cpicker; ?>
                  </div>
                  <a href='#' class='reset_milestone_color_ip <?php if($milestone_color_ip == ''){echo 'hide';} ?>' data-color=''>
                  <?php echo _l('reset_to_default_color'); ?>
                  </a>
				  <?php if($cpicker != ''){echo '<hr class=\'cpicker-top\' />';}; ?>
				  <span class='cpicker-title'><?php echo _l('color_for_completed'); ?></span>
                  <div data-status='completed' class='kan-ban-settings cpicker-wrapper completed'>
                  <?php echo $cpicker; ?>
                  </div>
                  <a href='#' class='reset_milestone_color_cp <?php if($milestone_color_cp == ''){echo 'hide';} ?>' data-color=''>
                  <?php echo _l('reset_to_default_color'); ?>
                  </a>
				  
				  " data-html="true" data-trigger="focus">
                  <i class="fa fa-angle-down"></i>
            </a>
            <?php } ?>
                <?php if(has_permission('tasks','','create')){ ?>
                <?php echo '<br /><small>' . _l('milestone_total_logged_time') . ': ' . seconds_to_time_format($milestone['total_logged_time']). ' - '. _l('milestone_'.$milestone_status). '</small>';
                   } ?>
            </div>
            <div class="panel-body">
                <?php
                    $tasks = $this->projects_model->get_tasks($project->id,array('milestone'=>$milestone['id']),true);

                echo '<ul class="milestone-tasks-wrapper ms-task">';
                echo '<li class="inline-block"></li>';
                foreach($tasks as $task){ ?>
                <li data-task-id="<?php echo $task['id']; ?>" class="task<?php if(has_permission('tasks','','create') || has_permission('tasks','','edit')){echo ' sortable';} ?><?php if($this->tasks_model->is_task_assignee(get_staff_user_id(),$task['id'])){echo ' current-user-task';} ?>">
                <div class="media">
                    <?php if($this->tasks_model->is_task_assignee(get_staff_user_id(),$task['id'])){ ?>
                    <div class="media-left">
                        <?php echo staff_profile_image(get_staff_user_id(),array('staff-profile-image-small pull-left'),'small',array('data-toggle'=>'tooltip','data-title'=>_l('project_task_assigned_to_user'))); ?>
                    </div>
                    <?php } ?>
                    <div class="media-body">
                        <a href="#" class="task_milestone pull-left mtop5<?php if($task['status'] == 5){echo ' text-muted line-throught';} ?>" onclick="init_task_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['name']; ?></a>
                        <?php if(has_permission('tasks','','create')){ ?>
                        <small><?php echo _l('task_total_logged_time'); ?>
                        <b>
                        <?php echo seconds_to_time_format($this->tasks_model->calc_task_total_time($task['id'])); ?>
                        </b>
                        <?php } ?>
                        </small>
                        <br />
                        <small><?php echo _l('task_status'); ?>: <?php echo format_task_status($task['status'],true); ?></small>
                    </div>
                </div>
                </li>
                <?php } ?>
                  </ul>
            </div>
            <div class="panel-footer">
                <div class="progress no-margin progress-bg-dark">
                    <div class="progress-bar not-dynamic progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent; ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php if($m == 4){echo '<div class="clearfix"></div>';} ?>
        <?php $m++;} ?>
    </div>
</div>
<div id="milestones-table" class="hide mtop25">
    <?php render_datatable(array(
        _l('milestone_name'),
        _l('milestone_due_date'),
        _l('options')
        ),'milestones'); ?>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
	$('body').on('click', '.my-cpicker', function() {
        var color = $(this).data('color');
        $(this).parents('.cpicker-wrapper').find('.cpicker-big').removeClass('cpicker-big').addClass('cpicker-small');
        var status = $(this).parents('.cpicker-wrapper').data('status');
        $(this).removeClass('cpicker-small', 'fast').addClass('cpicker-big', 'fast');
        if ($(this).hasClass('kanban-cpicker')) {
            $(this).parents('.'+status+' .panel-heading-bg').css('background', color);
            $(this).parents('.'+status+' .panel-heading-bg').css('border', '1px solid ' + color);
        } else if ($(this).hasClass('calendar-cpicker')) {
            $('body').find('._event input[name="color"]').val(color);
        }
    });
   $('body').on('click', '.milestone-column .not_started .my-cpicker,.milestone-column .reset_milestone_color_ns', function(e) {
		e.preventDefault();
		var color = $(this).data('color');
		var invoker = $(this);
		var milestone_id = invoker.parents('.milestone-column').data('milestone-id');
		var colors = invoker.parents('.milestone-column').data('colors');
		colors['ns'] = color;
		$.post(admin_url + 'projects/change_milestone_color', {
			color: JSON.stringify(colors),
			milestone_id: milestone_id
		}).done(function() {
		  // Reset color needs reload
			if (color == '') {
				window.location.reload();
			} else {
				invoker.parents('.milestone-column').find('.reset_milestone_color_ns').removeClass('hide');
				invoker.parents('.milestone-column').find('.panel-heading').addClass('color-white').removeClass('task-phase');
				invoker.parents('.milestone-column').find('.edit-milestone-phase').addClass('color-white');
			}
		})
	});
	$('body').on('click', '.milestone-column .in_progress .my-cpicker,.milestone-column .reset_milestone_color_ip', function(e) {
		e.preventDefault();
		var color = $(this).data('color');
		var invoker = $(this);
		var milestone_id = invoker.parents('.milestone-column').data('milestone-id');
		var colors = invoker.parents('.milestone-column').data('colors');
		colors['ip'] = color;
		$.post(admin_url + 'projects/change_milestone_color', {
			color: JSON.stringify(colors),
			milestone_id: milestone_id
		}).done(function() {
		  // Reset color needs reload
			if (color == '') {
				window.location.reload();
			} else {
				invoker.parents('.milestone-column').find('.reset_milestone_color_ip').removeClass('hide');
				invoker.parents('.milestone-column').find('.panel-heading').addClass('color-white').removeClass('task-phase');
				invoker.parents('.milestone-column').find('.edit-milestone-phase').addClass('color-white');
			}
		})
	});
	$('body').on('click', '.milestone-column .completed .my-cpicker,.milestone-column .reset_milestone_color_cp', function(e) {
		e.preventDefault();
		var color = $(this).data('color');
		var invoker = $(this);
		var milestone_id = invoker.parents('.milestone-column').data('milestone-id');
		var colors = invoker.parents('.milestone-column').data('colors');
		colors['cp'] = color;
		$.post(admin_url + 'projects/change_milestone_color', {
			color: JSON.stringify(colors),
			milestone_id: milestone_id
		}).done(function() {
		  // Reset color needs reload
			if (color == '') {
				window.location.reload();
			} else {
				invoker.parents('.milestone-column').find('.reset_milestone_color_cp').removeClass('hide');
				invoker.parents('.milestone-column').find('.panel-heading').addClass('color-white').removeClass('task-phase');
				invoker.parents('.milestone-column').find('.edit-milestone-phase').addClass('color-white');
			}
		})
	});
});
</script>
