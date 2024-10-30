CrossWindowCommunication = function (iframe) {
    var self = this;
    self.listners = [];
    self.init = function () {

        // Listen to messages from parent window
        bindEvent(window, 'message', function (e) {
            if (!e || !e.data) return;

            console.log('Received', e.data);

            var data = JSON.parse(e.data);
            triggerListner(data.action, data.data);
        });
    };

    self.on = function (actionName, callBack) {
        self.listners.push({
            'key': actionName,
            'fn': callBack
        });
    };

    function triggerListner(action, data) {
        for (var i = 0; i < self.listners.length; i++) {
            if (self.listners[i].key == action) {
                self.listners[i].fn(data);
            }
        }
    }

    // addEventListener support for IE8
    function bindEvent(element, eventName, eventHandler) {
        if (element.addEventListener) {
            element.addEventListener(eventName, eventHandler, false);
        } else if (element.attachEvent) {
            element.attachEvent('on' + eventName, eventHandler);
        }
    }

    self.postMessage = function (actionName, data) {

        // Make sure you are sending a string, and to stringify JSON
        var message = JSON.stringify({ action: actionName, data: data });

        if (typeof (iframe) !== "undefined" && iframe.contentWindow) {
            iframe.contentWindow.postMessage(message, '*');
        } else {
            window.parent.postMessage(message, '*');
        }


    };

    self.init();
};