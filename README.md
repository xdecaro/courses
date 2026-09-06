# Courses

**Courses** è il componente Joomla per la gestione centralizzata di corsi, edizioni, periodi didattici e collegamenti con la piattaforma didattica SILIS.

- Nome visibile: `Courses`
- Componente Joomla: `com_decarocourses`
- Pacchetto Joomla: `pkg_decarocourses`
- Repository GitHub: `xdecaro/courses`
- Versione iniziale: `1.0.0`
- Versione corrente: `1.0.29`
- ZIP componente: `com_decarocourses_1.0.29.zip`
- ZIP completo: `pkg_decarocourses_1.0.29.zip`

## Obiettivo

Courses deve diventare il centro della gestione scolastica: catalogo corsi, edizioni, periodi di iscrizione, stato del corso e associazione opzionale con **Forms by xdecaro** (`com_decaroforms`).

Il componente mantiene separati:

- corso;
- edizione del corso;
- periodo/anno accademico;
- formula;
- stato operativo;
- periodo iscrizioni;
- modulo Forms associato.

## Design system

La UI adotta un design system unico per font, titoli, pulsanti, campi, card, badge, tabelle, filtri, modali, messaggi e responsive. Le azioni globali amministrative usano la toolbar nativa Joomla, mentre nel contenuto restano soltanto le azioni contestuali del singolo record.

## Integrazione Forms by xdecaro

L'integrazione è opzionale. Ogni edizione può memorizzare l'ID di un modulo di `com_decaroforms`; in assenza di Forms, Courses continua a funzionare normalmente.

## 1.0.29

Corretto alla radice il cambio fra `Anno singolo` e `Anno accademico` nell'editor Edizione. Durante il refactor della griglia 1.0.28 era stato perso l'attributo `data-dc-period-builder`, quindi il JavaScript del period builder non veniva inizializzato: il radio cambiava visivamente ma il select continuava a mostrare il vecchio periodo. Ripristinato il binding senza duplicare script e mantenuta la sincronizzazione del campo nascosto `academic_year`, della modale `+ Nuovo anno` e del titolo automatico. Eseguita inoltre una pulizia del layout Configurazione: label, control-group, controls, radio, select, input e testo di aiuto condividono ora lo stesso ritmo verticale e le due righe desktop sono realmente allineate (`Tipo periodo | Formula`, `Anno / Periodo | Formula personalizzata`). Tablet e smartphone mantengono l'ordine adattivo già previsto. Nessuna modifica a dati o schema database.

## 1.0.28

Consolidata la strategia responsive delle gestioni amministrative: desktop e tablet privilegiano la vista lista/tabella, mentre la modalità card resta riservata alle larghezze realmente da smartphone. Le regole adattive tengono conto della larghezza effettiva di Courses anche con menu Joomla aperto, senza sovrapposizioni fra label, valori, badge o azioni. Le statistiche di Gestione Corsi restano in quattro colonne finché lo spazio desktop lo consente e passano a 2+2 su tablet. Aggiunte anche quattro statistiche cliccabili in Edizioni — Totale edizioni, Iscrizioni aperte, Programmate e Attive — rispettando l'eventuale filtro corso. Nell'editor Edizione la configurazione è ora una griglia coordinata: Tipo periodo e Formula sono allineati sulla prima riga, Anno / Periodo e Formula personalizzata sulla seconda, mentre su smartphone l'ordine resta verticale e naturale. Nessuna modifica distruttiva a dati o schema database.

## 1.0.27

Introdotto un livello responsive adattivo basato sulla larghezza reale disponibile a `.dc-app`, tramite container queries caricate dopo gli asset esistenti. Corsi, Edizioni, editor, tabelle, filtri, card, griglie e controlli live non dipendono più soltanto dalla larghezza della finestra: quando il menu amministrativo Joomla viene aperto, la finestra viene affiancata o lo spazio disponibile si riduce, il layout si ricompone automaticamente senza compressioni o overflow. Uniformate inoltre le label dei form, incluse `Tipo periodo` e `Anno / Periodo`, e normalizzato il ritmo verticale mobile fra Periodo, Nuovo anno, Formula e Formula personalizzata. Aggiunta la nuova voce amministrativa `Informazioni` con versioni componente/pacchetto, Joomla/PHP, schema database, stato delle tabelle, integrazione Forms, update server e diagnostica copiabile priva di password, token e dati personali. Nessuna modifica distruttiva a dati o schema database.

## 1.0.26

