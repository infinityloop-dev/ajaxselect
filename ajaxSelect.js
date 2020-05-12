(function ($) {
    $.fn.ajaxSelectBox = function (options) {
        return this.each(function() {
            var options = Object.assign({}, selectOptions);
            var select = $(this);
            if (select.data('ajaxselect')) {
                options = Object.assign(options, {
                    tokenSeparators: [','],
                    ajax: {
                        url: select.data('ajaxselect'),
                        delay: 250,
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data, params) {
                            return {
                                results: data
                            };
                        }
                    }
                });
            }
            if (select.data('onchange')) {
                var currentQuery;
                select.on('select2:open', function() {
                    setTimeout(function() {
                        if(currentQuery && currentQuery.length) {
                            $('.select2-search input').val(currentQuery).trigger('input');
                        };
                    }, 0);
                }).on('select2:closing', function() {
                    currentQuery = $('.select2-search input').prop('value');
                }).on('change', function(e) {
                    $.nette.ajax({
                        method: 'GET',
                        url: select.data('onchange'),
                        data: {
                            s: $(this).val(),
                        }
                    });
                });
            }
            select.select2(options);
            select.closest('form').on('reset', function (e) { select.change();});
        });
    }
})(jQuery);