<?php
include "inc/core/config.php";
include "inc/core/load.php";
$doajob = new basic();
?>
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

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="theme-color" content="#ffffff">

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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="assets/css/materialize.min.css" media="screen,projection"/>
    <script src="assets/js/masonry.min.js"></script>
    <script src="assets/js/packery.js"></script>
    <script>
        $(document).ready(function () {
            var $grid = $('.grid').masonry({
                itemSelector: '.grid-item',
                percentPosition: true
            });
            $('.grid').packery({
                // options
                itemSelector: '.grid-item',
                gutter: 10
            });
            console.log("ready!");
        });

    </script>
    <!--    <link rel="stylesheet" href="//code.jquery.com/mobile/1.5.0-alpha.1/jquery.mobile-1.5.0-alpha.1.min.css">-->
    <!--    <script src="//code.jquery.com/mobile/1.5.0-alpha.1/jquery.mobile-1.5.0-alpha.1.min.js"></script>-->
    <!--    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>-->
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <meta name="google-signin-client_id" content="844839744090-o9olobk4s225nphudvt62pi62el9iv6o.apps.googleusercontent.com">
</head>
<body>


<!-- <nav> navbar content here  </nav>-->
<!--    <ul id="slide-out" class="sidenav">-->
<!--        <li>-->
<!--            <div class="user-view">-->
<!--                <div class="background">-->
<!--                    <img src="images/office.jpg">-->
<!--                </div>-->
<!--                <a href="#user"><img class="circle" src="images/yuna.jpg"></a>-->
<!--                <a href="#name"><span class="white-text name">Filip Skerik</span></a>-->
<!--                <a href="#email"><span class="white-text email">filip@skerik.me</span></a>-->
<!--            </div>-->
<!--        </li>-->
<!--        <li><a href="#!"><i class="material-icons">cloud</i>Archivované</a></li>-->
<!--        <li><a href="#!">Second Link</a></li>-->
<!--        <li>-->
<!--            <div class="divider"></div>-->
<!--        </li>-->
<!--        <li><a class="subheader">Subheader</a></li>-->
<!--        <li><a class="waves-effect" href="#!">Third Link With Waves</a></li>-->
<!--    </ul>-->
<!--    <a href="#" data-target="slide-out" class="sidenav-trigger" style="padding-top:10px"><i class="material-icons">menu</i></a>-->
<!---->

<div class="grid">
    <!--    <button type="button" onclick="registerOneTimeSync()" class="btn btn-danger text-center">Synchronizace</button>-->
    <?php
    foreach ($database->queryAll("select * from zalozky where archivovano=0 order by id desc") as $item) {
        if ($item['typ'] === "odkaz") {
            echo "
            <div class='grid-item zalozka_$item[id]'>
                <div class='card-panel z-depth-4 $item[barva] darken-1' onclick='window.open(\"$item[text]\")' style='cursor: pointer'>
                    <span class='white-text'>
                        <p style='padding-bottom:5px;margin:0;'>$item[nazev]</p>
                    </span>
                    <a class='white-text text-accent-3 delete' data-id='$item[id]' data-typ='$item[typ]'>
                        <i class='material-icons'>done_all</i>
                    </a>
                    <i class='white-text material-icons'>link</i> 
                </div>
            </div>
            ";
        } else {
            echo "
            <div class='grid-item zalozka_$item[id]'>
                <div class='card-panel z-depth-4 $item[barva] darken-1'>
                    <span class='white-text'>
                        <strong>$item[nazev]</strong>
                        <p>$item[text]</p>
                        <small>" . $doajob->formatDatum($item['datum_vytvoreni']) . "</small>
                    </span>
                    <br>
                    <a class='white-text text-accent-3 delete' style='cursor:pointer;' data-id='$item[id]' data-typ='$item[typ]'>
                        <i class='material-icons'>done_all</i>
                    </a>
                </div>
            </div>
            ";
        }
    }
    ?>
</div>
<div class="fixed-action-btn">
    <a class="btn-floating btn-large red">
        <i class="large material-icons">add</i>
    </a>
    <ul>
        <li><a class="btn-floating red"><i class="material-icons">insert_chart</i></a></li>
        <li><a class="btn-floating green"><i class="material-icons">publish</i></a></li>
        <li><a class="btn-floating yellow darken-1 modal-trigger tooltipped" data-position="left"
               data-tooltip="Vložit text" href="#modalText"><i class="material-icons">format_quote</i></a>
        </li>
        <li><a class="btn-floating blue modal-trigger tooltipped" data-position="left"
               data-tooltip="Vložit odkaz" href="#modalOdkaz"><i class="material-icons">link</i></a>
        </li>
    </ul>
</div>

