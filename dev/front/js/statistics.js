import { Chart, PieController, ArcElement, Colors, Title, Tooltip, Legend,
    BarController, BarElement, CategoryScale, LinearScale } from "chart.js";
import { Axette } from "axette";

Chart.register(PieController, ArcElement, Colors, Title, Tooltip, Legend,
    BarController, BarElement, CategoryScale, LinearScale);

const BASE_URL = location.origin;

const ENDPOINTS = {
    moneySources: BASE_URL + "/statistics/charts/money-sources",
    timeSpent: BASE_URL + "/statistics/charts/time-spent",
    activities: BASE_URL + "/statistics/charts/activities",
};

const axette = new Axette();

document.addEventListener("DOMContentLoaded", () => {
    createMoneySourceCharts();
    createTimeSpentCharts();
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

async function createTimeSpentCharts() {
    const timeSpentPieEl = document.getElementById("timeSpentPie");
    const timeSpentBarsEl = document.getElementById("timeSpentBars");
    const username = timeSpentPieEl.getAttribute("data-stats-username") ?? null;
    const timeSpentUrl = ENDPOINTS.timeSpent + (username ? `?username=${username}` : "");
    const activitiesUrl = ENDPOINTS.activities + (username ? `?username=${username}` : "");
    const timeSpentData = await axette.get(timeSpentUrl);
    const activitiesData = await axette.get(activitiesUrl);

    const moneySourcePie = new Chart(timeSpentPieEl, {
        type: "pie",
        data: {
            labels: Object.keys(timeSpentData).map((item) => item.charAt(0).toUpperCase() + item.slice(1)),
            datasets: [{
                label: "Time spent",
                data: Object.values(timeSpentData),
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
                    text: 'Time spent (in minutes)'
                }
            }
        }
    });

    const moneySourceBars = new Chart(timeSpentBarsEl, {
        type: "bar",
        data: {
            labels: Object.keys(timeSpentData).map((item) => item.charAt(0).toUpperCase() + item.slice(1)),
            datasets: [{
                label: "Time spent",
                data: Object.values(timeSpentData),
            }, {
                label: "Activities",
                data: Object.values(activitiesData),
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
                    text: 'Time spent on activities'
                }
            }
        }
    });
}
