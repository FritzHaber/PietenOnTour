<?php
    session_start();
    include_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);

    // checken of de gebruiker is ingelogd
    if (!$user->is_ingelogd()) {
        $user->redirect('../login/login.php');
    }

    if (isset($_SESSION['flash'])) {
        $error = $_SESSION['flash'];
        unset($_SESSION['flash']);
    }

    if (isset($_SESSION['ingelogd'])) {
        $error = $_SESSION['ingelogd'];
        unset($_SESSION['ingelogd']);
    }
    $ingelogde_gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);

    // rolID van de gebruike ophalen
    $rolID = $ingelogde_gebruiker['rol_id'];

    // gebruikersrol ophalen doormiddel van een functie
    $rol = $user->gebruikers_rol($rolID);
    
    if ($rolID != 3) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om deze pagina te bezoeken!'
        );
        $user->redirect('../pakken/pietenpakken.php');
    }

    // Pagina's
    //===============================================
    // limiet per pagina instellen
    $limiet = 4;
    // gebruikers ophalen uit de database
    $query = "SELECT * FROM gebruiker";
    $s = $dbh->prepare($query);
    $s->execute();

    // berekenen hoeveel pagina's er zijn
    $aantal_resultaten = $s->rowCount();
    $aantal_paginas = ceil($aantal_resultaten / $limiet);

    // paginanummer ophalen
    if (!isset($_GET['pagina'])) {
        $pagina_active = 1;
    } else {
        $pagina_active = $_GET['pagina'];
    }

    $begin_limiet = ($pagina_active - 1) * $limiet;
    $lijst = "SELECT *
              FROM gebruiker
              ORDER BY voornaam DESC
              LIMIT $begin_limiet, 
              $limiet";

    $r = $dbh->prepare($lijst);
    $r->execute();

    $gebruikers = $r->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['zoek-resulaten'])) {
        $zoekterm = $_GET['zoek-resulaten'];
        $gebruikers = $user->zoek_gebruikers($zoekterm);
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
    <link rel="stylesheet" href="../styling/footer.css">
    <link rel="stylesheet" href="../styling/base.css">
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="icon" href="../plaatjes/favicon.png" type="image/gif" sizes="16x16">
    <script src="../scripts/script.js"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gebruikers | overzicht</title>
</head>
<body>
<div class="topnav">
    <a href="../pakken/pietenpakken.php?pagina=1">Pietenpakken</a>
    <a href="../pakken/sinterklaaspakken.php?pagina=1">Sinterklaaspakken</a>
    <?php if ($rolID > 1) { ?>
        <a href="../pakken/beschadigd.php?pagina=1">Beschadigd</a>
    <?php } ?>    
    <?php if ($rolID == 3) { ?>
        <a class="active" href="../gebruikers/overzicht.php?pagina=1">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm">
            <h2>Overzicht gebruikers</h2>
        </div>
        <form action="overzicht.php?pagina=1" method="GET">
            <div class="col-sm">
                <div class="input-group">
                    <input name="zoek-resulaten" type="text" class="form-control"
                           placeholder="Zoek een gebruiker..."
                           value="<?php echo isset($_GET['zoek-resulaten']) ? strip_tags($zoekterm) : '' ?>">
                    <span class="input-group-btn">
                    <button class="btn btn" type="submit"><img src="../plaatjes/zoeken.png" width="20"
                                                               height="20"/></button>


                </span>
                </div>
                <?php echo isset($_GET['zoek-resulaten']) ? '<a href="overzicht.php?pagina=1"> Terug naar overzicht</a>' : '' ?>
            </div>
        </form>
    </div>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>
    <table id="gebruikersTabel" class="table">
        <thead>
        <tr>
            <th>Voornaam</th>
            <th>Email</th>
            <th>Telefoonnummer</th>
            <th>Maat</th>
        </tr>
        </thead>
        <tbody>
        <?php if (is_null($gebruikers)) { ?>
            <tr>
                <td>Geen gebruikers gevonden</td>
            </tr>
        <?php } else { ?>
            <?php foreach ($gebruikers as $gebruiker): ?>
                <tr data-href="bewerken.php?id=<?php echo $gebruiker['gebruiker_id'] ?> ">
                    <td>
                        <?php
                            echo $gebruiker['voornaam'];
                            echo !is_null($gebruiker['tussenvoegsel']) ? ' ' . $gebruiker['tussenvoegsel'] . ' ' : ' ';
                            echo $gebruiker['achternaam'];
                        ?>
                    </td>
                    <td> <?php echo $gebruiker['email'] ?> </td>
                    <td> <?php echo $gebruiker['telefoonnummer'] ?> </td>
                    <td class="text-uppercase"> <?php echo $gebruiker['maat'] ?> </td>
                </tr>
            <?php endforeach;
        } ?>
        </tbody>
    </table>
    <hr>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <?php
                for ($pagina = 1; $pagina <= $aantal_paginas; $pagina ++):?>
                    <li class="page-item <?php echo ($pagina == $pagina_active) ? 'active' : '' ?>"><a
                            class="page-link"
                        <a href='<?php echo "?pagina=$pagina"; ?>'
                           class="links"><?php echo $pagina; ?></a></li>
                <?php endfor; ?>
        </ul>
    </nav>
    <div class="footer">
        <div class="left">
            <a href="../gebruikers/aanmaken.php">Gebruiker toevoegen</a>
        </div>
        <div class="right">
            <a href="../gebruikers/mijn-account.php">Account</a>
            <a href="../login/uitloggen.php">Uitloggen</a>
        </div>
    </div>
</div>
</body>
</html>
<script src="../scripts/script.js"></script>