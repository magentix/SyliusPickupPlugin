var pickupClass = function (options) {
    var vars = {};

    this.construct = function (options) {
        var pickup = this;

        pickup.addOptions(options);

        var shippingMethods = $('#sylius-shipping-methods');
        var selected = shippingMethods.find('input.pickup:checked');

        shippingMethods.find('input').click(function () {
            pickup.remove();
        });

        if (selected.hasClass('pickup')) {
            pickup.list(selected, selected.attr('tabindex'), selected.attr('value'), null, null);
        }

        shippingMethods.find('input.pickup').click(function () {
            pickup.list($(this), $(this).attr('tabindex'), $(this).attr('value'), null, null);
        });
    };

    this.addOptions = function (options) {
        $.extend(vars, options);
    };

    this.list = function(item, index, method, postcode, countryCode) {
        var pickup = this;
        var form   = $('form[name="sylius_checkout_select_shipping"]');
        pickup.remove();
        form.addClass('loading');
        $.ajax({
            url: getUrl(method, postcode, countryCode),
            type: 'post',
            context: this,
            data:{'index':index},
            success: function (response) {
                item.closest('.item').after(response);
                form.removeClass('loading');
                pickup.search(item, index, method);
            }
        });
    };

    this.remove = function () {
        $('.pickup-form').remove();
    };

    this.search = function (item, index, method) {
        var pickup = this;
        $('.pickup-address').submit(function (event) {
            event.preventDefault();

            var data = $(this).serializeArray();

            var postcode = null;
            var countryCode = null;

            $.each(data, function (i, field) {
                if (field.name === 'postcode') {
                    postcode = field.value;
                }
                if (field.name === 'country_code') {
                    countryCode = field.value;
                }
            });

            pickup.list(item, index, method, postcode, countryCode);
        });
    };

    var getUrl = function(method, postcode, countryCode) {
        var url = vars.url + '/' + method;
        if (postcode) {
            url += '/' + postcode;
        }
        if (countryCode) {
            url += '/' + countryCode;
        }

        return url;
    };

    this.construct(options);
};
