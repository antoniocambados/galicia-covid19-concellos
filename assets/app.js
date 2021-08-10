import "./styles/app.scss";
import * as echarts from "echarts";
import "whatwg-fetch";
import Swal from "sweetalert2";
import Choices from "choices.js";

window.concellosData  = [];
window.concellos      = [];
let chartElement      = document.getElementById("chart");
let regularToolbar    = document.getElementById("regular-chart-user-options");
let comparisonToolbar = document.getElementById("comparison-chart-user-options");
let toggle_7dx2       = document.getElementById("7dx2");
let toggle_ia         = document.getElementById("ia");
let toggle_compare7d  = document.getElementById("compare7d");
let input_concellos   = document.getElementById("input-concellos");
let button_compare    = document.getElementById("compare-concellos");
let concellosChoices  = null;

function comparing() {
  return concellosChoices ? concellosChoices.getValue(true).length > 1 : false;
}

function updateUserChartOptions() {
  if (comparing()) {
    comparisonToolbar.classList.remove('is-hidden');
    regularToolbar.classList.add('is-hidden');
    toggle_ia.checked  = true;
    toggle_ia.disabled = true;
  } else {
    comparisonToolbar.classList.add('is-hidden');
    regularToolbar.classList.remove('is-hidden');
    toggle_ia.disabled = false;
  }
}

function getConcello(concello) {
  return window
      .fetch(`/data/${concello}.json`)
      .then(function (response) {
        return response.json();
      })
      .then(function (json) {
        return Promise.resolve({
          concello,
          data: json,
        });
      })
      .catch(function (ex) {
        Swal.fire({
          title: "Erro",
          text: `Houbo un erro obtendo os datos do concello "${concello}".`,
          icon: "error",
        });
      });
}

function getHabitantes(json = []) {
  let habitantes = 0;

  for (let i = 0; i < json.length; i++) {
    const line = json[i];

    if (habitantes <= 0 && line.habitantes) {
      habitantes = Number(line.habitantes);
      break;
    }
  }

  return habitantes;
}

function getSeries14(json = [], ia = false) {
  let habitantes = ia ? getHabitantes(json) : null;

  return json.map((line) => {
    const fecha = line.fecha;
    const valor = line.casos_14d ? (ia ? (100000 * line.casos_14d / habitantes).toFixed(2) : line.casos_14d) : null;

    return [fecha, valor];
  });
}

function getSeries7(json = [], ia = false, x2 = false) {
  let habitantes = ia ? getHabitantes(json) : null;

  return json.map((line) => {
    const fecha = line.fecha;
    let   valor = line.casos_7d ? (ia ? (100000 * line.casos_7d / habitantes).toFixed(2) : line.casos_7d) : null;

    if (x2 && valor !== null) {
      valor = valor * 2;
    }

    return [fecha, valor];
  });
}

function getChartSeries() {
  let series        = [];
  let showIa        = comparing() ? true : !!toggle_ia.checked;
  let doubleSeries7 = !!toggle_7dx2.checked;
  let compare7d     = !!toggle_compare7d.checked;

  for (let i = 0; i < window.concellosData.length; i++) {
    const item = window.concellosData[i];

    if (comparing()) {
      series.push({
        name: item.concello,
        data: compare7d ? getSeries7(item.data, showIa): getSeries14(item.data, showIa),
        type: "line",
        smooth: true,
      });
    } else {
      series.push({
        name: (showIa ? "IA " : "Positivos ") + "14 días",
        data: getSeries14(item.data, showIa),
        type: "line",
        smooth: true,
      });

      series.push({
        name: (showIa ? "IA " : "Positivos ") + "7 días",
        data: getSeries7(item.data, showIa, doubleSeries7),
        type: "line",
        smooth: true,
      });

      // Só permitimos o primeiro concello se non é unha comparación
      break;
    }
  }

  return series;
}

function updateSeries() {
  window.chart.setOption({
    series: getChartSeries(),
  });
}

function getImageFilename() {
  if (comparing()) {
    return 'galicia-covid19-concellos';
  }

  return `galicia-covid19-concello - ${window.concello}`;
}

function getChatOptions() {
  return {
    animation: false,
    legend: {},
    tooltip: {
      trigger: "axis",
    },
    toolbox: {
      show: true,
      feature: {
        saveAsImage: {
          name: getImageFilename(),
          title: 'Gardar imaxe',
        },
      },
    },
    xAxis: {
      type: "time",
    },
    yAxis: {
      type: "value",
    },
    dataZoom: [
      {
        type: "inside",
        startValue: new Date().setMonth(new Date().getMonth() - 1),
        end: 100,
        minValueSpan: 3600 * 24 * 1000 * 14,
      },
      {
        startValue: new Date().setMonth(new Date().getMonth() - 1),
        end: 100,
        minValueSpan: 3600 * 24 * 1000 * 14,
      },
    ],
    series: getChartSeries(),
  };
}

function updateConcellos() {
  Promise.all(window.concellos.map(concello => {
    return getConcello(concello);
  })).then((values) => {
    window.concellosData = values;
    window.chart.clear();
    window.chart.setOption(getChatOptions());
  });
}

// INIT

if (chartElement) {
  window.chart = echarts.init(chartElement);

  if (window.concello) {
    window.concellos.push(window.concello);
    updateConcellos();
  }

  updateUserChartOptions();

  window.addEventListener("resize", (e) => {
    window.chart.resize();
  });

  toggle_7dx2.addEventListener("change", (e) => {
    updateSeries();
  });

  toggle_ia.addEventListener("change", (e) => {
    updateSeries();
  });

  toggle_compare7d.addEventListener("change", (e) => {
    updateSeries();
  });

  if (input_concellos && button_compare) {
    const choices = [];
    document.querySelectorAll(".concellos li").forEach(element => {
      choices.push({
        value: element.innerText,
        label: element.innerText,
        selected: element.innerText === window.concello,
      });
    });

    concellosChoices = new Choices(input_concellos, {
      choices,
      maxItemCount: 10,
    });

    input_concellos.addEventListener('addItem', event => {
      window.concellos = concellosChoices.getValue(true);
    });

    input_concellos.addEventListener('removeItem', event => {
      window.concellos = concellosChoices.getValue(true);

      if (event.detail.value === window.concello) {
        window.concellos.unshift(window.concello)
        concellosChoices.setValue(window.concellos);

      }
    });

    button_compare.addEventListener("click", e => {
      e.preventDefault();
      updateUserChartOptions();
      updateConcellos();
    });
  }
}
