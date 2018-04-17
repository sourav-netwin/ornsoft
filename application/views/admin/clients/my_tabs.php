<ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
	<?php if(isset($client)) { ?>
    <li class="active"><a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=overview'); ?>" data-group="overview"><i class="fa fa-eye menu-icon"></i><?php echo _l( 'overview'); ?></a></li>
   <?php } ?>
   
   <li>
      <?php
         $url_profile = admin_url('clients/client');
         if(isset($client)){
            $url_profile = admin_url('clients/client/'.$client->userid.'?group=profile');
         }
         ?>
      <a href="<?php echo $url_profile; ?>" data-group="profile">
      <i class="fa fa-user-circle menu-icon" aria-hidden="true"></i><?php echo _l( 'client_add_edit_profile'); ?>
      </a>
   </li>
   <?php if(isset($client)) { ?>
   <?php if(has_permission('invoices','','view') || has_permission('invoices','','view_own')) { ?>
    <li><a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=invoices'); ?>" data-group="invoices"><i class="fa fa-file-text menu-icon"></i><?php echo _l( 'client_invoices_tab'); ?></a></li>
   <?php } ?>

    <?php if(has_permission('payments','','view') || has_permission('invoices','','view_own')) { ?>
    <li><a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=payments'); ?>" data-group="payments"><i class="fa fa-line-chart menu-icon" aria-hidden="true"></i><?php echo _l( 'client_payments_tab'); ?></a></li>
   <?php } ?>

    <?php if(has_permission('proposals','','view') || has_permission('proposals','','view_own')) { ?>
    <li><a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=proposals'); ?>" data-group="proposals"><i class="fa fa-file-text-o menu-icon" aria-hidden="true"></i><?php echo _l( 'proposals'); ?></a></li>
   <?php } ?>

     <?php if(has_permission('estimates','','view') || has_permission('estimates','','view_own')) { ?>
    <li><a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=estimates'); ?>" data-group="estimates"><i class="fa fa-file-text-o menu-icon" aria-hidden="true"></i><?php echo _l( 'estimates'); ?></a></li>
   <?php } ?>

   <?php if(has_permission('expenses','','view') || has_permission('expenses','','view_own')){ ?>
   <li><a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=expenses'); ?>" data-group="expenses"><i class="fa fa-heartbeat menu-icon"></i><?php echo _l( 'client_expenses_tab'); ?></a></li>
   <?php } ?>
   <?php if(has_permission('contracts','','view') || has_permission('contracts','','view_own')){ ?>
      <li><a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=contracts'); ?>" data-group="contracts">
       <i class="fa fa-file menu-icon"></i><?php echo _l( 'contracts_invoices_tab'); ?></a></li>
   <?php } ?>
   <li>
      <a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=projects'); ?>" data-group="projects">
      <i class="fa fa-bars menu-icon"></i><?php echo _l( 'projects'); ?>
      </a>
   </li>
   <?php if((get_option('access_tickets_to_none_staff_members') == 1 && !is_staff_member()) || is_staff_member()){ ?>
   <li>
      <a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=tickets'); ?>" data-group="tickets">
      <i class="fa fa-ticket menu-icon"></i><?php echo _l( 'contracts_tickets_tab'); ?>
      </a>
   </li>
   <?php } ?>
   <li>
      <a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=tasks'); ?>" data-group="tasks">
      <i class="fa fa-tasks menu-icon"></i><?php echo _l( 'tasks'); ?>
      </a>
   </li>
   <li>
      <a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=attachments'); ?>" data-group="attachments">
      <i class="fa fa-paperclip menu-icon" aria-hidden="true"></i><?php echo _l( 'customer_attachments'); ?>
      </a>
   </li>
   <li>
      <a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=reminders'); ?>" data-group="reminders">
      <i class="fa fa-clock-o menu-icon" aria-hidden="true"></i><?php echo _l( 'client_reminders_tab'); ?>
      </a>
   </li>
   <li>
      <a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=map'); ?>" data-group="map">
      <i class="fa fa-map-marker menu-icon" aria-hidden="true"></i><?php echo _l( 'customer_map'); ?>
      </a>
   </li>
   <li>
      <a href="<?php echo admin_url('clients/client/'.$client->userid.'?group=notes'); ?>" data-group="notes">
      <i class="fa fa-sticky-note-o menu-icon" aria-hidden="true"></i><?php echo _l( 'contracts_notes_tab'); ?>
      </a>
   </li>
   <?php } ?>
</ul>
