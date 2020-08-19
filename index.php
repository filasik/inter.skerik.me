<!doctype html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>

    <title>inter.skerik.me</title>
    <meta name='description' content='PWA skerik.me'>
    <meta name='author' content='Filip Skerik, skerik.me'>
    <meta name='robot' content='noindex, nofollow'/>
    <meta name='theme-color' content='#2e3135'>

    <!-- Add to home screen for Safari on iOS -->
    <meta name='apple-mobile-web-app-capable' content='yes'>
    <meta name='apple-mobile-web-app-status-bar-style' content='black'>
    <meta name='apple-mobile-web-app-title' content='PWA skerik.me'>
    <link rel='apple-touch-icon' href='/assets/images/launcher-icon-3x.png'>
    <meta name='msapplication-TileImage' content='/assets/images/launcher-icon-3x.png'>
    <meta name='msapplication-TileColor' content='#2e3135'>

    <link rel='manifest' href='/manifest.json'>

    <link rel='stylesheet' type='text/css' href='/assets/css/style.css'>
    <!--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="assets/css/materialize.min.css" media="screen,projection"/>

</head>
<body>


<div class="container text-center">
    <div class="row">
        <!--    <button type="button" onclick="registerOneTimeSync()" class="btn btn-danger text-center">Synchronizace</button>-->
        <div class="col s12 m4">
            <div class="card-panel teal">
        <span class="white-text">I am a very simple card. I am good at containing small bits of information.
        I am convenient because I require little markup to use effectively. I am similar to what is called a panel in other frameworks.
        </span>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card-panel teal">
        <span class="white-text"><a href="https://materializecss.com/switches.html" class="white-text" target="_blank">https://materializecss.com/switches.html</a>
        </span>
            </div>
        </div>
    </div>
</div>


<div class="fixed-action-btn">
    <a class="btn-floating btn-large red">
        <i class="large material-icons">mode_edit</i>
    </a>
    <ul>
        <li><a class="btn-floating red"><i class="material-icons">insert_chart</i></a></li>
        <li><a class="btn-floating yellow darken-1"><i class="material-icons">format_quote</i></a></li>
        <li><a class="btn-floating green"><i class="material-icons">publish</i></a></li>
        <li><a class="btn-floating blue"><i class="material-icons">attach_file</i></a></li>
    </ul>
</div>

<div class='offline-banner'>Momentálně jste offline. Není možné provádět žádné akce na webu, prosím připojte se.</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var elems = document.querySelectorAll('.fixed-action-btn');
        var instances = M.FloatingActionButton.init(elems, options);
    });

    // Or with jQuery

    $(document).ready(function () {
        $('.fixed-action-btn').floatingActionButton();
    });
</script>
<script>
    /* SERVICE WORKER - REQUIRED */
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker
            .register('./sw.js')
            .then(function (reg) {
                console.log("ServiceWorker registered ◕‿◕", reg);
            })
            .catch(function (error) {
                console.log("Failed to register ServiceWorker ಠ_ಠ", error);
            });
    }

    function registerOneTimeSync() {
        if (navigator.serviceWorker.controller) {
            navigator.serviceWorker.ready.then(function (reg) {
                if (reg.sync) {
                    reg.sync.register({
                        tag: 'oneTimeSync'
                    })
                        .then(function (event) {
                            console.log('Sync registration successful', event);
                        })
                        .catch(function (error) {
                            console.log('Sync registration failed', error);
                        });
                } else {
                    console.log("Onw time Sync not supported");
                }
            });
        } else {
            console.log("No active ServiceWorker");
        }
    }

    /* OFFLINE BANNER */
    function updateOnlineStatus() {
        var d = document.body;
        d.className = d.className.replace(/\ offline\b/, '');

        if (!navigator.onLine) {
            notifyMe("Offline", "Připojení k internetu ztraceno.");
            d.className += " offline";
        }
    }

    updateOnlineStatus();
    window.addEventListener
    (
        'load',
        function () {
            window.addEventListener('online', updateOnlineStatus);
            window.addEventListener('offline', updateOnlineStatus);
        }
    );

    /* CHANGE PAGE TITLE BASED ON PAGE VISIBILITY */
    function handleVisibilityChange() {
        if (document.visibilityState == "hidden") {
            document.title = "Haló! Pojď zpátky!";
        } else {
            document.title = original_title;
        }
    }

    var original_title = document.title;
    document.addEventListener('visibilitychange', handleVisibilityChange, false);

    /* NOTIFICATIONS */
    window.addEventListener('load', function () {
        // At first, let's check if we have permission for notification
        // If not, let's ask for it
        if (window.Notification && Notification.permission !== "granted") {
            Notification.requestPermission(function (status) {
                if (Notification.permission !== status) {
                    Notification.permission = status;
                }
            });
        }
    });

    function notifyMe(alert_title, alert_body) {
        var options =
            {
                body: alert_body,
                icon: 'assets/images/launcher-icon-4x.png',
            }

        // Let's check if the browser supports notifications
        if (!("Notification" in window)) {
            alert("Tento prohlížeč nepodporuje zasílání oznámení.");
            return false;
        }

        // Let's check whether notification permissions have already been granted
        else if (Notification.permission === "granted") {
            // If it's okay let's create a notification
            var notification = new Notification(alert_title, options);
            return true;
        }

        // Otherwise, we need to ask the user for permission
        else if (Notification.permission !== 'denied') {
            Notification.requestPermission(function (permission) {
                // If the user accepts, let's create a notification
                if (permission === "granted") {
                    var notification = new Notification(alert_title, options);
                    return true;
                }
            });
        }

        // Finally, if the user has denied notifications and you
        // want to be respectful there is no need to bother them any more.
        console.log("Notifications denied");
        return false;
    }

    //Usage:
    //notifyMe("Title goes here", "Body text goes here");
</script>
<script type="text/javascript" src="assets/js/materialize.min.js"></script>

</body>
</html>