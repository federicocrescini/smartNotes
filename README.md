# smartNotes

> Webapp per la gestione di contenuti su più dispositivi in maniera dinamica.
 
> Tutto il progetto è stato completamente ideato, strutturato, realizzato e gestito in maniera autonoma e indipendente.

> I file di questa repo sono ideati per l'utilizzo in un ambiente locale già predisposto all'uso di Ratchet e sono utili solamente per una verifica del suo funzionamento.
> Contengono una versione molto semplificata dello scheletro del progetto, con le funzioni commentate per capirne la logica. Per qualsiasi utilizzo non didattico necessita di revisioni per la messa in sicurezza. 

---

### Logica

WebApp costruita tramite [Ratchet](http://socketo.me/docs/deploy) che permette ad un utente loggato su più dispositivi di aprire link su entrami i browser.

Client e Server WebSocket comunicano tramite messagi json:
	richiesta di connessione: { type = 'conn' , user_id }
	apertura link : 	  { type = 'link' , user_id, link }

Nel Database vengono memorizzati gli utenti registrati (ID, username, hash password) e i link che memorizzano.
#### Vista Utente:
    Un utente tramite Login accede alla piattaforma da diversi dispositivi;
    Tramite l'interfaccia della WebApp sceglie quale link (tra quelli precedentemente inseriti) aprire;
    Tutti i Browser su cui l'utente si è registrato aprono in una nuova finestra il link scelto;
    

#### Backend Client:
    Al momento del Login, l'utente avvia una Sessione contenente l' ID con cui è memorizzato nel Database (ID: campo della tabella Utente, numero intero univoco);
    Visualizzando la pagina principale viene effettuata una richiesta al Server (con l'invio di un messaggio Json "conn" + Id) di connettersi alla WebSocket;
    Una volta accettata la connessione il Server indirizzera' i messaggi ricevuti da un Utente a tutte* le connessioni che ha instaurato dai vari browser.
    
    \*tutte le connessioni attive ed in attesa sulla pagina principale: una volta cambiata pagina, la connessione con il Server viene interrotta e le risorse deallocate, in modo da evitare di esaurire le risorse per connessioni inattive in favore di una comunicazione maggiormente Event-Driven.
 

#### Richiesta Link:
	- l'Utente seleziona un link dal menù visualizzato nella WebApp;
	- viene passato l'identificativo della scelta al Server;
	- il Server effettua la Query per ottenere dal database il Link associato all'ID;
	- il link viene inviato al Server WS in un campo del messaggio json;
	- il messaggio viene instradato in ogni connessione che l'utente ha instaurato;


#### Backend Server:
    Il Server WS riceve le richieste di connessione da parte di un Client, contenenti l'ID Utente;
    Vengono allocate in un oggetto Connessione tutte le risorse utili alla comunicazione Client-Server, includendo l'ID dell'Utente a cui appartiene la richiesta;
    Quando un client invia una richiesta di apertura link, il Server WS scorre tutti gli Oggetti, ed invia il messaggio a tutti i Client aventi lo stesso proprietario;



#### Funzioni Attive (non presenti nel codice di Demo):
    - Registrazione / Login
	- Inserimento / rimozione Link
	- Apertura Link su più dispositivi

#### Funzioni progettate da Implementare:
	- utilizzo senza login
	- selezione dispositivi destinatari
	- invio file
	- funzione Clipboard
	- comunicazione tra piu' utenti

---

### 1 / 2

File associati ai due utenti di prova contenenti link utilizzabili per la demo, corrispondenti a tabelle di Database popolati.

---
### index.php

1. Un utente registrato si collega al server, inviando un messaggio dove indica l'intenzione di instaurare una connessione WS e il proprio Id.

2. Una volta collegato alla WebSocket ed inviato il messaggio con il dato da condividere, viene aperto un pop-up su ogni browser da cui è connesso.

---

### server.php

1. Una volta avviato, il server rimane in attesa di connessioni.

2. Quando un Utente vi si collega, associa l'identificativo della connessione a quello dell'Utente.

3. Quando riceve un messaggio da condividere, scorre l'elenco delle connessioni e lo invia a tutte quelle associate a quello specifico utente.

---

### Caso d'uso base:
Permette ad un utente registrato contemporaneamente su due dispositivi di farli comunicare rapidamente nello scambio di informazioni. 
Utilizzando adesivi RFID correttamente impostati la webapp permette di utilizzare il telefono come "mouse" per l'apertura di file e link sul computer.
