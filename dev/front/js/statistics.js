import { Chart, PieController, ArcElement, Colors, Title, Tooltip, Legend,
    BarController, BarElement, CategoryScale, LinearScale } from "chart.js";
import { Axette } from "axette";

Chart.register(PieController, ArcElement, Colors, Title, Tooltip, Legend,
    BarController, BarElement, CategoryScale, LinearScale);

const BASE_URL = location.origin;

const ENDPOINTS = {
    moneySources: BASE_URL + "/statistics/charts/money-sources",
};

const axette = new Axette();

document.addEventListener("DOMContentLoaded", () => {
    createMoneySourceCharts();
});

async function createMoneySourceCharts() {
    const moneySourcePieEl = document.getElementById("moneySourcePie");
    const moneySourceBarsEl = document.getElementById("moneySourceBars");
    const username = moneySourcePieEl.getAttribute("data-stats-username") ?? null;
    const url = ENDPOINTS.moneySources + (username ? `?username=${username}` : "");
    const data = await axette.get(url);

    const moneySourcePie = new Chart(moneySourcePieEl, {
        type: "pie",
        data: {
            labels: Object.keys(data).map((item) => item.charAt(0).toUpperCase() + item.slice(1)),
            datasets: [{
                label: "Source of income",
                data: Object.values(data),
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Source of income'
                }
            }
        }
    });

    const moneySourceBars = new Chart(moneySourceBarsEl, {
        type: "bar",
        data: {
            labels: Object.keys(data).map((item) => item.charAt(0).toUpperCase() + item.slice(1)),
            // datasets: [
            //     {
            //         label: Object.keys(data)[0],
            //         data: Object.values(data)[0],
            //     }
            // ],
            datasets: [{
                label: "Source of income",
                data: Object.values(data),
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Source of income'
                }
            }
        }
    });
}
