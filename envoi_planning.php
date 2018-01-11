<?php
include('../grr/include/connect.inc.php');


	try
	{
		$bdd = new PDO('mysql:host='.$dbHost.';dbname='.$dbDb, $dbUser, $dbPass);
	}
	catch(Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
        $periode =  mktime(0, 0, 0, date("m"), date("d")+8,   date("Y"));
        $to      = 'claire.le-ble@neuf.fr,af.grignon@gmail.com';
        $subject = 'Liste des prochaines réservations';
       // $message = "Bonjour"."\r\n";
        // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8 Content-Transfer-Encoding: 8bit' . "\r\n";
        $headers .= 'From: Fourmicite <contact@fourmicite.fr>' . "\r\n";
        
        $message = "<html><head><title>Calendrier des réservations</title></head>";
        $message .=" <body><br> Réservation pour les 8 prochains jours<br>";
        $message .= '<p><table Border="1" width="100%"><tr><th>Description</th><th>Début</th><th>Fin</th><th>Créer par</th><th>Salle</th></tr>';
                
        $req="SELECT ro.room_name, entry.* FROM grr_room ro,grr_entry entry WHERE ro.id=entry.room_id";
        $req .= " and  start_time > ".time()." and start_time < ".$periode." order by start_time ASC";
	//echo '<br> requete :'.$req;
        
        $response=$bdd->query($req);
	while ($data=$response->fetch())
	{
		$create_by=$data['create_by'];
		$description=$data['name'];
		$debut=$data['start_time'];
                $fin=$data['end_time'];
                $room_id=$data['room_name'];
		//echo "<br>Creer par : ".$create_by."  - description : ".$description." - debut  : ".date('d-M-Y H:i',$debut)." - fin : ".date('d-M-Y H:i',$fin)." salle: ".$room_id;
                $message .= "<tr><td>".$description."</td><td>".date('d-M-Y H:i',$debut)."</td><td>".date('d-M-Y H:i',$fin)."</td><td>".$create_by."</td><td>".$room_id."</td></tr>";
	}
	
        $response->CloseCursor();
	$message .= '</table><p>Cordialement<br><B>Fourmicite réservation</B></body></html>';
        mail($to, $subject, $message, $headers);
        echo $message;
?>