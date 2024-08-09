# smartNotes

> Webapp per la gestione di contenuti su più dispositivi in maniera dinamica.
 
> Tutto il progetto è stato completamente ideato, strutturato, realizzato e gestito in maniera autonoma e indipendente.

> I file di questa repo sono ideati per l'utilizzo in un ambiente locale già predisposto all'uso di Ratchet e sono utili solamente per una verifica del suo funzionamento.
> Contengono una versione molto semplificata dello scheletro del progetto, con le funzioni commentate per capirne la logica. Per qualsiasi utilizzo non didattico necessita di revisioni per la messa in sicurezza. 


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
