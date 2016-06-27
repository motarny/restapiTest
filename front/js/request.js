var Request = {

    requestUri: '../app/api.php/',

    prepare: function (resourcesName, resourceId, method, requestParams, urlParams, callbackFunction) {
        if (typeof requestParams == 'undefined') {
            requestParams = [];
        }
        if (typeof urlParams == 'undefined') {
            urlParams = '';
        }
        var request = new XMLHttpRequest();
        request.open(method, this.requestUri + resourcesName + '/' + resourceId + '?' + urlParams, true);
        request.setRequestHeader('Accept', 'application/json');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        request.send(requestParams);

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.responseText) {
                var responseObj = JSON.parse(request.responseText);
                callbackFunction(responseObj);
                request.abort();
            }
        }
    }
}