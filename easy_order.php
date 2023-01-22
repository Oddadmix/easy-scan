<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class='container'>
        <div class='row'>
            <div class='col-md-12'>
                <h1>Easy Order</h1>
                <div class='row'>
                    <div class='col-6'>
                        <input type="text" name="scan" id="scanInput" value="" placeholder="Scan the bardcode" />
                    </div>
                    <div class='col-6'>
                        <label for="exampleFormControlSelect1">Quantity</label>
                        <select id="quantity">
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                </div>




            </div>

            <div class="col-m1-12 mt-3">
                <div class='row'>
                    <div class="col-md-12">
                        <h3>Order Items</h3>
                        <ul id="products" class="list-group">

                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-5">
                <button type="button" id="place-order" class="btn btn-primary">Place Order</button>
                <button type="button" id="reset-order" class="btn btn-primary">Reset Order</button>
            </div>

        </div>

        <script type="text/javascript">
            let product = {};
            let products = [];


            jQuery('#products').on('click', '.quantity-right-plus', function(e) {
                e.preventDefault();
                const id = jQuery(e.currentTarget).data('id');
                const quantity = parseInt(jQuery(`.input-quantity[data-id="${id}"]`).val());
                jQuery(`.input-quantity[data-id="${id}"]`).val(quantity + 1);
                products.find((product) => product.id === id).quantity = quantity + 1;
            });

            jQuery('#products').on('click', '.quantity-left-minus', function(e) {
                e.preventDefault();
                const id = jQuery(e.currentTarget).data('id');
                const quantity = parseInt(jQuery(`.input-quantity[data-id="${id}"]`).val());
                if (quantity > 0) {
                    jQuery(`.input-quantity[data-id="${id}"]`).val(quantity - 1);
                    products.find((product) => product.id === id).quantity = quantity - 1;
                }
            });

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

            jQuery('#reset-order').click(function() {
                products = [];
                jQuery('#products').html('');
                jQuery('#scanInput').val('');
                jQuery('#scanInput').focus();
            });

            jQuery('#place-order').click(function() {
                var data = {
                    'action': 'place_order',
                    'products': products
                };

                jQuery.post(ajaxurl, data, function(response) {
                    alert("Order complete");
                    products = [];
                    jQuery('#products').html('');
                    jQuery('#scanInput').val('');
                    jQuery('#scanInput').focus();
                }).fail(function() {
                    alert("No product found");
                })
            });

            jQuery(document).ready(function() {
                jQuery('#scanInput').focus();
            });

            const populateProducts = () => {
                jQuery('#products').html('');
                products.forEach((product, index) => {
                    jQuery('#products').append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                    <img src="${product.image_src}" class="card-img-top" style="max-width:50px;"/>
                        (${product.id}) ${product.title}
                        <div class="input-group">
                                    <span class="input-group-btn">
                                        <button type="button" class="quantity-left-minus btn btn btn-light" data-id="${product.id}"  data-type="minus" data-field="">
                                            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11.5 0c6.347 0 11.5 5.153 11.5 11.5s-5.153 11.5-11.5 11.5-11.5-5.153-11.5-11.5 5.153-11.5 11.5-11.5zm0 1c5.795 0 10.5 4.705 10.5 10.5s-4.705 10.5-10.5 10.5-10.5-4.705-10.5-10.5 4.705-10.5 10.5-10.5zm-6.5 10h13v1h-13v-1z"/></svg>
                                        </button>
                                    </span>
                                    <input type="text" data-id="${product.id}" name="quantity" class="input-quantity mx-1" value="${product.quantity}" min="1" max="100">
                                    <span class="input-group-btn">
                                        <button type="button" data-id="${product.id}" class="quantity-right-plus btn btn-light" data-type="plus" data-field="">
                                            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11.5 0c6.347 0 11.5 5.153 11.5 11.5s-5.153 11.5-11.5 11.5-11.5-5.153-11.5-11.5 5.153-11.5 11.5-11.5zm0 1c5.795 0 10.5 4.705 10.5 10.5s-4.705 10.5-10.5 10.5-10.5-4.705-10.5-10.5 4.705-10.5 10.5-10.5zm.5 10h6v1h-6v6h-1v-6h-6v-1h6v-6h1v6z"/></svg>
                                        </button>
                                    </span>
                        </div>
                    </li>
                    `)
                });
            }

            jQuery("#scanInput").keyup(debounce(() => {
                const sku = jQuery('#scanInput').val();
                if (sku == '') return;
                var data = {
                    'action': 'filter_products',
                    'sku_universel': jQuery('#scanInput').val()
                };

                const toAdd = jQuery('#quantity').val();

                jQuery.post(ajaxurl, data, function(response) {
                    product = JSON.parse(response);
                    const foundProduct = products.find((p) => p.id == product.id);
                    if (foundProduct) {
                        foundProduct.quantity = parseInt(foundProduct.quantity) + parseInt(toAdd);
                    } else {
                        product.quantity = parseInt(toAdd);
                        products.push(product);
                    }

                    populateProducts();
                    jQuery('#scanInput').val('');
                    jQuery('#scanInput').focus();
                }).fail(function() {
                    alert("No product found");
                })
            }, 200));
        </script>

</body>


</html>