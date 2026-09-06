# Courses

**Courses** è il componente Joomla per la gestione centralizzata di corsi, edizioni, anni accademici e collegamenti con la piattaforma didattica SILIS.

- Nome visibile: `Courses`
- Componente Joomla: `com_decarocourses`
- Pacchetto Joomla: `pkg_decarocourses`
- Repository GitHub: `xdecaro/courses`
- Versione iniziale: `1.0.0`
- Versione corrente: `1.0.13`
- ZIP componente: `com_decarocourses_1.0.13.zip`
- ZIP completo: `pkg_decarocourses_1.0.13.zip`

## Obiettivo

Courses deve diventare il centro della gestione scolastica: catalogo corsi, edizioni, periodi di iscrizione, stato del corso e associazione opzionale con **Forms by xdecaro** (`com_decaroforms`).

Il componente mantiene separati:

- corso;
- edizione del corso;
- anno accademico;
- stato operativo;
- periodo iscrizioni;
- modulo Forms associato.

## Design system

La UI adotta un design system unico per font, titoli, pulsanti, campi, card, badge, tabelle, filtri, modali, messaggi e responsive. Il riferimento visivo è la gestione SILIS già approvata, senza duplicare stili diversi tra i vari moduli.

## Integrazione Forms by xdecaro

L'integrazione è opzionale. Ogni edizione può memorizzare l'ID di un modulo di `com_decaroforms`; in assenza di Forms, Courses continua a funzionare normalmente.

## 1.0.13

Pulizia e consolidamento generale di Gestione Corsi senza modificare il layout approvato. Localizzati in IT/EN i testi della lista e dell'editor corso, incluse validazioni e label accessibili; aggiunto `scope` alle intestazioni tabella; ripulite classi CSS obsolete dalle azioni di riga e ridotte regole responsive duplicate. Il filtro stato ora accetta solo i valori previsti e la ricerca viene normalizzata e limitata prima della query. Semplificato inoltre lo script delle azioni massive, sincronizzando in modo esplicito `boxchecked` con le righe selezionate e mantenendo i pulsanti disabilitati senza selezione. Nessuna modifica a database o dati esistenti.

## 1.0.12

Corretto il layout delle azioni nella tabella Corsi su desktop: `Modifica` ed `Edizioni` sono ora sempre sulla stessa riga, in due colonne uguali 50/50, con stessa altezza e larghezza uniforme. La modifica è limitata al breakpoint desktop per non alterare il comportamento già approvato su iPad Air e iPhone.

## 1.0.11

Release di consolidamento UI. Introdotto un sistema pulsanti proprietario di Courses per eliminare le differenze causate da `btn-sm` e dagli outline Bootstrap/Joomla: azioni principali, secondarie, positive, neutre e distruttive condividono ora altezza, font, peso, radius, hover, focus e dark mode. `Modifica` usa il colore primario pieno e `Edizioni` uno stile neutro coerente su desktop, iPad Air e iPhone. Le azioni massive partono disabilitate e si attivano solo quando è selezionata almeno una riga tramite uno script comune. Uniformati Dashboard, Corsi, Edizioni e relativi editor. Nell'editor corso la card laterale usa ora la gerarchia `VISIBILITÀ` → `Pubblicazione` → `Stato`. Aggiunto il caricamento del nuovo asset JavaScript nel manifest Joomla.

## 1.0.10

Rivista la card smartphone di Gestione Corsi secondo il layout a blocchi approvato: checkbox spostata in alto a destra, titolo e ID in testa, dati Codice/Edizioni/Stato/Aggiornato in blocchi compatti e azioni Modifica/Edizioni a due colonne. Modifica ora usa un colore primario pieno mentre Edizioni resta neutro, con adattamento dark mode. Il responsive viene caricato globalmente insieme al design system per mantenere lo stesso comportamento nelle viste amministrative.

## 1.0.9

Ottimizzato Gestione Corsi per tablet, con riferimento iPad Air: eliminato l'overflow orizzontale della tabella, nascosta la colonna Aggiornato su tablet riportando la data sotto il titolo, ridotte e stabilizzate le colonne secondarie e mantenute le azioni sempre leggibili. La barra filtri ora usa tutta la larghezza disponibile senza lasciare spazio vuoto a destra e resta coerente tra desktop, tablet e smartphone. Differenziate inoltre le azioni Modifica ed Edizioni con una gerarchia visiva più chiara.

## 1.0.8

Uniformata la barra filtri di Edizioni a quella di Corsi, aggiungendo il filtro per stato operativo. Quando Edizioni viene aperto da un corso specifico, il banner mostra il nome reale del corso anziché il solo ID. Il comando Azzera pulisce ricerca e stato mantenendo il corso selezionato, mentre Mostra tutte rimuove esplicitamente il filtro corso.

## 1.0.7

Rimosse le toolbar Joomla dalle viste di Courses: creazione, salvataggio, pubblicazione, sospensione e cestino sono ora gestiti dentro l'interfaccia del componente. Uniformati i form con label sopra i campi, Titolo e Descrizione a tutta larghezza, intestazioni di sezione più leggibili, barra azioni interna e responsive smartphone. Codice corso e alias restano modificabili ma vengono generati automaticamente quando lasciati vuoti.

## 1.0.6

Corretto definitivamente il caricamento del design system tramite Web Asset Manager e `joomla.asset.json`, applicandolo in modo centrale a Dashboard, Corsi, Edizioni e relativi editor.

## 1.0.4

Corretto il caricamento del design system nell'amministrazione Joomla, ripristinando card, griglie, tabelle, filtri e layout degli editor. Tradotto il messaggio quando si esegue un'azione senza selezionare elementi e nascoste le azioni massive quando l'elenco è vuoto.

## 1.0.3

Prima revisione completa della Gestione Corsi: elenco con filtri e contatori, collegamento rapido alle edizioni, editor corso più chiaro, responsive smartphone, dark mode, validazione dati, alias univoci e controlli ACL nelle viste amministrative. La pipeline GitHub verifica ora sintassi PHP e XML prima di pubblicare gli aggiornamenti Joomla.

## 1.0.2

Aggiunto il canale di aggiornamento automatico del pacchetto Joomla, con build GitHub, pubblicazione degli ZIP versionati e verifica SHA-256.

## 1.0.1

Correzione installazione database: i percorsi SQL del manifest sono stati corretti e aggiunto lo schema di aggiornamento Joomla per creare automaticamente le tabelle mancanti nelle installazioni 1.0.0.
