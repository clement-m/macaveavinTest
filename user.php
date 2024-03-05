<?php

//Modifications du carnet d'adresses client
if(isset($_POST['edit_adresses_user'])) {
	//Récupération des informations sur les adresses
	$sql = ""
		. "SELECT * "
		. "FROM adresses "
		. "WHERE adresseid = '" . $_POST['adresseid'] . "' "
	;

	$adresse = $DB_site2->query_first($sql);

	//Mise en forme du texte de suivi
	$txt = "[infos_client] Modification de l'adresse client";

	function textPrepare(&$txt, $backData, $fieldName, $testToAdd) {
		if($backData[$fieldName] != $_POST[$fieldName]) {
			$txt .= "<br>"
				. $testToAdd
				. " : "
				. $backData[$fieldName]
				. " => "
				. $_POST[$fieldName]
			;
		}
	}

	textPrepare($txt, $adresse, 'raisonsociale'	, 'Société');
	textPrepare($txt, $adresse, 'nom'						, 'Nom');
	textPrepare($txt, $adresse, 'prenom'				, 'Prenom');
	textPrepare($txt, $adresse, 'adresse'				, 'Adresse');
	textPrepare($txt, $adresse, 'codepostal'		, 'Code postal');
	textPrepare($txt, $adresse, 'ville'					, 'Ville');
	textPrepare($txt, $adresse, 'telephone'			, 'Tel');
	textPrepare($txt, $adresse, 'portable'			, 'Portable');
	textPrepare($txt, $adresse, 'digicode'			, 'Digicode');
	textPrepare($txt, $adresse, 'interphone'		, 'Interphone');
	textPrepare($txt, $adresse, 'etage'					, 'Etage');
	textPrepare($txt, $adresse, 'ascenseur'			, 'Ascenseur');
	textPrepare($txt, $adresse, 'codeascenseur'	, 'Code ascenseur');

	/*
		/!\ $user_info['horairesouverture'] ici. Je pense que c'est une erreur ?

		if($adresse['horairesouverture'] != $_POST['horairesouverture']) $txt .= "<br>Horaires ouverture : " . $user_info['horairesouverture'] . " => " . $_POST['horairesouverture'];

		En temps normal j'irais chercher si $user_info est instancié plus tôt dans le code, si j'ai bien tous le code alors il s'agit d'une erreur de copié coller
		surement...

		- option 1 - Ce n'est pas une erreur je laisse
		if($adresse['horairesouverture'] != $_POST['horairesouverture'])
			$txt .= "<br>Horaires ouverture : " . $user_info['horairesouverture'] . " => " . $_POST['horairesouverture'];

		option 2 - C'est une erreur je corrige (et vérifie bien sure ce qu'on voyait avant)
			textPrepare($txt, $adresse, 'horairesouverture', 'Horaires ouverture');
	*/

	textPrepare($txt, $adresse, 'infoimportante', 'Info importante');

	$sql = ""
		. "SELECT etat "
		. "FROM suivi_client "
		. "WHERE userid = " . $_GET['userid'] . " "
		. "ORDER BY date DESC "
		. "LIMIT 1"
	;

	$last = $DB_site2->query_first($sql);

	if($last == "") {
		$last['etat'] = 1;
	}

	//Insertion dans le suivi
	$sql = ""
		. " INSERT INTO suivi_client"
		. " SET"
		. "  userid 			= '" . $_GET['userid'] . "' "
		. ", commercial 	= '" . $user_info['username'] . "' "
		. ", date 				= "  . time()
		. ", suivi 				= '" . mysqli_real_escape_string($DB_site2->link_id, $txt) . "' "
		. ", etat 				= "  . intval($last['etat'])
		. ", compte 			= 0"
	;

	$DB_site2->query($sql);

	function escapeString($db, $data) {
		return mysqli_real_escape_string($db->link_id, $data);
	}

	$sql = ""
		. "UPDATE adresses "
		. "SET "
		. "  ville 							= '" . escapeString($DB_site, $_POST['ville']) 							. "' "
		. ", adresse 						= '" . escapeString($DB_site, $_POST['adresse']) 						. "' "
		. ", codepostal 				= '" . escapeString($DB_site, $_POST['codepostal']) 				. "' "
		. ", telephone 					= '" . $_POST['telephone'] 																	. "' "
		. ", raisonsociale 			= '" . escapeString($DB_site, $_POST['raisonsociale']) 			. "' "
		. ", civilite 					= '" . escapeString($DB_site, $_POST['civilite']) 					. "' "
		. ", nom 								= '" . escapeString($DB_site, $_POST['nom']) 								. "' "
		. ", prenom 						= '" . escapeString($DB_site, $_POST['prenom']) 						. "' "
		. ", email 							= '" . escapeString($DB_site, $_POST['email']) 							. "' "
		. ", civilite 					= '" . $_POST['civilite'] 																	. "' "
		. ", digicode 					= '" . escapeString($DB_site, $_POST['digicode']) 					. "' "
		. ", interphone 				= '" . escapeString($DB_site, $_POST['interphone']) 				. "' "
		. ", etage 							= '" . escapeString($DB_site, $_POST['etage']) 							. "' "
		. ", ascenseur 					= "  . $_POST['ascenseur'] 																	. " "
		. ", horairesouverture 	= '" . escapeString($DB_site, $_POST['horairesouverture']) 	. "' "
		. ", codeascenseur 			= '" . escapeString($DB_site, $_POST['codeascenseur']) 			. "' "

		// /!\ d'ou vient $infoimportante ? si le script est complet alors on corrige
		// donc : $infoimportante => $_POST['infoimportante']
		. ", infoimportante 		= '" . escapeString($DB_site, $infoimportante) . "' "
		// /!\

		. "WHERE adresseid = " . $_POST['adresseid']
	;

	/*
		$infoimportante ? je ne vois pas sa déclaration
		. ", infoimportante 		= '" . espaceString($DB_site, /!\ $infoimportante /!\) . "' "
	*/

	$DB_site2->query($sql);
}