<!-- Modal Text -->
<div id="modalText" class="modal">
    <form method="post">
        <div class="modal-content">
            <h4>Přidat záložku</h4>
            <div class="input-field col s12">
                <input id="nazev" name="nazev" type="text" class="validate" required>
                <label for="nazev">Název záložky</label>
            </div>
            <div class="input-field col s12">
                <input id="text" name="text" type="text" class="validate">
                <label for="text">Text</label>
            </div>
            <div class="input-field col s12 m6">
                <select class="icons" name="barva">
                    <option value="teal" data-icon="images/sample-1.jpg">Teal</option>
                    <option value="red" data-icon="images/office.jpg">Red</option>
                    <option value="purple" data-icon="images/yuna.jpg">Purple</option>
                    <option value="blue" data-icon="images/office.jpg">Blue</option>
                </select>
                <label>Vyberte barvu</label>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Zavřít</a>
            <input type="submit" class="waves-effect waves-green btn-flat" value="Přidat" name="add">
        </div>
    </form>
</div>

<!-- Modal Odkaz -->
<div id="modalOdkaz" class="modal">
    <form method="post">
        <div class="modal-content">
            <h4>Přidat odkaz</h4>
            <div class="input-field col s12">
                <input id="nazev_odkazu" name="nazev" type="text" class="validate" required>
                <label for="nazev_odkazu">Název odkazu</label>
            </div>
            <div class="input-field col s12">
                <input id="text_url" name="text" type="text" class="validate" required>
                <label for="text_url">URL</label>
            </div>
            <div class="input-field col s12 m6">
                <select class="icons" name="barva">
                    <option value="teal" data-icon="images/sample-1.jpg">Teal</option>
                    <option value="red" data-icon="images/office.jpg">Red</option>
                    <option value="purple" data-icon="images/yuna.jpg">Purple</option>
                    <option value="blue" data-icon="images/office.jpg">Blue</option>
                </select>
                <label>Vyberte barvu</label>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Zavřít</a>
            <input type="submit" class="waves-effect waves-green btn-flat" value="Přidat" name="addOdkaz">
        </div>
    </form>
</div>

<?php
if (isset($_POST['add'])) {
    $database->query("insert into zalozky (nazev,text,barva,datum_vytvoreni,typ) values ('$_POST[nazev]','$_POST[text]','$_POST[barva]',NOW(),'zalozka')");
    $doajob->customRedirect("https://inter.skerik.me/");
}
if (isset($_POST['addOdkaz'])) {
    $database->query("insert into zalozky (nazev,text,barva,datum_vytvoreni,typ) values ('$_POST[nazev]','$_POST[text]','$_POST[barva]',NOW(),'odkaz')");
    $doajob->customRedirect("https://inter.skerik.me/");
}
//if (isset($_GET['archive'])) {
//    $database->query("update zalozky set archivovano=1 where id='$_GET[archive]'");
//    $doajob->customRedirect("https://inter.skerik.me/");
//}
?>

<div class='offline-banner'>Momentálně jste offline. Není možné provádět žádné akce na webu, prosím připojte se.</div>
<div class="g-signin2" data-onsuccess="onSignIn"></div>
<!--<a href="#" onclick="signOut();"><i class="material-icons">logout</i></a>-->
<h1 style="font-weight: 100;color: #00000029;">skerik.me</h1>
<script>
    function signOut() {
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.signOut().then(function () {
            console.log('User signed out.');
        });
    }

    function onSignIn(googleUser) {
        var profile = googleUser.getBasicProfile();
        console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
        console.log('Name: ' + profile.getName());
        console.log('Image URL: ' + profile.getImageUrl());
        console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
    }

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
<script>
    M.AutoInit();
    document.addEventListener('DOMContentLoaded', function () {
        var elems = document.querySelectorAll('.modal');
        var instances = M.Modal.init(elems, options);
    });

    // Or with jQuery

    $(document).ready(function () {
        $('.modal').modal();
    });

    document.addEventListener('DOMContentLoaded', function () {
        var elems = document.querySelectorAll('.sidenav');
        var instances = M.Sidenav.init(elems, options);
    });

    // Initialize collapsible (uncomment the lines below if you use the dropdown variation)
    // var collapsibleElem = document.querySelector('.collapsible');
    // var collapsibleInstance = M.Collapsible.init(collapsibleElem, options);

    // Or with jQuery

    $(document).ready(function () {
        $('.sidenav').sidenav();
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.delete').click(function () {
            var deleteid = $(this).data('id');
            $.ajax({
                url: 'archive.php',
                type: 'POST',
                data: {id: deleteid},
                success: function (response) {
                    console.log(response)
                    if (response === "1") {
                        $(".zalozka_" + deleteid).fadeOut(300, function () {
                            $(this).remove();
                            $grid.masonry();
                        });
                    } else {
                        alert('chyba');
                    }
                },
            });
        });
    });
</script>

<!--<script>-->
<!--    $(function () {-->
<!--        // Bind the swipeHandler callback function to the swipe event on div.box-->
<!--        $(".grid-item").on("swipe", swipeHandler);-->
<!---->
<!--        // Callback function references the event target and adds the 'swipe' class to it-->
<!--        function swipeHandler(event) {-->
<!--            $(this).hide("slide", {direction: "left"}, 200);-->
<!--        }-->
<!--    });-->
<!--</script>-->
</body>
</html>