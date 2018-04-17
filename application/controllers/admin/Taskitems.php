<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Taskitems extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model('tasks_model');
		$this->load->model('tasks_extra_model');
        $this->load->model('projects_model');
    }
    public function add_extra_checklist_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
				$result = false;
				$data = $this->input->post();
				$value = '';
				if(isset($data['value'])){
					$value = $data['value'];
				}
				$this->db->insert('tbltaskchecklists', array(
					'taskid' => $data['taskid'],
					'type' => $data['type'],
					'description' => '',
					'dateadded' => date('Y-m-d H:i:s'),
					'addedfrom' => get_staff_user_id(),
					'value' => $value
				));
				
				echo "<div><pre>";
				print_r($this->db->last_query());
				echo "</pre></div>";
				
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					$result = true;
				}
                echo json_encode(array(
                    'success' => $result
				));
            }
        }
    }
	
	public function update_checklist_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
				$data = $this->input->post();
				$this->db->where('id', $data['listid']);
				$this->db->update('tbltaskchecklists', array(
					'value' => nl2br($data['value'])
				));
            }
        }
    }
	
	public function show_checklist_on_project()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
				$data = $this->input->post();
				$this->db->where('id', $data['listid']);
				$this->db->update('tbltaskchecklists', array(
					'show_on_project' => $data['value']
				));
            }
        }
    }
	
    public function upload_file()
    {
        if ($this->input->post()) {
            $itemid = $this->input->post('itemid');
            $taskid = $this->input->post('taskid');
            $file   = handle_tasks_attachments($taskid);
            if ($file) {
                $files   = array();
                $files[] = $file;
                $file_id = $this->tasks_extra_model->add_attachment_to_database($taskid, $file);
				if($file_id > 0){
					$this->db->where('id', $itemid);
					$this->db->update('tbltaskchecklists', array(
						'value' => $file_id,
						'finished' => 1,
						'finished_from' => get_staff_user_id()
					));
				}
                echo json_encode(array(
                    'success' => $file_id
				));
            }
        }
    }
	
	public function delete_select_file_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
				$data = $this->input->post();
				$list = $this->tasks_model->get_checklist_item($data['listid']);
				if (has_permission('tasks', '', 'delete') || $list->addedfrom == get_staff_user_id()) {
					if($data['keep'] == 0 && !empty($list->value)){
						$this->tasks_model->remove_task_attachment(intval($list->value));
					}
					echo json_encode(array(
						'success' => $this->tasks_model->delete_checklist_item($list->id)
					));
				}
            }
        }
    }
	
	public function delete_select_file()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
				$data = $this->input->post();
				$list = $this->tasks_model->get_checklist_item($data['listid']);
				if ($list) {
					if($data['keep'] == 0 && !empty($list->value)){
						$this->tasks_model->remove_task_attachment(intval($list->value));
					}
					$this->db->where('id', $list->id);
					$this->db->update('tbltaskchecklists', array(
						'value' => '',
						'finished' => 0
					));
					echo json_encode(array(
						'success' => true
					));
				}
            }
        }
    }

}
