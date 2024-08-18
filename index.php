<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surprise Video</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #000; /* Black background for better contrast */
        }
        .video-container {
            width: 100%;
            max-width: 100%;
            height: 100%;
        }
        video {
            width: 100%;
            height: auto;
            border: 0;
            display: block;
        }
    </style>
</head>
<body>
    <div class="video-container">
        <video autoplay muted>
            <source src="/img/surprise.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
</body>
</html>
