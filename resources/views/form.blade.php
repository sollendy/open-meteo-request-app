<div class="form-cnt m-auto w-50 h-50 pt-5">
  <form action="GET" id="form-openmeteo">
    <div class="mb-3">
      <label for="exampleInputEmail1" class="form-label">Inserisci la città.</label>
      <input type="text" class="form-control city-input" id="city-input" aria-describedby="textHelp"">
      <ul id="city-suggestions" class="form-text">

      </ul>
      <div class="mb-3">
        <label for="dataInizio" class="form-label">Data di Inizio</label>
        <input type="date" class="form-control" id="start-date-input" required>
      </div>
      <div class="mb-3">
        <label for="dataFine" class="form-label">Data di Fine</label>
        <input type="date" class="form-control" id="end-date-input" required>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>

<div class="w-75 border border-1 m-auto p-5" id="meteo-cnt">

</div>

<script>
  let cityInput = document.getElementById("city-input");
  const citySuggestions = document.getElementById("city-suggestions");
  const meteoCnt = document.getElementById("meteo-cnt");
  let selectedCityName;
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
      // console.log("Nessun nome di città inserito");
      citySuggestions.innerHTML = `
          <b class="text-danger">Nessun nome di città inserito</b>
        `;
      return;
    }

    const url = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(cityName)}&language=it&format=json`;

    try {
      const response = await fetch(url);

      if (!response.ok) {
        throw new Error(`Errore nella richiesta: ${response.status}`);
      }

      const result = await response.json();
      console.log("Anteprima della richiesta: ", result);

      if (!result.results || result.results.length === 0) {
        // console.error("nessuna città trovata");
        citySuggestions.innerHTML = `
          <b class="text-danger">Nessuna città trovata</b>
        `;
        return;
      }

      let html = `
        <ul class="list-group suggestions">`;

      for (let i = 0; i < result.results.length; i++) {
        let city = result.results[i];

        html += `
          <li class="list-group-item list-group-item-action" 
            data-name="${city.name}" 
            data-latitude="${city.latitude}" 
            data-longitude="${city.longitude}">
            <strong">${city.name},</strong> <small> ${city.country}, ${city.admin1}</small>
          </li>
        `;
      }

      html += `</ul>`;
      citySuggestions.innerHTML = html;

      document.querySelectorAll(".list-group-item").forEach(element => {
        element.addEventListener("click", () => {

          selectedCityName = element.dataset.id;
          selectedCityName = element.dataset.name;
          selectedCityLat = element.dataset.latitude;
          selectedCityLon = element.dataset.longitude;
          fillCity(element.dataset.name);


        })
      });

    } catch (error) {
      console.error("Errore", error.message);
    }
  };


  function fillCity(cityName) {
    cityInput.value = cityName;
    citySuggestions.innerHTML = "";
  }

  async function callForMeteo(cityLat, cityLon, startDate, endDate) {
    const url = `https://archive-api.open-meteo.com/v1/archive?latitude=${encodeURIComponent(cityLat)}&longitude=${encodeURIComponent(cityLon)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}&hourly=temperature_2m`;
  
    try {
      const response = await fetch(url);

      if (!response.ok) {
        throw new Error(`Errore nella richiesta: ${response.status}`);
      }

      const result = await response.json();
      console.log("Anteprima del meteo: ", result);

      if (!result.results || result.results.length === 0) {
        // console.error("Nessun dato disponibile");
        meteoCnt.innerHTML = `
          <b class="text-danger">Nessun dato disponibile</b>
        `;
        return;
      }
    } catch (error) {
      // console.error("Errore", error.message)
      meteoCnt.innerHTML = `
          <b class="text-danger">${error.message}</b>
        `;
    }
  }

  document.addEventListener("click", (e) => {
    if (!cityInput.contains(e.target) && !citySuggestions.contains(e.target)) {
      citySuggestions.innerHTML = "";
    }
  });

  document.getElementById("form-openmeteo").addEventListener("submit", async function(e) {
    e.preventDefault();
    startDate = document.getElementById("start-date-input").value;
    endDate = document.getElementById("end-date-input").value;

    await callForMeteo(selectedCityLat, selectedCityLon, startDate, endDate);
  });
</script>
@vite(['resources/js/app.js'])