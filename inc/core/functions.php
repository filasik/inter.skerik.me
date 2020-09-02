<?php
/**
 * Copyright (c) 2019. Filip Skerik
 * https://filipskerik.eu
 */

//include "DataControl.php";

class basic extends DataControl
{
    //region qCMS
    function versionCheck()
    {
        $string = file_get_contents("https://raw.githubusercontent.com/filasik/qcms/master/VERSION.txt?token=AE3NPUNGIORC4LP4TVGPSMC7CCOZC");
        if ($string === FALSE) {
            echo "<div class='alert alert-danger col-md-12 mx-auto'>Nelze zjistit aktuálnost verze " . CMS_NAME . "</div>";
        } else {
            if ($string !== CMS_VERSION) {
                $this->generateAlert("Upozornění:", "Aktualizujte prosím na novější verzi " . CMS_NAME . " < br>Používáte verzi < strong>" . CMS_VERSION . " </strong >. Nejnovější verze je: <strong > " . $string . "</strong >.", "info", 10000);
            }
        }
    }
    function formatDatum($datum){
        return date("d. n. Y H:i", strtotime($datum));
    }
    function unsupportedVersion()
    {
        $this->generateAlert("Upozornění","V této verzi " . CMS_NAME . " tyto funkce nefungují.","danger",10000);
    }
    function root()
    {
        return str_replace("index.php", "", $_SERVER['PHP_SELF']);
    }
    //endregion

    //region WEBSITE - META / HTML
    function set($name)
    {
        global $database;
        global $lang;
        return $database->queryOne("select * from settings where name = '$name'")['content_' . $lang];
    }
    function meta($what)
    {
        switch ($what) {
            case "url":
                return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                break;
            default:
                return "website";
        }
    }
    function HTML($tag, $where)
    {
        if ($where === "start") {
            echo "<$tag>";
        } elseif ($where === "end") {
            echo "</$tag>";
        } elseif (empty($where)) {
            echo "$tag";
        } else {
            echo "Začátek nebo konec elementu? HTML($tag, start/end)";
        }
    }
    //endregion

    //region PAGE - HTML, stránky, part, errors
    function page($pageName)
    {
        include "./inc/page/$pageName.php";
    }
    function part($partName)
    {
        global $doajob;
        global $database;
        include "./inc/part/$partName.php";
    }
    function error404($URL)
    {
        echo "
        <div class='text-center col-md-6 mx-auto py-5'>
            <h1 class='my-3'>404</h1>
            <p>Stránka ($URL) neexistuje.</p>
            <a class='btn btn-primary' href=" . $this->getProt() . "://" . WEBSITE_URL . ">Zpět na " . WEBSITE_NAME . "</a>
            <a class='btn btn-secondary' href=mailto:" . MAIN_MAIL . "><i class='fa fa-envelope'></i> " . MAIN_MAIL . "</a>
        </div>
        ";
    }
    function getProt()
    {
        return isset($_SERVER["HTTPS"]) ? 'https' : 'http';
    }
    //endregion

    //region LANGUAGES
    function loginMenu()
    {
        global $q;
        //global
        if (!isset($_SESSION['loggedin'])) {
            echo "
                <li class='nav-item'>
                    <a class='nav-link' href='/login$q'>Přihlásit se</a>
                </li>
                ";
        } else {
            echo "
                <li class='nav-item list-inline-item'>
                    <a class='nav-link' href='?logout' title='Odhlásit'>Odhlásit se</a>
                </li>";
        }
        if (isset($_GET['logout'])) {
            $this->logout();
        }
    }
    function langCheck($col)
    {
        global $item;
        global $defaultLang;
        global $lang;

        if (empty($item[$col . '_' . $lang])) {
            return $item[$col . '_' . $defaultLang];
        } else {
            return $item[$col . '_' . $lang];
        }
    }
    //endregion

