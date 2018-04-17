<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Pos extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('taxes_model');
        $this->load->model('invoice_items_model');
    }

    /* Get all invoices in case user go on index page */
    public function index()
    {
        $this->show_pos();
    }

    /* List all invoices datatables */
    public function show_pos($id = false, $clientid = false)
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('invoices');
        }

        if ($this->input->is_ajax_request()) {

            //$this->load->view('admin/pos/items', $data);
            exit();
        }

        $this->load->model('taxes_model');
        $data['taxes']        = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        //$data['items_grouped'] = $this->invoice_items_model->get_grouped();

        $data['title'] = _l('invoice_items');







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



        $data['staff']             = $this->staff_model->get('', 1);
        $data['bodyclass']         = 'invoice';
        $data['accounting_assets'] = true;













        $this->load->view('admin/pos/pos', $data);

    }

    public function upload_file($project_id)
    {
        handle_project_file_uploads($project_id);
    }

    public function generateBarcode($code='', $type = 'PNG', $inline=true)
    {
        require_once 'application/third_party/barcode/src/BarcodeGenerator.php';
        if ($type == 'SVG'){
            require_once 'application/third_party/barcode/src/BarcodeGeneratorSVG.php';
            $generator = new Picqer\Barcode\BarcodeGeneratorSVG();
            if ($inline){
                echo $generator->getBarcode($code, $generator::TYPE_CODE_128);
            }
        }elseif ($type == 'PNG'){
            require_once 'application/third_party/barcode/src/BarcodeGeneratorPNG.php';
            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            if ($inline){
                echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($code, $generator::TYPE_CODE_128)) . '">';
            }else{
                header('Content-Type: image/png');
                echo $generator->getBarcode($code, $generator::TYPE_CODE_128);
            }
        }elseif($type == "JPG"){
            require_once 'application/third_party/barcode/src/BarcodeGeneratorJPG.php';
            $generator = new Picqer\Barcode\BarcodeGeneratorJPG();
            if ($inline){
                echo '<img src="data:image/jpeg;base64,' . base64_encode($generator->getBarcode($code, $generator::TYPE_CODE_128)) . '">';
            }else{
                header('Content-Type: image/jpg');
                echo $generator->getBarcode($code, $generator::TYPE_CODE_128);
            }
        }elseif($type == "HTML"){
            require_once 'application/third_party/barcode/src/BarcodeGeneratorHTML.php';
            $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
            echo $generator->getBarcode($code, $generator::TYPE_CODE_128);
        }
    }



}
