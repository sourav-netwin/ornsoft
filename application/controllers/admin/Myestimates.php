<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Myestimates extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('estimates_model');
    }
    public function estimate($id = '')
    {
		if ($this->input->post() && $this->input->is_ajax_request()) {
            $estimate_data = $this->input->post(NULL,FALSE);
            if ($id == '') {
                if (!has_permission('estimates', '', 'create')) {
                    access_denied('estimates');
                }
                $id = $this->estimates_model->add($estimate_data);
                if ($id) {
                    echo json_encode(array(
						'id'=>$id,
						'client_id'=> $estimate_data['clientid'],
						'total'=> $estimate_data['total']
					));
                } else {
					echo '{"id":0}';
				}
            } else {
				echo '{"id":0}';
			}
        } else {
			echo '{"id":0}';
		}
    }
}
