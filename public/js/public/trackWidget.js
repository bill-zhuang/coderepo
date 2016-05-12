var trackWidget = (function () {
    /*
     * http://stackoverflow.com/questions/105034/create-guid-uuid-in-javascript
     * */
    function generateGUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    return {
        trackUser: function (ajaxUrl) {
            var guid = $.cookie('guid'); //jquery cookie plugin
            if (typeof guid == 'undefined') {
                guid = generateGUID();
                $.cookie('guid', guid, {expires: 1, path: '/'});
            }
            var trackData = {
                'pageUrl': (typeof ajaxUrl == 'undefined') ? location.href : ('http://' + window.location.hostname + ajaxUrl),
                'referrerUrl': (typeof ajaxUrl == 'undefined') ? document.referrer : location.href,
                'userAgent': navigator.userAgent,
                'language': navigator.language,
                'title': document.title,
                'width': $(window).width(),
                'height': $(window).height(),
                'vpWidth': Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
                'vpHeight': Math.max(document.documentElement.clientHeight, window.innerHeight || 0),
                'GUID': guid,
                //server usage, get time & guid, diff last time, < alarm ms, abort
                'time': Math.round(new Date().getTime() / 1000),
                'timezone': new Date().getTimezoneOffset(),
                'colorDepth': screen.colorDepth
            };
            console.log(trackData);
            //create img document, send data
            /*var img = new Image();
            var requestUrl = '/track/track';
            img.src = requestUrl + '?' + $.param(trackData);
            document.body.appendChild(img);*/
        }
    }
}());
