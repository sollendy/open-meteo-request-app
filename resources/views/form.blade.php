<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Open Meteo App</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
  <div class="form-cnt m-auto w-50 h-50 pt-5">
    <form action="GET" id="form-openmeteo">
      <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Inserisci la città.</label>
        <input type="text" class="form-control city-input" id="city-input" aria-describedby="textHelp">
        <ul id="city-suggestions" class="form-text"></ul>
      </div>

      <div class="mb-3">
        <label for="dataInizio" class="form-label">Data di Inizio</label>
        <input type="date" class="form-control" id="start-date-input" required>
      </div>

      <div class="mb-3">
        <label for="dataFine" class="form-label">Data di Fine</label>
        <input type="date" class="form-control" id="end-date-input" required>
      </div>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>

  <!-- Box per visualizzare i dati meteo -->
  <div class="w-75 border border-1 m-auto p-5 mt-5" id="meteo-cnt">
    <h4>Statistiche Meteo</h4>
    <p id="meteo-status"></p>
    <div id="temperature-box" style="display: none;">
      <div id="temperature-table-container"></div>
    </div>
  </div>

  <script>
    let cityInput = document.getElementById("city-input");
    const citySuggestions = document.getElementById("city-suggestions");
    const meteoCnt = document.getElementById("meteo-cnt");
    const meteoStatus = document.getElementById("meteo-status");
    const avgTemp = document.getElementById("avg-temp");
    const minTemp = document.getElementById("min-temp");
    const maxTemp = document.getElementById("max-temp");
    const temperatureBox = document.getElementById("temperature-box");

    let selectedCityName;
    let selectedCityCountry;
    let selectedCityLat;
    let selectedCityLon;
    let selectedCityId;
    let startDate;
    let endDate;
    let debounceTimeout = null;

    cityInput.addEventListener("input", () => {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(loadCities, 300);
    });

    async function loadCities() {
      const cityName = cityInput.value.trim();

      if (!cityName) {
        citySuggestions.innerHTML = `<b class="text-danger">Nessun nome di città inserito</b>`;
        return;
      }

      const url = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(cityName)}&language=it&format=json`;

      try {
        const response = await fetch(url);

        if (!response.ok) {
          throw new Error(`Errore nella richiesta: ${response.status}`);
        }

        const result = await response.json();

        if (!result.results || result.results.length === 0) {
          citySuggestions.innerHTML = `<b class="text-danger">Nessuna città trovata</b>`;
          return;
        }

        let html = `<ul class="list-group suggestions">`;

        for (let i = 0; i < result.results.length; i++) {
          let city = result.results[i];
          html += `
                        <li class="list-group-item list-group-item-action"
                            data-name="${city.name}"
                            data-country="${city.country}"
                            data-latitude="${city.latitude}"
                            data-longitude="${city.longitude}">
                            <strong>${city.name}</strong>, <small>${city.country}, ${city.admin1}</small>
                        </li>
                    `;
        }

        html += `</ul>`;
        citySuggestions.innerHTML = html;

        document.querySelectorAll(".list-group-item").forEach(element => {
          element.addEventListener("click", () => {
            selectedCityId = element.dataset.id;
            selectedCityName = element.dataset.name;
            selectedCityCountry = element.dataset.country;
            selectedCityLat = element.dataset.latitude;
            selectedCityLon = element.dataset.longitude;
            fillCity(element.dataset.name);
          });
        });

      } catch (error) {
        console.error("Errore", error.message);
      }
    }

    function fillCity(cityName) {
      cityInput.value = cityName;
      citySuggestions.innerHTML = "";
    }

    async function callForMeteo(cityLat, cityLon, startDate, endDate) {
      const payload = {
        name: selectedCityName,
        country: selectedCityCountry,
        latitude: cityLat,
        longitude: cityLon,
        start_date: startDate,
        end_date: endDate
      };

      try {
        const response = await fetch("/weather-data/store", {
          method: "POST",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "Content-Type": "application/json",
            "Accept": "application/json",
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok) {
          meteoStatus.innerHTML = `<b class="text-success">Dati salvati con successo!</b>`;
          showTemperatureData(result);
        } else {
          meteoStatus.innerHTML = `<b class="text-danger">${result.error || "Errore salvataggio"}</b>`;
        }

      } catch (error) {
        meteoStatus.innerHTML = `<b class="text-danger">${error.message}</b>`;
      }
    }

    function showTemperatureData(result) {
      const temperatureBox = document.getElementById("temperature-box");
      const tableContainer = document.getElementById("temperature-table-container");

      // Pulisci eventuale contenuto precedente
      tableContainer.innerHTML = "";

      const dataArray = result.data;

      if (!Array.isArray(dataArray) || dataArray.length === 0) {
        tableContainer.textContent = "Nessun dato disponibile.";
        temperatureBox.style.display = "block";
        return;
      }

      const cityName = dataArray[0].city_name || "Sconosciuta";

      // Crea la tabella
      const table = document.createElement("table");

      table.className = "table table-bordered";
      // Intestazione
      const thead = document.createElement("thead");
      const headerRow = document.createElement("tr");
      ["Data", "Media (°C)", "Min (°C)", "Max (°C)"].forEach(header => {
        const th = document.createElement("th");
        th.textContent = header;
        headerRow.appendChild(th);
      });
      thead.appendChild(headerRow);
      table.appendChild(thead);

      // Corpo tabella
      const tbody = document.createElement("tbody");

      dataArray.forEach(entry => {
        const row = document.createElement("tr");

        const dateCell = document.createElement("td");
        dateCell.textContent = entry.period;

        const avgCell = document.createElement("td");
        avgCell.textContent = entry.temperature.avg;

        const minCell = document.createElement("td");
        minCell.textContent = entry.temperature.min;

        const maxCell = document.createElement("td");
        maxCell.textContent = entry.temperature.max;

        row.appendChild(dateCell);
        row.appendChild(avgCell);
        row.appendChild(minCell);
        row.appendChild(maxCell);

        tbody.appendChild(row);
      });

      table.appendChild(tbody);
      tableContainer.appendChild(table);

      // Mostra il box
      temperatureBox.style.display = "block";
    }


    document.getElementById("form-openmeteo").addEventListener("submit", async function(e) {
      e.preventDefault();
      startDate = document.getElementById("start-date-input").value;
      endDate = document.getElementById("end-date-input").value;

      await callForMeteo(selectedCityLat, selectedCityLon, startDate, endDate);
    });
  </script>
</body>

</html>