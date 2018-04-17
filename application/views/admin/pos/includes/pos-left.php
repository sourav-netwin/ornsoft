<div class="row mbot10">
    <div class="col-md-12">
        <input class="form-control" id="pcodeBarcode" style="border: 1px solid #373942;" placeholder="Scan your barcode" type="text">
    </div>
</div>
<?php

echo form_open('admin/invoices/invoice',array('id'=>'invoice-form','class'=>'_transaction_form invoice-form'));
if(isset($invoice)){
    echo form_hidden('isedit');
}


//default values:
echo form_hidden('show_quantity_as',1);
$customer_id = 2;//default walk it thing -> @todo we need to make it configurable!!


$default_tax = get_option('default_tax');
$default_tax_name = '';
$default_tax_name = explode('+',$default_tax);
$select = '<select class="" name="taxname" multiple style="display: none;">';
$no_tax_selected = '';
if($default_tax == ''){
    $no_tax_selected = 'selected';
}
$select .= '<option value="" '.$no_tax_selected.'>'._l('no_tax').'</option>';
foreach($taxes as $tax){
    $selected = '';
    if(is_array($default_tax_name)){
        if(in_array($tax['name'] . '|'.$tax['taxrate'],$default_tax_name)){
            $selected = ' selected ';
        }
    }
    $select .= '<option value="'.$tax['name'].'|'.$tax['taxrate'].'"'.$selected.'data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';
}
$select .= '</select>';
echo $select;

