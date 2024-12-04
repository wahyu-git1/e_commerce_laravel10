// sementara

$(document).ready(function(){

    $("#slider-range").slider({
        range: true,
        min: 0,
        max: 1000000,
        values: [$("#min_price").val(), $("#max_price").val()],
        slide: function (event, ui) {
            $("#amount").val(ui.values[0] + " - " + ui.values[1]);
        }
    });
    $("#amount").val($("#slider-range").slider("values", 0) +
        " - " + $("#slider-range").slider("values", 1));

    $('.delivery-address').change(function(){
        $('.courier-code').removeAttr('checked');
        $('.available-services').hide();
    });
    
    console.log("main.js is loaded");
    $('.courier-code').change(function(){
        // console.log("Click detected");
        // alert('sds');
        let courier = $(this).val();
        let addressID = $('.delivery-address:checked').val();

        // console.log(courier);
        // console.log(addressID)
        
        $.ajax({
            url: "/orders/shipping-fee",
            method: "POST",
            data: {
                address_id: addressID,
                courier: courier,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(result) {
                $('.available-services').show();
                $('.available-services').html(result);
                console.log("success/tampil");  
            },
            error: function(e) {
                console.log(e);
            }
        })

    });
});
