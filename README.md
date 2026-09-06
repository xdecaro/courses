# Courses

**Courses** è il componente Joomla per la gestione centralizzata di corsi, edizioni, periodi didattici e collegamenti con la piattaforma didattica SILIS.

- Nome visibile: `Courses`
- Componente Joomla: `com_decarocourses`
- Pacchetto Joomla: `pkg_decarocourses`
- Repository GitHub: `xdecaro/courses`
- Versione iniziale: `1.0.0`
- Versione corrente: `1.0.24`
- ZIP componente: `com_decarocourses_1.0.24.zip`
- ZIP completo: `pkg_decarocourses_1.0.24.zip`

## Obiettivo

Courses deve diventare il centro della gestione scolastica: catalogo corsi, edizioni, periodi di iscrizione, stato del corso e associazione opzionale con **Forms by xdecaro** (`com_decaroforms`).

Il componente mantiene separati corso, edizione, periodo/anno accademico, formula, stato operativo, periodo iscrizioni e modulo Forms associato.

## Design system

La UI adotta un design system unico per font, titoli, campi, card, badge, tabelle, filtri, modali, messaggi e responsive. Le azioni globali amministrative usano ora la toolbar nativa Joomla, mentre nel contenuto restano soltanto le azioni contestuali del singolo record.

## Integrazione Forms by xdecaro

L'integrazione è opzionale. Ogni edizione può memorizzare l'ID di un modulo di `com_decaroforms`; in assenza di Forms, Courses continua a funzionare normalmente.

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