?>
<div>
    <div class="panel_s invoice accounting-template">
        <div class="additional"></div>
        <div class="">
            <?php if(isset($invoice)){ ?>
                <?php  echo format_invoice_status($invoice->status); ?>
                <hr />
            <?php } ?>
            <?php do_action('before_render_invoice_template'); ?>
            <?php if(isset($invoice)){
                echo form_hidden('merge_current_invoice',$invoice->id);
            }
            ?>
            <div class="modal fade" id="pos_invoice_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">
                                Invoice
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="f_client_id">
                                        <div class="form-group">
                                            <label for="clientid"><?php echo _l('invoice_select_customer'); ?></label>
                                            <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="selectpicker ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php $selected = (isset($invoice) ? $invoice->clientid : '');
                                                if($selected == ''){
                                                    $selected = (isset($customer_id) ? $customer_id: '');
                                                }
                                                if($selected != ''){
                                                    $rel_data = get_relation_data('customer',$selected);
                                                    $rel_val = get_relation_values($rel_data,'customer');
                                                    echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                    $next_invoice_number = get_option('next_invoice_number');
                                    $format = get_option('invoice_number_format');
                                    if(isset($invoice)){$format = $invoice->number_format;}
                                    $prefix = get_option('invoice_prefix');
                                    if ($format == 1) {
                                        // Number based
                                        $__number = $next_invoice_number;
                                        if(isset($invoice)){
                                            $__number = $invoice->number;
                                            $prefix = '<span id="prefix">' . $invoice->prefix . '</span>';
                                        }
                                    } else {
                                        if(isset($invoice)){
                                            $__number = $invoice->number;
                                            $prefix = $invoice->prefix;
                                            $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_year">' .date('Y',strtotime($invoice->date)).'</span>/';
                                        } else {
                                            $__number = $next_invoice_number;
                                            $prefix = $prefix.'<span id="prefix_year">'.date('Y').'</span>/';
                                        }
                                    }
                                    $_invoice_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                                    if(isset($invoice)){
                                        $isedit = 'true';
                                        $data_original_number = $invoice->number;
                                    } else {
                                        $isedit = 'false';
                                        $data_original_number = 'false';
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label for="number"><?php echo _l('invoice_add_edit_number'); ?></label>
                                        <div class="input-group">
                      <span class="input-group-addon">
                      <?php if(isset($invoice)){ ?>
                          <a href="#" onclick="return false;" data-toggle="popover" data-container='._transaction_form' data-html="true" data-content="<label class='control-label'><?php echo _l('settings_sales_invoice_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo $invoice->prefix; ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('invoices/update_number_settings/'.$invoice->id); ?>' class='btn btn-info btn-block mtop15'><?php echo _l('submit'); ?></button>"><i class="fa fa-cog"></i></a>
                      <?php } ?>
                          <?php echo $prefix; ?></span>
                                            <input type="text" name="number" class="form-control" value="<?php echo $_invoice_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>">
                                        </div>
                                    </div>
                                    <div class="row" style="display: none;">
                                        <div class="col-md-6">
                                            <?php $value = (isset($invoice) ? _d($invoice->date) : _d(date('Y-m-d'))); ?>
                                            <?php echo render_date_input('date','invoice_add_edit_date',$value); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            $value = '';
                                            if(isset($invoice)){
                                                $value = _d($invoice->duedate);
                                            } else {
                                                if(get_option('invoice_due_after') != 0){
                                                    $value = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
                                                }
                                            }
                                            ?>
                                            <?php echo render_date_input('duedate','invoice_add_edit_duedate',$value); ?>
                                        </div>
                                    </div>
                                    <?php $rel_id = (isset($invoice) ? $invoice->id : false); ?>
                                    <?php echo render_custom_fields('invoice',$rel_id); ?>
                                    <div class="form-group ">
                                        <label for="allowed_payment_modes" class="control-label"><?php echo _l('invoice_add_edit_allowed_payment_modes'); ?></label>
                                        <br />
                                        <?php if(count($payment_modes) > 0){ ?>
                                            <?php foreach($payment_modes as $mode){
                                                $checked = '';
                                                if(isset($invoice)){
                                                    if($invoice->allowed_payment_modes){
                                                        $inv_modes = unserialize($invoice->allowed_payment_modes);
                                                        if(is_array($inv_modes)) {
                                                            foreach($inv_modes as $_allowed_payment_mode){
                                                                if($_allowed_payment_mode == $mode['id']){
                                                                    $checked = 'checked';
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    if($mode['selected_by_default'] == 1){
                                                        $checked = 'checked';
                                                    }
                                                }
                                                ?>
                                                <div class="checkbox checkbox-inline">
                                                    <input type="checkbox" value="<?php echo $mode['id']; ?>" id="pm_<?php echo $mode['id']; ?>" name="allowed_payment_modes[]" <?php echo $checked; ?>>
                                                    <label for="pm_<?php echo $mode['id']; ?>"><?php echo $mode['name']; ?></label>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <p><?php echo _l('invoice_add_edit_no_payment_modes_found'); ?></p>
                                            <a class="btn btn-info" href="<?php echo admin_url('paymentmodes'); ?>">
                                                <?php echo _l('new_payment_mode'); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php
                                            $s_attrs = array('disabled'=>true);
                                            $s_attrs = do_action('invoice_currency_disabled',$s_attrs);
                                            foreach($currencies as $currency){
                                                if($currency['isdefault'] == 1){
                                                    $s_attrs['data-base'] = $currency['id'];
                                                }
                                                if(isset($invoice)){
                                                    if($currency['id'] == $invoice->currency){
                                                        $selected = $currency['id'];
                                                    }
                                                } else {
                                                    if($currency['isdefault'] == 1){
                                                        $selected = $currency['id'];
                                                    }
                                                }
                                            }
                                            ?>
                                            <?php echo render_select('currency',$currencies,array('id','name','symbol'),'invoice_add_edit_currency',$selected,$s_attrs); ?>
                                        </div>
                                        <div class="col-md-6" style="display: none">
                                            <?php
                                            //@todo select the current log in user??
                                            $i = 0;
                                            $selected = '';
                                            foreach($staff as $member){
                                                if(isset($invoice)){
                                                    if($invoice->sale_agent == $member['staffid']) {
                                                        $selected = $member['staffid'];
                                                    }
                                                }
                                                $i++;
                                            }
                                            echo render_select('sale_agent',$staff,array('staffid',array('firstname','lastname')),'sale_agent_string',$selected);
                                            ?>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount_type" class="control-label"><?php echo _l('discount_type'); ?></label>
                                                <select name="discount_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""><?php echo _l('no_discount'); ?></option>
                                                    <option value="before_tax"><?php echo _l('discount_type_before_tax'); ?></option>
                                                    <option value="after_tax" selected><?php echo _l('discount_type_after_tax'); ?></option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <table class="table text-right">
                                                <tbody>
                                                <tr id="subtotal">
                                                    <td><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span>
                                                    </td>
                                                    <td class="subtotal">
                                                    </td>
                                                </tr>
                                                <tr id="discount_percent">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-7">
                                                                <span class="bold"><?php echo _l('invoice_discount'); ?> (%)</span>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <?php
                                                                $discount_percent = 0;
                                                                if(isset($invoice)){
                                                                    if($invoice->discount_percent != 0){
                                                                        $discount_percent =  $invoice->discount_percent;
                                                                    }
                                                                }
                                                                ?>
                                                                <input type="number" value="<?php echo $discount_percent; ?>" class="form-control pull-left" min="0" max="100" name="discount_percent">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="discount_percent"></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-7">
                                                                <span class="bold"><?php echo _l('invoice_adjustment'); ?></span>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="number" value="<?php if(isset($invoice)){echo $invoice->adjustment; } else { echo _format_number(0); } ?>" class="form-control pull-left" name="adjustment">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="adjustment"></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
                                                    </td>
                                                    <td class="total">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>


                                        </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="paid_amount" class="control-label"><?php echo 'Paid amount'; ?></label>
                                                <input type="number" class="form-control" id="paid_amount" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="return_change" class="control-label"><?php echo 'Return change'; ?></label>
                                                <input type="number" class="form-control" id="return_change" readonly="readonly">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">


                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                            <button type="button" class="btn btn-info text-center invoice-form-submit" disabled="disabled">
                                <?php echo "Payment" ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>




















        </div>
            <div class="table-responsive s_table">
                <table class="table invoice-items-table items table-main-invoice-edit no-mtop">
                    <thead>
                    <tr>
                        <th class="text-left"><?php echo _l('invoice_table_item_heading'); ?></th>
                        <?php
                        $qty_heading = _l('invoice_table_quantity_heading');
                        if(isset($invoice) && $invoice->show_quantity_as == 2 || isset($hours_quantity)){
                            $qty_heading = _l('invoice_table_hours_heading');
                        } else if(isset($invoice) && $invoice->show_quantity_as == 3){
                            $qty_heading = _l('invoice_table_quantity_heading') .'/'._l('invoice_table_hours_heading');
                        }
                        ?>
                        <th class="text-left qty"><?php echo $qty_heading; ?></th>
                        <th class="text-left"><?php echo _l('invoice_table_amount_heading'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="col-md-12">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel-body">
                <div style="display: none;">
                    <?php $value = (isset($invoice) ? clear_textarea_breaks($invoice->clientnote) : get_option('predefined_clientnote_invoice')); ?>
                    <?php echo render_textarea('clientnote','invoice_add_edit_client_note',$value,array(),array(),'mtop15'); ?>
                    <?php $value = (isset($invoice) ? clear_textarea_breaks($invoice->terms) : get_option('predefined_terms_invoice')); ?>
                    <?php echo render_textarea('terms','terms_and_conditions',$value,array(),array(),'mtop15'); ?>
                    <?php $value = (isset($invoice) ? $invoice->adminnote : ''); ?>
                    <?php echo render_textarea('adminnote','invoice_add_edit_admin_note',$value); ?>
                </div>









                <div class="row">
                    <div class="col-md-12" style="background-color: #373942;">

                        <div class="row" style="margin: 0px; font-weight: bold; color: #FFF; padding-top: 5px; font-size: 13px;">
                            <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                <table style="border-collapse: collapse;" height="auto" border="0" width="100%">
                                    <tbody>
                                    <tr>
                                        <td style="font-size: 12px;" height="25px" width="25%">Total Items :</td>
                                        <td align="right" height="25px" width="25%">
                                            <div id="pos_total_item_qty">0</div>
                                        </td>
                                        <td align="right" height="25px" width="25%">
                                            Total :
                                        </td>
                                        <td align="right" height="25px" width="25%">
                                            <div id="pos_subtotal">00.00</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px;" height="25px" width="25%"></td>
                                        <td align="right" height="25px" width="25%">
                                        </td>
                                        <td align="right" height="25px" width="25%">
                                            tax :
                                        </td>
                                        <td align="right" height="25px" width="25%">
                                            <div id="pos_tax_amt">00.00</div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row" style="margin: 0px; font-weight: bold; color: #FFF; padding-top: 7px; padding-bottom: 7px; font-size: 13px; border-top: 1px solid #dddddd;">
                            <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                <table style="border-collapse: collapse;" height="auto" border="0" width="100%">
                                    <tbody><tr>
                                        <td height="30px" width="50%">Total Payable :</td>
                                        <td align="right" height="30px" width="50%">
                                            <div id="total_payable">00.00</div>
                                        </td>
                                    </tr>
                                    </tbody></table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mtop15">
                    <div class="col-xs-4">
                        <button class="btn btn-danger text-center invoice-form-cancel" style="width: 100%" type="button">
                            <?php echo "Cancel"; ?>
                        </button>
                    </div>
                    <div class="col-xs-4">
                        <button type="button" class="btn btn-default text-center invoice-form-submit save-as-draft" style="width: 100%" disabled="disabled">
                            <?php echo "Hold"; ?>
                        </button>
                    </div>
                    <div class="col-xs-4">
                        <button type="button" class="btn btn-success pos-payment" style="width: 100%" data-toggle="modal" data-target="#pos_invoice_modal" disabled="disabled"><?php echo "Payment" ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php echo form_close(); ?>

<table style="display: none">
    <tr id="invoice-row-template" class="item">
        <td class="">
            <input type="hidden" name="order" class="order form-control" >
            <input type="hidden" name="description" class="description form-control" >
            <input type="hidden" name="long_description" class="long_description form-control" >
            <input type="hidden" name="unit" class="unit form-control" >
            <input type="hidden" name="id" class="id form-control" >

            <div style="display: none">
                <?php
                $default_tax = get_option('default_tax');
                $default_tax_name = '';
                $default_tax_name = explode('+',$default_tax);
                $select = '<select class="tax main-tax" name="taxname" multiple style="display: none;">';
                $no_tax_selected = '';
                if($default_tax == ''){
                }
                $select .= '<option value="">'._l('no_tax').'</option>';
                foreach($taxes as $tax){
                    $select .= '<option value="'.$tax['name'].'|'.$tax['taxrate'].'"'.' data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-taxid="'.$tax['id'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';
                }
                $select .= '</select>';
                echo $select;
                ?>
            </div>
            <div class="item-name"></div>
        </td>
        <td style="display: none;" class="rate">
            <input type="hidden" name="rate" class="rate form-control" >
        </td>
        <td>
            <input type="number" name="qty" min="0" value="1" data-quantity="1" class="form-control qty" placeholder="<?php echo _l('item_quantity_placeholder'); ?>">
        </td>
        <td class="amount">

        </td>
        <td><a href="#" class="btn btn-danger pull-left remove-item-row"><i class="fa fa-trash"></i></a></td>
    </tr>
</table>