    //region CONTROL - Redirecty, stránky
    function logout()
    {
        @session_start();
        session_destroy();
        unset($_SESSION['loggedin']);
        $this->redirectIndex();
    }
    function redirectHome()
    {
        echo "<script type='text/javascript'>window.location.href = '?p=home';</script>";
    }
    function redirectIndex()
    {
        echo "<script type='text/javascript'>window.location.href = '/';</script>";
    }
    function redirect($url)
    {
        echo "<script type='text/javascript'>window.location.href = 'index.php?" . $url . "';</script>";
    }
    function redirect_pretty($url)
    {
        echo "<script type='text/javascript'>window.location.href = '" . $url . "';</script>";
    }
    function customRedirect($url)
    {
        echo "<script type='text/javascript'>window.location.href = '$url';</script>";
    }
    //endregion

    //region EMAILY
    function getHeaders(){

    }
    //endregion

    //region UI - Login, register, lost pw mail
    function login_v2()
    {
        if (isset($_POST['login'])) {
            global $database;
            $hash = $database->queryOne("select * from users where email='$_POST[email]'");
            if (password_verify($_POST['password'], $hash['password'])) {
                $_SESSION['loggedin'] = $_POST['email'];
                //pre_last_login = last_login do tabulky pre_last_login
                $last = $database->queryOne("select * from users where email='$_POST[email]'");
                $database->query("update users set pre_last_login='$last[last_login]' where email='$_POST[email]'");
                //aktualizace last loginu
                $database->query("update users set last_login='" . date("Y-m-d H:i:s") . "' where email='$_POST[email]'");
                if (isset($_GET['redirect'])) {
                    $this->redirect('/' . $_GET['redirect']);
                } else {
                    //$this->redirect("p=account");
                    $this->redirect_pretty("account");
                }
            } else {
                $this->generateAlert("Chyba při přihlášení!", "Zadaný email nebo heslo je špatné. Pokud jste zapomněli heslo klikněte prosím na <a href='?p=lostpw'>tento odkaz</a>", 'danger');
            }
        }
    }
    function register_v2()
    {
        if (isset($_POST['register'])) {
            global $database;
            if ($_POST["password"] == $_POST["password-re"]) {
                $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $user_code = rand(10000, 99999);
                $checkMail = $database->queryOne("select * from users where email='$_POST[email]'");
                if (empty($checkMail['email'])) {
                    if ($database->query("INSERT INTO `users`(`firstname`, `lastname`, `phone`, `email`, `bank_account`, `ip`, `verify`, `code`,`password`) VALUES ('$_POST[firstname]','$_POST[lastname]','$_POST[phone]','$_POST[email]','0','" . getenv('REMOTE_ADDR') . "','0','$user_code','$pass')")) {
                        $_SESSION['loggedin'] = $_POST["email"];
                        $database->query("update users set last_login='" . date("Y-m-d H:i:s") . "' where email='$_POST[email]'");

                        $subject = WEBSITE_NAME . " - Registrace nového uživatele $_SESSION[loggedin]";
                        //tohle přijde jenom adminům MAIN_MAIL
                        $message = "<html><body>";
                        $message .= "<img src='" . $this->getProt() . "://" . WEBSITE_URL . "/assets/images/" . PAGE_LOGO . "' width=240 alt='" . WEBSITE_NAME . "'><br><br>";
                        $message .= "<p>Nový uživatel:</p>";
                        $message .= "<strong>Uživatel:</strong> " . $_SESSION['loggedin'] . " <br><br> <strong>E-mail:</strong> " . $_SESSION['loggedin'] . "<br><br> <strong>Dne:</strong> " . date('d.M.Y') . "<br><br>IP Adresa uživatele je: " . $_SERVER['REMOTE_ADDR'] . "<br><br>";
                        $message .= "<p>Děkujeme a s pozdravem,</p>";
                        $message .= "<p><strong>" . WEBSITE_NAME . "</strong></p><br>";
                        $message .= "<img src='" . $this->getProt() . "://" . WEBSITE_URL . "/assets/images/" . PAGE_LOGO . "' width=240 alt='" . WEBSITE_NAME . "'>";
                        $message .= "<p>E: <a href='mailto:" . MAIN_MAIL . "'>" . MAIN_MAIL . "</a><br>W: <a href='" . $this->getProt() . "://" . WEBSITE_URL . "'>" . WEBSITE_URL . "</a></p>";
                        $message .= "</html></body>";

                        //tohle přijde registrovanému
                        $messageuser = "<html><body>";
                        $messageuser .= "<img src='" . $this->getProt() . "://" . WEBSITE_URL . "/assets/images/" . PAGE_LOGO . "' width=240 alt='" . WEBSITE_NAME . "'><br><br>";
                        $messageuser .= "Dobrý den,<br><br>";
                        $messageuser .= "registrovali jste se na " . WEBSITE_NAME . " <br>Vaše uživatelské jméno: <a href='" . $this->getProt() . "://" . WEBSITE_URL . "/index.php?p=login' style='text-decoration:none;color:black;'><strong>" . $_SESSION['loggedin'] . "</strong></a>.<br>";
                        $messageuser .= "<p>Děkujeme a s pozdravem,</p>";
                        $messageuser .= "<p><strong>" . WEBSITE_NAME . "</strong></p><br>";
                        $messageuser .= "<img src='" . $this->getProt() . "://" . WEBSITE_URL . "/assets/images/" . PAGE_LOGO . "' width=240 alt='" . WEBSITE_NAME . "'>";
                        $messageuser .= "<p><br>E: <a href='mailto:" . MAIN_MAIL . "'>" . MAIN_MAIL . "</a><br>W: <a href='" . $this->getProt() . "://" . WEBSITE_URL . "'>" . WEBSITE_URL . "</a></p>";
                        $messageuser .= "</html></body>";

                        //hlavičky mailu
                        $headers = "From: " . MAIN_MAIL . "" . "\r\n";
                        $headers .= "Reply-To: " . MAIN_MAIL . "" . "\r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
                        mail(MAIN_MAIL, $subject, $message, $headers);
                        mail($_SESSION['loggedin'], $subject, $messageuser, $headers);

                        //todo - maily do funkcí, pročistit, update

                        $this->redirect("p=account");
                    } else {
                        $this->generateAlert("Chyba při registraci", "Tento email je již registrován", "danger");
                    }
                } else {
                    $this->generateAlert("Chyba při registraci!", "Tento email je již registrován, přihlaste se prosím kliknutím na <a href='?p=login'>tento odkaz.</a>", 'warning');
                }
            } else {
                $this->generateAlert("Chyba", "Zadaná hesla nejsou shodná", "warning");
            }
        }
    }
    function resetPassword()
    {
        global $database;
        if ($_POST["password"] == $_POST["password-re"]) {
            $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $database->query("update users set password='$pass' where email='$_GET[username]'");
            $_SESSION['loggedin'] = $_GET['username'];
            $this->redirectHome();
        } else {
            echo "<div class='alert alert-danger col-md-12 mx-auto'>Zadaná hesla nejsou shodná.</div>";
        }
    }
    function emailExists($email)
    {
        global $database;
        global $q;
        $emailCheck = $database->queryOne("select * from users where email='$email'");
        if (!empty($emailCheck['email'])) {
            $this->sendLostPasswordMail();
        } else {
            $this->generateStaticAlert("Zadaný e-mail není registrován", "Můžete si <a href='/register$q'>vytvořit účet</a>.", "danger");
        }
    }
    function sendLostPasswordMail()
    {
        global $database;
        $code = $database->queryOne("select * from users where email='$_POST[email]'");

        //todo email
        $subject = "Zapomenuté heslo k účtu $_POST[email] na " . WEBSITE_NAME;
        //tohle přijde registrovanému
        $messageuser = "<html><body>";
        $messageuser .= "Dobrý den,<br><br>";
        $messageuser .= "pro obnovení hesla na " . WEBSITE_URL . " klikněte na následující odkaz.<br><br>";
        $messageuser .= "<br><a href='" . $this->getProt() . "://" . WEBSITE_URL . "/lostpw&username=$_POST[email]&code=$code[code]'><strong>VYŽÁDAT NOVÉ HESLO</strong></a><br><br>";
        $messageuser .= "<p>Děkujeme a s pozdravem,</p>";
        $messageuser .= "<p><strong>" . WEBSITE_NAME . "</strong></p><br>";
        $messageuser .= "<img src='" . $this->getProt() . "://" . WEBSITE_URL . "/assets/images/" . PAGE_LOGO . "' width=240 alt='" . WEBSITE_NAME . "'>";
        $messageuser .= "<p><br>E: <a href='mailto:" . MAIN_MAIL . "'>" . MAIN_MAIL . "</a><br>W: <a href='" . $this->getProt() . "://" . WEBSITE_URL . "'>" . WEBSITE_URL . "</a></p>";
        $messageuser .= "</html></body>";


        //global headers
        $headers = "From: " . MAIN_MAIL . "" . "\r\n";
        $headers .= "Reply-To: " . MAIN_MAIL . "" . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";

        mail($_POST['email'], $subject, $messageuser, $headers);
        $this->customRedirect($this->getProt() . "://" . WEBSITE_URL);
    }
    function generateAlert($title, $text, $style, $delay = 10000, $icon = "fa fa-paw")
    {
        echo "
        <script>
            $.notify({
                icon: '$icon',
                title: '$title<br>',
                message: \"$text\"
            }, 
            {type:'$style',delay:$delay,timer:$delay});
        </script>        
        ";
    }
    function generateStaticAlert($title, $text, $style)
    {
        echo "
        <div class='alert alert-$style'>
            <strong>$title</strong><p class='m-0'>$text</p>
        </div>        
        ";
    }
    //endregion


