# Applicativo Open Meteo App

## Descrizione

Open Meteo App è un'applicazione che permette di cercare una città, scaricare i dati storici delle temperature orarie da Open-Meteo e visualizzarli in tabella e in forma aggregata. Con essa, possiamo salvare i dati nel database per un successivo riutilizzo e calcolare automaticamente le statistiche giornaliere (min, max, media) e quelle aggregate per il periodo selezionato.

---

## Funzionalità implementate

-   Ricerca città tramite API Open-Meteo Geocoding
-   Salvataggio città nel database
-   Download dati storici temperatura (Archive API)
-   Salvataggio dati storici nel database (in forma aggregata giornaliera)
-   API interne per restituzione statistiche aggregate
-   Frontend con tabella dati e box statistiche

---

## Struttura del codice

### Controller

-   **`WeatherDataController`**: Gestisce tutte le operazioni principali:
    -   `store()`: Salva i dati di una città e relativi dati meteo in un intervallo di date richiesto
    -   `aggregaDati()`: Calcola e restituisce le statistiche aggregate

### Note di funzionamento

-   La logica di chiamata alle API "Historical Weather" è gestita direttamente nel controller (per semplicità del progetto)
-   Funzioni ausiliarie come `insertWeatherData()` per il salvataggio dei dati elaborati
-   I dati orari (fino a 24 valori/giorno) vengono aggregati a livello server,
così da ridurre il traffico tra Front-end e Back-end
-   Memorizzazione ottimizzata (1 record/giorno invece di 24)

### Modelli e relazioni

-   **`City`**: Rappresenta una città con:
     ```php
    public function city(): BelongsTo { ... }
    ```
-   **`WeatherData`**: Rappresenta i dati meteo giornalieri con:
    ```php
    public function weatherData(): HasMany { ... }
    ```
-   Relazione one-to-many tra città e dati meteo


### Database MySQL

-   **`cities` table**:
    -   id, name, country, latitude, longitude
-   **`weather_data` table**:
    -   city_id (foreign key)
    -   avg_temperature_date (data giornaliera)
    -   avg_temperature, max_temperature, min_temperature

### Interfaccia utente con Blade

-   **`form.blade.php`**: Interfaccia utente completa con:
    -   Ricerca città con autocompletamento
    -   Selezione date di inizio/fine
    -   Tabella dati giornalieri
    -   Box statistiche aggregate
    -   Gestione errori in tempo reale
---

## Scelte tecniche

### Calcolo delle statistiche aggregate

1. **Giornaliere**:

    - Raggruppamento per data dei dati orari
    - Calcolo min/max/avg per ogni giorno
    - Salvataggio in database come singolo record giornaliero

2. **Per periodo**:

    - Media globale delle temperature medie giornaliere
    - Minima assoluta tra tutte le minime giornaliere
    - Massima assoluta tra tutte le massime giornaliere

### Validazione dati

-   **Lato Back-end mediante il metodo nativo di Laravel "validate"**:
    ```php
    $request->validate([
        'cityId' => 'required|integer',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date'
    ]);
    ```

-   **Lato Front-end mediante le funzioni handleValidationError() e isValidDate()**:
    ```javascript
    function handleValidationErrors(errors) {
        const startDateInput = document.getElementById("start-date-input");
        const endDateInput = document.getElementById("end-date-input");
        // ...
        ______

    const isValidDate = (inputDate) => {
      const date = new Date(inputDate);
      return !isNaN(date.getTime());
    }
    ```

### Gestione degli errori

-   **Lato Front-end: utilizzo di error label**:
    ```javascript
        const errorLabel = document.createElement("label");
        errorLabel.id = "start-date-error";
        errorLabel.className = "text-danger";
        errorLabel.textContent = Array.isArray(errors.start_date) ? errors.start_date[0] : errors.start_date;
        startDateInput.insertAdjacentElement("afterend", errorLabel);
        // ...
    ```
-   **Lato Back-end: logging approfondito degli errori nel file log**:
    ```php
    return response()->json(['error' => 'Error occurred in server.'], 500);
    ```

### Note di sviluppo

-   **Approccio ibrido Eloquent/Query pure**:

    -   Utilizzo di query pure per operazioni complesse di aggregazione e maggiore flessibilità nella query
    -   Mantenimento delle relazioni Eloquent per semplificare potenziali interventi futuri
    -   Esempio di query pura ottimizzata:
        ```php
        DB::selectOne(
            "SELECT * FROM cities WHERE name = ? AND country = ? ...",
            [$cityName, $country, ...]
        );
        ```

-   **Front-end leggero**:

    -   JavaScript puro senza framework esterni
    -   Integrazione diretta con le API Laravel
    -   Gestione dinamica della UI senza ricaricamenti di pagina

---

## Utilizzo

1. Inserire il nome di una città nel campo di ricerca
2. Selezionare le date di inizio e fine
3. Cliccare su "Conferma" per visualizzare i dati

L'applicazione mostrerà:

-   Tabella con dati giornalieri (data, media, min, max)
-   Box con statistiche aggregate per il periodo selezionato
-   Feedback in tempo reale per errori di validazione o problemi API