loadTestMap = function() {
    if ( !this.m_main_map )
        {
        this.m_current_lat = 0.0;
        this.m_current_long = 0.0;
        this.m_current_zoom = 3;
        this.m_current_radius = 1000000;
        
        var myOptions = {
                        'center': new google.maps.LatLng(this.m_current_lat, this.m_current_long),
                        'zoom': this.m_current_zoom,
                        'mapTypeId': google.maps.MapTypeId.ROADMAP,
                        'mapTypeControlOptions': { 'style': google.maps.MapTypeControlStyle.DROPDOWN_MENU },
                        'zoomControl': true,
                        'mapTypeControl': true,
                        'disableDoubleClickZoom' : true,
                        'draggableCursor': "crosshair",
                        'scaleControl' : true
                        };

        myOptions.zoomControlOptions = { 'style': google.maps.ZoomControlStyle.LARGE };

        this.m_main_map = new google.maps.Map(document.getElementById('map_div'), myOptions);

        if ( this.m_main_map ) {
            this.m_main_map.setOptions({'scrollwheel': false});   // For some reason, it ignores setting this in the options.
            this.m_main_map.map_marker = null;
            this.m_main_map.circle_overlay = null;
            this.m_main_map.context = this;
    
            google.maps.event.addListener(this.m_main_map, 'click', this.mapClicked);
            google.maps.event.addListener(this.m_main_map, 'zoom_changed', this.mapZoomChanged);
            google.maps.event.addListener(this.m_main_map, 'center_changed', this.mapZoomChanged);
            google.maps.event.addListenerOnce(this.m_main_map, 'tilesloaded', this.mapLoaded);
        };
    };

    if ( this.m_main_map ) {
        var position = new google.maps.LatLng(this.m_current_lat, this.m_current_long);
        this.m_main_map.setCenter(position);
        this.m_main_map.setZoom(this.m_current_zoom);
        position = null;
    };
};

loadTestMap.prototype.m_main_map = null;
loadTestMap.prototype.m_current_lat = 0.0;
loadTestMap.prototype.m_current_long = 0.0;
loadTestMap.prototype.m_current_zoom = 3;
loadTestMap.prototype.m_current_radius = 1000.0;

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
            myMap.context.m_current_long = this.center.lng()
            myMap.context.m_current_lat = this.center.lat()
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
    };
};

loadTestMap.prototype.mapClicked = function(clickEvent) {
    this.context.m_current_long = clickEvent.latLng.lng();
    this.context.m_current_lat = clickEvent.latLng.lat();
    var position = new google.maps.LatLng(this.context.m_current_lat, this.context.m_current_long);
    this.circle_overlay.setOptions({center: position});
    this.panTo(position);
};
