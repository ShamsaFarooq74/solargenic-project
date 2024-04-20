function hexToRGB(a, e) {
    var r = parseInt(a.slice(1, 3), 16),
        t = parseInt(a.slice(3, 5), 16),
        o = parseInt(a.slice(5, 7), 16);
    return e ? "rgba(" + r + ", " + t + ", " + o + ", " + e + ")" : "rgb(" + r + ", " + t + ", " + o + ")"
}! function(a) {
    "use strict";

    function e() { this.$body = a("body"), this.charts = [] }
    e.prototype.respChart = function(e, r, t, o) {
        var l = e.get(0).getContext("2d"),
            s = a(e).parent();
        return Chart.defaults.global.defaultFontColor = "#8391a2", Chart.defaults.scale.gridLines.color = "#8391a2",
            function() {
                var n;
                switch (e.attr("width", a(s).width()), r) {
                    case "Line":
                        n = new Chart(l, { type: "line", data: t, options: o });
                        break;
                    case "Doughnut":
                        n = new Chart(l, { type: "doughnut", data: t, options: o });
                        break;
                    case "Pie":
                        n = new Chart(l, { type: "pie", data: t, options: o });
                        break;
                    case "Bar":
                        n = new Chart(l, { type: "bar", data: t, options: o });
                        break;
                    case "Radar":
                        n = new Chart(l, { type: "radar", data: t, options: o });
                        break;
                    case "PolarArea":
                        n = new Chart(l, { data: t, type: "polarArea", options: o })
                }
                return n
            }()
    }, e.prototype.initCharts = function() {
        for (var e = [], r = ["#1abc9c", "#f1556c", "#4a81d4", "#e3eaef"], t = a("#key").val(), o = 0; o <= t.length; o++)
            if (console.log("#line-chart-example_" + o), 0 < a("#line-chart-example_" + o).length) {
                var l = a("#line-chart-example_" + o).data("values").split(","),
                    s = { labels: ["1", "2", "3", "4", "5", "6"], datasets: [{ label: "Hours", backgroundColor: hexToRGB((g = (b = a("#line-chart-example_" + o).data("colors")) ? b.split(",") : r.concat())[0], .3), borderColor: g[0], data: l }] };
                e.push(this.respChart(a("#line-chart-example_" + o), "Line", s, { maintainAspectRatio: !1, legend: { display: !1 }, tooltips: { intersect: !1 }, hover: { intersect: !0 }, plugins: { filler: { propagate: !1 } }, scales: { xAxes: [{ reverse: !0, gridLines: { color: "rgba(0,0,0,0.05)" } }], yAxes: [{ ticks: { stepSize: 20 }, display: !0, borderDash: [5, 5], gridLines: { color: "rgba(0,0,0,0)", fontColor: "#fff" } }] } }))
            }
        if (0 < a("#bar-chart-example").length) {
            var n = { labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], datasets: [{ label: "Fault", backgroundColor: (g = (b = a("#bar-chart-example").data("colors")) ? b.split(",") : r.concat())[0], borderColor: g[0], hoverBackgroundColor: g[0], hoverBorderColor: g[0], data: [65, 59, 80, 81, 56, 89, 40, 32, 65, 59, 80, 81] }, { label: "Warning", backgroundColor: g[1], borderColor: g[1], hoverBackgroundColor: g[1], hoverBorderColor: g[1], data: [89, 40, 32, 65, 59, 80, 81, 56, 89, 40, 65, 59] }] };
            e.push(this.respChart(a("#bar-chart-example"), "Bar", n, { maintainAspectRatio: !1, legend: { display: !1 }, scales: { yAxes: [{ gridLines: { display: !1 }, stacked: !1, ticks: { stepSize: 20 } }], xAxes: [{ barPercentage: .7, categoryPercentage: .5, stacked: !1, gridLines: { color: "rgba(0,0,0,0.01)" } }] } }))
        }
        if (0 < a("#bar-chart-example1").length && (n = { labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], datasets: [{ label: "Fault", backgroundColor: (g = (b = a("#bar-chart-example1").data("colors")) ? b.split(",") : r.concat())[0], borderColor: g[0], hoverBackgroundColor: g[0], hoverBorderColor: g[0], data: [65, 59, 80, 81, 56, 89, 40, 32, 65, 59, 80, 81] }] }, e.push(this.respChart(a("#bar-chart-example1"), "Bar", n, { maintainAspectRatio: !1, legend: { display: !1 }, scales: { yAxes: [{ gridLines: { display: !1 }, stacked: !1, ticks: { stepSize: 20 } }], xAxes: [{ barPercentage: .7, categoryPercentage: .5, stacked: !1, gridLines: { color: "rgba(0,0,0,0.01)" } }] } }))), 0 < a("#bar-chart-example2").length) {
            var c = a("#bar-chart-example2").data("actual_generation").split(","),
                i = a("#bar-chart-example2").data("expected_generation").split(",");
            n = { labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], datasets: [{ label: "Actual Generation", backgroundColor: (g = (b = a("#bar-chart-example2").data("colors")) ? b.split(",") : r.concat())[0], borderColor: g[0], hoverBackgroundColor: g[0], hoverBorderColor: g[0], data: c }, { label: "Expected Generation", backgroundColor: g[1], borderColor: g[1], hoverBackgroundColor: g[1], hoverBorderColor: g[1], data: i }] }, e.push(this.respChart(a("#bar-chart-example2"), "Bar", n, { maintainAspectRatio: !1, legend: { display: !1 }, scales: { yAxes: [{ gridLines: { display: !1 }, stacked: !1, ticks: { callback: function(a, e, r) { return a }, stepSize: 1e3 * Math.round(a("#bar-chart-example2").data("expected_max") / 5 / 1e3) } }], xAxes: [{ barPercentage: .7, categoryPercentage: .5, stacked: !1, gridLines: { color: "rgba(0,0,0,0.01)" } }] } }))
        }
        if (0 < a("#bar-chart-example3").length && (n = { labels: ["0-50 kW", "50-100 kW", "100-150 kW", "150-200 kW", "200-250 kW"], datasets: [{ label: "Plant(s)", backgroundColor: (g = (b = a("#bar-chart-example3").data("colors")) ? b.split(",") : r.concat())[0], borderColor: g[0], hoverBackgroundColor: g[0], hoverBorderColor: g[0], data: [a("#bar-chart-example3").attr("data-capacity_1"), a("#bar-chart-example3").attr("data-capacity_2"), a("#bar-chart-example3").attr("data-capacity_3"), a("#bar-chart-example3").attr("data-capacity_4"), a("#bar-chart-example3").attr("data-capacity_5")] }] }, e.push(this.respChart(a("#bar-chart-example3"), "Bar", n, { maintainAspectRatio: !1, legend: { display: !1 }, tooltips: { callbacks: { title: function(a, e) { return "Capacity: " + a[0].xLabel }, label: function(a, e) { return "No. of Plant: " + a.yLabel } } }, scales: { yAxes: [{ gridLines: { display: !1 }, stacked: !1, ticks: { stepSize: 20 } }], xAxes: [{ barPercentage: .7, categoryPercentage: .5, stacked: !1, gridLines: { color: "rgba(0,0,0,0.01)" } }] } }))), 0 < a("#bar-chart-example4").length) {
            var d = a("#bar-chart-example4").data("revenue").split(",");
            n = { labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], datasets: [{ label: "Revenue", backgroundColor: (g = (b = a("#bar-chart-example4").data("colors")) ? b.split(",") : r.concat())[0], borderColor: g[0], hoverBackgroundColor: g[0], hoverBorderColor: g[0], data: d }] }, e.push(this.respChart(a("#bar-chart-example4"), "Bar", n, { maintainAspectRatio: !1, legend: { display: !1 }, scales: { yAxes: [{ gridLines: { display: !1 }, stacked: !1, ticks: { callback: function(a, e, r) { return a }, stepSize: 1e3 * Math.round(a("#bar-chart-example4").data("revenue_max") / 5 / 1e3) } }], xAxes: [{ barPercentage: .7, categoryPercentage: .5, stacked: !1, gridLines: { color: "rgba(0,0,0,0.01)" } }] } }))
        }
        if (0 < a("#bar-chart-example5").length && (n = { labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30"], datasets: [{ label: "Fault", backgroundColor: (g = (b = a("#bar-chart-example5").data("colors")) ? b.split(",") : r.concat())[0], borderColor: g[0], hoverBackgroundColor: g[0], hoverBorderColor: g[0], data: [65, 59, 80, 81, 56, 89, 40, 32, 65, 59, 80, 81] }, { label: "Warning", backgroundColor: g[1], borderColor: g[1], hoverBackgroundColor: g[1], hoverBorderColor: g[1], data: [89, 40, 32, 65, 59, 80, 81, 56, 89, 40, 65, 59] }] }, e.push(this.respChart(a("#bar-chart-example5"), "Bar", n, { maintainAspectRatio: !1, legend: { display: !1 }, scales: { yAxes: [{ gridLines: { display: !1 }, stacked: !1, ticks: { stepSize: 20 } }], xAxes: [{ barPercentage: .7, categoryPercentage: .5, stacked: !1, gridLines: { color: "rgba(0,0,0,0.01)" } }] } }))), 0 < a("#pie-chart-example").length) {
            var p = { labels: ["Direct", "Affilliate", "Sponsored", "E-mail"], datasets: [{ data: [300, 135, 48, 154], backgroundColor: g = (b = a("#pie-chart-example").data("colors")) ? b.split(",") : r.concat(), borderColor: "transparent" }] };
            e.push(this.respChart(a("#pie-chart-example"), "Pie", p, { maintainAspectRatio: !1, legend: { display: !1 } }))
        }
        if (0 < a("#donut-chart-example").length && (o = { labels: ["Direct", "Affilliate", "Sponsored"], datasets: [{ data: [128, 78, 48], backgroundColor: g = (b = a("#donut-chart-example").data("colors")) ? b.split(",") : r.concat(), borderColor: "transparent", borderWidth: "1" }] }, e.push(this.respChart(a("#donut-chart-example"), "Doughnut", o, { maintainAspectRatio: !1, cutoutPercentage: 60, legend: { display: !1 } }))), 0 < a("#polar-chart-example").length) {
            var h = { labels: ["Direct", "Affilliate", "Sponsored", "E-mail"], datasets: [{ data: [251, 135, 48, 154], backgroundColor: g = (b = a("#polar-chart-example").data("colors")) ? b.split(",") : r.concat(), borderColor: "transparent" }] };
            e.push(this.respChart(a("#polar-chart-example"), "PolarArea", h))
        }
        if (0 < a("#radar-chart-example").length) {
            var b, g, u = { labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"], datasets: [{ label: "Desktops", backgroundColor: hexToRGB((g = (b = a("#radar-chart-example").data("colors")) ? b.split(",") : r.concat())[0], .3), borderColor: g[0], pointBackgroundColor: g[0], pointBorderColor: "#fff", pointHoverBackgroundColor: "#fff", pointHoverBorderColor: g[0], data: [65, 59, 90, 81, 56, 55, 40] }, { label: "Tablets", backgroundColor: hexToRGB(g[1], .3), borderColor: g[1], pointBackgroundColor: g[1], pointBorderColor: "#fff", pointHoverBackgroundColor: "#fff", pointHoverBorderColor: g[1], data: [28, 48, 40, 19, 96, 27, 100] }] };
            e.push(this.respChart(a("#radar-chart-example"), "Radar", u, { maintainAspectRatio: !1 }))
        }
        return e
    }, e.prototype.init = function() {
        var e = this;
        Chart.defaults.global.defaultFontFamily = "Nunito,sans-serif", e.charts = this.initCharts(), a(window).on("resize", function(r) { a.each(e.charts, function(a, e) { try { e.destroy() } catch (a) {} }), e.charts = e.initCharts() })
    }, a.ChartJs = new e, a.ChartJs.Constructor = e
}(window.jQuery),
function() {
    "use strict";
    window.jQuery.ChartJs.init()
}();