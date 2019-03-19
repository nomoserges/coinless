<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Coinless</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100%;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    Coinless
                </div>
                <div class="links">
                    <p style="color:#333;">Hello <?php echo $firstname; ?>, welcome to our easy coinless payment. To start using it,
                    please click the link bellow to activate your account.</p>
                    <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/opener/confirm/?token=<?php echo $token.'&credential='.$userid; ?>">Active account</a>
                </div>
                <div class="links" style="position:fixed;bottom:10px;">
                    <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/#">Policies</a>
                    <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/#">Privacy</a>
                    <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/#">Faq</a>
                    <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/#">Blog</a>
                </div>
            </div>
        </div>
    </body>
</html>