Introdotto un sistema riutilizzabile di aggiornamento live per le sorgenti dati dinamiche di tutto Courses. Nell'editor Edizione i select Corso e Modulo Forms possono essere aggiornati manualmente con `Aggiorna` e vengono riallineati automaticamente quando si ritorna alla scheda dopo aver lavorato in un'altra pagina o scheda, senza polling continuo e senza ricaricare l'intero editor. La selezione corrente viene preservata; se il valore selezionato non esiste più viene mantenuto visibile e segnalato come non disponibile invece di essere cancellato in silenzio. Le sorgenti sono servite da un endpoint Joomla dedicato con token CSRF GET e ACL server-side, mentre la lettura dei dati è centralizzata in `LiveDataHelper` per evitare query duplicate e rendere il meccanismo riutilizzabile da future viste, campi e integrazioni del componente. Aggiunto asset responsive/dark-mode `live-refresh.css`. Nessuna modifica a dati o schema database.

## 1.0.25

Rifiniti i testi della toolbar nativa Joomla dopo la verifica reale della 1.0.24: rimossi i simboli decorativi duplicati dalle etichette `Corsi`, `Nuovo corso` e `Nuova edizione`, riutilizzando le chiavi lingua pulite già presenti mentre le icone restano gestite esclusivamente dalla Toolbar API Joomla. Nessuna modifica a dati, database, task MVC, ACL o layout del contenuto.

## 1.0.24

Migrazione completa delle azioni globali amministrative alla toolbar nativa Joomla. `Nuovo`, `Salva`, `Salva e chiudi`, `Annulla`, `Pubblica`, `Sospendi`, `Cestino`, eliminazione definitiva e `Opzioni` sono ora definiti centralmente tramite `AdminToolbarHelper` e `ToolbarHelper`, eliminando toolbar simulate e grandi blocchi di pulsanti dal contenuto. Corsi ed Edizioni mantengono nel contenuto solo azioni contestuali come Modifica, Edizioni e apertura delle edizioni del singolo corso. Rafforzati ACL lato server nei controller per create/edit/state/delete, preservazione di state/ordering per utenti senza `core.edit.state`, gestione dei record `checked_out`, token CSRF, multiselect Joomla, keepalive e validazione form. Rimossa la vecchia barra Salva fissa custom e il relativo `mobile-ui.js`; CSS e JavaScript sono stati semplificati per affidarsi al responsive nativo Joomla. Nessuna modifica distruttiva a dati o schema database.

## 1.0.23

Centralizzata la gestione degli offset dell'interfaccia Joomla in `admin-ui.js` per tutte le viste `.dc-app`, non più soltanto nell'editor Edizioni. L'altezza reale delle barre superiori e inferiori Atum viene calcolata e scritta nelle variabili CSS condivise `--dc-joomla-sticky-offset` e `--dc-joomla-bottom-offset` anche nella lista Edizioni, con aggiornamento su resize, cambio orientamento, load e modifiche dinamiche di header/subhead. `mobile-ui.js` non duplica più il rilevamento della barra Joomla: usa l'offset centrale e resta responsabile soltanto della safe area e del focus nell'editor smartphone. Aggiunto un evento interno `decarocourses:layoutoffsets` per sincronizzare in modo affidabile i comportamenti mobili dopo ogni ricalcolo. Nessuna modifica a dati o database.

## 1.0.22

Rifinitura finale della lista Edizioni su smartphone dopo il test reale iPhone SE 375×667. Il rilevamento della barra inferiore Joomla ora cerca dinamicamente gli elementi Atum anche quando vengono aggiornati dopo il caricamento iniziale, riallineando `--dc-joomla-bottom-offset` all'altezza effettiva della barra invece di dipendere dal solo fallback CSS. Aggiunti ricalcolo su resize/orientamento/load e osservazione delle modifiche dell'header. Nelle card mobile il titolo del corso e l'ID sono ora esplicitamente allineati a sinistra, mentre la checkbox resta in alto a destra; Periodo, Formula, Stato e Forms restano compatti con valori a destra. Nessuna modifica a dati, database o comportamento tablet/desktop.

## 1.0.21

Rifinitura responsive smartphone di Edizioni dopo i test reali su iPhone SE 375×667 e iPad Air 820×1180. Le righe mobile Periodo, Formula, Stato e Forms sono ora più compatte e uniformi, con label a sinistra e valore allineato a destra. Aumentata la safe area inferiore della lista per evitare che la barra mobile fissa di Joomla copra le card. Nell'editor la barra `Salva / Salva e chiudi / Annulla` è stata compattata su una sola riga su smartphone e viene mantenuta sopra la barra Joomla. Aggiunto inoltre un controllo mobile dedicato che calcola l'offset inferiore reale di Joomla, imposta lo spazio di scorrimento necessario e porta automaticamente sopra la barra azioni il campo che riceve il focus, evitando che radio, select o altri controlli restino nascosti durante la compilazione. Tablet invariato. Nessuna modifica ai dati o allo schema database.

