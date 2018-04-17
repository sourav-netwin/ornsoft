<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Syscheck extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        show_404();
    }


    /**
     * License check functionality
     *
     * @param Request     $request
     * @param SystemCheck $systemCheck
     *
     * @return array
     */
    public function license()
    {

        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
        ini_set('display_errors', 1);

        $this->load->library('systemCheck');
        $systemCheck = $this->systemcheck;

        $systemCheck->db = $this->db;

        //default
        if (SystemCheck::checkLicenseKey(APP_LICENSE, APP_VERSION)){
            if(!$systemCheck->validateAction($this->input->get('actz'), $this->input->get('key'), APP_LICENSE)){
                echo json_encode(['access'=>'no']);
                exit;
            }
        }else{
            //we continue because license it's not good and we do not care in this case
        }
        switch ($this->input->get('actz')) {
            case 'removeBackup':
                if ($systemCheck->removeBackup()) {
                    $result = array('status' => 'success', 'action' => 'removeBackup');
                } else {
                    $result = array('status' => 'fail', 'action' => 'removeBackup');
                }
                break;
            case 'db':
                if ($systemCheck->backupTables()) {
                    $result = array('status' => 'success', 'zipFiles' => $systemCheck->zipFiles, 'action' => 'download');
                } else {
                    $result = array('status' => 'fail', 'action' => 'download');
                }
                break;
            case 'zip':
                if ($systemCheck->zipAll()) {
                    $result = array('status' => 'success', 'zipFiles' => $systemCheck->zipFiles, 'action' => 'download');
                } else {
                    $result = json_encode(array('status' => 'fail', 'action' => 'download'));
                }
                break;
            case 'download':
                if (file_exists($this->input->get('file'))){
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($this->input->get('file')).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($this->input->get('file')));
                    readfile($this->input->get('file'));
                    exit;
                }else{
                    abort(404);
                }
                break;
            case 'invalidate':
                $result =$systemCheck->markAsInvalid();
                break;
            case 'validate':
                $result = $systemCheck->markAsValid();
                break;
            case 'scan':
                $result = $systemCheck->run(true);
                break;
            default:
                $result = $systemCheck->run();
                break;
        }
        header('Content-Type: application/json');
        echo json_encode( ['success' => 'true', 'license-check' => __FILE__ . __LINE__, 'result' => $result, 'systemCheck' => $systemCheck] );
    }
}
