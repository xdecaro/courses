# Courses

**Courses** è il componente Joomla per la gestione centralizzata di corsi, edizioni, anni accademici e collegamenti con la piattaforma didattica SILIS.

- Nome visibile: `Courses`
- Componente Joomla: `com_decarocourses`
- Pacchetto Joomla: `pkg_decarocourses`
- Repository GitHub: `xdecaro/courses`
- Versione iniziale: `1.0.0`
- Versione corrente: `1.0.4`
- ZIP componente: `com_decarocourses_1.0.4.zip`
- ZIP completo: `pkg_decarocourses_1.0.4.zip`

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

## 1.0.4

Corretto il caricamento del design system nell'amministrazione Joomla, ripristinando card, griglie, tabelle, filtri e layout degli editor. Tradotto il messaggio quando si esegue un'azione senza selezionare elementi e nascoste le azioni massive quando l'elenco è vuoto.

## 1.0.3

Prima revisione completa della Gestione Corsi: elenco con filtri e contatori, collegamento rapido alle edizioni, editor corso più chiaro, responsive smartphone, dark mode, validazione dati, alias univoci e controlli ACL nelle viste amministrative. La pipeline GitHub verifica ora sintassi PHP e XML prima di pubblicare gli aggiornamenti Joomla.

## 1.0.2

Aggiunto il canale di aggiornamento automatico del pacchetto Joomla, con build GitHub, pubblicazione degli ZIP versionati e verifica SHA-256.

## 1.0.1

Correzione installazione database: i percorsi SQL del manifest sono stati corretti e aggiunto lo schema di aggiornamento Joomla per creare automaticamente le tabelle mancanti nelle installazioni 1.0.0.
