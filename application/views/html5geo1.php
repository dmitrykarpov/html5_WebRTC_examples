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
                if("geolocation" in navigator){
                    navigator.geolocation.getCurrentPosition(function(position){
                        console.log(position);
                        $('#success').show().text( "lat: " + position.coords.latitude + " log: " + position.coords.longitude);
                    });
                }else{
                    $('#error').show().text("Gelocation not supported.");
                }
            });
        </script>
    </head>
    <body>
        <div id="success" style="display: none;"></div>
        <div id="error" style="display: none;"></div>
        
        <hr/>
        <?php echo anchor(array("webrtc","index"),"back");?>
    </body>
</html>