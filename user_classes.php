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
                    if (password_verify($wachtwoord, $userRow['Wachtwoord'])) {
                        $_SESSION['user_session'] = $userRow['gebruikerID'];

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

        public function alle_gebruikers(){
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
                $stmt = $this->db->prepare("SELECT * FROM rol WHERE RolID=:rolID");
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
                $stmt = $this->db->prepare("SELECT * FROM gebruiker WHERE gebruikerID=:gebruiker_id");
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
                $datum_registratie = '2017-01-01';

                $stmt = $this->db->prepare("SELECT * FROM gebruiker WHERE email=:email");
                $stmt->execute(array(':email' => $mail));
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($userRow > 1) {
                    $error = array('type' => 'danger', 'message' => 'Dit e-mail adres bestaat al');

                    return $error;
                }

                $stmt = $this->db->prepare("INSERT INTO `gebruiker`(`Voornaam`, `Tussenvoegsel`, `Achternaam`,`geb_datum`, `maat`, `Woonplaats`, `Telefoonnummer`, `email`, `Wachtwoord`, `datum_registratie`, `RolID`) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute(array(
                    $voornaam,
                    $tussenvoegsel,
                    $achternaam,
                    $woonplaats,
                    $telefoon,
                    $geb_datum,
                    $maat,
                    $mail,
                    $wachtwoordHash,
                    $datum_registratie,
                    $functie
                ));


                $to = $mail;
                $subject = "Account registratie";
                $txt = "Huidige wachtwoord: " . $wachtwoord;
                $headers = "From: info@pietenontour.com" . "\r\n";

                mail($to,$subject,$txt,$headers);

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

    }

?>