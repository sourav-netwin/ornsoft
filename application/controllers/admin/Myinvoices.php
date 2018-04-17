<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Myinvoices extends Invoices
{
    /* Add new invoice or update existing */
    public function invoice($id = '')
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('invoices');
        }
        if ($this->input->post()) {
            $invoice_data = $this->input->post(null, false);
            if ($id == '') {
                if (!has_permission('invoices', '', 'create')) {
                    access_denied('invoices');
                }
                $id = $this->invoices_model->add($invoice_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('invoice')));


                    //ornsoft change |--| BOF -- if is an ajax request we need the id not redirection
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['id'=>$id]);
                        die;
                    }else{
                        redirect(admin_url('invoices/list_invoices/' . $id));
                    }
                    //ornsoft change |--| EOF -- if is an ajax request we need the id not redirection

                }
            } else {
                if (!has_permission('invoices', '', 'edit')) {
                    access_denied('invoices');
                }
                $success = $this->invoices_model->update($invoice_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('invoice')));
                }

                //ornsoft change |--| BOF -- if is an ajax request we need the id not redirection
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['id'=>$id]);
                    die;
                }else{
                    redirect(admin_url('invoices/list_invoices/' . $id));
                }
                //ornsoft change |--| EOF -- if is an ajax request we need the id not redirection
            }
        }
        if ($id == '') {
            $title                  = _l('create_new_invoice');
            $data['billable_tasks'] = array();
        } else {
            $invoice = $this->invoices_model->get($id);

            if (!$invoice || (!has_permission('invoices', '', 'view') && $invoice->addedfrom != get_staff_user_id())) {
                blank_page(_l('invoice_not_found'), 'danger');
            }

            $data['invoices_to_merge']          = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $invoice->id);
            $data['expenses_to_bill']           = $this->invoices_model->get_expenses_to_bill($invoice->clientid);
            $data['invoice_recurring_invoices'] = $this->invoices_model->get_invoice_recurring_invoices($id);

            $data['invoice']        = $invoice;
            $data['edit']           = true;
            $data['billable_tasks'] = $this->tasks_model->get_billable_tasks($invoice->clientid);
            $title                  = _l('edit', _l('invoice_lowercase')) . ' - ' . format_invoice_number($invoice->id);
        }
        if ($this->input->get('customer_id')) {
            $data['customer_id']        = $this->input->get('customer_id');
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', array(
            'expenses_only !=' => 1
        ));
        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['items']        = $this->invoice_items_model->get_grouped();
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['projects'] = array();

        if ($id != '' || isset($data['customer_id'])) {
            $where             = '';
            $where_customer_id = (isset($data['customer_id']) ? $data['customer_id'] : $invoice->clientid);
            $where .= 'clientid=' . $where_customer_id;

            if (!has_permission('projects', '', 'view')) {
                $where .= ' AND id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id=' . get_staff_user_id() . ')';
            }

            $data['projects'] = $this->projects_model->get('', $where);

            if ($id != '' && $data['invoice']->project_id != 0) {
                if (total_rows('tblprojectmembers', array(
                    'staff_id' => get_staff_user_id(),
                    'project_id' => $data['invoice']->project_id
                )) == 0 && !has_permission('projects', '', 'view')) {
                    $this->db->where('id', $data['invoice']->project_id);
                    $data['projects'][] = $this->db->get('tblprojects')->row_array();
                }
            }
        }

        $data['staff']             = $this->staff_model->get('', 1);
        $data['title']             = $title;
        $data['bodyclass']         = 'invoice';
        $data['accounting_assets'] = true;
        $this->load->view('admin/invoices/invoice', $data);
    }
}
