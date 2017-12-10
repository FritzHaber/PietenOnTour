<?php
    class pak {
        private $db;
        function __construct($dbh) {
            $this->db = $dbh;
        }
                public function pak_ophalen_pakid($pak_id) {
                    try {
                        // gebruiker ophalen uit de database op basis van de gebuikerID
                        $stmt = $this->db->prepare("SELECT * FROM pak JOIN status_pak ON pak.staat_id=status_pak.staat_id JOIN foto_pak ON pak.pak_id=foto_pak.pak_id JOIN onderdeel_pak ON pak.pak_id=onderdeel_pak.pak_id WHERE pak.pak_id=:pakid");
                        $stmt->execute(array(":pakid" => $pak_id));
                        $pak = $stmt->fetch(PDO::FETCH_ASSOC);

                        return $pak;
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                }

                public function nieuw_pak_details($gegevens) {
            try {
                $pakid = $gegevens['pakid'];
                $staatid = $gegevens['beschadigd'];
                $kleur = $gegevens['kleur'];
                $geslacht = $gegevens['geslacht'];
                $maat = $gegevens['maat'];
                $type = $gegevens['type'];
                $foto_id = "C:/xampp/htdocs/KBS_login/uploads/" . basename($_FILES["profiel_foto"]["name"]);
                $datum_upload = date("Y/m/d h:i:sa");
                $status = $gegevens['beschadigd'];

                $stmt = $this->db->prepare("SELECT * FROM pak WHERE pak_id=:pakid");
                $stmt->execute(array(':pakid' => $pakid));
                $pakrow = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($pakrow > 1) {
                    $error = array('type' => 'danger', 'message' => 'Dit pakid bestaat al');

                    return $error;
                }

                $stmt = $this->db->prepare("INSERT INTO `pak`(`pak_id`, `staat_id`, `kleur`, `geslacht`,`maat`, `type`) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute(array(
                    $pakid,
                    $staatid,
                    $kleur,
                    $geslacht,
                    $maat,
                    $type,
                ));

                $stmt = $this->db->prepare("INSERT INTO `foto_pak`(`foto_id`, `pak_id`, `datum_upload`) 
                                  VALUES (?, ?, ?)");
                $stmt->execute(array(
                   $foto_id,
                   $pakid,
                   $datum_upload,
                ));

//                $stmt = $this->db->prepare("INSERT INTO `status_pak`(`staat_id`, `omschrijving`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                   $pakid,
//                   $status,
//                ));

                return $this->pak_ophalen_pakid($this->db->lastInsertId());
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
                public function nieuw_pak_onderdelen($gegevens) {
            try {
                $pakid = $gegevens['pakid'];
                $vastonderdeel1 = $gegevens['vastonderdeel1'];
                $vastonderdeel2 = $gegevens['vastonderdeel2'];
                $vastonderdeel3 = $gegevens['vastonderdeel3'];
                $onderdeel4 = $gegevens['onderdeel4'];
                $onderdeel5 = $gegevens['onderdeel5'];
                $onderdeel6 = $gegevens['onderdeel6'];
                $onderdeel7 = $gegevens['onderdeel7'];
                $onderdeel8 = $gegevens['onderdeel8'];
                $onderdeel9 = $gegevens['onderdeel9'];
                $onderdeel10 = $gegevens['onderdeel10'];
                $onderdeel11 = $gegevens['onderdeel11'];
                $onderdeel12 = $gegevens['onderdeel12'];
                $stmt = $this->db->prepare("SELECT * FROM onderdeel_pak WHERE pak_id=:pakid");
                $stmt->execute(array(':pakid' => $pakid));
                $pakrow = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($pakrow > 12) {
                    $error = array('type' => 'danger', 'message' => 'Dit pakid bestaat al');

                    return $error;
                }
                
                $onderdelen = array($vastonderdeel1, $vastonderdeel2, $vastonderdeel3, $onderdeel4, $onderdeel5, $onderdeel6, $onderdeel7, $onderdeel8, $onderdeel9, $onderdeel10, $onderdeel11, $onderdeel12);
                
                foreach ($onderdelen as $part) {
                    $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
                                      VALUES (?, ?)");
                    $stmt->execute(array(
                        $pakid,
                        $part,
                    ));
                }

//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $vastonderdeel1,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $vastonderdeel2,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $vastonderdeel3,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel4,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel5,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel6,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel7,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel8,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel9,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel10,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel11,
//                ));
//                
//                $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`) 
//                                  VALUES (?, ?)");
//                $stmt->execute(array(
//                    $pakid,
//                    $onderdeel12,
//                ));

                return $this->pak_ophalen_pakid($this->db->lastInsertId());
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        public function pak_bewerken($gegevens, $pak_id) {
            try {
                $pakid = $gegevens['pakid'];
                $staatid = $gegevens['beschadigd'];
                $kleur = $gegevens['kleur'];
                $geslacht = $gegevens['geslacht'];
                $maat = $gegevens['maat'];
                $type = $gegevens['type'];
                $foto_id = "C:/xampp/htdocs/KBS_login/uploads/" . basename($_FILES["profiel_foto"]["name"]);
                $datum_upload = date("Y/m/d h:i:sa");

                
//                $stmt = "UPDATE pak 
//                            SET  pak_id =?,
//                                 staat_id =?,
//                                 kleur =?, 
//                                 geslacht =?,
//                                 maat =?, 
//                                 type =?, 
//                            WHERE pak_id IN (SELECT pak_id
//                                            FROM foto_pak
//                                            WHERE   foto_id =?,
//                                                    pak_id =?,
//                                                    datum_upload =?,
//                                                    pak_id IN (SELECT pak_id
//                                                              FROM status_pak
//                                                              WHERE staat_id =?,
//                                                                    omschrijving =?)";
//                
//
//                $stmt = $this->db->prepare($stmt);
//
//                $stmt->execute(array($pakid, $pakid, $kleur, $geslacht, $maat, $type, $foto_id, $pakid, $datum_upload, $staat_id, $omschrijving));
                
                $stmt = $this->db->prepare("UPDATE pak
                                            SET pak_id=?
                                                staat_id =?
                                                kleur =?
                                                geslacht =?
                                                maat =?
                                                type =?
                                            WHERE pak_id =?");
                $stmt->execute(array(
                    $pakid,
                    $staatid,
                    $kleur,
                    $geslacht,
                    $maat,
                    $type,
                    $pakid,
                ));
                
                $stmt = $this->db->prepare("UPDATE foto_pak
                                            SET foto_id=?
                                                pak_id =?
                                                datum_upload =?
                                            WHERE pak_id =?");
                $stmt->execute(array(
                    $fotoid,
                    $pakid,
                    $datum_upload,
                    $pakid,
                ));

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        public function onderdeel_pak_bewerken($gegevens, $pak_id) {
            try {
                $pakid = $gegevens['pakid'];
                $kleur = $gegevens['kleur'];
                $geslacht = $gegevens['geslacht'];
                $maat = $gegevens['maat'];
                $type = $gegevens['type'];
                $foto_id = "C:/xampp/htdocs/KBS_login/uploads/" . basename($_FILES["profiel_foto"]["name"]);
                $datum_upload = date("Y/m/d h:i:sa");
                $status = $gegevens['beschadigd'];

                
                $stmt = $this->db->prepare("UPDATE pak
                                            SET pak_id=?
                                                staat_id =?
                                                kleur =?
                                                geslacht =?
                                                maat =?
                                                type =?
                                            WHERE pak_id =?");
                $stmt->execute(array(
                    $pakid,
                    $staatid,
                    $kleur,
                    $geslacht,
                    $maat,
                    $type,
                    $pakid,
                ));

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
?>