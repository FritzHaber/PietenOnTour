<?php

    class pak {
        private $db;

        function __construct($dbh) {
            $this->db = $dbh;
        }

        public function pak_ophalen_pakid($pak_id) {
            try {
                // gebruiker ophalen uit de database op basis van de gebuikerID
                $stmt = $this->db->prepare("SELECT * FROM pak 
                                            JOIN status_pak 
                                            ON pak.staat_id=status_pak.staat_id 
                                            JOIN foto_pak ON pak.pak_id=foto_pak.pak_id 
                                            
                                            WHERE pak.pak_id=:pakid");
                $stmt->execute(array(":pakid" => $pak_id));
                $pak = $stmt->fetch(PDO::FETCH_ASSOC);

                return $pak;
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

                // PAk opslaan in de database
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

                // Foto toevegen aan het pak
                $stmt = $this->db->prepare("INSERT INTO `foto_pak`(`foto_id`, `pak_id`, `datum_upload`) 
                                  VALUES (?, ?, ?)");
                $stmt->execute(array(
                    $foto_id,
                    $pakid,
                    $datum_upload,
                ));

                //
                $stmt = $this->db->prepare("INSERT INTO `status_pak`(`staat_id`, `omschrijving`)
                                                  VALUES (?, ?)");
                $stmt->execute(array(
                    $pakid,
                    $status,
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

        public function nieuw_pak_onderdelen($onderdelen, $pakid) {

            try {
                foreach ($onderdelen as $key => $onderdeel) {
                    if ($onderdeel != '') {
                        $stmt = $this->db->prepare("UPDATE onderdeel_pak
                                                    SET onderdeel = ? 
                                                    WHERE onderdeel_id = ?");
                        $stmt->execute(array(
                            $onderdeel,
                            $key,
                        ));
                    }
                }

                return $this->redirect('bewerken.php?id=' . $pakid);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function redirect($url) {
            header("Location: $url");
        }

        public function pak_bewerken($gegevens, $pak_id, $foto) {
            try {
                $pakid = $gegevens['pakid'];
                $staatid = $gegevens['beschadigd'];
                $kleur = $gegevens['kleur'];
                $geslacht = $gegevens['geslacht'];
                $maat = $gegevens['maat'];
                $type = $gegevens['type'];
                $datum_upload = date("Y/m/d h:i:sa");
                if ($foto) {
                    $pak = $this->pak_ophalen_pakid($pakid);
                    $foto_id = $pak['foto_id'];
                } else {
                    $foto_id = "C:/xampp/PietenOnTour/uploads/" . basename($_FILES["profiel_foto"]["name"]);
                }
                $stmt = $this->db->prepare("UPDATE pak
                                            SET pak_id=?
                                                staat_id =?
                                                kleur =?
                                                geslacht =?
                                                maat =?
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
                                            SET foto_id=?
                                                pak_id =?
                                                datum_upload =?
                                            WHERE pak_id =?");
                $stmt->execute(array(
                    $foto_id,
                    $pakid,
                    $datum_upload,
                    $pakid,
                ));

            } catch (PDOException $e) {
                echo $e->getMessage();
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
                          AND pak.pak_id LIKE '%" . $zoekterm . "%'
                          ORDER BY pak.pak_id");
                $stmt->execute();
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
                          WHERE pak.type = '$type'
                          AND pak.pak_id LIKE '%" . $zoekterm . "%'
                          ORDER BY pak.pak_id");
                $stmt->execute();
                $pakken = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($stmt->rowCount() > 0) {
                    return $pakken;
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
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

        public function aanmaken_melding($pak, $gebruiker_id, $melding) {
            $stmt = $this->db->prepare("INSERT INTO melding_pak(pak_id, gebruiker_id, status_id, bericht) 
                                        VALUES (?,?,?,?)");
            $stmt->execute(array($pak['pak_id'], $gebruiker_id, 1, $melding['schademelding']));

            $melding = $this->ophalen_melding($this->db->lastInsertId());
            // Update de staat van het pak naar 'Beschadigd'
            $stmt = $this->db->prepare("UPDATE pak 
                                        SET staat_id = ?
                                        WHERE pak_id = ?");
            $stmt->execute(array(2, $pak['pak_id']));

            $melding_id = $melding['melding_id'];

            // Foto van de schade opslaan
            $doelmap = "uploads/";
            $file = $doelmap . basename($_FILES["foto"]["name"]);
            move_uploaded_file($_FILES["foto"]["tmp_name"], $doelmap);
            $datum_upload = date("Y-m-d H:i:s"); // yyyy-mm-dd 24:60:60
            $stmt = $this->db->prepare("INSERT INTO foto_melding 
                                        VALUES (?, ?, ?)");
            $stmt->execute(array($file, $melding_id, $datum_upload));
            $_SESSION['flash'] = array(
                'type' => 'success',
                'message' => 'Je melding aan je pak is aangemaakt en de administratie is op de hoogte gebracht!'
            );
            if ($pak['type'] == 'piet') {
                header("Location: ../pakken/pietenpakken.php");
            } else {
                header("Location: ../pakken/sinterklaaspakken.php");
            }
        }

        public function wijzigen_melding($melding) {
            try {
                $status_id = $_POST["status_id"];
                $stmt = $this->db->prepare("UPDATE melding_pak 
                                        SET status_id = ?, kosten = ?, oplossing = ? 
                                        WHERE melding_id = ?");
                $stmt->execute(array($status_id, $_POST['kosten'], $_POST['oplossing'], $melding['melding_id']));

                // Verstuurt een e-mail als de melding is afgerond of afgewezen
                if ($status_id === 3 || $status_id === 4) {
                    $status = $status_id === 3 ? 'Afgerond' : 'Afgewezen';

                    // Haalt de e-mail van de ontvanger op
                    $stmt = $this->db->prepare("SELECT email FROM gebruiker WHERE gebruiker_id = ?");
                    $stmt->execute(array($_SESSION['user_session']));
                    $row = $stmt->fetch();
                    $ontvanger = $row['email'];

                    // Verstuurt de e-mail
                    $onderwerp = "Melding pak " . $_SESSION['pak_id'] . ": " . $status;
                    mail($ontvanger, $onderwerp, $_POST['oplossing']);
                }

                // Gaat terug naar de vorige pagina
                $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'Status van het pak in succesvol gewijzigd!'
                );
                $this->redirect('../pakken/beschadigd.php');
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

                $row = $stmt->fetch();

                return $row;
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

                $row = $stmt->fetch();

                return $row;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

?>