    //region GENERATOR

    function isLogged()
    {
        if (!isset($_SESSION['loggedin'])) {
            $redirect = isset($_GET['p']) ? '&redirect=' . $_GET['p'] : '';
            $this->redirect_pretty('login' . $redirect);
        }
    }
    function generateMissingPageTemplate()
    {
        global $database;
        $output = null;
        foreach ($database->queryAll("select * from menu") as $item) {
            if (is_file("../inc/page/" . $item['webalized_name'] . ".php")) {
            } else {
                $checkDynamic = $database->queryOne("select * from pages where menu_name='$item[webalized_name]'");
                if (empty($checkDynamic)) {
                    echo "
                    <script>
                        $.notify({
                            icon: 'fas fa-exclamation',
                            title: 'Upozornění:',
                            message: \"<a href='?p=pages&new'>pro stránku <strong>$item[name_cz]</strong> neexistuje šablona ani dynamická stránka.</a><br>Vytvořte ji prosím a jako název v menu použijte <strong>$item[webalized_name]</strong>\"
                        }, 
                        {type:'warning',delay:60000,timer:60000});
                    </script>
                    ";
                }
            }
        }
    }
    function generateInput($type, $name, $placeholder = "", $required = false, $label = NULL, $class = "form-control", $value = "")
    {
        if ($label !== NULL) {
            echo "<label for='$name'>$label</label>";
        }
        $required ? $required = "requiered" : null;
        echo "<input type='$type' id='$name' name='$name' value='$value' placeholder='$placeholder' class='$class' $required>";
    }
    //endregion

