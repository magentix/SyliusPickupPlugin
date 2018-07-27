/**
 * Pickup Class
 *
 * @param {Object} options
 */
var pickupClass = function (options) {
    var vars = {};

    /**
     * Constructor
     *
     * @param {Object} options
     */
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

    /**
     * Add Options
     *
     * @param {Object} options
     */
    this.addOptions = function (options) {
        $.extend(vars, options);
    };

    /**
     * Load Pickup List
     *
     * @param {Element} item
     * @param {int} index
     * @param {string} method
     * @param {string} postcode
     * @param {string} countryCode
     */
    this.list = function(item, index, method, postcode, countryCode) {
        var pickup = this;

        $.ajax({
            url: getUrl(method, postcode, countryCode),
            type: 'post',
            context: this,
            data:{'index':index},
            beforeSend: function() {
                pickup.remove();
                pickup.loading(1);
            },
            success: function (response) {
                item.closest('.item').after(response);
                pickup.loading(0);
                pickup.search(item, index, method);
            },
            error: function() {
                pickup.loading(0);
            }
        });
    };

    /**
     * Add loader
     *
     * @param {int} status
     */
    this.loading = function(status) {
        var form = $('form[name="sylius_checkout_select_shipping"]');

        form.removeClass('loading');
        if (status) {
            form.addClass('loading');
        }
    };

    /**
     * Remove Pickup List
     */
    this.remove = function () {
        $('.pickup-form').remove();
    };

    /**
     * Search trigger
     *
     * @param {Element} item
     * @param {int} index
     * @param {string} method
     */
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

    /**
     * Retrieve Controller URL
     *
     * @param {string} method
     * @param {string} postcode
     * @param {string} countryCode
     * @returns {string}
     */
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
