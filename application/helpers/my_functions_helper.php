<?php
//ornsoft change |--| BOF -- Added items images directory
define('INVOICE_ITEMS_IMAGES',FCPATH . 'uploads/invoices_items_images' . '/');
//ornsoft change |--| EOF -- Added items images directory


//add_action('after_render_single_aside_menu','my_custom_menu_items');
//
//function my_custom_menu_items($order){
//    if($order == 1){
//        echo '<li><a href="#">Test</a></li>';
//    }
//}

add_action('app_admin_head','my_custom_header_css');
add_action('before_invoice_added','my_update_items_stock');
//add_action('before_invoice_updated','my_update_items_stock');//@todo update the stock!!
//add_action('before_invoice_deleted','my_update_items_stock');//@todo update the stock!!


function my_custom_header_css(){
    echo ' <link rel="stylesheet" type="text/css" media="screen" href="'.base_url('assets/css/pos.css').'">';
}

function my_update_items_stock($args){

    //$data = $args['data'];
    $items = $args['items'];

    $CI =& get_instance();
    foreach ($items as $item){
        if (isset($item['id'])) {
            $CI->db->set('quantity', 'quantity - '.(int) $item['qty'], false);
            $CI->db->where('id', $item['id']);
            $CI->db->update('tblitems');
        }elseif($item['description']){
            $CI->db->set('quantity', 'quantity - '.(int) $item['qty'], false);
            $CI->db->where('description', $item['description']);
            $CI->db->update('tblitems');
        }
    }

    return $args;
}


//ornsoft change |--| BOF -- handle images upload
/**
 * Check for staff profile image
 * @return boolean
 */
function handle_invoices_items_images_upload($item_id = '')
{

    if ($item_id != '' && isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        //$path = get_upload_path_by_type('invoices_items_images').'/';//we need this hardcoded not to affect other shit
        $path = INVOICE_ITEMS_IMAGES;
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if ( ! empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["file"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = [
                'jpg',
                'jpeg',
                'png'
            ];
            if ( ! in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            // Setup our new file path
            if ( ! file_exists($path)) {
                mkdir($path);
                fopen($path.'/index.html', 'w');
            }
            $filename = unique_filename($path, $item_id.'.'.$extension);
            $newFilePath = $path.$filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $config = [];
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_'.$filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 75;
                $config['height'] = 75;
                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();

                $CI->db->where('id', $item_id);
                $CI->db->update('tblitems', [
                    'image' => 'thumb_'.$filename
                ]);
                // Remove original image
                unlink($newFilePath);

                return true;
            }
        }
    }
}
//ornsoft change |--| EOF -- handle images upload


