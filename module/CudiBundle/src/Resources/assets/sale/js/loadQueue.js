;(function ($) {
	$.loadQueue = function (options) {
		var nbTries = 0;
		
		createSocket();
	
		function createSocket() {
			try {
				var socket = new WebSocket(options.url);
			
				socket.onopen = function (msg) {
					nbTries = 0;
				};
				socket.onmessage = function (e) {
					if (!e.data)
						return;
					
					options.loaded($.parseJSON(e.data));
				};
				socket.onclose = recreateSocket;
				socket.onerror = options.error;
			} catch (ex) {
				recreateSocket();
			};	
		};
		
		function recreateSocket() {
			options.error();
			setTimeout(createSocket, Math.min(nbTries*200, 5000));
			nbTries++;
		}
	};
}) (jQuery)