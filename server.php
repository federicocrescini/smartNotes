<?php
set_time_limit(0); // imposta il tempo di esecuzione del server ad illimitato
use Ratchet\MessageComponentInterface;
// ^ fornisce la funzione onMessage
use Ratchet\ConnectionInterface;
// ^ fornisce onOpen, onClose, onError
require_once './ws/vendor/autoload.php';

class Chat implements MessageComponentInterface {

	protected $clients;
	// ^ oggetto Connection
	protected $users;
	// ^ array per fare riferimento alle connessioni in base agli utenti collegati

	/* vvv Creazione WS vvv
	 l'oggetto clients viene gestito tramite SplObjStor,
	 ma si potrebbe anche usare un array.
	 Conterrà gli oggetti di chi si connetterà alla WS */

	public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo " +++ APERTURA SERVER +++\n";	
    }


	/*vvv La WS apre una connessione vvv
	  
	  Un Client richiede una connessione WS su indirizzo:porta inviando un json avente parametri
		{	'type' : 'conn'
			'user_id' : id_sessione_client
		}

	  Il Server istanzia le risorse necessarie alla comunicazione; 
		*/
		
	public function onOpen(ConnectionInterface $conn) {
		echo "\n";
		$this->clients->attach($conn);
		$this->users[$conn->resourceId] = $conn;
		echo " > onOpen: {$conn->resourceId}\n"; /* -> {$data->user_id}.\n";*/		
	}

	/* vvv Una connessione viene Interrotta vvv
	
		tramite funzione detatch lo rimuove dall'istanza della WS
		(lo "dimentica" disconnettendolo)*/
	public function onClose(ConnectionInterface $conn) {
		$x = $conn->resourceId;
		$this->clients->detach($conn);
		echo " > $x : Disconnesso\n";
		unset($this->users[$x]);
		// resourceID viene salvato in $x perchè diventa inutilizzabile dopo la detatch
	}


	/* vvv Ricezione Messaggio vvv
		riceve in input l'oggetto del mittente e il json con i dati incapsulati
		- memorizza il codice associato alla connessione del mittente
		- decodifica il json contenente i messaggi type, user_id e chat_msg
		- legge il valore di type e si comporta a dovere
			conn : sovrascrive il campo ID con quello della sessione
			link : 
				salva il valore di msg
				salva il l'id della sessione
				salva l'id dell'oggetto
				sovrascrive l'id oggetto con l'id sessione
				
				scorro gli oggetti degli utenti collegati alla socket
					invio a tutti i collegamenti dello stesso utente il messaggio ricevuto*/
	
	public function onMessage(ConnectionInterface $from,  $data) {
		$from_id = $from->resourceId;
		/* ^ valore utile per riconoscere il Client Mittente;
		   tuttavia più avanti il valore resourceId viene sovrascritto a favore della comunicazione "broadcast"*/
		$data = json_decode($data);
		$type = $data->type;
		switch ($type) {

			/* In caso arrivi un messaggio di Connessione
				viene salvato l'ID di sessione dell'utente ($data->user_id)
				nel campo dell' ID della ConnectionInterface (from->resourceId).
				in modo che si faccia riferimento alle connessioni in base all'
				Utente che le ha instaurate.

				Viene stampato il messaggio di avvenuta connessione;
				il campo from_id viene mostrato per puro debug, non avendo più valore nel sistema.

				Ora l'oggetto from contiene l'Id della Sessione del Client connesso
				vvv	vvv	vvv*/
				case 'conn':{
					$from->resourceId = $data->user_id;
					echo "\n[{$type}] > User $data->user_id Connected from {$from_id}\n";
			}
			break;
			
			/* In caso arrivi un messaggio di tipo Link
				vengono salvati i dati del json ricevuto (ID Utente, Link da inoltrare)
		
				Si stampa il messaggio "User X, Message Z"

				Scorro tutti gli oggetti delle connessioni
				se l'ID della connessione combacia con l'ID Sessione dell'Utente
					inoltro al client il messaggio ricevuto*/
			case 'link':{
				$link = $data->msg;
				$id = $data->user_id;
				
				echo "\n[{$type}] > U: $id sent {$link}\n";

				foreach($this->users as $user) {
					if($user->resourceId == $id) {
						$user->send(json_encode([
							"type"=>$type,
							"msg"=>$link
						]));
					}
				}
			}
			break;
		}
	}

	/* vvv In caso di Errore vvv
		la connessione viene abortita e viene stampato un messaggio generico*/
	public function onError(ConnectionInterface $conn, \Exception $e) {
		$conn->close();
		echo "!!!errore!!!\n";
	}
}
$server = new Ratchet\App('localhost', 80);
$server->route('/', new Chat, ['*']);
$server->run();
?>