## 1.0.20

Rifinita la visualizzazione della Formula nella lista Edizioni dopo i test reali di salvataggio e persistenza. Quando l'edizione usa `Personalizzato`, la colonna Formula mostra direttamente il valore personalizzato, ad esempio `Test intensivo`, senza il prefisso ridondante `Personalizzato —`. Il database continua a conservare separatamente `format=custom` e `format_custom`, quindi la distinzione tecnica e la validazione restano invariate. Le formule standard continuano a mostrare Annuale, Intensivo, Serale o Weekend. Nessuna modifica ai dati o allo schema del database.

## 1.0.19

Corretto il salvataggio reale delle date nell'editor Edizioni con il CalendarField Joomla localizzato. Con `translateformat` attivo Joomla può restituire internamente una data come `Y-m-d H:i:s` anche se il campo non mostra l'orario; la validazione di Courses accettava invece solo `Y-m-d`, causando il falso errore “Una delle date inserite non è valida”. La normalizzazione ora accetta in modo rigoroso entrambi i formati validi e salva sempre solo la parte data richiesta dal database. Rifinita anche la configurazione iniziale: Periodo e Formula sono organizzati in due colonne logiche indipendenti, e quando Formula è `Personalizzato` il relativo campo resta direttamente sotto Formula invece di estendersi su tutta la card. Su smartphone le due colonne tornano automaticamente a una sola. Nessuna modifica distruttiva ai dati o allo schema database.

## 1.0.18

