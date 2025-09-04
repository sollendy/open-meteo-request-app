# Weather History Dashboard

## Descrizione
Applicazione Laravel che permette di cercare una città, scaricare i dati storici delle temperature orarie da Open-Meteo e visualizzarli in tabella e in forma aggregata. L'applicazione salva i dati nel database per un successivo riutilizzo e calcola automaticamente le statistiche giornaliere (min, max, media) e quelle aggregate per il periodo selezionato.

---

## Funzionalità implementate
- [x] Ricerca città tramite API Open-Meteo Geocoding
- [x] Salvataggio città nel database
- [x] Download dati storici temperatura (Archive API)
- [x] Salvataggio dati storici nel database (in forma aggregata giornaliera)
- [x] API interne per restituzione statistiche aggregate
- [x] Frontend con tabella dati e box statistiche
**Extra:**
- [ ] Cache locale dei dati
- [ ] Grafico delle temperature (es. Chart.js)
- [ ] Seed iniziale con città predefinite
- [ ] Test unitari

---

## Struttura del codice

### Controller
- **`WeatherDataController`**: Gestisce tutte le operazioni principali:
  - `store()`: Salva i dati meteo per una città e un intervallo di date
  - `aggregaDati()`: Calcola e restituisce le statistiche aggregate
  - Implementa la logica per il salvataggio delle città e l'elaborazione dei dati meteo

### Service
- La logica di chiamata alle API esterne è gestita direttamente nel controller (per semplicità del progetto)
- Funzioni ausiliarie come `insertWeatherData()` per il salvataggio dei dati elaborati

### Model e relazioni
- **`City`**: Rappresenta una città con:
  ```php
  public function weatherData(): HasMany { ... }
  ```
- **`WeatherData`**: Rappresenta i dati meteo giornalieri con:
  ```php
  public function city(): BelongsTo { ... }
  ```
- Relazione one-to-many tra città e dati meteo

### View Blade
- **`form.blade.php`**: Interfaccia utente completa con:
  - Ricerca città con autocompletamento
  - Selezione date di inizio/fine
  - Tabella dati giornalieri
  - Box statistiche aggregate
  - Gestione errori in tempo reale

### Database
- **`cities` table**: 
  - id, name, country, latitude, longitude
- **`weather_data` table**:
  - city_id (foreign key)
  - avg_temperature_date (data giornaliera)
  - avg_temperature, max_temperature, min_temperature

---

## Scelte tecniche

### Gestione degli errori
- **Validazione robusta**:
  ```php
  $request->validate([
      'cityId' => 'required|integer',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date'
  ]);
  ```
- **Gestione API esterne**:
  ```php
  if (!$response->ok()) {
      return response()->json([
          'error' => 'Errore nella chiamata all\'API Open Meteo.',
          'status_code' => $response->status()
      ], $response->status());
  }
  ```
- **Logging degli errori**:
  ```php
  Log::error("Errore nel salvataggio dati meteo: " . $e->getMessage());
  ```

### Calcolo delle statistiche aggregate
1. **Giornaliere**:
   - Raggruppamento per data dei dati orari
   - Calcolo min/max/avg per ogni giorno
   - Salvataggio in database come singolo record giornaliero

2. **Per periodo**:
   ```sql
   SELECT 
       AVG(avg_temperature) as avg,
       MIN(min_temperature) as min,
       MAX(max_temperature) as max
   FROM weather_data
   WHERE city_id = ? AND avg_temperature_date BETWEEN ? AND ?
   ```
   - Media globale delle temperature medie giornaliere
   - Minima assoluta tra tutte le minime giornaliere
   - Massima assoluta tra tutte le massime giornaliere

### Ottimizzazioni
- **Elaborazione lato server**:
  - I dati orari (fino a 24 valori/giorno) vengono aggregati a livello server
  - Riduzione del traffico dati tra backend e frontend
  - Memorizzazione ottimizzata (1 record/giorno invece di 24)

- **Validazione frontend/backend**:
  ```javascript
  // Frontend validation
  document.getElementById("form-openmeteo").addEventListener("submit", async function(e) {
      e.preventDefault();
      // ... validation logic
  });
  ```
  ```php
  // Backend validation
  'end_date' => 'required|date|after_or_equal:start_date'
  ```

- **Debouncing ricerca città**:
  ```javascript
  cityInput.addEventListener("input", () => {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(loadCities, 300);
  });
  ```

### Note importanti
- **Approccio ibrido Eloquent/Raw Queries**:
  - Utilizzo di query raw per operazioni complesse di aggregazione
  - Mantenimento delle relazioni Eloquent per semplicità
  - Esempio di query raw ottimizzata:
    ```php
    DB::selectOne(
        "SELECT * FROM cities WHERE name = ? AND country = ? ...",
        [$cityName, $country, ...]
    );
    ```

- **Frontend leggero**:
  - JavaScript vanilla senza framework esterni
  - Integrazione diretta con le API Laravel
  - Gestione dinamica della UI senza ricaricamenti di pagina

- **Precisione numerica**:
  ```php
  'avg' => round($value->avg_temperature, 1)
  ```
  - Arrotondamento a 1 cifra decimale per migliorare la leggibilità

---

## Utilizzo
1. Avviare il server Laravel con `php artisan serve`
2. Aprire il browser all'indirizzo `http://localhost:8000`
3. Inserire il nome di una città nel campo di ricerca
4. Selezionare le date di inizio e fine
5. Cliccare su "Submit" per visualizzare i dati

L'applicazione mostrerà:
- Tabella con dati giornalieri (data, media, min, max)
- Box con statistiche aggregate per il periodo selezionato
- Feedback in tempo reale per errori di validazione o problemi API

---

## Possibili miglioramenti
1. Implementare cache locale per ridurre le chiamate API
2. Aggiungere grafici interattivi con Chart.js
3. Creare seed con città predefinite
4. Implementare test unitari per le funzionalità critiche
5. Ottimizzare ulteriormente il salvataggio con batch inserts