//Modifications dans la fiche client (Coordonees, Informations complementaires)
if(isset($_POST['edituser'])) {
	//Récupération des informations sur le client
	$sql = ""
		. "SELECT * "
		. "FROM utilisateur "
		. "WHERE userid = '" . $_GET['userid'] . "'"
	;

	$utilisateur = $DB_site2->query_first($sql);

	//Mise en forme du texte de suivi
	$txt="[infos_client] Modification infos client";

	$sql = "";

	$redirect_to = 'null';
	if(isset($_POST['redirect_to'])
	&& isset($_POST['redirect_from'])) {
		if($_POST['redirect_from'] != $_POST['redirect_to']) {
			$redirect_to = "'" . $_POST['redirect_to'] . "'";
		}
	}

	//Mise en forme de la requête de modification des infos client
	foreach($utilisateur as $key => $value) {
		if(!isset($_POST['nocomplement'])) {

			if($key == 'allowemail'
			&& !isset($_POST['allowemail'])) {
				$_POST['allowemail'] = '';
			}

			//if($key == 'allowsms' && !isset($_POST['SMS'])) $_POST['SMS'] = '';

			if($key == 'allowsms'
			&& !isset($_POST['allowsms'])) {
				$_POST['allowsms'] = '';
			}
		}

		if($key == 'utilisateur_admin'
		&& !isset($_POST['utilisateur_admin'])) {
			$_POST['utilisateur_admin'] = 0;
		}

		if(isset($_POST[$key])) {
			switch($key) {
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
					$_POST[$key] = str_replace(
						array(',', '¤'),
						array('.', ''),
						$_POST[$key])
					;
				break;

				case 'portable':
					if(substr($_POST[$key], 0, 2) == "00") {
						$_POST[$key]= "+" . substr($_POST[$key], 2);
					}

					$_POST[$key] = str_replace(array(" ", ".", "-"), "", $_POST[$key]);
				break;

				case 'nom':
				case 'prenom':
				case 'codepostal':
				case 'adresse':
				case 'ville':
				case 'paysid':
				case 'raisonsociale':
					$sql .= " " . $key . "_livraison = '" . mysqli_real_escape_string($DB_site2->link_id, trim($_POST[$key])) . "',";
				break;

				case 'telephone':
					$_POST[$key] = str_replace(array(" ", ".", "-"), "", $_POST[$key]);

					if(substr($_POST[$key], 0, 2) == "00") {
						$_POST[$key] = "+" . substr($_POST[$key], 2);
					}

					$sql .= " " . $key . "_livraison = '" . mysqli_real_escape_string($DB_site2->link_id, trim($_POST[$key])) . "',";
					break;  
			}

			$sql .= " " . $key . " = '" . mysqli_real_escape_string($DB_site2->link_id, trim($_POST[$key])) . "',";

			if($value != $_POST[$key]) {
				$txt .= "<br />" . $key . " : " . $value . " =&gt; " . $_POST[$key];
			}
		}
	}

	$sql = ""
		. "UPDATE utilisateur "
		. "SET "
		. "" . substr($sql, 0, -1) . " "
		. ", redirection_site = " . $redirect_to . " "
		. "WHERE userid = " . $_GET['userid']
	;

	$DB_site2->query($sql);
} // } manquante + rajout d'un saut de ligne en fin de fichier pour les bonnes pratiques
