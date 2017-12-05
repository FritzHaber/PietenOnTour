<?php
    session_start();
    include_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);

    // checken of de gebruiker is ingelogd
    if (!$user->is_ingelogd()) {
        $user->redirect('login/login.php');
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

    // Pagina's
    //===============================================
    // limiet per pagina instellen
    $limiet = 2;
    // gebruikers ophalen uit de database
    $query = "SELECT * FROM gebruiker";
    $s = $dbh->prepare($query);
    $s->execute();

    // berekenen hoeveel pagina's er zijn
    $aantal_resultaten = $s->rowCount();
    $aantal_paginas = ceil($aantal_resultaten / $limiet);

    // paginanummer ophalen
    if (!isset($_GET['pagina'])) {
        $pagina = 1;
    } else {
        $pagina = $_GET['pagina'];
    }

    $begin_limiet = ($pagina - 1) * $limiet;
    $lijst = "SELECT * FROM gebruiker ORDER BY voornaam DESC LIMIT $begin_limiet, 
                      $limiet";

    $r = $dbh->prepare($lijst);
    $r->execute();

    $gebruikers = $r->fetchAll(PDO::FETCH_ASSOC);
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
    <script src="scripts/script.js"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gebruikers</title>
</head>
<body>
<div class="topnav">
    <a href="pakken/pietenpakken.php">Pietenpakken</a>
    <a href="pakken/sinterklaaspakken.php">Sinterklaaspakken</a>
    <?php if ($rolID == '3') { ?>
        <a href="pakken/beschadigd.php">Beschadigd</a>
        <a href="../gebruikers/overzicht.php?pagina=1">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm">
            <h2>Overzicht gebruikers</h2>
        </div>
        <div class="col-sm">
            <div class="input-group">
                <input type="text" class="form-control" id="filter" onkeyup="filter_table()"
                       placeholder="Zoek een gebruiker...">
            </div>
        </div>
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
                <td> <?php echo $gebruiker['maat'] ?> </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <?php
                for ($pagina = 1; $pagina <= $aantal_paginas; $pagina ++):?>
                    <li class="page-item <?php echo ($pagina == $_GET['pagina']) ? 'active' : '' ?>"><a
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
            <a href="../login/uitloggen.php">Uitloggen</a>
        </div>
    </div>
</div>
</body>
</html>

<script>
    $('tr[data-href]').on("click", function () {
        document.location = $(this).data('href');
    });

    function filter_table() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("gebruikersTabel");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>