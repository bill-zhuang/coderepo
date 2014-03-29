//js url:http://maps.googleapis.com/maps/api/js?key=AIzaSyCefZle2DqxF9i51PTfoZsZoOmvWzKYhF4&sensor=true
//<script type="text/javascript" src="/js/markerclusterer.js"></script>
//js url:http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js
//for more information, visit: https://developers.google.com/maps/documentation/javascript/demogallery
var map;

function showPosition(lng, lat)
{
	//mapTypeId: ROADMAP;SATELLITE;HYBRID;TERRAIN
	var myLatLng = new google.maps.LatLng(lat, lng);
	var mapOptions = {
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	map.setCenter(new google.maps.LatLng(31.230416,121.473701));
	
	var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        title: 'title_name',
	});
	//var georssLayer = new google.maps.KmlLayer('http://api.flickr.com/services/feeds/geo/?g=322338@N20&lang=en-us&format=feed-georss');
	//georssLayer.setMap(map);
	
	/*var heatmap = new google.maps.visualization.HeatmapLayer({
	    data: heatmapData
	});
	heatmap.setMap(map);*/
}

function markMultiPosition(corrdinates, div_id)
{
	var mapOptions = {
		zoom: 8,
		mapTypeId: google.maps.MapTypeId.ROADMAP
 	};
 	
	var map = new google.maps.Map(document.getElementById(div_id), mapOptions);
	map.setCenter(new google.maps.LatLng(31.230416,121.473701));

    //data is array, contain lng-lat info.
    for(var i = 0; i < corrdinates.length; i++) {
        var myLatLng = new google.maps.LatLng(corrdinates[i]['Latitude'], corrdinates[i]['Longitude']);
    	var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'title_name',
    	});

    }
    
   	//var ctaLayer = new google.maps.KmlLayer('your_kmllayer_url');
	//ctaLayer.setMap(map);
}

function markMultiPostionAndCluster(corrdinates, div_id)
{
	var mapOptions = {
		zoom: 8,
		mapTypeId: google.maps.MapTypeId.ROADMAP
 	};
 	
	map = new google.maps.Map(document.getElementById(div_id), mapOptions);
	map.setCenter(new google.maps.LatLng(31.230416,121.473701));

	var markers = [];
    for(var i = 0; i < corrdinates.length; i++) {
        var myLatLng = new google.maps.LatLng(corrdinates[i]['Latitude'], corrdinates[i]['Longitude']);
    	var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'title_name',
    	});
    	
    	markers.push(marker);
    }
    
   	//var ctaLayer = new google.maps.KmlLayer('your_kmllayer_url');
	//ctaLayer.setMap(map);

	var markerCluster = new MarkerClusterer(map, markers);
}

//google limit for 10 route, each route limit 9 points.
function drawRoutes(route_coord, map)
{
	//split crawler_coorninates
    var route_points_limit = 9;
    var inner_waypoints = [];
    var routes_len = (route_coord.length % route_points_limit == 0) 
                        ? (route_coord.length / route_points_limit) : (route_coord.length / route_points_limit + 1);
    for(var idx_route = 0; idx_route < routes_len; idx_route++) {
        var pos_start = (idx_route * route_points_limit < route_coord.length) ? (idx_route * route_points_limit) : -1;
        var pos_end = ((idx_route + 1) * route_points_limit < route_coord.length) 
                            ? ((idx_route + 1) * route_points_limit) : route_coord.length - 1;

        if(pos_start != -1) {
            inner_waypoints[idx_route] = [];
            for(var idx_waypoint = pos_start; idx_waypoint <= pos_end; idx_waypoint++) {
            	inner_waypoints[idx_route].push({location: route_coord[idx_waypoint], stopover: true});
            }
        }
    }

    var temp_result = [];
    
    for(var idx_route = 0; idx_route < inner_waypoints.length; idx_route++)
    {
    	var src, dst;
    	var route_request;

        if(inner_waypoints[idx_route].length > 2) {
        	src = inner_waypoints[idx_route].shift()['location'];
            dst = inner_waypoints[idx_route].pop()['location'];
            route_request = {
                origin: src,
                destination: dst,
                waypoints: inner_waypoints[idx_route],
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };
        } else if(inner_waypoints[idx_route].length == 2) {
        	src = inner_waypoints[idx_route].shift()['location'];
            dst = inner_waypoints[idx_route].pop()['location'];
            route_request = {
                origin: src,
                destination: dst,
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };
        } else {
        	src = inner_waypoints[idx_route].shift()['location'];
        	dst = src;
        	route_request = {
                origin: src,
                destination: dst,
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };
        }

        var service = new google.maps.DirectionsService();
        service.route(route_request, function (result, status) {
            console.log(status);
            if (status == google.maps.DirectionsStatus.OK) {
            	/*var legs = result.routes[0].legs;
                for(var m = 0; m < legs.length; m++) {
                    for (var n = 0; n < legs[m].steps.length; n++) {
                        for (var l = 0; l < legs[m].steps[n].lat_lngs.length; l++) {
                        	temp_result.push(legs[m].steps[n].lat_lngs[l]);
                        }
                    }
                }
                
                var poly = new google.maps.Polyline({ path: temp_result, strokeColor: '#F75C54', strokeWeight: 3 });
                poly.setMap(map);*/
                var directionsDisplay = new google.maps.DirectionsRenderer();
                directionsDisplay.setMap(map);
                directionsDisplay.setDirections(result);
            }
        });
    }
}

function drawPolyLines(multi_coord)
{
	var line_colors = ["#FF0000", "#00FF00", "#0000FF", "#000000", "#FFFF00", "#00FFFF", "#FF00FF", "CC0066", "66FF00", "660066", "99CC66", "009999"];
	var poly_path = new google.maps.Polyline({
        path: multi_coord,
        strokeColor: line_colors[0],
        strokeOpacity: 1.0,
        strokeWeight: 2
	});
	poly_path.setMap(map);
}

var infowindow;
function markerWithInfoWindow(lng, lat)
{
	//remove infowindow if exist.
	if(typeof infowindow !== 'undefined') {
    	infowindow.close();
    }

	var mapOptions = {
		zoom: 8,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	map.setCenter(new google.maps.LatLng(31.230416,121.473701));
	
	var myLatLng = new google.maps.LatLng(lat, lng);
    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        //icon: icon_location,
	});
	
     infowindow = new google.maps.InfoWindow();
     infowindow.setContent('content');
     infowindow.open(map, marker);    
}

////////////////////////////////////
/*var marker = new google.maps.Marker({
    map: map,
    position: new google.maps.LatLng(0, 0),
    xxxxx: "xxxxx"
});
google.maps.event.addListener(marker, 'click', function() {
    alert(this.xxxxx);
});*/