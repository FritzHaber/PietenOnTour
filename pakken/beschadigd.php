<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    include_once '../pak_classes.php';
    $user = new gebruiker($dbh);
    $custume = new pak($dbh);

    // checken of de gebruiker is ingelogd
    if (!$user->is_ingelogd()) {
        $user->redirect('../login/login.php');
    }

    if (isset($_SESSION['flash'])) {
        $error = $_SESSION['flash'];
        unset($_SESSION['flash']);
    }

    $ingelogde_gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);

    // rolID van de gebruike ophalen
    $rolID = $ingelogde_gebruiker['rol_id'];
    
    if ($rolID == 1) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om deze pagina te bezoeken!'
        );
        $user->redirect('../pakken/pietenpakken.php?pagina=1');
    }

    // Pagina's
    //===============================================
    // limiet per pagina instellen
    $limiet = 3;

    $query = ("SELECT * FROM pak 
              JOIN status_pak 
              ON pak.staat_id= status_pak.staat_id
              JOIN foto_pak 
              ON pak.pak_id = foto_pak.pak_id 
              JOIN melding_pak
              ON melding_pak.pak_id = pak.pak_id
              WHERE pak.staat_id = 1 
              AND melding_pak.oplossing is NULL
              ORDER BY pak.pak_id");
    $s = $dbh->prepare($query);
    $s->execute();

    $aantal_resultaten = $s->rowCount();
    $aantal_paginas = ceil($aantal_resultaten / $limiet);

    if (!isset($_GET['pagina'])) {
        $pagina_active = 1;
    } else {
        $pagina_active = $_GET['pagina'];
    }

    $begin_limiet = ($pagina_active - 1) * $limiet;
    $lijst = "SELECT * FROM pak 
              JOIN status_pak ON pak.staat_id= status_pak.staat_id
              JOIN foto_pak 
              ON pak.pak_id = foto_pak.pak_id 
              JOIN melding_pak
              ON melding_pak.pak_id = pak.pak_id
              WHERE pak.staat_id = 2 
              AND melding_pak.oplossing is NULL
              ORDER BY pak.pak_id ASC LIMIT $begin_limiet, 
                      $limiet";

    $r = $dbh->prepare($lijst);
    $r->execute();

    $pakken = $r->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['zoek-resulaten'])) {
        $zoekterm = $_GET['zoek-resulaten'];
        $pakken = $custume->zoek_beschadigde_pakken($zoekterm);
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
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <link rel="stylesheet" href="../styling/base.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="../plaatjes/favicon.png" type="image/gif" sizes="16x16">
    <title>Overzicht | Beschadigde pakken</title>
</head>
<body>
<div class="topnav">
    <a href="../pakken/pietenpakken.php?pagina=1">Pietenpakken</a>
    <a href="../pakken/sinterklaaspakken.php?pagina=1">Sinterklaaspakken</a>
    <?php if ($rolID > 1) { ?>
        <a class="active" href="../pakken/beschadigd.php?pagina=1">Beschadigd</a>
    <?php } ?>
    <?php if ($rolID = 3) { ?>
        <a href="../gebruikers/overzicht.php?pagina=1">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm">
            <h2>Overzicht beschadigde pakken</h2>
        </div>
        <form action="beschadigd.php?pagina=1" method="GET">
            <div class="col-sm">
                <div class="input-group">
                    <input name="zoek-resulaten" type="text" class="form-control"
                           placeholder="Zoek een pak..."
                           value="<?php echo isset($_GET['zoek-resulaten']) ? $zoekterm : '' ?>">
                    <span class="input-group-btn">
                    <button class="btn btn" type="submit"><img src="../plaatjes/zoeken.png" width="20"
                                                               height="20"/></button>
                </span>
                </div>
                <?php echo isset($_GET['zoek-resulaten']) ? '<a href="beschadigd.php?pagina=1"> Terug naar overzicht</a>' : '' ?>
            </div>
        </form>
    </div>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>
    <table class="table">
        <thead>
        <tr>
            <th>Foto</th>
            <th>Paknummer</th>
            <th>Maat</th>
            <th>Geslacht</th>
            <th>Kleur</th>
            <th>Type</th>
        </tr>

        </thead>
        <tbody>
        <?php if (empty($pakken)) { ?>
            <tr>
                <td>Geen beschadigde pakken gevonden</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php } else { ?>
            <?php foreach ($pakken as $pak): ?>
                <tr data-href="../melding/wijzigen.php?id=<?php echo $pak['melding_id'] ?> ">
                    <td><img src="../uploads/<?php echo $pak['foto_id']; ?>" alt="..." class="img-thumbnail"></td>
                    <td><?php print_r($pak['pak_id']); ?></td>
                    <td class="text-uppercase"><?php print_r($pak['maat']); ?></td>
                    <td><?php print_r($pak['geslacht']); ?></td>
                    <td><?php print_r($pak['kleur']); ?></td>
                    <td><?php print_r($pak['type']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php } ?>

        </tbody>
    </table>
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

</div>
<div class="footer">
    <div class="left">
        <a href="#">Pak toevoegen</a>
    </div>
    <div class="right">
        <a href="../gebruikers/mijn-account.php">Account</a>
        <a href="../login/uitloggen.php">Uitloggen</a>
    </div>
</div>
</body>
</html>
<script src="../scripts/script.js"></script>
