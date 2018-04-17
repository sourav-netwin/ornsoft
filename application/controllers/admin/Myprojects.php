<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Myprojects extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
    }
    public function copy($project_id)
    {
        if (has_permission('projects', '', 'create')) {
			$data      = $this->input->post();
			$project_name = '';
			$client_id = '';
			if(isset($data['project_name']))
				$project_name = $data['project_name'];
			if(isset($data['client_id']))
				$client_id = $data['client_id'];
            $id = $this->projects_model->copy($project_id);
            if ($id) {
				if(!empty($project_name) && !empty($client_id)){
					$this->db->where('id', $id);
					$this->db->update('tblprojects', array(
						'name' => $project_name,
						'clientid' => $client_id,
						'deadline' => to_sql_date($data['deadline'], true)
					));
				} else if(!empty($project_name) && isset($data['estimate_id'])){
					$this->db->where('id', $data['estimate_id']);
					$estimate =	$this->db->get('tblestimates')->row();
					if($estimate){
						$this->db->where('id', $id);
						$this->db->update('tblprojects', array(
							'name' => $project_name,
							'clientid' => $estimate->clientid,
							'billing_type' => 1,
							'project_cost' => $estimate->total,
							'deadline' => to_sql_date($data['deadline'], true)
						));
						$this->db->where('id', $estimate->id);
						$this->db->update('tblestimates', array(
							'project_id' => $id,
                            'status'=>4
						));
					}
				} else {
					$this->db->where('id', $id);
					$this->db->update('tblprojects', array(
						'deadline' => to_sql_date($data['deadline'], true)
					));
				}
                set_alert('success', _l('project_copied_successfuly'));
                redirect(admin_url('projects/view/' . $id));
            } else {
                set_alert('danger', _l('failed_to_copy_project'));
                redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }
	public function project($id = '')
    {
        if (!has_permission('projects', '', 'edit') && !has_permission('projects', '', 'create')) {
            access_denied('Projects');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['description'] = $this->input->post('description',FALSE);
            if ($id == '') {
                if (!has_permission('projects', '', 'create')) {
                    acccess_danied('Projects');
                }
				$est_id = '';
				if(isset($data['est_id']) && !empty($data['est_id'])){
					$est_id = $data['est_id'];
				}
				unset($data['est_id']);
                $id = $this->projects_model->add($data);
                if ($id) {
					if(!empty($data['deadline'])){
						$this->db->where('id', $id);
						$this->db->update('tblprojects', array(
							'deadline' => to_sql_date($data['deadline'], true)
						));
					}
					if($est_id != ''){
						$this->db->where('id', $est_id);
						$this->db->update('tblestimates', array(
							'project_id' => $id
						));
					}
                    set_alert('success', _l('added_successfuly', _l('project')));
                    redirect(admin_url('projects/view/' . $id));
                } else {
					redirect(admin_url('projects/project/' . $id));
				}
            } else {
                if (!has_permission('projects', '', 'edit')) {
                    acccess_danied('Projects');
                }
                $success = $this->projects_model->update($data, $id);
				if(!empty($data['deadline'])){
					$this->db->where('id', $id);
					$this->db->update('tblprojects', array(
						'deadline' => to_sql_date($data['deadline'], true)
					));
				}
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('project')));
                }
                redirect(admin_url('projects/view/' . $id));
            }
        }
    }
}
