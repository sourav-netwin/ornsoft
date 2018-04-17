<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Myclients extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function client($id = '')
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('customers', '', 'create')) {
                    access_denied('customers');
                }
                $data = $this->input->post();
                if(isset($data['save_and_add_contact'])){
                    unset($data['save_and_add_contact']);
                }
                $id = $this->clients_model->add($data);
                if(!has_permission('customers','','view')){
                    $assign['customer_admins'] = array();
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->clients_model->assign_admins($assign,$id);
                }
                if ($id) {
                    echo '{"id":'.$id.'}';
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
	public function get_proposals_total()
    {
		if ($this->input->post()) {
			$data = $this->input->post();
			$statuses = array(
				6,
				4,
				5,
				2,
				3
			);
			$this->load->model('currencies_model');
			if (isset($data['currency'])) {
				$currencyid = $data['currency'];
			} else if (isset($data['customer_id']) && $data['customer_id'] != '') {
				$currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);
				if ($currencyid == 0) {
					$currencyid = $this->currencies_model->get_base_currency()->id;
				}
			} else {
				$currencyid = $this->currencies_model->get_base_currency()->id;
			}

			$symbol = $this->currencies_model->get_currency_symbol($currencyid);
			$where  = '';
			if (isset($data['customer_id']) && $data['customer_id'] != '') {
				$where = ' AND rel_type="customer" AND rel_id=' . $data['customer_id'];
			}
			if (!has_permission('proposals', '', 'view')) {
				$where .= ' AND addedfrom=' . get_staff_user_id();
			}
			$sql = 'SELECT';
			foreach ($statuses as $proposal_status) {
				$sql .= '(SELECT SUM(total) FROM tblproposals WHERE status=' . $proposal_status;
				$sql .= ' AND currency =' . $currencyid;
				$sql .= $where;
				$sql .= ') as "' . $proposal_status . '",';
			}

			$sql     = substr($sql, 0, -1);
			$result  = $this->db->query($sql)->result_array();
			$_result = array();
			$i       = 1;
			foreach ($result as $key => $val) {
				foreach ($val as $status => $total) {
					$_result[$i]['total']  = $total;
					$_result[$i]['symbol'] = $symbol;
					$_result[$i]['status'] = $status;
					$i++;
				}
			}
			$_result['currencyid'] = $currencyid;			
				
			
			$currencies = $this->currencies_model->get();
			$data['totals'] = $_result;
			if(!$this->input->post('customer_id')){
				$multiple_currencies = call_user_func('is_using_multiple_currencies','tblproposals');
			} else {
				$multiple_currencies = false;
				$total_currencies_used = 0;
				foreach ($currencies as $currency) {
					$this->db->where(array('currency'=>$currency['id'], 'rel_type'=>'customer', 'rel_id'=>$data['customer_id']));
					$total = $this->db->count_all_results('tblproposals');
					if ($total > 0) {
						$total_currencies_used++;
					}
				}
				if ($total_currencies_used > 1) {
					$multiple_currencies = true;
				}
			}
			if ($multiple_currencies) {
				$data['currencies'] = $currencies;
			}
			$data['_currency'] = $data['totals']['currencyid'];
			unset($data['totals']['currencyid']);
			$this->load->view('admin/clients/proposals_total_template', $data);
		}
    }
}
