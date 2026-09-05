# Courses

**Courses** è il componente Joomla per la gestione centralizzata di corsi, edizioni, anni accademici e collegamenti con la piattaforma didattica SILIS.

- Nome visibile: `Courses`
- Componente Joomla: `com_decarocourses`
- Pacchetto Joomla: `pkg_decarocourses`
- Repository GitHub: `xdecaro/courses`
- Versione iniziale: `1.0.0`
- ZIP componente: `com_decarocourses_1.0.0.zip`
- ZIP completo: `pkg_decarocourses_1.0.0.zip`

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
