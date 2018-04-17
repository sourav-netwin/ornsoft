<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">

            <div class="col-md-4">
                <?php
                include_once(APPPATH . 'views/admin/pos/includes/pos-left.php');
                ?>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <?php
                    include_once(APPPATH . 'views/admin/pos/includes/pos-categories.php');
                    ?>
                    <?php
                    include_once(APPPATH . 'views/admin/pos/includes/pos-products.php');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(function(){

        $('body').addClass('hide-sidebar');

        $('#pcodeBarcode').on('keyup', function(){
            if(addToCart($(this).val())){
                $(this).val('');
            }
        });

        $('#product-search').on('keyup', function(){
            var searchFor = $(this).val();
            if(searchFor == ''){
                $('.products .product').show(); //display all of them
            }else{
                $('.products .product').hide();
                $('.products').find('.product[data-product-name*="'+searchFor+'"]').show(); //display all of them
                $('.products').find('.product[data-product-code*="'+searchFor+'"]').show(); //display all of them
            }
            selectGroup(0);
        });

        $('.groups_buttons').on('click', '.groups_button', function(){
            var groupId = $(this).data('group_id');
            var groupIds = $(this).data('group_ids');
            if(groupIds){
                $('.products .product').hide();

                $.each(groupIds, function (index, groupId){
                    $('.products').find('.product[data-group="'+groupId+'"]').show(); //display all of them
                });
            }else{
                $('.products .product').show(); //display all of them
            }

            if ($(this).parents('.sub-group').length==0) {
                $('.sub-group').hide();
                $('.sub-group-' + groupId).show();
            }
            selectGroup(groupIds);
        });


        $('.products').on('click', '.product', function(){
            var productCode = $(this).data('product-code');
            addToCart(productCode);
        });

        $('.invoice-items-table').on('change', '.qty', function(){
            calculate_pos_total();
        });

        $('input[name="discount_percent"]').on('change', function(){
            calculate_pos_total();
        });
        $('input[name="adjustment"]').on('change', function(){
            calculate_pos_total();
        });

        $('.invoice-items-table').on('click', '.remove-item-row',  function(){
            var element = $(this);
            element.parents('tr').first().addClass('animated fadeOut', function() {
                setTimeout(function() {
                    element.parents('tr').first().remove();
                    calculate_pos_total();
                }, 50)
            });
        });

        $('#paid_amount').on('change keyup', function(){

            var total = parseFloat($('.total input').val());
            var change = parseFloat($(this).val()) - total;
            $('#return_change').val(change.toFixed(decimal_places));

            if (change < 0){
                $('.invoice-form-submit').prop('disabled', true);
                $('#return_change').parents('.form-group').addClass("has-error");
            } else{
                $('.invoice-form-submit').prop('disabled', false);
                $('#return_change').parents('.form-group').removeClass("has-error");
            }
        });

        $('.invoice-form-cancel').on('click', function(){
            $('.invoice-items-table').find('.item').remove();
            calculate_pos_total();
        });

        $('#pcodeBarcode').focus();


        $('#invoice-form').on('submit', function(){

            //block for multiple submits??
            var form = $(this);
            var data = $(form).serialize();
            $.post(form.attr('action'), data).done(function(htmlResponse) {
                var json = $.parseJSON(htmlResponse);
                window.open("<?php echo admin_url('invoices/pdf/'); ?>"+json.id);
                $('#pos_invoice_modal').modal('hide');
            });
            $('.invoice-items-table').find('.item').remove();

            $('#paid_amount').val('0');

            calculate_pos_total();

            return false;
        });


        function selectGroup(groupId){
            if (typeof groupId == 'undefined' || groupId == '' ){
                groupId = 0;
            }

            $('.groups_buttons .groups_button').removeClass('btn-success').addClass('btn-info');
            if (typeof groupId == 'number'){
                $('.groups_buttons #groups_button-'+groupId).addClass('btn-success').removeClass('btn-info');
            }else{
                $.each(groupId, function (index, groupId) {
                    $('.groups_buttons #groups_button-'+groupId).addClass('btn-success').removeClass('btn-info');
                })
            }

        }

        function addToCart(productCode) {
            var selectedProduct = $('.products').find('.product[data-product-code="'+productCode+'"]').first();
            if (selectedProduct.length) {
                var productsTable = $('.invoice-items-table');
                var rowId = 'prod-' + productCode;

                var prodRow = productsTable.find('#' + rowId);

                if (prodRow.length == 0) {
                    var index = productsTable.find('tr').length;

                    var template = $('#invoice-row-template').clone();
                    $('.invoice-items-table').append(template);

                    template.attr('id', rowId);
                    template.find('.item-name').html(selectedProduct.data('product-name'));

                    //set the hidden inputs!!
                    template.find('input.description').val(selectedProduct.data('product-name')).attr('name', 'newitems[' + index + '][description]');
                    template.find('input.long_description').val(selectedProduct.data('product-name')).attr('name', 'newitems[' + index + '][long_description]');
                    template.find('input.rate').val(selectedProduct.data('product-rate')).attr('name', 'newitems[' + index + '][rate]');
                    template.find('input.order').val(index).attr('name', 'newitems[' + index + '][order]');

                    template.find('input.qty').val(1).attr('name', 'newitems[' + index + '][qty]');
                    template.find('input.unit').val(selectedProduct.data('product-unit')).attr('name', 'newitems[' + index + '][unit]');
                    template.find('input.id').val(selectedProduct.data('product-id')).attr('name', 'newitems[' + index + '][id]');

                    template.find('select.tax').attr('name', 'newitems[' + index + '][tax]').find('option[data-taxid="' + selectedProduct.data('product-tax') + '"]').prop('selected', true);


                } else {
                    prodRow.find('.qty').val(parseFloat(prodRow.find('.qty').val()) + 1)
                }
                calculate_pos_total();
                return true;
            }else{
                return false;
            }
        }

        function calculate_pos_total(){
            calculate_total();
            var totalItems = 0;
            $.each($('.invoice-items-table input.qty'), function(index, input){
                totalItems += parseInt($(input).val());
            });

            $('#pos_total_item_qty').html(totalItems);
            $('#pos_subtotal').html($('.subtotal').text());
            $('#pos_tax_amt').html(  (parseFloat($('.total input').val()) - parseFloat($('.subtotal input').val())).toFixed(decimal_places)  );
            $('#total_payable').html($('.total').text());

            if(totalItems > 0){
                $('.save-as-draft').prop('disabled', false);
                $('.pos-payment').prop('disabled', false);
            }else{
                $('.save-as-draft').prop('disabled', true);
                $('.pos-payment').prop('disabled', true);
            }
            $('#paid_amount').change();
        }
    });
</script>
</body>
</html>
