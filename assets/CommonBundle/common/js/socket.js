(function ($) {
    var socketConnectTimeout;

    var defaults = {
        name: 'webSocket',
        url: '',
        open: function(){},
        error: function(){},
        message: function(){}
    };

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);

            url = options.url;
            if (url[0] == ':') {
                url = document.location.host + url;
            }

            if (location.protocol != 'https:') {
                var ws = new WebSocket('ws://' + url);
            } else {
                var ws = new WebSocket('wss://' + url);
            }

            clearTimeout(socketConnectTimeout);
            socketConnectTimeout = setTimeout(
                function () {
                    ws.close();
                },
                5000
            );

            $(ws)
                .bind('open', function (e) {
                    clearTimeout(socketConnectTimeout);
                    settings.open(e);
                })
                .bind('close', function (e) {
                    clearTimeout(socketConnectTimeout);
                    socketConnectTimeout = setTimeout(function () {$.webSocket(settings)}, 1000);
                    settings.error(e);
                })
                .bind('error', function (e) {
                    clearTimeout(socketConnectTimeout);
                    settings.error(e);
                })
                .bind('message', function (e) {
                    clearTimeout(socketConnectTimeout);
                    if (e.originalEvent.type == 'message' && e.originalEvent.data)
                        settings.message(e.originalEvent, $.parseJSON(e.originalEvent.data));
                });

            $(window).on('beforeunload', function() {
                $.webSocket('close', settings);
            });

            $(document).data(settings.name, ws);
            return this;
        },
        send : function (data) {
            var socket = $(document).data(data.name);
            if (socket != undefined)
                socket.send(data.text);
            return this;
        },
        close : function (data) {
            var socket = $(document).data(data.name);
            if (socket != undefined)
                $(document).data(data.name).close();
            $(document).removeData(data.name);
            return this;
        }
    };

    $.extend({
        webSocket: function ( method ) {
            if ( methods[ method ] ) {
                return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, arguments );
            } else {
                $.error( 'Method ' +  method + ' does not exist on $.webSocket' );
            }
        }
    });
}) (jQuery);