    //UI - maily - objednavky, registrace
    /**
     * Converts to web safe characters [a-z0-9-] text.
     * @param string  UTF-8 encoding
     * @param string  allowed characters
     * @param bool
     * @return string
     */
    function webalize($s, $charlist = NULL, $lower = TRUE)
    {
        $s = $this->toAscii($s);
        if ($lower) {
            $s = strtolower($s);
        }
        $s = preg_replace('#[^a-z0-9' . preg_quote($charlist, '#') . ']+#i', '-', $s);
        $s = trim($s, '-');
        return $s;
    }
    function toAscii($s)
    {
        $s = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $s);
        $s = strtr($s, '`\'"^~', "\x01\x02\x03\x04\x05");
        if (ICONV_IMPL === 'glibc') {
            $s = @iconv('UTF-8', 'WINDOWS-1250//TRANSLIT', $s); // intentionally @
            $s = strtr($s, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"
                . "\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"
                . "\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"
                . "\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe\x96",
                "ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt-");
        } else {
            $s = @iconv('UTF-8', 'ASCII//TRANSLIT', $s); // intentionally @
        }
        $s = str_replace(array('`', "'", '"', '^', '~'), '', $s);
        return strtr($s, "\x01\x02\x03\x04\x05", '`\'"^~');
    }
}