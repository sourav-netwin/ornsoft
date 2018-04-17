<?php
$is_admin = is_admin();
$i = 0;
foreach ($statuses as $status) {
	$search = $this->input->get('search');
	$page = 1; 
	$sort = array();
	$limit                         = get_option('leads_kanban_limit');
	$defaut_leads_kanban_sort      = get_option('defaut_leads_kanban_sort');
	$defaut_leads_kanban_sort_type = get_option('defaut_leads_kanban_sort_type');

	$this->db->select('tblleads.name as lead_name,tblleadssources.name as source_name,tblleads.id as id,assigned,email,phonenumber,company,dateadded,status,lastcontact');
	$this->db->from('tblleads');
	$this->db->join('tblleadssources', 'tblleadssources.id=tblleads.source', 'left');
	$this->db->where('status', $status['id']);
	if (!$this->is_admin) {
		$this->db->where('(' . get_staff_user_id() . ' IN (SELECT staff_id FROM tblleadmembers WHERE lead_id=tblleads.id) OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
	}
	if ($search != '') {
		$this->db->where('(tblleads.name LIKE "%' . $search . '%" OR tblleadssources.name LIKE "%' . $search . '%" OR email LIKE "%' . $search . '%" OR phonenumber LIKE "%' . $search . '%" OR company LIKE "%' . $search . '%")');
	}

	if (isset($sort['sort_by']) && $sort['sort_by'] && isset($sort['sort']) && $sort['sort']) {
		$this->db->order_by($sort['sort_by'], $sort['sort']);
	} else {
		$this->db->order_by($defaut_leads_kanban_sort, $defaut_leads_kanban_sort_type);
	}

	if ($page > 1) {
		$page--;
		$position = ($page * $limit);
		$this->db->limit($limit, $position);
	} else {
		$this->db->limit($limit);
	}
	$total_pages = $this->db->count_all_results();
	
  $total_pages = ceil($total_pages/get_option('leads_kanban_limit'));

  $settings = '';
  foreach(get_system_favourite_colors() as $color){
    $color_selected_class = 'cpicker-small';
    if($color == $status['color']){
      $color_selected_class = 'cpicker-big';
    }
    $settings .= "<div class='kanban-cpicker cpicker ".$color_selected_class."' data-color='".$color."' style='background:".$color.";border:1px solid ".$color."'></div>";
  }
  ?>
  <ul class="kan-ban-col" data-col-status-id="<?php echo $status['id']; ?>" data-total-pages="<?php echo $total_pages; ?>">
    <li class="kan-ban-col-wrapper">
      <div class="border-right panel_s">
        <?php
        $status_color = '';
        if(!empty($status["color"])){
          $status_color = 'style="background:'.$status["color"].';border:1px solid '.$status['color'].'"';
        }
        ?>
        <div class="panel-heading-bg primary-bg" <?php if($status['isdefault'] == 1){ ?>data-toggle="tooltip" data-title="<?php echo _l('leads_default_status') . ' - '. _l('client'); ?>"<?php } ?> <?php echo $status_color; ?> data-status-id="<?php echo $status['id']; ?>">
          <div class="kan-ban-step-indicator<?php if($status['isdefault'] == 1){ echo ' kan-ban-step-indicator-full'; } ?>"></div>
          <i class="fa fa-reorder pointer"></i>
          <span class="heading pointer" <?php if($is_admin){ ?> data-order="<?php echo $status['statusorder']; ?>" data-color="<?php echo $status['color']; ?>" data-name="<?php echo $status['name']; ?>" onclick="edit_status(this,<?php echo $status['id']; ?>); return false;" <?php } ?>><?php echo $status['name']; ?>
          </span>
          <a href="#" onclick="return false;" class="pull-right color-white kanban-color-picker kanban-stage-color-picker<?php if($status['isdefault'] == 1){ echo ' kanban-stage-color-picker-last'; } ?>" data-placement="bottom" data-toggle="popover" data-content="
            <div class='text-center'>
              <button type='button' return false;' class='btn btn-success btn-block mtop10 new-lead-from-status'>
                <?php echo _l('new_lead'); ?>
              </button>
            </div>
            <hr />
            <div class='kan-ban-settings cpicker-wrapper'>
              <?php echo $settings; ?>
            </div>" data-html="true" data-trigger="focus">
            <i class="fa fa-angle-down"></i>
          </a>
        </div>
        <div class="kan-ban-content-wrapper">
          <div class="kan-ban-content">
            <ul class="status leads-status sortable" data-lead-status-id="<?php echo $status['id']; ?>">
              <?php
				$search = $this->input->get('search');
				$page = 1; 
				$sort = array('sort_by'=>$this->input->get('sort_by'),'sort'=>$this->input->get('sort'));
				$limit                         = get_option('leads_kanban_limit');
				$defaut_leads_kanban_sort      = get_option('defaut_leads_kanban_sort');
				$defaut_leads_kanban_sort_type = get_option('defaut_leads_kanban_sort_type');

				$this->db->select('tblleads.name as lead_name,tblleadssources.name as source_name,tblleads.id as id,assigned,email,phonenumber,company,dateadded,status,lastcontact');
				$this->db->from('tblleads');
				$this->db->join('tblleadssources', 'tblleadssources.id=tblleads.source', 'left');
				$this->db->where('status', $status['id']);
				if (!$this->is_admin) {
					$this->db->where('(' . get_staff_user_id() . ' IN (SELECT staff_id FROM tblleadmembers WHERE lead_id=tblleads.id) OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
				}
				if ($search != '') {
					$this->db->where('(tblleads.name LIKE "%' . $search . '%" OR tblleadssources.name LIKE "%' . $search . '%" OR email LIKE "%' . $search . '%" OR phonenumber LIKE "%' . $search . '%" OR company LIKE "%' . $search . '%")');
				}

				if (isset($sort['sort_by']) && $sort['sort_by'] && isset($sort['sort']) && $sort['sort']) {
					$this->db->order_by($sort['sort_by'], $sort['sort']);
				} else {
					$this->db->order_by($defaut_leads_kanban_sort, $defaut_leads_kanban_sort_type);
				}

				if ($page > 1) {
					$page--;
					$position = ($page * $limit);
					$this->db->limit($limit, $position);
				} else {
					$this->db->limit($limit);
				}
              $leads = $this->db->get()->result_array();
              $total_leads = count($leads);
              foreach ($leads as $lead) {
                $this->load->view('admin/leads/_kan_ban_card',array('lead'=>$lead,'status'=>$status));
              } ?>
              <?php if($total_leads > 0 ){ ?>
              <li class="text-center not-sortable kanban-load-more" data-load-status="<?php echo $status['id']; ?>">
              <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1){echo ' disabled';} ?>" data-page="1" onclick="kanban_load_more(<?php echo $status['id']; ?>,this,'leads/leads_kanban_load_more',290,360); return false;"; autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('load_more'); ?></a>
             </li>
             <?php } ?>
             <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_leads > 0){echo ' hide';} ?>">
              <h4 class="text-muted">
                <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                <?php echo _l('no_leads_found'); ?></h4>
              </li>
            </ul>
          </div>
        </div>
      </li>
    </ul>
    <?php $i++; } ?>
