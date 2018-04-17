<div class="products">
    <?php foreach ($items as $groupId => $items){?>
        <div class="group-<?php echo $groupId;?>">

                <?php foreach ($items as $item){?>
                    <div class="col-md-3 product" data-group="<?php echo $groupId;?>"
                         data-product-name="<?php echo $item['description'];?>"
                         data-product-code="<?php echo $item['code'];?>"
                         data-product-rate="<?php echo $item['rate'];?>"
                         data-product-quantity="<?php echo $item['quantity'];?>"
                         data-product-id="<?php echo $item['id'];?>"
                         data-product-unit="<?php echo $item['unit'];?>"
                         data-product-tax="<?php echo $item['tax'];?>" >
                        <div class="panel panel-primary">
                            <div class="panel-body bg-primary">
                                <div class="img-wrapper">
                                    <?php echo ($item['image'] != '')? '<img class="img-responsive" src="' . base_url('uploads/invoices_items_images/' . $item['image']) . '" class="img-responsive" alt="' . $item['description'] . '"></a>' :'';?>
                                </div>
                                <div>
                                    [<?php echo $item['code'];?>]
                                </div>
                                <div>
                                    <?php echo $item['description'];?>
                                </div>
                                <div>
                                    <?php echo $item['rate'];?>
                                    
                                    <?php //echo "<div><pre>";
                                    //print_r($item);
                                    //echo "</pre></div>";?>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
    <?php } ?>
</div>
