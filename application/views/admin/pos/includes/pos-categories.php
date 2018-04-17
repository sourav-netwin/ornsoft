<div class="panel" style="margin-bottom: 0px;">
    <div class="panel-body">

        <div class="row mb-md" style="margin-bottom: 16px;">
            <div class="col-md-12">
                <input type="text" id="product-search" class="form-control" placeholder="Search Product by Name OR Code">

            </div>
        </div>

        <div id="carousel-categories-wrapper">
            <?php
            $parentGroup = 0;
            $tmp = [];
            foreach ($items_groups as $items_group){
                if ($items_group['parent']){
                    $tmp[$items_group['parent']]['chields'][$items_group['id']] = $items_group;
                }else{
                    if (isset($tmp[$items_group['id']])){
                        $tmp[$items_group['id']]= array_merge($tmp[$items_group['id']], $items_group);
                    }else{
                        $tmp[$items_group['id']] = $items_group;
                    }
                }
            }
            $items_groups = $tmp;
            include(APPPATH . 'views/admin/pos/includes/pos-categories-carousel.php');
            ?>
            <?php foreach ($tmp as $items_group){ ?>
                <?php if (isset($items_group['chields']) && count($items_group['chields'])){ ?>
                    <div class="sub-group sub-group-<?php echo $items_group['id'];?>" style="display: none">
                        <?php $items_groups = $items_group['chields'];?>
                        <?php $parentGroup = $items_group['id'];?>
                        <?php include(APPPATH . 'views/admin/pos/includes/pos-categories-carousel.php'); ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>