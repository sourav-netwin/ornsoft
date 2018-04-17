<style>
.checklist .input-group {
	width: 100%;
    margin-top: -2px;
    padding-left: 25px;
    padding-right: 44px;
}
.input-group.date input[type="text"], .input-group.colpicker input[type="text"]{
	width: 100%;
    padding: 0 0 0 5px !important;
    height: 22px !important;
    margin-top: 6px;
    border: 1px solid #ccc;
    font-size: 13px;
}
.input-group.date .input-group-addon, .input-group.colpicker .input-group-addon{
	height: 22px !important;
    padding: 5px;
    display: inline-block;
    width: 22px;
    position: absolute;
    top: 6px;
}
.input-group.date .input-group-addon{
    padding: 2px 0 0 !important;
}
.input-group.colpicker i{
	width:10px !important;
	height:10px !important;
}
.checklist textarea[name="checklist-description"]{
	height: 22px !important;
	padding-top: 2px;
}
.input-group .dropzone .dz-message{
	margin: 0 auto;
}
.input-group .select-file-item{
	padding-left: 5px;
    margin-top: 2px;
}
.input-group .remove-select-file{
	margin-left: 8px;
}
.input-group .remove-select-file i{
	margin-top: 5px;
}
.input-group textarea[name="checklist-value"]{
	position: absolute;
    resize: none;
    overflow: hidden;
    left: 25px;
    top: 0px;
    width: 91%;
    border-radius: 3px;
    outline: 0px;
    padding-left: 5px;
	border: 1px solid #d7d4d4;
    height: 23px;
}
.input-group.select-box{
	padding-right:22px;
}
.input-group.select-box select[name="checklist-select-box"] {
	height: 22px;
    font-size: 13px;
    padding: 0;
    margin-top: 6px;
    border-radius: 3px;
	box-shadow: none;
}
.checklist-checkbox > label {
	padding-left: 10px;
}
.height56{
	height:56px !important;
}
.height76{
	height:76px !important;
}
</style>

