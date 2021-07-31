import "./styles/app.scss";
import * as echarts from "echarts";
import "whatwg-fetch";
import Swal from "sweetalert2";

window.casosChart = echarts.init(document.getElementById("casos"));
let toggle_7dx2   = document.getElementById("7dx2");
let toggle_ia     = document.getElementById("ia");
let series14      = [];
let series7       = [];
let series7x2     = [];
let habitantes    = 0;

window
  .fetch(`/data/${concello}.json`)
  .then(function (response) {
    return response.json();
  })
  .then(function (json) {
    json.forEach((line) => {
      series14.push([
        line.fecha,
        line.casos_14d ? Number(line.casos_14d) : null,
      ]);
      series7.push([line.fecha, line.casos_7d ? Number(line.casos_7d) : null]);
      series7x2.push([line.fecha, line.casos_7d ? Number(line.casos_7d * 2) : null]);

      if (habitantes <= 0 && line.habitantes) {
        habitantes = Number(line.habitantes);
      }
    });
    window.casosChart.setOption(getCasosOptions());
  })
  .catch(function (ex) {
    Swal.fire({
      title: "Erro",
      text: "Houbo un erro obtendo os datos do concello.",
      icon: "error",
    });
  });

function getChartSeries(showIa = false, doubleSeries7 = false) {
  return [
    {
      name: (showIa ? "IA " : "Positivos ") + "14 días",
      data: showIa ? series14.map(item => { return [item[0], (100000 * item[1] / habitantes).toFixed(2)]; }) : series14,
      type: "line",
      smooth: true,
    },
    {
      name: (showIa ? "IA " : "Positivos ") + "7 días" + (doubleSeries7 ? ' × 2' : ''),
      data: (doubleSeries7 ? series7x2 : series7).map(item => { return showIa ? [item[0], (100000 * item[1] / habitantes).toFixed(2)] : item; }),
      type: "line",
      smooth: true,
    },
  ]
}

function updateSeries() {
  let showIa        = !!toggle_ia.checked;
  let doubleSeries7 = !!toggle_7dx2.checked;

  window.casosChart.setOption({
    series: getChartSeries(showIa, doubleSeries7),
  });
}

function getCasosOptions() {
  return {
    animation: false,
    legend: {},
    // title: {
    //   text: `Evolución de positivos en ${concello}`,
    //   top: 'top',
    //   left: 'center'
    // },
    tooltip: {
      trigger: "axis",
      // alwaysShowContent: true,
    },
    toolbox: {
      show: true,
      feature: {
        // magicType: { type: ["line", "bar"] },
        // restore: {},
        saveAsImage: {
          name: `galicia-covid19-concello - ${concello}`,
          title: 'Gardar imaxe',
        },
        // mySeries7x2: {
        //   show: true,
        //   title: 'Positivos 7 días × 2',
        //   icon: "path://M 117.285 56.499 L 117.285 72.417 L 65.479 72.417 L 65.479 59.721 L 82.91 42.094 A 426.151 426.151 0 0 0 89.087 35.521 A 92.167 92.167 0 0 0 92.676 31.425 A 23.37 23.37 0 0 0 95.874 26.518 A 10.546 10.546 0 0 0 96.777 22.221 Q 96.777 19.438 94.971 17.827 A 7.37 7.37 0 0 0 89.893 16.216 A 13.856 13.856 0 0 0 83.081 18.144 A 48.858 48.858 0 0 0 75.293 23.833 L 64.697 11.43 A 60.951 60.951 0 0 1 73.376 4.724 A 25.525 25.525 0 0 1 73.975 4.375 A 33.131 33.131 0 0 1 82.153 1.128 A 40.404 40.404 0 0 1 92.09 0.005 Q 99.023 0.005 104.468 2.446 Q 109.912 4.887 112.915 9.404 A 17.894 17.894 0 0 1 115.918 19.536 A 27.383 27.383 0 0 1 114.868 27.299 A 27.418 27.418 0 0 1 111.621 34.306 A 48.437 48.437 0 0 1 105.786 41.557 A 149.813 149.813 0 0 1 100.092 47.075 Q 96.22 50.685 90.295 56 A 988.769 988.769 0 0 0 90.283 56.01 L 90.283 56.499 L 117.285 56.499 Z M 0 72.417 L 17.822 44.194 L 0.928 17.094 L 22.607 17.094 L 31.006 32.622 L 39.6 17.094 L 61.328 17.094 L 43.994 44.194 L 62.012 72.417 L 40.332 72.417 L 31.006 55.62 L 21.729 72.417 L 0 72.417 Z",
        //   onclick: function (){
        //     
        //   }
        // }
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

window.addEventListener("resize", (e) => {
  window.casosChart.resize();
});

toggle_7dx2.addEventListener("change", (e) => {
  updateSeries();
});

toggle_ia.addEventListener("change", (e) => {
  updateSeries();
});
