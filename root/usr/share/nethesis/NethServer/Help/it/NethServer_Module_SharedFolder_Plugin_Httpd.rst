.. --initial-header-level=3

Accesso web
^^^^^^^^^^^

Abilita l'accesso da web alla cartella condivisa.

Host virtuale
    Permette di scegliere da quale nome di host è accessibile la
    cartella condivisa. L'elenco è modificabile tramite la scheda
    "Alias server" del modulo "DNS e DHCP". 
    
Indirizzo web (URL)
    Definisce l'indirizzo web a cui è disponibile la risorsa.

Consenti l'accesso solo dalle reti fidate
    Se abiltato, limita l'accesso alla risorsa alle sole reti fidate.

Richiedi la password
    L'accesso alla risorsa da web non richiede alcuna
    autenticazione. Abilitare questa opzione per richiedere una
    password: specificarla nel campo sottostante.

Richiedi connessione SSL cifrata
    I client web saranno obbligati ad accedere alla cartella condivisa tramite
    il protocollo HTTPS.

Consenti override di .htaccess e dei permessi di scrittura
    Un file ``.htaccess`` nella radice della cartella condivisa viene incluso
    come file di configurazione del server web. Vedere `Apache .htaccess howto`_.
    Un file ``.htwritable`` sempre nella radice della cartella condivisa abilita
    i permessi di scrittura per il server web su sotto-cartelle specifiche.
    Il contenuto di quel file è una lista di sotto-directory (una per riga)
    su cui il server web acquisisce i diritti di scrittura.

.. _Apache .htaccess howto: http://httpd.apache.org/docs/2.2/howto/htaccess.html