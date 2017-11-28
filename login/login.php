<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);
    $loginError = '';

    // checken als er is ingelogd
    if ($user->is_ingelogd()) {
        $user->redirect('../index.php');
    }

    // zo niet, dan data uit het ingevulde formulier halen
    if (isset($_POST['inloggen'])) {
        $email = $_POST['email'];

        // let op dat je wachtwoord in de database gehashed moet zijn: http://www.passwordtool.hu/php5-password-hash-generator
        $wachtwoord = $_POST['wachtwoord'];

        if ($user->login($email, $wachtwoord)) {

            $user->redirect('../index.php');
        } else {
            $loginError = 'E-mail of wachtwoord is niet bekend bij ons!';
        }
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php echo $loginError; ?>
<div class="container">
    <form method="post" action="login.php">
        <div class="form-group">
            <label for="exampleInputEmail1">E-mail</label>
            <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                   placeholder="Voer je e-mail in">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Wachtwoord</label>
            <input name="wachtwoord" type="password" class="form-control" id="exampleInputPassword1"
                   placeholder="Wachtwoord">
        </div>
        <button name="inloggen" type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</body>
</html>