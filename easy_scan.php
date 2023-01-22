<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class='container'>
        <div class='row'>
            <div class='col-md-12'>
                <h1>Easy Scan</h1>
                <input type="text" name="scan" id="scanInput" value="" placeholder="Scan the bardcode" />
            </div>

            <div class="col-m1-12 mt-3">
                <div class="card" id="product" style="display:none;width: 18rem;">
                    <img src="..." class="card-img-top" alt="...">
                    <div class="card-body">
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        <p id="stock">0</p>
                    </div>
                </div>

            </div>

            <div class="col-md-12 mt-5">

                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" value="nothing" id="btnradio1" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="btnradio1">Open Product</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" value="remove" autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnradio2">Stock Qty (-1)</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio3" value="add" autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnradio3">Stock Qty (+1)</label>
                </div>
            </div>

        </div>

        <script type="text/javascript">
            let product = {};

            function debounce(func, wait, immediate) {
                var timeout;
                return function() {
                    var context = this,
                        args = arguments;
                    var later = function() {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    var callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            };

            jQuery(document).ready(function() {
                jQuery('#scanInput').focus();
            });

            const populateProduct = (product) => {
                jQuery('.card-img-top').attr('src', product.image_src);
                jQuery('.card-text').html(`(${product.id}) ${product.title}`);
                jQuery('#stock').html(product.quantity);
                jQuery('#product').show();
                jQuery('#scanInput').val('');
                jQuery('#scanInput').focus();
            }

            const doAction = () => {
                const action = jQuery('input[name="btnradio"]:checked').val();
                console.log(action);
                if (action == 'nothing') return;
                if (action == 'remove') {
                    product.quantity--;
                }
                if (action == 'add') {
                    product.quantity++;
                }
                jQuery('#stock').html(product.quantity);
                var data = {
                    'action': 'update_product_quantity',
                    'product_id': product.id,
                    'quantity': product.quantity,
                    'action_type': action
                };

                jQuery.post(ajaxurl, data, function(response) {
                    product = JSON.parse(response);
                    console.log(product);
                    populateProduct(product);
                });
            }

            jQuery("#scanInput").keyup(debounce(() => {
                const sku = jQuery('#scanInput').val();
                if (sku == '') return;
                var data = {
                    'action': 'filter_products',
                    'sku_universel': jQuery('#scanInput').val()
                };

                jQuery.post(ajaxurl, data, function(response) {
                    product = JSON.parse(response);
                    console.log(product);
                    populateProduct(product);
                    doAction();
                }).fail(function() {
                    alert("No product found");
                })
            }, 200));
        </script>

</body>


</html>