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
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
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
                var get_geolocation = function() {
                    if("geolocation" in navigator){
                        navigator.geolocation.getCurrentPosition(function(position){
                            $('#success').show().text( "lat: " + position.coords.latitude + " log: " + position.coords.longitude);
                            $('#error').hide();     
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

        <hr/>
        <?php echo anchor(array("webrtc", "index"), "back"); ?>
    </body>
</html>