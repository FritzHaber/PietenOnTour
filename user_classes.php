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

        public function zoek_gebruikers($zoekterm) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM gebruiker WHERE email LIKE '%" . $zoekterm .
                    "%' OR voornaam LIKE '%" . $zoekterm . "%'OR achternaam LIKE '%" . $zoekterm . "%'");
                $stmt->execute();
                $userRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($stmt->rowCount() > 0) {
                    return $userRow;
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }


        public function wachtwoord_veranderen($wachtwoorden, $gebruiker_id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM gebruiker WHERE gebruiker_id=:id");
                $stmt->execute(array(':id' => $gebruiker_id));
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($wachtwoorden['oud-wachtwoord'], $userRow['wachtwoord'])) {
                    if ($wachtwoorden['wachtwoord'] === $wachtwoorden['her-wachtwoord']) {
                        $wachtwoord = $wachtwoorden['wachtwoord'];
                        $wachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);
                        $stmt = $this->db->prepare("UPDATE gebruiker 
                                                    SET wachtwoord = ?
                                                    WHERE gebruiker_id = ?");
                        $stmt->execute(array($wachtwoord, $gebruiker_id));

                        return $_SESSION['flash'] = array(
                            'type' => 'success',
                            'message' => 'Je wachtwoord is aangepast!'
                        );
                    } else {
                        return $_SESSION['flash'] = array(
                            'type' => 'danger',
                            'message' => 'De ingevulde wachtwoorden komen niet overeen!'
                        );
                    }
                } else {
                    return $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Je oude wachtwoord klopt niet!'
                    );
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

?>
