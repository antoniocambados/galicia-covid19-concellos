import * as echarts from 'echarts';
import './styles/app.scss';

window.casosChart = echarts.init(document.getElementById('casos'));
let series14 = [];
let series7  = [];

data.forEach(line => {
   series14.push([
    line.fecha,
    line.casos_14d ? Number(line.casos_14d) : null
   ]);
   series7.push([
    line.fecha,
    line.casos_7d ? Number(line.casos_7d) : null
   ]);
});

function getCasosOptions() {
    return {
        animation: false,
        legend: {
            data: ['Positivos nos últimos 14 días', 'Positivos nos últimos 7 días'],
        },
        tooltip: {
            trigger: 'axis',
            alwaysShowContent: true,
        },
        toolbox: {
            show: true,
            feature: {
                magicType: {type: ['line', 'bar']},
                restore: {},
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'time',
        },
        yAxis: {
            type: 'value',
        },
        dataZoom: [
            {
                type: 'inside',
                startValue: (new Date()).setMonth((new Date()).getMonth() - 1),
                end: 100,
                minValueSpan: 3600 * 24 * 1000 * 14,
            }, 
            {
                startValue: (new Date()).setMonth((new Date()).getMonth() - 1),
                end: 100,
                minValueSpan: 3600 * 24 * 1000 * 14,
            }
        ],
        series: [
            {
                name: 'Positivos nos últimos 14 días',
                data: series14,
                type: 'line',
                smooth: true,
            },
            {
                name: 'Positivos nos últimos 7 días',
                data: series7,
                type: 'line',
                smooth: true,
            }
        ],
    };
}

window.casosChart.setOption(getCasosOptions());

window.addEventListener('resize', (e) => {
    window.casosChart.resize();
});