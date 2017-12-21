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
                echo $e->getMessage();
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
                echo $e->getMessage();
            }
        }

        public function inschrijvingen_ophalen_per_gebruiker($gebruiker){
            try{
                $stmt = $this->db->prepare("SELECT *
                                            FROM inschrijving_vrijwilliger iv
                                            JOIN tijdblok t
                                            ON iv.tijdsblok_id = t.tijdblok_id
                                            WHERE vrijwilliger_id = ?");
                $stmt->execute(array(
                    $gebruiker['gebruiker_id']
                ));

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch (PDOException $e){
                echo $e->getMessage();
            }
        }

        public function inschrijvingen_verwijderen($inschrijving_id, $gebruiker_id){
            try{
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
            }catch (PDOException $e){
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Deze inschrijving kan niet worden verwijderd'
                );
            }
        }

        public function tijdblokken_inschrijvingen(){
            try{
                $stmt = $this->db->prepare("SELECT *
                                            FROM tijdblok t
                                            JOIN inschrijving_vrijwilliger iv
                                            ON t.tijdblok_id = iv.tijdsblok_id");
                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch (PDOException $e){
                echo $e->getMessage();
            }
        }

    }

?>