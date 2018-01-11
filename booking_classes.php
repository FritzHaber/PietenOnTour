<?php

    class booking {
        private $db;

        function __construct($dbh) {
            $this->db = $dbh;
        }

        public function ophalen_tijdblokken() {
            try {
                $stmt = $this->db->prepare("SELECT *    
                                            FROM tijdblok
                                            ORDER BY begin_tijd");
                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het ophalen'
                );
            }
        }

        public function ophalen_bezoeken() {
            try {
                $stmt = $this->db->prepare("SELECT *    
                                            FROM bezoek
                                            ORDER BY begin");
                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het ophalen'
                );
            }
        }

        public function aanmaken_bezoek($gegevens) {
            try {
                $verhaaltjes_binnen = isset($gegevens['verhaaltjes_binnen']) ? 1 : 0;
                $betaald = isset($gegevens['betaald']) ? 1 : 0;
                $email = $gegevens['email'];

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Dit e-mail adres is niet valide'
                    );
                }

                $stmt = $this->db->prepare("INSERT INTO bezoek(type, begin, eind, naam, adres, aantal_kinderen, allergie, telefoonnummer, email, verhaaltjes_binnen, opmerking, betaald, aantal_pieten, aantal_sinterklazen, aantal_schminkers, tijdblok_id)
                                            VALUES(?, ?, ?,?, ?, ?,?, ?, ?,?, ?, ?,?, ?, ?, ?)");
                $stmt->execute(array(
                    $gegevens['type'],
                    $gegevens['begin'],
                    $gegevens['eind'],
                    $gegevens['naam'],
                    $gegevens['adres'],
                    $gegevens['aantal_kinderen'],
                    $gegevens['allergie'],
                    $gegevens['telefoon'],
                    $gegevens['email'],
                    $verhaaltjes_binnen,
                    $gegevens['opmerking'],
                    $betaald,
                    $gegevens['aantal_pieten'],
                    $gegevens['aantal_sinterklazen'],
                    $gegevens['aantal_schminkers'],
                    $gegevens['tijdblok'],
                ));

                return $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'Bezoek is opgeslagen!'
                );
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het opslaan'
                );
            }
        }

        public function ophalen_bezoek_id($bezoek_id) {
            try {
                $stmt = $this->db->prepare("SELECT *    
                                            FROM bezoek
                                            WHERE bezoek_id = ?");
                $stmt->execute(array(
                    $bezoek_id
                ));

                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het ophalen'
                );
            }
        }

        public function bewerken_bezoek($bezoek_id, $gegevens) {
            try {
                $verhaaltjes_binnen = isset($gegevens['verhaaltjes_binnen']) ? 1 : 0;
                $betaald = isset($gegevens['betaald']) ? 1 : 0;
                $email = $gegevens['email'];
                $begin_tijd = $gegevens['begin'];
                $eind_tijd = $gegevens['eind'];

                if (DateTime::createFromFormat('j-m-Y H:i', $begin_tijd) == false || DateTime::createFromFormat('j-m-Y H:i', $eind_tijd) == false ){
                    return $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Je hebt geen geldige datums ingevuld!'
                    );
                }


                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Dit e-mail adres is niet valide'
                    );
                }

                $stmt = $this->db->prepare("UPDATE bezoek
                                            SET type = ?, 
                                                begin = ?, 
                                                eind = ?, 
                                                naam = ?, 
                                                adres = ?, 
                                                aantal_kinderen = ?, 
                                                allergie = ?, 
                                                telefoonnummer = ?, 
                                                email = ?, 
                                                verhaaltjes_binnen = ?,
                                                opmerking = ?, 
                                                betaald = ?, 
                                                aantal_pieten = ?, 
                                                aantal_sinterklazen = ?, 
                                                aantal_schminkers = ?, 
                                                tijdblok_id = ?
                                                WHERE bezoek_id = $bezoek_id");
                $stmt->execute(array(
                    $gegevens['type'],
                    $begin_tijd,
                    $eind_tijd,
                    $gegevens['naam'],
                    $gegevens['adres'],
                    $gegevens['aantal_kinderen'],
                    $gegevens['allergie'],
                    $gegevens['telefoon'],
                    $gegevens['email'],
                    $verhaaltjes_binnen,
                    $gegevens['opmerking'],
                    $betaald,
                    $gegevens['aantal_pieten'],
                    $gegevens['aantal_sinterklazen'],
                    $gegevens['aantal_schminkers'],
                    $gegevens['tijdblok'],
                ));

                return array(
                    'type' => 'success',
                    'message' => 'Bezoek is opgeslagen!'
                );
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het opslaan'
                );
            }
        }

        public function bewerken_tijdblok($tijdblok_id, $gegevens) {
            try {
                $stmt = $this->db->prepare("UPDATE tijdblok
                                                   SET begin_tijd = ?, eind_tijd = ?, tijdblok_naam = ?
                                                   WHERE tijdblok_id = ? ");
                $stmt->execute(array(
                    $gegevens['begin_tijd'],
                    $gegevens['eind_tijd'],
                    $gegevens['tijdblok_naam'],
                    $tijdblok_id,
                ));

                return array(
                    'type' => 'success',
                    'message' => 'Tijdblok is bijgewerkt!'
                );
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het opslaan'
                );
            }
        }

        public function aanmelden_tijdsblok($gegevens, $gebruiker) {
            try {
                $datum_inschrijving = date("Y/m/d h:i:sa");

                $stmt = $this->db->prepare("INSERT INTO inschrijving_vrijwilliger(vrijwilliger_id, tijdsblok_id, datum_inschrijving, bevestigd)
                                            VALUES(?, ?, ?, ?)");
                $stmt->execute(array(
                    $gebruiker['gebruiker_id'],
                    $gegevens['id'],
                    $datum_inschrijving,
                    0
                ));

                return array(
                    'type' => 'success',
                    'message' => 'Je hebt je aangemeld voor dit tijdblok'
                );
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het opslaan'
                );
            }
        }

        public function inschrijvingen_ophalen_per_gebruiker($gebruiker) {
            try {
                $stmt = $this->db->prepare("SELECT *
                                            FROM inschrijving_vrijwilliger iv
                                            JOIN tijdblok t
                                            ON iv.tijdsblok_id = t.tijdblok_id
                                            WHERE vrijwilliger_id = ?");
                $stmt->execute(array(
                    $gebruiker['gebruiker_id']
                ));

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het ophalen'
                );
            }
        }

        public function inschrijvingen_verwijderen($inschrijving_id, $gebruiker_id) {
            try {
                $stmt = $this->db->prepare("DELETE FROM inschrijving_vrijwilliger
                                            WHERE vrijwilliger_id = ?
                                            AND inschrijving_id = ?");
                $stmt->execute(array(
                    $gebruiker_id,
                    $inschrijving_id
                ));

                return $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'Je inschrijving is verwijderd!'
                );
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Deze inschrijving kan niet worden verwijderd'
                );
            }
        }

        public function verwijderen_bezoek($bezoek_id) {
            try {
                $stmt = $this->db->prepare("DELETE FROM bezoek
                                            WHERE bezoek_id = ?");
                $stmt->execute(array(
                    $bezoek_id
                ));

                return $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'Je bezoek is verwijderd!'
                );
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het verwijderen'
                );
            }
        }

        public function tijdblokken_inschrijvingen() {
            try {
                $stmt = $this->db->prepare("SELECT *
                                            FROM tijdblok t
                                            JOIN inschrijving_vrijwilliger iv
                                            ON t.tijdblok_id = iv.tijdsblok_id
                                            JOIN gebruiker g 
                                            ON g.gebruiker_id = iv.vrijwilliger_id");
                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het ophalen'
                );
            }
        }

        public function inschrijvingen_bevestigen($gegevens, $tijdblok_id) {
            try {
                unset($gegevens['bevestigen']);
                unset($gegevens['tijdblok_id']);

                $inschrijvingen = $this->inschrijvingen_ophalen_tijdblokid($tijdblok_id);
                $teamnaam = 'Team-' . $tijdblok_id;
                $stmt = $this->db->prepare("INSERT INTO team(teamnaam, actief, tijdblok_id)
                                                    VALUES(?, ?, ?)");
                $stmt->execute(array(
                    $teamnaam,
                    0,
                    $tijdblok_id
                ));
                $team_id = $this->db->lastInsertId();

                foreach ($inschrijvingen as $id => $inschrijving) {
                    $vrijwilliger = $inschrijving['vrijwilliger_id'];

                    if (isset($gegevens[$inschrijving['inschrijving_id']])) {
                        $stmt = $this->db->prepare("UPDATE inschrijving_vrijwilliger
                                                                    SET bevestigd = 1
                                                                    WHERE inschrijving_id = ? ");
                        $stmt->execute(array(
                            $inschrijving['inschrijving_id']
                        ));

                        $stmt = $this->db->prepare("INSERT INTO team_lid(team_id, vrijwilliger_id)
                                                    VALUES(?, ?)");
                        $stmt->execute(array(
                            $team_id,
                            $vrijwilliger,
                        ));
                    } else {
                        $stmt = $this->db->prepare("UPDATE inschrijving_vrijwilliger
                                                                    SET bevestigd = 0
                                                                    WHERE inschrijving_id = ? ");
                        $stmt->execute(array(
                            $inschrijving['inschrijving_id']
                        ));
                    }
                }

                $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'De inschrijvingen zijn bevestigd'
                );
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het opslaan'
                );
            }
        }

        public function inschrijvingen_ophalen_tijdblokid($id) {
            try {
                $stmt = $this->db->prepare("SELECT * 
                                            FROM inschrijving_vrijwilliger
                                            WHERE tijdsblok_id = ?");
                $stmt->execute(array(
                    $id
                ));

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het ophalen'
                );
            }
        }

        public function ophalen_teams() {
            try {
                $stmt = $this->db->prepare("SELECT *
                                            FROM team t");

                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het ophalen'
                );
            }
        }

        public function ophalen_teamleden() {
            try {
                $stmt = $this->db->prepare("SELECT *
                                            FROM team_lid t
                                            JOIN gebruiker g 
                                            ON g.gebruiker_id = t.vrijwilliger_id");

                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijdens het ophalen'
                );
            }
        }
        public function tijdblok_aanmaken($gegevens, $gebruiker) {
            try{
                $begin = str_replace(["T", ":"], "-", $gegevens["begin_datum"]);
                $eind = str_replace(["T", ":"], "-",$gegevens["eind_datum"]);
                
                $stmt = $this->db->prepare("INSERT INTO tijdblok (begin_tijd, eind_tijd, tijdblok_naam, aangemaakt_door) 
                                            VALUES (STR_TO_DATE(?, '%Y-%m-%d-%H-%i'), STR_TO_DATE(?, '%Y-%m-%d-%H-%i'), ?, ?)");
                $stmt->execute(array(
                    $begin,
                    $eind,
                    $gegevens["tijdsblok_naam"],
                    $gebruiker["gebruiker_id"]
                ));

                return array(
                    'type' => 'success',
                    'message' => 'Het tijdblok is succesvol aangemaakt'
                );
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

?>