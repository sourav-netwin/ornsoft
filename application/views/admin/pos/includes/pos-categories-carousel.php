<div class="groups_buttons">
    <?php $split = 7; ?>
    <?php $buttonNo = 0; ?>
    <?php (isset($parentGroup))?:array_unshift($items_groups, array('id'=>0, 'name'=>"All")) ?>
    <div id="carousel-categories-<?php echo $parentGroup;?>" class="carousel slide" data-ride="carousel" data-interval="99999999" data-pause="true">
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <?php foreach ($items_groups as $k => $items_group){?>
                    <?php if($buttonNo && $buttonNo%$split == 0){ echo '</div><div class="item">'; }?>
                    <div id="groups_button-<?php echo $items_group['id'];?>" class="btn btn-info pull-left groups_button" data-group_id="<?php echo $items_group['id'];?>"
                         data-group_ids='<?php echo (json_encode( isset($items_group['chields'])?array_merge(array_keys($items_group['chields']), [$items_group['id']]):[$items_group['id']])); ?>'>
                        <?php echo $items_group['name']; ?>
                    </div>
                    <?php $buttonNo++;?>
                <?php } ?>
            </div>
        </div>
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <?php $buttonNo = 0; ?>
            <?php $slideNo = 0; ?>
            <?php foreach ($items_groups as $k => $items_group){?>
                <?php if($buttonNo%$split == 0){ ?>
                    <li data-target="#carousel-categories-<?php echo $parentGroup;?>" data-slide-to="<?php echo $slideNo;?>" class="<?php echo ($buttonNo==0)?'active':'';?>"></li>
                    <?php $slideNo++;?>
                <?php } ?>
                <?php $buttonNo++;?>
            <?php }?>
        </ol>
        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-categories-<?php echo $parentGroup;?>" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#carousel-categories-<?php echo $parentGroup;?>" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>