loadTestMap = function() {
    if ( !this.m_main_map )
        {
        var myOptions = {
                        'center': new google.maps.LatLng(40.7829, -73.9654),
                        'zoom': 10,
                        'mapTypeId': google.maps.MapTypeId.ROADMAP,
                        'mapTypeControlOptions': { 'style': google.maps.MapTypeControlStyle.DROPDOWN_MENU },
                        'zoomControl': true,
                        'mapTypeControl': true,
                        'draggableCursor': "crosshair",
                        'scaleControl' : true
                        };

        myOptions.zoomControlOptions = { 'style': google.maps.ZoomControlStyle.LARGE };

        this.m_main_map = new google.maps.Map(document.getElementById('map_div'), myOptions);

        if ( this.m_main_map ) {
            this.m_main_map.map_marker = null;
            this.m_main_map.circle_overlay = null;
            this.m_main_map.context = this;
    
            google.maps.event.addListener(this.m_main_map, 'click', this.mapClicked);
            google.maps.event.addListener(this.m_main_map, 'double_click', this.mapDoubleClicked);
            google.maps.event.addListener(this.m_main_map, 'zoom_changed', this.mapZoomChanged);
            google.maps.event.addListenerOnce(this.m_main_map, 'tilesloaded', this.mapLoaded);
        };
    };
};

loadTestMap.prototype.m_main_map = null;

loadTestMap.prototype.mapLoaded = function() {
    var myBounds = this.getBounds();
    
    if (myBounds) {
        var northCentral = new google.maps.LatLng(myBounds.getNorthEast().lat(), 0);
        var southCentral = new google.maps.LatLng(myBounds.getSouthWest().lat(), 0);
        var mapHeightInMeters = google.maps.geometry.spherical.computeDistanceBetween(northCentral, southCentral);
        
        var circleOptions = {
                            strokeColor: '#FF0000',
                            strokeOpacity: 0.75,
                            strokeWeight: 1,
                            fillColor: '#FF0000',
                            fillOpacity: 0.25,
                            map: this,
                            center: this.getCenter(),
                            draggable: true,
                            geodesic: true,
                            radius: Math.max(1000, mapHeightInMeters / 20.0)
                            };
                            
        this.circle_overlay = new google.maps.Circle(circleOptions);
        google.maps.event.addListener(this.circle_overlay, 'dragend', circleDragged);        

        function circleDragged(dragEvent) {
            var myMap = this.map;
            var position = this.center;
            myMap.panTo(position);
        };
    };
};

loadTestMap.prototype.mapZoomChanged = function(in_event) {
    var myBounds = this.getBounds();
    
    if (myBounds) {
        var northCentral = new google.maps.LatLng(myBounds.getNorthEast().lat(), 0);
        var southCentral = new google.maps.LatLng(myBounds.getSouthWest().lat(), 0);
        var mapHeightInMeters = google.maps.geometry.spherical.computeDistanceBetween(northCentral, southCentral);
        this.circle_overlay.setOptions({radius: mapHeightInMeters / 10.0});
        this.panTo(this.circle_overlay.position);
    };
};

loadTestMap.prototype.mapClicked = function(clickEvent) {
    var position = new google.maps.LatLng(clickEvent.latLng.lat(), clickEvent.latLng.lng());
    this.circle_overlay.setOptions({center: position});
    this.panTo(position);
    var uri = 'mapDemo.php?resolve_query=' + clickEvent.latLng.lng().toString() + ',' + clickEvent.latLng.lat().toString() + ',' + (this.circle_overlay.radius / 1000.0);
    this.context.ajaxRequest(uri, this.context.requestCallback, 'GET', this.context);
};

loadTestMap.prototype.requestCallback = function (  in_response_object, ///< The HTTPRequest response object.
                                                    in_context
                                                ) {
    if ( in_response_object.responseText ) {
        eval("var new_object = " + in_response_object.responseText + ";");
    };
};

/****************************************************************************************//**
*   \brief A simple, generic AJAX request function.                                         *
*                                                                                           *
*   \returns a new XMLHTTPRequest object.                                                   *
********************************************************************************************/
loadTestMap.prototype.ajaxRequest = function(   url,        ///< The URI to be called
                                                callback,   ///< The success callback
                                                method,     ///< The method ('get' or 'post')
                                                extra_data  ///< If supplied, extra data to be delivered to the callback.
                                            ) {
    /************************************************************************************//**
    *   \brief Create a generic XMLHTTPObject.                                              *
    *                                                                                       *
    *   This will account for the various flavors imposed by different browsers.            *
    *                                                                                       *
    *   \returns a new XMLHTTPRequest object.                                               *
    ****************************************************************************************/
    
    function createXMLHTTPObject()
    {
        var XMLHttpArray = [
            function() {return new XMLHttpRequest()},
            function() {return new ActiveXObject("Msxml2.XMLHTTP")},
            function() {return new ActiveXObject("Msxml2.XMLHTTP")},
            function() {return new ActiveXObject("Microsoft.XMLHTTP")}
            ];
            
        var xmlhttp = false;
        
        for ( var i=0; i < XMLHttpArray.length; i++ )
            {
            try
                {
                xmlhttp = XMLHttpArray[i]();
                }
            catch(e)
                {
                continue;
                };
            break;
            };
        
        return xmlhttp;
    };
    
    var req = createXMLHTTPObject();
    req.finalCallback = callback;
    var sVars = null;
    method = method.toString().toUpperCase();
    var drupal_kludge = '';
    
    // Split the URL up, if this is a POST.
    if ( method == "POST" )
        {
        var rmatch = /^([^\?]*)\?(.*)$/.exec ( url );
        url = rmatch[1];
        sVars = rmatch[2];
        // This horrible, horrible kludge, is because Drupal insists on having its q parameter in the GET list only.
        var rmatch_kludge = /(q=admin\/settings\/bmlt)&?(.*)/.exec ( rmatch[2] );
        if ( rmatch_kludge && rmatch_kludge[1] )
            {
            url += '?'+rmatch_kludge[1];
            sVars = rmatch_kludge[2];
            };
        };
    if ( extra_data )
        {
        req.extra_data = extra_data;
        };
    req.open ( method, url, true );
	if ( method == "POST" )
        {
        req.setRequestHeader("Method", "POST "+url+" HTTP/1.1");
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        };
    req.onreadystatechange = function ( )
        {
        if ( req.readyState != 4 ) return;
        if( req.status != 200 ) return;
        callback ( req, req.extra_data );
        req = null;
        };
    req.send ( sVars );
    
    return req;
};
