<?php

class gebruiker {

    private $db;

    function __construct($dbh) {
        $this->db = $dbh;
    }

    public function login($email, $wachtwoord) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM gebruiker WHERE email=:email");
            $stmt->execute(array(':email' => $email));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                if (password_verify($wachtwoord, $userRow['wachtwoord'])) {
                    $_SESSION['user_session'] = $userRow['gebruiker_id'];

                    return true;
                } else {
                    return false;
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function is_ingelogd() {
        if (isset($_SESSION['user_session'])) {
            return true;
        } else {
            return false;
        }
    }

    public function alle_gebruikers() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM gebruiker");
            $stmt->execute();
            $userRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                return $userRow;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function redirect($url) {
        header("Location: $url");
    }

    public function gebruikers_rol($rolID) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM rol WHERE rol_id=:rolID");
            $stmt->execute(array(':rolID' => $rolID));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                return $userRow;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function gebruiker_ophalen_id($gebruiker_id) {
        try {
            // gebruiker ophalen uit de database op basis van de gebuikerID
            $stmt = $this->db->prepare("SELECT * FROM gebruiker WHERE gebruiker_id=:gebruiker_id");
            $stmt->execute(array(":gebruiker_id" => $gebruiker_id));
            $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

            return $gebruiker;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function random_passwoord($length = 8) {
        $chars = "0123456789";
        $password = substr(str_shuffle($chars), 0, $length);

        return $password;
    }

    public function nieuwe_gebruiker($gegevens) {
        try {
            $voornaam = $gegevens['voornaam'];
            $tussenvoegsel = $gegevens['tussenvoegsel'];
            $achternaam = $gegevens['achternaam'];
            $geb_datum = $gegevens['geb_datum'];
            $mail = $gegevens['mail'];
            $woonplaats = $gegevens['woonplaats'];
            $telefoon = $gegevens['telefoon'];
            $functie = $gegevens['functie'];
            $maat = $gegevens['maat'];
            $wachtwoord = $this->random_passwoord();
            $wachtwoordHash = password_hash($wachtwoord, PASSWORD_DEFAULT);
            $datum_registratie = date("Y/m/d h:i:sa");

            $stmt = $this->db->prepare("SELECT * FROM gebruiker WHERE email=:email");
            $stmt->execute(array(':email' => $mail));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userRow > 1) {
                $error = array('type' => 'danger', 'message' => 'Dit e-mail adres bestaat al');

                return $error;
            }

            $stmt = $this->db->prepare("INSERT INTO `gebruiker`(`voornaam`, `tussenvoegsel`, `achternaam`,`geb_datum`, `maat`, `woonplaats`, `telefoonnummer`, `email`, `wachtwoord`, `datum_registratie`, `rol_id`) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array(
                $voornaam,
                $tussenvoegsel,
                $achternaam,
                $geb_datum,
                $maat,
                $woonplaats,
                $telefoon,
                $mail,
                $wachtwoordHash,
                $datum_registratie,
                $functie
            ));

            return $this->gebruiker_ophalen_id($this->db->lastInsertId());


            //                $to = $mail;
            //                $subject = "Account registratie";
            //                $txt = "Huidige wachtwoord: " . $wachtwoord;
            //                $headers = "From: info@pietenontour.com" . "\r\n";
            //
                //                mail($to, $subject, $txt, $headers);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function gebruiker_bewerken($gegevens, $gebruikerId) {
        try {
            $voornaam = $gegevens['voornaam'];
            $tussenvoegsel = $gegevens['tussenvoegsel'];
            $achternaam = $gegevens['achternaam'];
            $geb_datum = $gegevens['geb_datum'];
            $mail = $gegevens['mail'];
            $woonplaats = $gegevens['woonplaats'];
            $telefoon = $gegevens['telefoon'];
            $functie = $gegevens['functie'];
            $maat = $gegevens['maat'];

            $stmt = "UPDATE gebruiker 
                            SET  voornaam = ?, 
                                 tussenvoegsel =?, 
                                 achternaam =?,
                                 geb_datum =?, 
                                 maat =?, 
                                 woonplaats =?, 
                                 telefoonnummer =?,
                                 email =?, 
                                 rol_id =?
                                 WHERE gebruiker_id = ?";
            $stmt = $this->db->prepare($stmt);

            $stmt->execute(array(
                $voornaam,
                $tussenvoegsel,
                $achternaam,
                $geb_datum,
                $maat,
                $woonplaats,
                $telefoon,
                $mail,
                $functie,
                $gebruikerId
            ));
        } catch (PDOException $e) {
            return $error = array(
                'type' => 'danger',
                'message' => 'Dit e-mail adres is al in gebruik!'
            );
        }
    }

    public function pak_ophalen_pakid($pak_id) {
        try {
            // gebruiker ophalen uit de database op basis van de gebuikerID
            $stmt = $this->db->prepare("SELECT * FROM pak WHERE PakID=:pakid");
            $stmt->execute(array(":pakid" => $pak_id));
            $pak = $stmt->fetch(PDO::FETCH_ASSOC);

            return $pak;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function nieuw_pak($gegevens) {
        try {
            $pakid = $gegevens['pakid'];
            $kleur = $gegevens['kleur'];
            $geslacht = $gegevens['geslacht'];
            $maat = $gegevens['maat'];
            $type = $gegevens['type'];
            $pat_foto = "C:/xampp/htdocs/KBS_login/uploads/" . basename($_FILES["profiel_foto"]["name"]);
            $datum_upload = date("Y/m/d h:i:sa");
            $status = $gegevens['beschadigd'];
            $stmt = $this->db->prepare("SELECT * FROM pak WHERE PakID=:pakid");
            $stmt->execute(array(':pakid' => $pakid));
            $pakrow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($pakRow > 1) {
                $error = array('type' => 'danger', 'message' => 'Dit pakid bestaat al');

                return $error;
            }

            $stmt = $this->db->prepare("INSERT INTO `pak`(`PakID`, `Kleur`, `Geslacht`,`Maat`, `Type`) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(array(
                $pakid,
                $kleur,
                $geslacht,
                $maat,
                $type,
            ));

            $stmt = $this->db->prepare("INSERT INTO `foto_pak`(`FotoID`, `datum_upload`, `PakID`) 
                                  VALUES (?, ?, ?)");
            $stmt->execute(array(
                $pat_foto,
                $datum_upload,
                $pakid,
            ));

            $stmt = $this->db->prepare("INSERT INTO `status_pak`(`Status`, `PakID`) 
                                  VALUES (?, ?)");
            $stmt->execute(array(
                $status,
                $pakid,
            ));

            return $this->pak_ophalen_pakid($this->db->lastInsertId());
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function gebruiker_verwijderen($gebruiker) {
        try {
            $gebruiker_id = $gebruiker['gebruiker_id'];
            $stmt = "DELETE FROM gebruiker WHERE gebruiker_id = $gebruiker_id";
            $stmt = $this->db->prepare($stmt);
            $stmt->execute();

            return $_SESSION['flash'] = array(
                'type' => 'success',
                'message' => 'Gebruiker is succesvol verwijderd!'
            );
        } catch (PDOException $e) {
            return $_SESSION['flash'] = array(
                'type' => 'danger',
                'message' => 'Er is iets fout gegaan tijden het verwijderen van deze gebruikter!'
            );
        }
    }

    // Controleert of de checkbox 'Beschadigd' is aangevinkt
    public function staat_pak($staat) {
        if (isset($_POST['staat'])) {
            $staat = 1;
        } else {
            $staat = 0;
        }
        return $staat;
    }

    // Controleert of de 'Opslaan' knop is ingedrukt, maakt een nieuwe melding aan
    public function aanmaken_melding() {
        // Voegt de nieuwe melding toe aan melding_pak
        $stmt = $this->db->prepare("INSERT INTO melding_pak(pak_id, gebruiker_id, status_id, bericht) VALUES (?,?,?,?)");
        $stmt->execute(array($_SESSION['pak_id'], $_SESSION['user_session'], $_POST['status_id'], $_POST['schademelding']));

        // Haalt de melding_id op van de nieuw aangemaakte melding
        $stmt = $this->db->prepare("SELECT MAX(melding_id) AS melding_id FROM melding_pak WHERE gebruiker_id = ? AND pak_id = ?");
        $stmt->execute(array($_SESSION['user_session'], $_SESSION['pak_id']));

        // Haalt de melding_id op
        $row = $stmt->fetch();
        $melding_id = $row['melding_id'];

        // Verplaatst de geüploade foto naar de doelmap
        // $_FILES["form input name"]["bestandsnaam"]
        $doelmap = "uploads/" . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $doelmap);
        $datum_upload = date("y-m-d H:i:s");

        // Voegt de foto van de schade toe 
        $stmt = $this->db->prepare("INSERT INTO foto_melding VALUES ('$doelmap', $melding_id, '$datum_upload')");
        $stmt->execute();

        header("Location: wijzigen.php");
    }

    public function wijzigen_melding() {
        $stmt = $this->db->prepare("UPDATE melding_pak SET status_id = ?, kosten = ?, oplossing = ? WHERE pak_id = ?");
        $stmt->execute($_POST['status_id'], $_POST['kosten'], $_POST['oplossing'], $_SESSION['pak_id']);

        header("Location: aanmaken.php");
    }

    public function ophalen_melding() {
        $stmt = $this->db->prepare("SELECT status_id, bericht, oplossing, kosten FROM melding_pak WHERE pak_id = ?");
        $stmt->execute(array($_SESSION['pak_id']));

        $row = $stmt->fetch();
        return $row;
    }
}
?>
