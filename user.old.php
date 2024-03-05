<?php

//Modifications du carnet d'adresses client
          if(isset($_POST['edit_adresses_user'])){
            //Récupération des informations sur les adresses
               $sql = "SELECT *";
               $sql .= " FROM adresses";
               $sql .= " WHERE adresseid = '".$_POST['adresseid']."'";
               $adresse = $DB_site2->query_first($sql);

               //Mise en forme du texte de suivi
               $txt="[infos_client] Modification de l'adresse client";
               if($adresse['raisonsociale']!=$_POST['raisonsociale']) $txt.="<br>Société : ".$adresse['raisonsociale']." => ".$_POST['raisonsociale'];
                if($adresse['nom']!=$_POST['nom']) $txt.="<br>Nom : ".$adresse['nom']." => ".$_POST['nom'];
                if($adresse['prenom']!=$_POST['prenom']) $txt.="<br>Prenom : ".$adresse['prenom']." => ".$_POST['prenom'];
                if($adresse['adresse']!=$_POST['adresse']) $txt.="<br>Adresse : ".$adresse['adresse']." => ".$_POST['adresse'];
                if($adresse['codepostal']!=$_POST['codepostal']) $txt.="<br>Code postal : ".$adresse['codepostal']." => ".$_POST['codepostal'];
                if($adresse['ville']!=$_POST['ville']) $txt.="<br>Ville : ".$adresse['ville']." => ".$_POST['ville'];
                if($adresse['telephone']!=$_POST['telephone']) $txt.="<br>Tel : ".$adresse['telephone']." => ".$_POST['telephone'];
                if($adresse['portable']!=$_POST['portable']) $txt.="<br>Portable : ".$adresse['portable']." => ".$_POST['portable'];
                if($adresse['digicode']!=$_POST['digicode']) $txt.="<br>Digicode : ".$adresse['digicode']." => ".$_POST['digicode'];
                if($adresse['interphone']!=$_POST['interphone']) $txt.="<br>Interphone : ".$adresse['interphone']." => ".$_POST['interphone'];
                if($adresse['etage']!=$_POST['etage']) $txt.="<br>Etage : ".$adresse['etage']." => ".$_POST['etage'];
                if($adresse['ascenseur']!=$_POST['ascenseur']) $txt.="<br>Ascenseur : ".$adresse['ascenseur']." => ".$_POST['ascenseur'];
                if($adresse['codeascenseur']!=$_POST['codeascenseur']) $txt.="<br>Code ascenseur : ".$adresse['codeascenseur']." => ".$_POST['codeascenseur'];
                if($adresse['horairesouverture']!=$_POST['horairesouverture']) $txt.="<br>Horaires ouverture : ".$user_info['horairesouverture']." => ".$_POST['horairesouverture'];
                if($adresse['infoimportante']!=$_POST['infoimportante']) $txt.="<br>Info importante : ".$adresse['infoimportante']." => ".$_POST['infoimportante'];

                $sql = "SELECT etat";
               $sql .= " FROM suivi_client";
               $sql .= " WHERE userid=".$_GET['userid'];
               $sql .= " ORDER BY date DESC";
               $sql .= " LIMIT 1";
               $last = $DB_site2->query_first($sql);
               if($last==""){
                    $last['etat'] = 1;
               }
               
               //Insertion dans le suivi
               $sql = "INSERT INTO suivi_client SET";
               $sql .= " userid = '".$_GET['userid']."'";
               $sql .= ", commercial = '".$user_info['username']."'";
               $sql .= ", date = ".time();
               $sql .= ", suivi = '".mysqli_real_escape_string($DB_site2->link_id,$txt)."'";
               $sql .= ", etat = ".intval($last['etat']);
               $sql .= ", compte =0";
               $DB_site2->query($sql);
    
               $sql = "UPDATE adresses SET ville = '".mysqli_real_escape_string($DB_site->link_id,$_POST['ville'])."', adresse = '".mysqli_real_escape_string($DB_site->link_id,$_POST['adresse'])."', codepostal = '".mysqli_real_escape_string($DB_site->link_id,$_POST['codepostal'])."', telephone = '".$_POST['telephone']."', raisonsociale = '".mysqli_real_escape_string($DB_site->link_id,$_POST['raisonsociale'])."', civilite = '".mysqli_real_escape_string($DB_site->link_id,$_POST['civilite'])."', nom = '".mysqli_real_escape_string($DB_site->link_id,$_POST['nom'])."', prenom = '".mysqli_real_escape_string($DB_site->link_id,$_POST['prenom'])."', email = '".mysqli_real_escape_string($DB_site->link_id,$_POST['email'])."', civilite = '".$_POST['civilite']."', digicode = '".mysqli_real_escape_string($DB_site->link_id,$_POST['digicode'])."', interphone = '".mysqli_real_escape_string($DB_site->link_id,$_POST['interphone'])."', etage = '".mysqli_real_escape_string($DB_site->link_id,$_POST['etage'])."', ascenseur = ".$_POST['ascenseur'].", horairesouverture = '".mysqli_real_escape_string($DB_site->link_id,$_POST['horairesouverture'])."', codeascenseur = '".mysqli_real_escape_string($DB_site->link_id,$_POST['codeascenseur'])."', infoimportante = '".mysqli_real_escape_string($DB_site->link_id,$infoimportante)."' WHERE adresseid = ".$_POST['adresseid'];
               $DB_site2->query($sql);
          }
          
          //Modifications dans la fiche client (Coordonees, Informations complementaires)
          if(isset($_POST['edituser'])){
               //Récupération des informations sur le client
               $sql = "SELECT *";
               $sql .= " FROM utilisateur";
               $sql .= " WHERE userid = '".$_GET['userid']."'";
               $utilisateur = $DB_site2->query_first($sql);
               
               //Mise en forme du texte de suivi
               $txt="[infos_client] Modification infos client";
               
               $sql = "";

               $redirect_to = 'null';
               if(isset($_POST['redirect_to']) && isset($_POST['redirect_from'])) {
                    if($_POST['redirect_from'] != $_POST['redirect_to']) $redirect_to = "'".$_POST['redirect_to']."'";
                }
               //Mise en forme de la requête de modification des infos client
               foreach($utilisateur as $key => $value){
                    if (!isset($_POST['nocomplement'])) {
                        if($key == 'allowemail' && !isset($_POST['allowemail'])) $_POST['allowemail'] = '';
                        //if($key == 'allowsms' && !isset($_POST['SMS'])) $_POST['SMS'] = '';
                        if($key == 'allowsms' && !isset($_POST['allowsms'])) $_POST['allowsms'] = '';
                    }   
                    if($key == 'utilisateur_admin' && !isset($_POST['utilisateur_admin'])) $_POST['utilisateur_admin'] = 0;

                    if(isset($_POST[$key])){
                         switch($key){
                              case 'tvaintracom':
                                   //$result = checkvatnumber($_POST[$key]); mis en commentaire le 28/02/2019 car service indisponible pour le moment
                                   /*if($result == false){
                                        $txt .= "<br />Vérification TVA Intra : Erreur serveur, vérification impossible";
                                        $_POST[$key] = "";
                                   }else{
                                        $txt .= "<br />Vérification TVA Intra ".$_POST[$key]." : ".($result->valid ? "OK" : "Erreur");
                                        $_POST[$key] = ($result->valid ? $result->countryCode.$result->vatNumber : "");
                                   }*/
                                   
                                   break;
                              case 'pourcentage_remise':
                                   $_POST[$key] = str_replace(array(',','¤'), array('.',''), $_POST[$key]);
                                   break;

                               case 'portable':
                                    if(substr($_POST[$key],0,2)=="00"){
                                      $_POST[$key]="+".substr($_POST[$key],2);
                                    }
                                    $_POST[$key] = str_replace(array(" ",".","-"), "", $_POST[$key]);
                                    break;
                               case 'nom':
                               case 'prenom':
                               case 'codepostal':
                               case 'adresse':
                               case 'ville':
                               case 'paysid':
                               case 'raisonsociale':
                                    $sql .= " ".$key."_livraison = '".mysqli_real_escape_string($DB_site2->link_id,trim($_POST[$key]))."',";
                                    break;  
                               case 'telephone':
                                    $_POST[$key] = str_replace(array(" ",".","-"), "", $_POST[$key]);
                                    if(substr($_POST[$key],0,2)=="00"){
                                      $_POST[$key]="+".substr($_POST[$key],2);
                                    }
                                    $sql .= " ".$key."_livraison = '".mysqli_real_escape_string($DB_site2->link_id,trim($_POST[$key]))."',";
                                    break;  

                         }
                         $sql .= " ".$key." = '".mysqli_real_escape_string($DB_site2->link_id,trim($_POST[$key]))."',";
                         if($value != $_POST[$key]){
                              $txt.="<br />".$key." : ".$value." =&gt; ".$_POST[$key];
                         }
                    }
               }
               
               $sql = "UPDATE utilisateur SET ".substr($sql,0,-1).", redirection_site = $redirect_to WHERE userid = ".$_GET['userid'];
               $DB_site2->query($sql);
