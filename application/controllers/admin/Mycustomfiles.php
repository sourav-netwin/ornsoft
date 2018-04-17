<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mycustomfiles extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model('misc_model');
    }	
    public function upload()
    {
		$belongs_to = $this->input->get('belongs_to');
		$field_id = $this->input->get('field_id');
		if(!empty($belongs_to) && !empty($field_id)) {
			$rel_type = 'custom_select_file';
			$file   = $this->_handle_attachments($rel_type, $field_id);
			if ($file) {
				$files   = array();
				$files[] = $file;
				$file_id = $this->misc_model->add_attachment_to_database($field_id, $rel_type, $file, false);
				$file_row = $this->misc_model->get_file($file_id);
				if($file_row){					
					$href = site_url('mydownload/file/customfiles/'. $file_row->id);
					$class = get_mime_class($file_row->filetype);
					$file_name = $file_row->file_name;
					echo json_encode(array(
						'data' => '<a href="'.$href.'" class="pull-left" target="_blank"><i class="'.$class.'"></i> '.$file_name.'</a>',
						'rem_link' => '<a href="#" class="text-danger" style="margin-left:10px;" onclick="remove_file_field'.$field_id.'('.$file_id.',this); return false;"><i class="fa fa-remove"></i></a>'
					));
				}
			}
		}
    }
	
	private function _handle_attachments($rel_type, $rel_id, $index_name = 'file')
	{
	   if(isset($_FILES[$index_name]) && empty($_FILES[$index_name]['name'])){return;}

		 if(isset($_FILES[$index_name]) && _progibos_upload_error($_FILES[$index_name]['error'])){
			header('HTTP/1.0 400 Bad error');
			echo _progibos_upload_error($_FILES[$index_name]['error']);
			die;
		}
		$root_path = FCPATH . 'uploads/'.$rel_type . '/';
		if (!file_exists($root_path)) {
			mkdir($root_path);
		}
		$path           = $root_path . $rel_id . '/';
		$uploaded_files = array();
		if (isset($_FILES[$index_name]['name']) && $_FILES[$index_name]['name'] != '') {
			do_action('before_upload_'.$rel_type.'_attachment',$rel_id);
			// Get the temp file path
			$tmpFilePath = $_FILES[$index_name]['tmp_name'];
			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				// Setup our new file path
				if (!file_exists($path)) {
					mkdir($path);
					fopen($path . 'index.html', 'w');
				}
				  $filename    = unique_filename($path, $_FILES[$index_name]["name"]);
				   $newFilePath = $path . $filename;
				// Upload the file into the temp dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					array_push($uploaded_files, array(
						'file_name' => $filename,
						'filetype' => $_FILES[$index_name]["type"]
					));
				}
			}
		}
		if (count($uploaded_files) > 0) {
			return $uploaded_files;
		}
		return false;
	}
}
