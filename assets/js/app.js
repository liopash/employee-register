// imports

require('../css/app.css');
require('@fortawesome/fontawesome-free/css/all.min.css');

require('@fortawesome/fontawesome-free/js/all.js');
const $ = require('jquery');
import Chart from 'chart.js'

// js for pages
let ctx = document.getElementById('myChart');

if (ctx) {
    ctx.style.visibility = 'hidden';

    let graphToggle = document.getElementById('graphToggle');
    graphToggle.onclick = () => (ctx.style.visibility == 'hidden') ?
        (ctx.style.visibility = 'visible', graphToggle.className = 'px-2 text-primary') :
        (ctx.style.visibility = 'hidden', graphToggle.className = 'px-2 text-muted');

    let myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: JSON.parse(chartData).map(name => name[0]),
            datasets: [{
                label: 'age of employee',
                lineTension: 0,
                fill: false,
                data: JSON.parse(chartData).map(age => age[1]),
                backgroundColor: 'rgba(255, 204, 0, 0.4)',
                borderColor: 'rgba(255, 99, 132, 0.8)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
        }
    });
}
console.log('webpack works...');