<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title>getUserMedia 2</title>
        <style>
            video{
                border: 5px solid black;
                width: 480px;
                height: 360px;
            }
            canvas{
                border: 5px solid black;
                width: 480px;
                height: 360px;
            }
            button {
                font: 18px sans-serif;
                padding: 8px;
            }
        </style>
    </head>
    <body>
        <video id="vid" autoplay="true"></video>
        <canvas id="cvs"></canvas>
        <br/>
        <button id="btn1" onclick="start()">Start</button>
        <button id="btn2" onclick="snap()">snap</button>
        <script>
            video = document.getElementById('vid');
            canvas = document.getElementById("cvs");
            canvas.width = 480;
            canvas.height = 360;
            btn2.disabled = true;
            function start(){
                navigator.webkitGetUserMedia({video:true},gotStream, function(){});
                btn1.disabled = true;
                btn2.disabled = false;
            }
            function gotStream(stream){
                video.src = webkitURL.createObjectURL(stream);
            }
            function snap(){
                canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
            }
        </script>
    </body>
</html>