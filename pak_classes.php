<?php

    class pak {
        private $db;

        function __construct($dbh) {
            $this->db = $dbh;
        }

        public function pak_ophalen_pakid($pak_id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM pak 
                                            JOIN status_pak 
                                            ON pak.staat_id=status_pak.staat_id 
                                            JOIN foto_pak ON pak.pak_id=foto_pak.pak_id 
                                            
                                            WHERE pak.pak_id=:pakid");
                $stmt->execute(array(
                    ":pakid" => $pak_id
                ));

                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        
        public function pak_event_ophalen($pak_id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM pak_event JOIN actie ON pak_event.actie_id=actie.actie_id JOIN gebruiker ON pak_event.gebruiker_id=gebruiker.gebruiker_id WHERE pak_event.pak_id=:pakid");
                $stmt->execute(array(
                    ":pakid" => $pak_id
                ));
                $pak_events = $stmt->fetchall(PDO::FETCH_ASSOC);

                return $pak_events;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function vaste_onderdelen_ophalen($pak_id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM onderdeel_pak
                                            WHERE pak_id =:pak_id
                                            AND is_vast_onderdeel = TRUE");
                $stmt->execute(array(
                    ':pak_id' => $pak_id
                ));

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }

        public function losse_onderdelen_ophalen($pak_id) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM onderdeel_pak
                                            WHERE pak_id =:pak_id
                                            AND is_vast_onderdeel = FALSE ");
                $stmt->execute(array(
                    ':pak_id' => $pak_id
                ));

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }

        public function nieuw_pak_details($gegevens, $gebruiker, $target_file) {
            try {

                $pakid = $gegevens['pakid'];
                $kleur = $gegevens['kleur'];
                $geslacht = $gegevens['geslacht'];
                $maat = $gegevens['maat'];
                $type = $gegevens['type'];
                $foto_id = $target_file;
                $datum_upload = date("Y/m/d h:i:sa");
                $status = $gegevens['beschadigd'];
                $gebruikerid = $gebruiker['gebruiker_id'];
                $actie = 1;

                $stmt = $this->db->prepare("SELECT * FROM pak WHERE pak_id=:pakid");
                $stmt->execute(array(':pakid' => $pakid));
                $pak = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($pak > 1) {
                    $error = array(
                        'type' => 'danger',
                        'message' => 'Dit pakid bestaat al'
                    );

                    return $error;
                }

                // PAk opslaan in de database
                $stmt = $this->db->prepare("INSERT INTO `pak`(`pak_id`, `staat_id`, `kleur`, `geslacht`,`maat`, `type`) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute(array(
                    $pakid,
                    $status,
                    $kleur,
                    $geslacht,
                    $maat,
                    $type,
                ));

                // Foto toevegen aan het pak
                $stmt = $this->db->prepare("INSERT INTO `foto_pak`(`foto_id`, `pak_id`, `datum_upload`) 
                                  VALUES (?, ?, ?)");
                $stmt->execute(array(
                    $foto_id,
                    $pakid,
                    $datum_upload,
                ));
                
                $stmt = $this->db->prepare("INSERT INTO `pak_event`(`pak_id`, `gebruiker_id`, `actie_id`, `datum`)
                                  VALUES (?, ?, ?, ?)");
                $stmt->execute(array(
                    $pakid,
                    $gebruikerid,
                    $actie,
                    $datum_upload,
                ));

                $onderdelen = array();
                if ($type === 'sinterklaas') {
                    $onderdelen = array(
                        'mantel' => 'mantel',
                        'jurk' => 'jurk',
                        'mijter' => 'mijter'
                    );

                } elseif ($type === 'piet') {
                    $onderdelen = array(
                        'pak' => 'pak',
                        'pet met veer' => 'pet met veer',
                    );
                }
                foreach ($onderdelen as $onderdeel) {
                    $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`, `is_vast_onderdeel`)
                                                  VALUES (?, ?, ?)");
                    $stmt->execute(array(
                        $pakid,
                        $onderdeel,
                        true,
                    ));
                }

                for ($x = 0; $x <= 7; $x ++) {
                    $stmt = $this->db->prepare("INSERT INTO `onderdeel_pak`(`pak_id`, `onderdeel`, `is_vast_onderdeel`)
                                                  VALUES (?, ?, ?)");
                    $stmt->execute(array(
                        $pakid,
                        $onderdeel = '',
                        false,
                    ));
                }

                return $this->pak_ophalen_pakid($this->db->lastInsertId());
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function redirect($url) {
            header("Location: $url");
        }

        public function pak_bewerken($gegevens, $pak_id, $foto, $gebruiker, $target_file) {
            try {
                $pakid = $gegevens['pakid'];
                $staatid = $gegevens['beschadigd'];
                $kleur = $gegevens['kleur'];
                $geslacht = $gegevens['geslacht'];
                $maat = $gegevens['maat'];
                $type = $gegevens['type'];
                $datum_upload = date("Y/m/d h:i:sa");
                $gebruikerid = $gebruiker['gebruiker_id'];
                $actie = 2;
                if ($foto==false) {
                    $pak = $this->pak_ophalen_pakid($pakid);
                    echo $foto_id = $pak['foto_id'];
                } else {
                    echo $foto_id = $target_file;
                }
                
                $stmt = $this->db->prepare("UPDATE pak
                                            SET pak_id =?,
                                                staat_id =?,
                                                kleur =?,
                                                geslacht =?,
                                                maat =?,
                                                type = ?
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
                                            SET foto_id =?,
                                                pak_id =?,
                                                datum_upload =?
                                            WHERE pak_id =?");
                $stmt->execute(array(
                    $foto_id,
                    $pakid,
                    $datum_upload,
                    $pakid,
                ));
                
                $stmt = $this->db->prepare("INSERT INTO `pak_event`(`pak_id`, `gebruiker_id`, `actie_id`, `datum`)
                                  VALUES (?, ?, ?, ?)");
                $stmt->execute(array(
                    $pakid,
                    $gebruikerid,
                    $actie,
                    $datum_upload,
                ));

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        
        public function pak_onderdelen_bewerken($pak_id, $onderdelen, $gebruikerid) {
            try {
                $vast = 0;
                $actie = 2;
                $datum_upload = date("Y/m/d h:i:sa");
                
                foreach ($onderdelen as $key => $onderdeel) {
                    $stmt = $this->db->prepare("UPDATE onderdeel_pak
                                                SET pak_id =?,
                                                    onderdeel =?
                                                WHERE pak_id =? && is_vast_onderdeel =? && onderdeel_id =?");
                    $stmt->execute(array(
                        $pak_id,
                        $onderdeel,
                        $pak_id,
                        $vast,
                        $key,
                        
                    ));
                    echo $key + 1;
                }
                $stmt = $this->db->prepare("INSERT INTO `pak_event`(`pak_id`, `gebruiker_id`, `actie_id`, `datum`)
                                  VALUES (?, ?, ?, ?)");
                $stmt->execute(array(
                    $pak_id,
                    $gebruikerid,
                    $actie,
                    $datum_upload,
                ));
                
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        
        public function pak_verwijderen($pak) {
            try {
                $pak_id = $pak['pak_id'];
                $foto_pak = "C:/xampp/htdocs/KBS_login-kopie(2)/uploads/" . $pak['foto_id'];
//                $foto_melding = "C:/xampp/htdocs/KBS_login-kopie(2)/uploads/" . $pak['foto_id'];
                $stmt = "DELETE FROM pak WHERE pak_id = $pak_id";
                $stmt = $this->db->prepare($stmt);
                $stmt->execute();

                $stmt = "DELETE FROM onderdeel_pak WHERE pak_id = $pak_id";
                $stmt = $this->db->prepare($stmt);
                $stmt->execute();
                
                unlink($foto_pak);
                
                $stmt = "DELETE FROM foto_pak WHERE pak_id = $pak_id";
                $stmt = $this->db->prepare($stmt);
                $stmt->execute();
                
                $stmt = "DELETE FROM pak_event WHERE pak_id = $pak_id";
                $stmt = $this->db->prepare($stmt);
                $stmt->execute();
                
//                unlink($foto_melding);
                
                $stmt = "DELETE FROM melding_pak WHERE pak_id = $pak_id";
                $stmt = $this->db->prepare($stmt);
                $stmt->execute();
                
                return $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'pak is succesvol verwijderd!'
                );
            } catch (PDOException $e) {
                return $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Er is iets fout gegaan tijden het verwijderen van dit pak!'
                );
            }
        }

        public function zoek_beschadigde_pakken($zoekterm) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM pak 
                          JOIN melding_pak
                          ON melding_pak.pak_id = pak.pak_id
                          JOIN status_pak 
                          ON pak.staat_id = status_pak.staat_id 
                          JOIN foto_pak 
                          ON pak.pak_id = foto_pak.pak_id 
                          WHERE status_pak.staat_id  = 2 
                          AND melding_pak.kosten is NULL
                          AND pak.pak_id LIKE ?
                          ORDER BY pak.pak_id");
                $stmt->execute("%$zoekterm%");
                $beschadigde_pakken = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($stmt->rowCount() > 0) {
                    return $beschadigde_pakken;
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function zoek_pakken($zoekterm, $type) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM pak 
                                            JOIN status_pak 
                                            ON pak.staat_id=status_pak.staat_id 
                                            JOIN foto_pak 
                                            ON pak.pak_id=foto_pak.pak_id
                                            WHERE pak.type = ?
                                            AND pak.pak_id LIKE ?
                                            ORDER BY pak.pak_id");
                $stmt->execute(array($type, "%$zoekterm%"));
                
                $pakken = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($stmt->rowCount() > 0) {
                    return $pakken;
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function staat_pak() {
            if (isset($_POST['staat'])) {
                $staat = 1;
            } else {
                $staat = 0;
            }

            return $staat;
        }

        public function aanmaken_melding($pak, $gebruiker_id, $melding, $target_file) {
            try {
                $stmt = $this->db->prepare("INSERT INTO melding_pak(pak_id, gebruiker_id, status_id, bericht) 
                                            VALUES (?,?,?,?)");
                $stmt->execute(array(
                    $pak['pak_id'],
                    $gebruiker_id,
                    1,
                    $melding['schademelding']
                ));
                $melding = $this->ophalen_melding($this->db->lastInsertId());

                $this->update_pak_staat($pak['pak_id'], 2);

                $stmt = $this->db->prepare("INSERT INTO pak_event(pak_id, gebruiker_id, actie_id, datum)
                                  VALUES (?, ?, ?, ?)");
                $stmt->execute(array(
                    $pak["pak_id"],
                    $gebruiker_id,
                    3,
                    date("Y/m/d h:i:sa"),
                ));
                
                $melding_id = $melding['melding_id'];
                move_uploaded_file($_FILES["foto"]["tmp_name"], '../uploads/'.$target_file);
                $datum_upload = date("Y-m-d H:i:s");
                $stmt = $this->db->prepare("INSERT INTO foto_melding 
                                            VALUES (?, ?, ?)");
                $stmt->execute(array(
                    $target_file,
                    $melding_id,
                    $datum_upload
                ));

                $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'Je melding aan je pak is aangemaakt en de administratie is op de hoogte gebracht!'
                );

                if ($pak['type'] == 'piet') {
                    $this->redirect('../pakken/pietenpakken.php');
                } else {
                    $this->redirect('../pakken/sinterklaaspakken.php');
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function wijzigen_melding($melding, $pak_id, $gebruiker) {
            try {
                $status_id = $_POST["status_id"];

                if ($status_id == 3 || $status_id == 4) {
                    $kosten = $_POST['kosten'];
                    $oplossing = $_POST['oplossing'];
                } else {
                    $kosten = null;
                    $oplossing = null;
                }

                $stmt = $this->db->prepare("UPDATE melding_pak 
                                        SET status_id = ?, kosten = ?, oplossing = ? 
                                        WHERE melding_id = ?");
                $stmt->execute(array(
                    $status_id,
                    $kosten,
                    $oplossing,
                    $melding['melding_id']
                ));

                $stmt = $this->db->prepare("INSERT INTO `pak_event`(`pak_id`, `gebruiker_id`, `actie_id`, `datum`)
                                  VALUES (?, ?, ?, ?)");
                $stmt->execute(array(
                    $pak_id,
                    $gebruiker["gebruiker_id"],
                    4,
                    date("Y/m/d h:i:sa"),
                ));
                
                // Verstuurt een e-mail als de melding is afgerond of afgewezen
                if ($status_id == 3 || $status_id == 4) {
                    $status = $status_id == 3 ? 'Afgerond' : 'Afgewezen';

                    // Haalt de e-mail van de ontvanger op
                    $stmt = $this->db->prepare("SELECT email 
                                                FROM gebruiker 
                                                WHERE gebruiker_id = ?");
                    $stmt->execute(array(
                        $melding['gebruiker_id']
                    ));

                    $this->update_pak_staat($pak_id, 1);

                    //                    $gebruiker = $stmt->fetch();
                    //                    $mail = $gebruiker['email'];

                    // Verstuurt de e-mail
                    //                    $onderwerp = "Melding pak " . $_SESSION['pak_id'] . ": " . $status;
                    //                    mail($mail, $onderwerp, $_POST['oplossing']);
                }

                $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'Status van het pak in succesvol gewijzigd!'
                );
                $this->redirect('../pakken/beschadigd.php');
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function update_pak_staat($pak_id, $staat_id) {
            try {
                $stmt = $this->db->prepare("UPDATE pak 
                                        SET staat_id = ?
                                        WHERE pak_id = ?");
                $stmt->execute(array(
                    $staat_id,
                    $pak_id
                ));
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function ophalen_melding_pak($melding_id) {
            try {
                $stmt = $this->db->prepare("SELECT *
                                            FROM melding_pak 
                                            JOIN pak
                                            ON melding_pak.pak_id = pak.pak_id
                                            JOIN foto_pak
                                            ON foto_pak.pak_id = pak.pak_id
                                            JOIN foto_melding
                                            ON foto_melding.melding_id = melding_pak.melding_id
                                          WHERE melding_pak.melding_id = ?");
                $stmt->execute(array($melding_id));

                $pak = $stmt->fetch();

                return $pak;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function ophalen_melding($melding_id) {
            try {
                $stmt = $this->db->prepare("SELECT *
                                        FROM melding_pak 
                                        WHERE melding_id = ?");
                $stmt->execute(array($melding_id));

                $melding = $stmt->fetch();

                return $melding;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

?>