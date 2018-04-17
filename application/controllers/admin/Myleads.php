<?php
header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') OR exit('No direct script access allowed');
class Myleads extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
        $this->load->model('projects_model');
        $this->load->model('clients_model');
    }
	
    public function copy_project($id = '')
    {
		$data['projects'] = $this->projects_model->get();
        $this->load->view('admin/leads/copy_project', $data);
    }
	
	public function convert_to_customer()
    {
        if ($this->input->post()) {
            $merge_db_field_country_found = false;
            $default_country              = get_option('customer_default_country');
            $data                         = $this->input->post();
            $original_lead_email          = $data['original_lead_email'];
            unset($data['original_lead_email']);
            if (isset($data['merge_db_fields'])) {
                $merge_db_fields = $data['merge_db_fields'];
                unset($data['merge_db_fields']);
            }
            if (isset($data['merge_db_contact_fields'])) {
                $merge_db_contact_fields = $data['merge_db_contact_fields'];
                unset($data['merge_db_contact_fields']);
            }
            if (isset($data['include_leads_custom_fields'])) {
                $include_leads_custom_fields = $data['include_leads_custom_fields'];
                unset($data['include_leads_custom_fields']);
            }
            if (!isset($merge_db_fields)) {
                if ($default_country != '') {
                    $data['country'] = $default_country;
                }
            } else if (isset($merge_db_fields)) {
                foreach ($merge_db_fields as $key => $val) {
                    if ($val == 'country') {
                        $merge_db_field_country_found = true;
                        break;
                    }
                }
                if ($merge_db_field_country_found === false) {
                    if ($default_country != '') {
                        $data['country'] = $default_country;
                    }
                }
            }
            $id = $this->clients_model->add($data, true);
            if ($id) {
                if (!has_permission('customers', '', 'view') && get_option('auto_assign_customer_admin_after_lead_convert') == 1) {
                    $this->db->insert('tblcustomeradmins', array(
                        'date_assigned' => date('Y-m-d H:i:s'),
                        'customer_id' => $id,
                        'staff_id' => get_staff_user_id()
                    ));
                }
                $this->leads_model->log_lead_activity($data['leadid'], 'not_lead_activity_converted', false, serialize(array(
                    get_staff_full_name()
                )));
                $default_status = $this->leads_model->get_status('', array(
                    'isdefault' => 1
                ));
                $this->db->where('id', $data['leadid']);
                $this->db->update('tblleads', array(
                    'date_converted' => date('Y-m-d H:i:s'),
                    'status' => $default_status[0]['id'],
                    'junk' => 0,
                    'lost' => 0
                ));
                // Check if lead email is different then client email
                $contact = $this->clients_model->get_contact(get_primary_contact_user_id($id));
                if ($contact->email != $original_lead_email) {
                    if ($original_lead_email != '') {
                        $this->leads_model->log_lead_activity($data['leadid'], 'not_lead_activity_converted_email', false, serialize(array(
                            $original_lead_email,
                            $contact->email
                        )));
                    }
                }
                if (isset($include_leads_custom_fields)) {
                    foreach ($include_leads_custom_fields as $fieldid => $value) {
                        // checked dont merge
                        if ($value == 5) {
                            continue;
                        }
                        // get the value of this leads custom fiel
                        $this->db->where('relid', $data['leadid']);
                        $this->db->where('fieldto', 'leads');
                        $this->db->where('fieldid', $fieldid);
                        $lead_custom_field_value = $this->db->get('tblcustomfieldsvalues')->row()->value;
                        // Is custom field for contact ot customer
                        if ($value == 1 || $value == 4) {
                            if ($value == 4) {
                                $field_to = 'contacts';
                            } else {
                                $field_to = 'customers';
                            }
                            $this->db->where('id', $fieldid);
                            $field = $this->db->get('tblcustomfields')->row();
                            // check if this field exists for custom fields
                            $this->db->where('fieldto', $field_to);
                            $this->db->where('name', $field->name);
                            $exists               = $this->db->get('tblcustomfields')->row();
                            $copy_custom_field_id = NULL;
                            if ($exists) {
                                $copy_custom_field_id = $exists->id;
                            } else {
                                // there is no name with the same custom field for leads at the custom side create the custom field now
                                $this->db->insert('tblcustomfields', array(
                                    'fieldto' => $field_to,
                                    'name' => $field->name,
                                    'required' => $field->required,
                                    'type' => $field->type,
                                    'options' => $field->options,
                                    'field_order' => $field->field_order,
                                    'slug'=>slug_it($field_to . '_' . $field->name,array('delimiter'=>'_')),
                                    'active' => $field->active,
                                    'only_admin' => $field->only_admin,
                                    'show_on_table' => $field->show_on_table,
                                    'bs_column' => $field->bs_column
                                ));
                                $new_customer_field_id = $this->db->insert_id();
                                if ($new_customer_field_id) {
                                    $copy_custom_field_id = $new_customer_field_id;
                                }
                            }
                            if ($copy_custom_field_id != NULL) {
                                $insert_to_custom_field_id = $id;
                                if ($value == 4) {
                                    $insert_to_custom_field_id = get_primary_contact_user_id($id);
                                    ;
                                }
                                $this->db->insert('tblcustomfieldsvalues', array(
                                    'relid' => $insert_to_custom_field_id,
                                    'fieldid' => $copy_custom_field_id,
                                    'fieldto' => $field_to,
                                    'value' => $lead_custom_field_value
                                ));
                            }
                        } else if ($value == 2) {
                            if (isset($merge_db_fields)) {
                                $db_field = $merge_db_fields[$fieldid];
                                // in case user dont select anything from the db fields
                                if ($db_field == '') {
                                    continue;
                                }
                                if ($db_field == 'country' || $db_field == 'shipping_country' || $db_field == 'billing_country') {
                                    $this->db->where('iso2', $lead_custom_field_value);
                                    $this->db->or_where('short_name', $lead_custom_field_value);
                                    $this->db->or_like('long_name', $lead_custom_field_value);
                                    $country = $this->db->get('tblcountries')->row();
                                    if ($country) {
                                        $lead_custom_field_value = $country->country_id;
                                    } else {
                                        $lead_custom_field_value = 0;
                                    }
                                }
                                $this->db->where('userid', $id);
                                $this->db->update('tblclients', array(
                                    $db_field => $lead_custom_field_value
                                ));
                            }
                        } else if ($value == 3) {
                            if (isset($merge_db_contact_fields)) {
                                $db_field = $merge_db_contact_fields[$fieldid];
                                if ($db_field == '') {
                                    continue;
                                }
                                $primary_contact_id = get_primary_contact_user_id($id);
                                $this->db->where('id', $primary_contact_id);
                                $this->db->update('tblcontacts', array(
                                    $db_field => $lead_custom_field_value
                                ));
                            }
                        }
                    }
                }
                // set the lead to status client in case is not status client
                $this->db->where('isdefault', 1);
                $status_client_id = $this->db->get('tblleadsstatus')->row()->id;
                $this->db->where('id', $data['leadid']);
                $this->db->update('tblleads', array(
                    'status' => $status_client_id
                ));
                logActivity('Created Lead Client Profile [LeadID: ' . $data['leadid'] . ', ClientID: ' . $id . ']');
                echo '{"id":'.$id.'}';
            } else {
				echo '{"id":0}';
			}
        } else {
			echo '{"id":0}';
		}
    }	
	public function lead($id = '')
    {
		$reminder_data = '';

        $data['lead_locked'] = false;
        if ($this->input->get('status_id')) {
            $data['status_id'] = $this->input->get('status_id');
        } else {
            $data['status_id'] = get_option('leads_default_status');
        }
        $lead = null;
        if (is_numeric($id)) {
            $lead = $this->leads_model->get($id);
            if (!$lead) {
                header("HTTP/1.0 404 Not Found");
                echo _l('lead_not_found');
                die;
            }
            if (!is_admin()) {
				$this->db->where(array('lead_id'=> $id, 'staff_id' => get_staff_user_id()));
				$assign = $this->db->get('tblleadmembers')->row();
				$not_assigned = true;
				if($assign){
					$not_assigned = false;
				}
				$assignees = $this->db->get('tblleadmembers')->result();
                if (($not_assigned && $lead->addedfrom != get_staff_user_id() && $lead->is_public != 1)) {
                    header('HTTP/1.0 400 Bad error');
                    echo _l('access_denied');
                    die;
                }
            }
        }
		if ($this->input->post()) {
			$assigneds = array();
			$data = $this->input->post();
			if(!empty($data['assigned']) && $data['assigned'] != 0){
				$assigneds = $data['assigned'];
			}
			unset($data['assigned']);
			$data['assigned'] = 0;
            if ($id == '') {
                $id      = $this->leads_model->add($data);
                $_id     = false;
                $success = false;
                $message = '';
                if ($id) {
                    $success = true;
                    $_id     = $id;
                    $message = _l('added_successfuly', _l('lead'));
                }
				if($id){
					foreach($assigneds as $assigned){
						$assign_data = array('lead_id' => $id, 'staff_id' => $assigned);
						$this->db->insert('tblleadmembers', $assign_data);
						$this->leads_model->lead_assigned_member_notification($id, $assigned);
					}
				}
                echo json_encode(array(
                    'success' => $success,
                    'id' => $_id,
                    'message' => $message
                ));
            } else {
                $proposal_warning = false;
                $original_lead    = $this->leads_model->get($id);
                $success          = $this->leads_model->update($data, $id);
                $message          = '';
				
				$lead = $this->leads_model->get($id);
				if (total_rows('tblproposals', array(
					'rel_type' => 'lead',
					'rel_id' => $id
				)) > 0 && ($original_lead->email != $lead->email) && $lead->email != '') {
					$proposal_warning = true;
				}
				$message = _l('updated_successfuly', _l('lead'));
				$this->db->where('lead_id', $id);
				$original_assigneds_all = $this->db->get('tblleadmembers')->result();
				$original_assigneds = array();
				foreach($original_assigneds_all as $assigned) {
					if(!in_array($assigned->staff_id, $assigneds)){
						$success = true;
						$this->db->where('id', $assigned->id);
						$this->db->delete('tblleadmembers');
					} else {
						array_push($original_assigneds, $assigned->staff_id);
					}
				}
				
				if (count($assigneds) > 0) {
					foreach($assigneds as $assigned){
						if (!in_array($assigned, $original_assigneds)) {
							$success = true;
							$assign_data = array('lead_id' => $id, 'staff_id' => $assigned);
							$this->db->insert('tblleadmembers', $assign_data);
							if ($assigned != get_staff_user_id()) {
								add_notification(array(
									'description' => 'not_assigned_lead_to_you',
									'touserid' => $assigned,
									'link' => '#leadid=' . $id,
									'additional_data' => serialize(array(
										get_staff_full_name(get_staff_user_id()),
										$data['name']
									))
								));

								$this->db->where('id', $id);
								$this->db->update('tblleads', array(
									'dateassigned' => date('Y-m-d')
								));
								$this->leads_model->log_lead_activity($id, 'not_lead_activity_assigned_to', false, serialize(array(
									get_staff_full_name(),
									'<a href="' . admin_url('profile/' . $assigned) . '">' . get_staff_full_name($assigned) . '</a>'
								)));
							}
						}
					}
				}
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'proposal_warning' => $proposal_warning
                ));
            }
            die;
        }
		if ($lead == null && is_numeric($id)) {
            echo _l('lead_not_found');
            die;
        } else {
            if (total_rows('tblclients', array(
                'leadid' => $id
            )) > 0) {
                if (!is_admin() && get_option('lead_lock_after_convert_to_customer') == 1) {
                    $data['lead_locked'] = true;
                }
            }
            $data['members'] = $this->staff_model->get('', 1,array('is_not_staff'=>0));

            if ($lead) {
                $reminder_data = $this->load->view('admin/includes/modals/reminder', array(
                    'id' => $lead->id,
                    'name' => 'lead',
                    'members' => $data['members'],
                    'reminder_title' => _l('lead_set_reminder_title')
                ), TRUE);
            }
            $data['lead']          = $lead;
            $data['mail_activity'] = $this->leads_model->get_mail_activity($id);
            $data['notes']         = $this->misc_model->get_notes($id, 'lead');
            $data['activity_log']  = $this->leads_model->get_lead_activity_log($id);
        }

        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();

        echo json_encode(array(
            'data' => $this->load->view('admin/leads/lead', $data, TRUE),
            'reminder_data' => $reminder_data
        ));
	}
	
	public function form($id = ''){
        if(!is_admin()){
            access_denied('Web To Lead Access');
        }
        if($this->input->post()){
			$data = $this->input->post();
			$responsibles = array();
			if(!empty($data['responsible']) && $data['responsible'] != 0){
				$responsibles = $data['responsible'];
			}
			unset($data['responsible']);
			$data['responsible'] = 0;
            if($id == ''){
                $id = $this->leads_model->add_form($data);
                if($id){
					foreach($responsibles as $responsible){
						$responsible_data = array('webtolead_id' => $id, 'staff_id' => $responsible);
						$this->db->insert('tblwebtoleadresponsibles', $responsible_data);
					}
                    set_alert('success', _l('added_successfuly', _l('web_to_lead_form')));
                    redirect(admin_url('leads/form/'.$id));
                }
            } else {
                $success = $this->leads_model->update_form($id, $data);
				
				$new_success = false;
				$this->db->where('webtolead_id', $id);
				$original_assigneds_all = $this->db->get('tblwebtoleadresponsibles')->result();
				$original_assigneds = array();
				foreach($original_assigneds_all as $assigned) {
					if(!in_array($assigned->staff_id, $responsibles)){
						$new_success = true;
						$this->db->where('id', $assigned->id);
						$this->db->delete('tblwebtoleadresponsibles');
					} else {
						array_push($original_assigneds, $assigned->staff_id);
					}
				}
				
				if (count($responsibles) > 0) {
					foreach($responsibles as $assigned){
						if (!in_array($assigned, $original_assigneds)) {
							$new_success = true;
							$assign_data = array('webtolead_id' => $id, 'staff_id' => $assigned);
							$this->db->insert('tblwebtoleadresponsibles', $assign_data);
						}
					}
				}
				
                if($success || $new_success){
                    set_alert('success', _l('updated_successfuly', _l('web_to_lead_form')));
                }
                redirect(admin_url('leads/form/'.$id));
            }
        }
	}
}
