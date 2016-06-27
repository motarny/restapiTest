var Locations = {

    locationOnEdit: '',


    getList: function (params) {
        var params = (params ? params : []);
        Request.prepare('locations', '', 'GET', params['post'], params['get'], this.printLocationsList);
    },


    getListBySearch: function () {
        var requestParams = [];

        var qText = document.getElementById('form_search_text').value;
        var qDistance_from_hq = document.getElementById('form_search_distance').value * 1000; // przekazujemy km, operujemy na metrach

        requestParams['get'] = 'text=' + qText + '&distance_from_hq=' + qDistance_from_hq;

        this.getList(requestParams);
    },


    printLocationsList: function (response) {
        document.getElementById('locations_list_container').innerHTML = '';
        var locationsArray = response.value.locations;

        var htmlOutput = '';

        for (i = 0, len = locationsArray.length; i < len; ++i) {
            var locationParams = locationsArray[i];

            var newRow = '<div class="location-row">';

            if (locationParams.is_headquarters == true) {
                var labelClass = 'hq';
            } else {
                var labelClass = '';
            }

            newRow += '<label class="' + labelClass + '">' + locationParams.description + '</label>';
            newRow += '<div class="address">' + locationParams.address + '</div>';
            newRow += '<div class="geo"><label>geo dł x sz:</label> ' + locationParams.longitude + ' x ' + locationParams.latitude + '</div>';
            newRow += '<div class="distance"><label>odległość od HQ pieszo:</label> ' + locationParams.distance_from_hq + 'm</div>';
            newRow += '<div class="actions"><button onclick="Locations.openToEdit(' + locationParams.id + ')">edytuj</button> ';
            newRow += '<button onclick="Locations.deleteLocation(' + locationParams.id + ')">usuń</button></div>';

            newRow += '</div>';

            htmlOutput = htmlOutput + newRow;
        }

        document.getElementById('locations_list_container').innerHTML = htmlOutput;
    },


    openToEdit: function (locationId) {
        Request.prepare('locations', locationId, 'GET', '', '', this.fillEditForm);
    },


    fillEditForm: function (response) {
        var locationParams = response.value;

        document.getElementById('form_description').value = locationParams.description;
        document.getElementById('form_address').value = locationParams.address;
        document.getElementById('form_latitude').value = locationParams.latitude;
        document.getElementById('form_longitude').value = locationParams.longitude;

        document.getElementById('form_button_label').innerHTML = 'Zapisz edytowany';

        Locations.locationOnEdit = locationParams.id;
    },


    saveForm: function () {
        postData = '';

        // todo opcjonalnie można spróbować z FormData()

        postData += 'description=' + encodeURIComponent(document.getElementById('form_description').value);
        postData += '&address=' + encodeURIComponent(document.getElementById('form_address').value);
        postData += '&latitude=' + encodeURIComponent(document.getElementById('form_latitude').value);
        postData += '&longitude=' + encodeURIComponent(document.getElementById('form_longitude').value);

        Request.prepare('locations', Locations.locationOnEdit, 'POST', postData, '', function() {
            Locations.getList();
        });
    },


    deleteLocation: function(locationId) {
        Request.prepare('locations', locationId, 'DELETE', '', '', function() {
            Locations.getList();
        });
    },


    clearEditForm: function() {
        document.getElementById('form_description').value = '';
        document.getElementById('form_address').value = '';
        document.getElementById('form_latitude').value = '';
        document.getElementById('form_longitude').value = '';
        document.getElementById('form_button_label').innerHTML = 'Dodaj nową lokalizację'; // tak, wiem że to słabe...
        Locations.locationOnEdit = '';
    }


}