import "./styles/app.scss";
import * as echarts from "echarts";
import "whatwg-fetch";
import Swal from "sweetalert2";

window.casosChart = echarts.init(document.getElementById("casos"));
let series14 = [];
let series7 = [];

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

function getCasosOptions() {
  return {
    animation: false,
    legend: {
      data: ["Positivos nos últimos 14 días", "Positivos nos últimos 7 días"],
    },
    tooltip: {
      trigger: "axis",
      // alwaysShowContent: true,
    },
    toolbox: {
      show: true,
      feature: {
        magicType: { type: ["line", "bar"] },
        restore: {},
        saveAsImage: {},
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
    series: [
      {
        name: "Positivos nos últimos 14 días",
        data: series14,
        type: "line",
        smooth: true,
      },
      {
        name: "Positivos nos últimos 7 días",
        data: series7,
        type: "line",
        smooth: true,
      },
    ],
  };
}

window.addEventListener("resize", (e) => {
  window.casosChart.resize();
});
