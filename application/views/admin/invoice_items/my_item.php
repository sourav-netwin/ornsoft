<div class="modal fade" id="sales_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('invoice_item_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('invoice_item_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/invoice_items/manage',array('id'=>'invoice_item_form')); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <?php echo render_input('code','invoice_item_add_edit_code'); ?>
                            </div>
                            <div class="col-md-4">
                                <img id="barcode" class="img-responsive" src="" alt="barcode">
                            </div>
                        </div>
                        <?php echo render_input('cost','invoice_item_add_edit_cost'); ?>
                        <?php echo render_input('quantity','invoice_item_add_edit_quantity'); ?>
                        <hr>

                        <?php echo render_input('description','invoice_item_add_edit_description'); ?>
                        <?php echo render_textarea('long_description','invoice_item_long_description'); ?>
                        <?php echo render_input('rate','invoice_item_add_edit_rate','','number'); ?>
                        <div class="form-group">
                            <label class="control-label" for="tax"><?php echo _l('invoice_item_add_edit_tax'); ?></label>
                            <select class="selectpicker display-block" data-width="100%" name="tax" title='<?php echo _l('invoice_item_add_edit_tax_select'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <?php foreach($taxes as $tax){ ?>
                                <option value="<?php echo $tax['id']; ?>" data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix mbot15"></div>
                         <?php echo render_input('unit','unit'); ?>

                        <?php echo render_select('group_id',$items_groups,array('id','name'),'item_group'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>

            <?php echo form_open_multipart(admin_url('invoice_items/upload_file/'),array('class'=>'dropzone','id'=>'project-files-upload')); ?>
            <?php echo form_hidden('itemid'); ?>
            <input type="file" name="file" multiple />
            <?php echo form_close(); ?>

        </div>
    </div>
</div>