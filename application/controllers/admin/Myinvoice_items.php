<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Myinvoice_items extends Admin_controller
{
    public function __construct()
    {
        echo "<div><pre>";
        echo "</pre></div>";
        die(__FILE__ . '' . __LINE__);
        parent::__construct();
    }

    //ornsoft change |--| BOF -- upload item image
    public function upload_file()
    {
        handle_invoices_items_images_upload($_POST['itemid']);
    }
    //ornsoft change |--| EOF -- upload item image
}
