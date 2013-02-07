<!DOCTYPE html>
<html>
    <head>
        <title>Geolocation demo1</title>
        <style>
            video {
                border:5px solid black;
                width: 480px;
                height: 360px;
            }
            button {
                font: 18px sans-serif;
                padding: 8px;
            }
            textarea{
                font-family: monospace;
                margin: 2px;
                width: 480px;
                height: 640px;
            }
            #error{
                font: 18px sans-serif;
                padding: 8px;
                color: red;
            }
            #success{
                font: 18px sans-serif;
                padding: 8px;
                color: green;
            }
            #mapholder{
                width: 90%;
                height: 400px;
                border: 1px solid black;
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
        <script>
            $(function(){ 
                var errorCallBack = function (error){
                    // When we select "not now" the gelocaion error dose not fires;
                    // http://screencast.com/t/wFruUQHAH
                    // but in Chrome Fires
                    //console.log(error);
                    $('#error').show().text(error.message);
                    $('#success').hide();
                };
                var showMap = function(lat, lng){
                    var mapOptions = {
                        zoom: 12,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                   var map = new google.maps.Map(document.getElementById('mapholder'),
                    mapOptions);
                    var pos = new google.maps.LatLng(lat, lng);
                    map.setCenter(pos);
                    var marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        title: "You are here!"
                    });
                };
                var get_geolocation = function() {
                    if("geolocation" in navigator){
                        navigator.geolocation.getCurrentPosition(function(position){
                            var lat = position.coords.latitude;
                            var lng = position.coords.longitude;
                            $('#success').show().text( "lat: " + lat + " long: " + lng);
                            $('#error').hide(); 
                            showMap(lat,lng);
                        },errorCallBack);
                    }else{
                        $('#error').show().text("Gelocation not supported.");
                        $('#success').hide();
                    }
                }
                $("#get_geolocation").click(get_geolocation);
            });
        </script>
    </head>
    <body>
        <h3>HTML5 geolocation (example1):</h3>
        <button id="get_geolocation">Get geolocation</button>
        <div id="success" style="display: none;"></div>
        <div id="error" style="display: none;"></div>
        <div id="mapholder"></div>

        <hr/>
        <?php echo anchor(array("webrtc", "index"), "back"); ?>
    </body>
</html>