Corretto l'aggiornamento da installazioni iniziali o parziali nelle quali poteva mancare la tabella `#__decarocourses_editions`. La migrazione storica 1.0.14 ora ricrea in modo non distruttivo le tabelle base mancanti con `CREATE TABLE IF NOT EXISTS` prima di aggiungere `format_custom`, così un aggiornamento da 1.0.2 non si interrompe più con `Table ... decarocourses_editions doesn't exist`. Le tabelle già presenti e i relativi dati non vengono cancellati o sovrascritti. Aggiunto inoltre il marker schema 1.0.18 per mantenere allineata la versione database Joomla. Nessuna modifica alla UI o ai dati esistenti.

## 1.0.17

Rifinito il flusso di compilazione reale dell'editor Edizioni. Una nuova edizione parte sempre da `Anno singolo` con l'anno corrente di Joomla già selezionato; il select Anno/Periodo contiene inizialmente solo quel valore, mentre in modifica conserva il periodo già salvato. `+ Nuovo anno` non espande più la card: apre una modale centrata con inserimento dell'anno di partenza e anteprima automatica del periodo; con `Anno accademico` il valore viene trasformato in `YYYY/YYYY+1`. Dopo Aggiungi, il nuovo anno viene inserito e selezionato immediatamente senza cambiare layout. La barra `Salva / Salva e chiudi / Annulla` è ora fissa in basso nell'editor, dimensionata sulla larghezza reale di Courses e con offset automatico per l'eventuale barra inferiore di Joomla su smartphone, così resta sempre disponibile senza coprire gli ultimi campi. Aggiornati responsive, dark mode e gestione dinamica degli offset Joomla. Nessuna modifica allo schema database.

## 1.0.16

Corretto il comportamento sticky della colonna laterale nell'editor Edizioni. Il pannello `STATO EDIZIONE` non usa più un offset fisso rispetto alla viewport: Courses rileva automaticamente le barre Joomla con posizione sticky/fixed, inclusa la toolbar `subhead`, calcola la loro altezza effettiva e mantiene la card sempre sotto l'interfaccia amministrativa Joomla. L'offset viene ricalcolato anche quando cambia la dimensione della toolbar o della finestra. Il comportamento resta attivo solo sul layout desktop già previsto; tablet e smartphone mantengono il flusso normale. Nessuna modifica ai dati o allo schema database.

## 1.0.15

Rifinito l'editor Edizioni dopo il test UI. Il Periodo non usa più un unico select misto: ora si sceglie prima `Anno singolo` oppure `Anno accademico`, poi un secondo elenco mostra solo gli anni coerenti con la scelta. Aggiunto `+ Nuovo anno`, che permette di inserire un nuovo anno di partenza e genera automaticamente il formato `YYYY/YYYY+1` quando è selezionato Anno accademico, mantenendo la validazione server-side già presente e lo stesso campo database `academic_year`. Unificati Stato operativo e Pubblicazione in una sola card `STATO EDIZIONE / Gestione stato`; la colonna laterale è sticky su desktop e resta vincolata all'area dell'editor, quindi non copre la barra finale Salva/Salva e chiudi/Annulla. La sezione Note mantiene la gerarchia uniforme a tre livelli `DETTAGLI / Informazioni aggiuntive / Note`. Aggiornati stili responsive, dark mode, testi IT/EN e asset versionati. Nessuna modifica ai dati o allo schema applicativo.

## 1.0.14

Redesign e consolidamento completo di Edizioni. Rimosso il Titolo edizione dalla compilazione manuale: il nome interno viene generato automaticamente da corso, periodo e formula. Il campo Anno accademico è diventato Periodo con select controllata per anno singolo (`2026`) o anno accademico (`2026/2027`) e validazione lato server. Aggiunta la formula Weekend e la formula Personalizzato con campo dedicato obbligatorio solo quando necessario, supportata dalla nuova colonna `format_custom` e relativo SQL di aggiornamento. Il campo Posti disponibili chiarisce che `0` significa illimitato. Corretto il caricamento del campo Forms personalizzato e introdotta la lista automatica dei moduli esistenti di Forms by xdecaro, mantenendo Courses funzionante anche senza Forms. L'editor Edizione è stato diviso in card Dati edizione, Calendario, Iscrizioni, Note, Stato edizione e Visibilità, con layout desktop a due aree e adattamento tablet/mobile. La lista Edizioni ora evita il doppione del titolo e mostra Corso, Periodo, Formula, Stato, Forms e Azioni, con card smartphone dedicate, dark mode e stili separati in `editions.css`. Localizzati testi e validazioni IT/EN e mantenuti ACL, token Joomla e query bindate.

## 1.0.13

Pulizia e consolidamento generale di Gestione Corsi senza modificare il layout approvato. Localizzati in IT/EN i testi della lista e dell'editor corso, incluse validazioni e label accessibili; aggiunto `scope` alle intestazioni tabella; ripulite classi CSS obsolete dalle azioni di riga e ridotte regole responsive duplicate. Il filtro stato ora accetta solo i valori previsti e la ricerca viene normalizzata e limitata prima della query. Semplificato inoltre lo script delle azioni massive, sincronizzando in modo esplicito `boxchecked` con le righe selezionate e mantenendo i pulsanti disabilitati senza selezione. Nessuna modifica a database o dati esistenti.

## 1.0.12

Corretto il layout delle azioni nella tabella Corsi su desktop: `Modifica` ed `Edizioni` sono ora sempre sulla stessa riga, in due colonne uguali 50/50, con stessa altezza e larghezza uniforme. La modifica è limitata al breakpoint desktop per non alterare il comportamento già approvato su iPad Air e iPhone.

## 1.0.11

Release di consolidamento UI. Introdotto un sistema pulsanti proprietario di Courses per eliminare le differenze causate da `btn-sm` e dagli outline Bootstrap/Joomla: azioni principali, secondarie, positive, neutre e distruttive condividono ora altezza, font, peso, radius, hover, focus e dark mode. `Modifica` usa il colore primario pieno e `Edizioni` uno stile neutro coerente su desktop, iPad Air e iPhone. Le azioni massive partono disabilitate e si attivano solo quando è selezionata almeno una riga tramite uno script comune. Uniformati Dashboard, Corsi, Edizioni e relativi editor. Nell'editor corso la card laterale usa ora la gerarchia `VISIBILITÀ` → `Pubblicazione` → `Stato`. Aggiunto il caricamento del nuovo asset JavaScript nel manifest Joomla.

## 1.0.10

Rivista la card smartphone di Gestione Corsi secondo il layout a blocchi approvato: checkbox spostata in alto a destra, titolo e ID in testa, dati Codice/Edizioni/Stato/Aggiornato in blocchi compatti e azioni Modifica/Edizioni a due colonne. Modifica ora usa un colore primario pieno mentre Edizioni resta neutro, con adattamento dark mode.

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

Prima revisione completa di Gestione Corsi: elenco con filtri e contatori, collegamento rapido alle edizioni, editor corso più chiaro, responsive smartphone, dark mode, validazione dati, alias univoci e controlli ACL nelle viste amministrative. La pipeline GitHub verifica ora sintassi PHP e XML prima di pubblicare gli aggiornamenti Joomla.

## 1.0.2

Aggiunto il canale di aggiornamento automatico del pacchetto Joomla, con build GitHub, pubblicazione degli ZIP versionati e verifica SHA-256.

## 1.0.1

Correzione installazione database: i percorsi SQL del manifest sono stati corretti e aggiunto lo schema di aggiornamento Joomla per creare automaticamente le tabelle mancanti nelle installazioni 1.0.0.
