<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Version_163 extends CI_Migration
{
    function __construct()
    {
        parent::__construct();
    }

    public function up()
    {

        $menu = get_option('aside_menu_active');
        $menu = json_decode($menu);
        if (is_object($menu)) {
            $i = 0;
            foreach ($menu->aside_menu_active as $item) {
                if ($item->id == 'sales') {
                    $menu->aside_menu_active[$i]->url = 'tickets';
                    if (isset($item->children)) {
                        //add POS in menu
                        $found = false;
                        foreach ($item->children as $child){
                            if($child->id == 'child-pos'){
                                $found = true;
                            }
                        }
                        if ($found == false){
                            $menu->aside_menu_active[$i]->children[] = (object) array('name' => 'pos', 'url' => 'pos', 'permission' => 'invoices', 'icon' => '', 'id' => 'child-pos', );
                        }
                    }
                } else {
                }
                $i++;
            }
        }
        $menu = json_encode($menu);
        update_option('aside_menu_active', $menu);


        $this->db->query("ALTER TABLE `tblitems` ADD `code` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tblitems` ADD UNIQUE (`code`);");
        $this->db->query("ALTER TABLE `tblitems` ADD `cost` DECIMAL(11,2);");
        $this->db->query("ALTER TABLE `tblitems` ADD `quantity` INT(11) NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tblitems` ADD `image` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");

        $this->db->query("ALTER TABLE `tblitems_groups` ADD `parent` INT(11) NULL DEFAULT NULL;");

        //ornsoft change |--| BOF -- rename discount field
        $this->db->query("ALTER TABLE `tblinvoices` ADD `discount_name` VARCHAR(255) NULL DEFAULT NULL AFTER `discount_type`;");
        //ornsoft change |--| EOF -- rename discount field


        $this->db->query("ALTER TABLE `tblmilestones` CHANGE `color` `color` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tblleadmembers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
              `lead_id` int(11) NOT NULL,
              `staff_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `lead_id` (`lead_id`),
              KEY `staff_id` (`staff_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
        //$this->db->query("ALTER TABLE `tblleads` DROP COLUMN `assignees`;");

        $this->db->query("ALTER TABLE `tblprojects` CHANGE `deadline` `deadline` DATETIME NULL DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tblleads` ADD `assignees` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `client_id`;");
        $this->db->query("ALTER TABLE `tblestimates` ADD `sent_date` DATETIME NULL AFTER `is_expiry_notified`;");

        $this->db->query("ALTER TABLE `tblestimates` ADD `accepted_date` DATETIME NULL AFTER `sent_date`;");
        $this->db->query("ALTER TABLE `tbltaskchecklists` ADD `type` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `list_order`");


        $this->db->query("ALTER TABLE `tbltaskchecklists` ADD `value` varchar(500) DEFAULT NULL;");
        $this->db->query("ALTER TABLE `tbltaskchecklists` ADD `show_on_project` tinyint(4) NOT NULL DEFAULT '0';");

        $this->db->query("CREATE TRIGGER `estimate_sent_accepted_date` BEFORE UPDATE ON `tblestimates` FOR EACH ROW 
                    BEGIN IF( NEW.status =2 AND OLD.status <>2 ) THEN SET NEW.sent_date = NOW( ) ; END IF ;
                    IF( NEW.status =4 AND OLD.status <>4 ) THEN SET NEW.accepted_date = NOW( ) ; END IF ;
                END ;");

    }

    function down(){

        //$this->db->query("DROP TABLE `tblleadmembers`;");
        ////$this->db->query("ALTER TABLE `tblleads` DROP `assignees`;");
        //
        //$this->db->query("ALTER TABLE `tblprojects` CHANGE `deadline` `deadline` DATETIME NULL DEFAULT NULL;");
        //$this->db->query("ALTER TABLE `tblleads` DROP `assignees`;");
        //$this->db->query("ALTER TABLE `tblestimates` DROP `sent_date` ;");
        //$this->db->query("ALTER TABLE `tblestimates` DROP `accepted_date` ;");
        //
        //
        //$this->db->query("ALTER TABLE `tblestimates` DROP `accepted_date`;");
        //$this->db->query("DROP TRIGGER IF EXISTS `estimate_sent_accepted_date`");
    }
}
