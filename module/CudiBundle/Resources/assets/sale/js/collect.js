(function ($) {
    var defaults = {
        tCurrentCustomer: 'Current Customer',
        tComments: 'Comments',
        tQueue: 'Queue',
        tConclude: 'Finish',
        tCancel: 'Cancel',

        articleTypeahead: '',
        membershipArticles: [{'id': 0, 'barcode': 0, 'title': '', 'price': 0}],
        lightVersion: false,

        saveComment: function (id, comment) {},
        showQueue: function () {},
        finish: function (id, articles) {},
        cancel: function (id) {},
        translateStatus: function (status) {return status;},
        addArticle: function (id, articleId) {},
    };

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            settings.isSell = false;
            settings.conclude = settings.finish;

            var $this = $(this);
            $(this).data('collectSettings', settings);

            return this;
        },
        show : function (data) {
            currentView = 'collect';
            $(this).saleInterface('show', $(this).data('collectSettings'), data);
            return this;
        },
        hide : function (data) {
            $(this).saleInterface('hide');
            return this;
        },
        gotBarcode : function (barcode) {
            $(this).saleInterface('gotBarcode', barcode);
            return this;
        },
        addArticle : function (data) {
            $(this).saleInterface('addArticle', data);
            return this;
        },
    };

    $.fn.collect = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.collect');
        }
    };
})(jQuery);