<?php
$total_checklist_items = total_rows('tbltaskchecklists',array('taskid'=>$task_id));
?>
<div class="clearfix"></div>
<?php if(count($checklists) > 0){ ?>
    <h4 class="bold chk-heading th font-medium"><?php echo _l('task_checklist_items'); ?></h4>
    <?php } ?>
    <div class="progress mtop15 hide">
        <div class="progress-bar not-dynamic progress-bar-default task-progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
        </div>
    </div>
    <?php foreach($checklists as $list){
		$item_row_height = 'height56';
		if($list['finished'] == 1 && $list['finished_from'] != get_staff_user_id()) $item_row_height = 'height76';; ?>
		<div class="checklist<?php echo ' '.$item_row_height; ?>" data-checklist-id="<?php echo $list['id']; ?>">
            <?php 
			$href_url = '';
			$mime_class = '';
			$file_name = '';
			if(!empty($list['type']) && $list['type'] == 3) {
				if(!empty($list['value'])) {
					$this->db->where('id', intval($list['value']));
					$attachment = $this->db->get('tblfiles')->row();
					if($attachment){
						$href_url = site_url('download/file/taskattachment/'. $attachment->id);
						$mime_class = get_mime_class($attachment->filetype);
						$file_name = $attachment->file_name;
						$list['finished'] = 1;
					} else {
						$this->db->where('id', $list['id']);
						$this->db->update('tbltaskchecklists', array(
							'value' => '',
							'finished' => 0
						));
						$list['finished'] = 0;
					}
				} else {
					$list['finished'] = 0;
				}
			}
			?>
			<div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip">
				<input type="checkbox" <?php if(($list['finished'] == 1 && $list['finished_from'] != get_staff_user_id() && !is_admin()) || (!empty($list['type']) && $list['type'] > 0)){echo 'disabled';} ?> name="checklist-box" <?php if($list['finished'] == 1){echo 'checked';}; ?>>
                <label for=""><span class="chl-desc"><?php echo $list['description']; ?></span></label>
                <textarea name="checklist-description" class="hide" rows="1"><?php echo clear_textarea_breaks($list['description']); ?></textarea>
				<?php if(has_permission('tasks','','delete') || $list['addedfrom'] == get_staff_user_id()){ ?>
					<?php if(!empty($list['type']) && $list['type'] == 3) { ?>
					<a href="#" class="pull-right text-muted remove-checklist hide" onclick="delete_select_file_item(<?php echo $list['id']; ?>,this); return false;"><i class="fa fa-remove"></i></a>
					<?php } else { ?>
                    <a href="#" class="pull-right text-muted remove-checklist hide" onclick="delete_checklist_item(<?php echo $list['id']; ?>,this); return false;"><i class="fa fa-remove"></i></a>
					<?php } ?>
				<?php } ?>
			</div>
			
			<?php if(isset($list['type']) && $list['type'] == 0) { ?>
			<div class="input-group" style="margin-top:4px;height: 23px;">
				<textarea data-taskid="<?php echo $task_id; ?>" name="checklist-value" rows="1"><?php echo clear_textarea_breaks($list['value']); ?></textarea>
			</div>
			<?php } else if(!empty($list['type']) && $list['type'] == 1) { ?>
			<div class="input-group date">
				<input desc="<?php echo $list['value']; ?>" name="checklist-description" type="text" class="form-control datepicker" value="<?php echo $list['value']; ?>" >
				<div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i></div>
			</div>
			<?php } else if(!empty($list['type']) && $list['type'] == 2) { ?>
			<div class="input-group colpicker colorpicker-component colorpicker-element">
				<input desc="<?php echo $list['value']; ?>" name="checklist-description" type="text" class="form-control" value="<?php echo $list['value']; ?>">
				<span class="input-group-addon"><i></i></span>
			</div>
			<?php } else if(!empty($list['type']) && $list['type'] == 4) { ?>
			<div class="input-group select-box">
				<select name="checklist-select-box" class="form-control">
					<option value=""></option>
					<?php if(isset($list['value']) && !empty($list['value'])) {
						$options = json_decode($list['value']);
						foreach($options as $option){ ?>
						<option value="<?php echo $option->n;?>" <?php if($option->s == 1){echo ' selected';} ?> class="db"><?php echo $option->n;?></option>
					<?php } } ?>
				</select>
			</div>
			<?php } else if(!empty($list['type']) && $list['type'] == 3) { ?>
			<div class="input-group" style="margin-top: 3px; padding-right: 22px;">
				<?php if($list['finished'] == 1) { ?> 
					<a href="<?php echo $href_url; ?>" class="pull-left select-file-item" target="_blank">
						<i class="<?php echo $mime_class; ?>"></i> <?php echo $file_name; ?>
					</a>
					<a href="#" class="text-danger remove-select-file" onclick="delete_select_file(<?php echo $list['id']; ?>,this); return false;"><i class="fa fa-remove"></i></a>
				<?php } else {
					echo form_open_multipart('admin/taskitems/upload_file',
						array('id'=>'item-attachment'.$list['id'],'class'=>'dropzone','style'=>'padding: 0;min-height: 21px;height: 24px;border-color: #ccc;')); ?>
					<?php echo form_close(); ?>
					<script>
					if (typeof(itemAttachmentDropzone<?php echo $list['id']; ?>) != 'undefined') {
					   itemAttachmentDropzone<?php echo $list['id']; ?>.destroy();
					}
					itemAttachmentDropzone<?php echo $list['id']; ?> = new Dropzone("#item-attachment<?php echo $list['id']; ?>", {
					   autoProcessQueue: true,
					   createImageThumbnails: false,
					   dictDefaultMessage: drop_files_here_to_upload,
					   dictFallbackMessage: browser_not_support_drag_and_drop,
					   addRemoveLinks: false,
					   dictMaxFilesExceeded: you_can_not_upload_any_more_files,
					   previewTemplate: '<div style="display:none"></div>',
					   maxFiles: 1,
					   acceptedFiles: allowed_files,
					   error: function(file, response) {
						   alert_float('danger', response);
					   },
					   sending: function(file, xhr, formData) {
						   formData.append("taskid", <?php echo $task_id; ?>);
						   formData.append("itemid", <?php echo $list['id']; ?>);
					   },
					   success: function(files, response) {
						   if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
							   init_task_modal(<?php echo $task_id; ?>);
							   init_tasks_checklist_items(true);
						   }
					   }
					});
					</script>
				<?php } ?>
			</div>
			<?php } ?>
				
			<?php if($list['finished'] == 1 && $list['finished_from'] != get_staff_user_id()){ ?>
				<p class="small mtop5"><?php echo _l('task_checklist_item_completed_by',get_staff_full_name($list['finished_from'])); ?></p>
			<?php } ?>
            </div>
            <?php } ?>
            <script>
                $("#checklist-items").sortable({
                    helper: 'clone',
                    items: 'div.checklist',
                    update: function(event, ui) {
                        update_checklist_order();
                    }
                });
				
				init_datepicker();
				$('.colorpicker-element').colorpicker();
				
				var locked = $('#lock-checkitems').hasClass('btn-default');
				if(locked){
					$("#checklist-items").sortable('disable');					
				} else {
					$('.checklist-checkbox').each(function(){
						$(this).find('span.chl-desc').addClass('hide');
						$(this).find('textarea[name="checklist-description"]').removeClass('hide');
						$(this).find('a.remove-checklist').removeClass('hide');
					});
				}
				
				function add_task_datepicker_item(task_id) {
					$.post(admin_url + 'taskitems/add_extra_checklist_item', {
						taskid: <?php echo $task_id; ?>,
						type: 1,
						value: ''
					}).done(function() {
						init_tasks_checklist_items(true, task_id);
					});
				}
				function add_task_colorpicker_item(task_id) {
					$.post(admin_url + 'taskitems/add_extra_checklist_item', {
						taskid: <?php echo $task_id; ?>,
						type: 2,
						value: ''
					}).done(function() {
						init_tasks_checklist_items(true, task_id);
					});
				}
				function add_task_select_file_item(task_id) {
					$.post(admin_url + 'taskitems/add_extra_checklist_item', {
						taskid: <?php echo $task_id; ?>,
						type: 3,
						value: ''
					}).done(function() {
						init_tasks_checklist_items(true, task_id);
					});
				}				
				function add_task_select_box_item(options) {
					$.post(admin_url + 'taskitems/add_extra_checklist_item', {
						taskid: <?php echo $task_id; ?>,
						type: 4,
						value: JSON.stringify(options)
					}).done(function() {
						init_tasks_checklist_items(true, <?php echo $task_id; ?>);
					});
				}
				function delete_select_file_item(id, field) {
					var r = confirm('<?php echo _l('confirm_file_item_delete_prompt'); ?>');
					if (r == false) {
						return false;
					} else {
						$.post(admin_url + 'taskitems/delete_select_file_item', {
							listid: id,
							keep: 0
						}).done(function() {
							init_task_modal(<?php echo $task_id; ?>);
							init_tasks_checklist_items(true);
						});
					}
				}
				function delete_select_file(id, field) {
					var r = confirm('<?php echo _l('confirm_file_item_delete_prompt'); ?>');
					if (r == false) {
						return false;
					} else {
						$.post(admin_url + 'taskitems/delete_select_file', {
							listid: id,
							keep: 0
						}).done(function() {
							init_task_modal(<?php echo $task_id; ?>);
							init_tasks_checklist_items(true);
						});
					}
				}
				
				if(window.checkDateColValuesTimeOut){
					clearTimeout(checkDateColValuesTimeOut);
				}
				function checkDateColValues() {
					$('input[name="checklist-description"]').each(function() {
						var description = $(this).val().trim();
						if(description != $(this).attr('desc')) {
							$(this).attr('desc', description);
							var finished = 0;
							if(description.length > 0){
								finished = 1;
							}
							var parent = $(this).parents('.checklist');
							var listid = parent.data('checklist-id');
							$.post(admin_url + 'taskitems/update_checklist_item', {
								value: description,
								listid: listid
							});
							var chb = parent.find('input[name="checklist-box"]');
							if(finished == 1)
								chb.attr('checked', 'checked');
							else chb.removeAttr('checked');
							chb.trigger('change');
						}
					});
					setTimeout(checkDateColValues, 500);
				}
				window.checkDateColValuesTimeOut = setTimeout(checkDateColValues, 500);
				$('body').on('blur', 'textarea[name="checklist-value"]', function() {
					var description = $(this).val();
					var listid = $(this).parents('.checklist').data('checklist-id');
					$.post(admin_url + 'taskitems/update_checklist_item', {
						value: description,
						listid: listid
					});
				});
				
				$('#attachments .task-attachment-user a.pull-right').click(function(){
					init_tasks_checklist_items(true);
				});
				
				$('.checklist a.select-file-item').each(function(){
					var f_href = $(this).attr('href');
					var att_item = $('li.task-attachment a[href="'+f_href+'"]');
					if(att_item){
						var li_parent = att_item.parents('li.task-attachment');
						var x_a = li_parent.find('.task-attachment-user a.pull-right');
						if(x_a){
							x_a.removeAttr('onclick');
							x_a.html('<?php echo _l('linked_item'); ?>');
						}
					}
				});
				$('select[name="checklist-select-box"]').change(function(){
					var parent = $(this).parents('.checklist');
					var listid = parent.data('checklist-id');
					var selOption = $(this).val();
					var options = [];
					$(this).find('option.db').each(function(){
						var sel = 0;
						if($(this).val() == selOption){
							sel = 1;
						}
						options.push({n:$(this).val(), s:sel});
					});
					$.post(admin_url + 'taskitems/update_checklist_item', {
						value: JSON.stringify(options),
						listid: listid
					});
					var chb = parent.find('input[name="checklist-box"]');
					if(selOption.trim().length > 0)
						chb.attr('checked', 'checked');
					else chb.removeAttr('checked');
					chb.trigger('change');
				});
            </script>
