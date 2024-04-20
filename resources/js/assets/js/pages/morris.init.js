!(function (r) {
    "use strict";
    function e() {}
    (e.prototype.createStackedChart = function (e, a, r, t, o, i) {
        Morris.Bar({ element: e, data: a, xkey: r, ykeys: t, stacked: !0, labels: o, hideHover: "auto", dataLabels: !1, resize: !0, gridLineColor: "rgba(65, 80, 95, 0.07)", barColors: i });
    }),
        (e.prototype.createAreaChart = function (e, a, r, t, o, i, l, s, b) {
            Morris.Area({ element: e, pointSize: a, lineWidth: r, data: t, xkey: o, dataLabels: !1, ykeys: i, labels: l, fillOpacity: s, hideHover: "auto", resize: !0, gridLineColor: "rgba(65, 80, 95, 0.07)", lineColors: b });
        }),
        (e.prototype.createLineChart = function (e, a, r, t, o, i, l, s, b) {
            Morris.Line({
                element: e,
                data: a,
                dataLabels: !1,
                xkey: r,
                ykeys: t,
                labels: o,
                fillOpacity: i,
                pointFillColors: l,
                pointStrokeColors: s,
                behaveLikeLine: !0,
                gridLineColor: "rgba(65, 80, 95, 0.07)",
                hideHover: "auto",
                lineWidth: "3px",
                pointSize: 0,
                preUnits: "$",
                resize: !0,
                lineColors: b,
            });
        }),
        (e.prototype.createBarChart = function (e, a, r, t, o, i) {
            Morris.Bar({ element: e, data: a, dataLabels: !1, xkey: r, ykeys: t, labels: o, hideHover: "auto", resize: !0, gridLineColor: "rgba(65, 80, 95, 0.07)", barSizeRatio: 0.4, xLabelAngle: 35, barColors: i });
        }),
        (e.prototype.createAreaChartDotted = function (e, a, r, t, o, i, l, s, b, c) {
            Morris.Area({
                element: e,
                pointSize: 3,
                lineWidth: 1,
                data: t,
                dataLabels: !1,
                xkey: o,
                ykeys: i,
                labels: l,
                hideHover: "auto",
                pointFillColors: s,
                pointStrokeColors: b,
                resize: !0,
                smooth: !1,
                behaveLikeLine: !0,
                fillOpacity: 0.4,
                gridLineColor: "rgba(65, 80, 95, 0.07)",
                lineColors: c,
            });
        }),
        (e.prototype.createDonutChart = function (e, a, r) {
            Morris.Donut({ element: e, data: a, barSize: 0.2, resize: !0, colors: r, backgroundColor: "transparent" });
        }),
        (e.prototype.init = function () {
            var e = ["#4a81d4", "#4fc6e1", "#e3eaef"];
            (a = r("#morris-bar-stacked").data("colors")) && (e = a.split(",")),
                this.createStackedChart(
                    "morris-bar-stacked",
                    [
                        { y: "2007", a: 45, b: 180, c: 100 },
                        { y: "2008", a: 75, b: 65, c: 80 },
                        { y: "2009", a: 100, b: 90, c: 56 },
                        { y: "2010", a: 75, b: 65, c: 89 },
                        { y: "2011", a: 100, b: 90, c: 120 },
                        { y: "2012", a: 75, b: 65, c: 110 },
                        { y: "2013", a: 50, b: 40, c: 85 },
                        { y: "2014", a: 75, b: 65, c: 52 },
                        { y: "2015", a: 50, b: 40, c: 77 },
                        { y: "2016", a: 75, b: 65, c: 90 },
                        { y: "2017", a: 100, b: 90, c: 130 },
                        { y: "2018", a: 80, b: 65, c: 95 },
                    ],
                    "y",
                    ["a", "b", "c"],
                    ["Bitcoin", "Ethereum", "Litecoin"],
                    e
                );
            e = ["#4a81d4", "#f1556c"];
            (a = r("#morris-area-example").data("colors")) && (e = a.split(",")),
                this.createAreaChart(
                    "morris-area-example",
                    0,
                    0,
                    [
                        { y: "2012", a: 10, b: 20 },
                        { y: "2013", a: 75, b: 65 },
                        { y: "2014", a: 50, b: 40 },
                        { y: "2015", a: 75, b: 65 },
                        { y: "2016", a: 50, b: 40 },
                        { y: "2017", a: 75, b: 65 },
                        { y: "2018", a: 90, b: 60 },
                    ],
                    "y",
                    ["a", "b"],
                    ["Bitcoin", "Ethereum"],
                    ["1"],
                    e
                );
            e = ["#4a81d4", "#f672a7"];
            (a = r("#morris-line-example").data("colors")) && (e = a.split(",")),
            (a = r("#morris-line-example").data("colors")) && (e = a.split(",")),
                this.createLineChart(
                    "morris-line-example",
                    [
                        { y: "12:22", a: 75},
                        { y: "1242", a: 30},
                        { y: "1259", a: 50},
                        { y: "1322", a: 75},
                        { y: "1343", a: 50},
                        { y: "1454", a: 75},
                        { y: "1524", a: 100},
                        { y: "1532", a: 50},
                    ],
                    "y",
                    ["a"],
                    ["Today"],
                    ["0.1"],
                    ["#ffffff"],
                    ["#999999"],
                    e
                );
            e = ["#02c0ce", "#0acf97", "#ebeff2"];
            (a = r("#morris-bar-example").data("colors")) && (e = a.split(",")),
                this.createBarChart(
                    "morris-bar-example",
                    [
                        { y: "2012", a: 100, b: 90, c: 40 },
                        { y: "2013", a: 75, b: 65, c: 20 },
                        { y: "2014", a: 50, b: 40, c: 50 },
                        { y: "2015", a: 75, b: 65, c: 95 },
                        { y: "2016", a: 50, b: 40, c: 22 },
                        { y: "2017", a: 75, b: 65, c: 56 },
                        { y: "2018", a: 100, b: 90, c: 60 },
                    ],
                    "y",
                    ["a", "b", "c"],
                    ["Bitcoin", "Ethereum", "Litecoin"],
                    e
                );
            e = ["#e3eaef", "#6658dd"];
            (a = r("#morris-area-with-dotted").data("colors")) && (e = a.split(",")),
                this.createAreaChartDotted(
                    "morris-area-with-dotted",
                    0,
                    0,
                    [
                        { y: "2012", a: 10, b: 20 },
                        { y: "2013", a: 75, b: 65 },
                        { y: "2014", a: 50, b: 40 },
                        { y: "2015", a: 75, b: 65 },
                        { y: "2016", a: 50, b: 40 },
                        { y: "2017", a: 75, b: 65 },
                        { y: "2018", a: 90, b: 60 },
                    ],
                    "y",
                    ["a", "b"],
                    ["Bitcoin", "Litecoin"],
                    ["#ffffff"],
                    ["#999999"],
                    e
                );
            var a;
            e = ["#4fc6e1", "#6658dd", "#ebeff2"];
            (a = r("#morris-donut-example").data("colors")) && (e = a.split(",")),
                this.createDonutChart(
                    "morris-donut-example",
                    [
                        { label: "Ethereum", value: 12 },
                        { label: "Bitcoin", value: 30 },
                        { label: "Litecoin", value: 20 },
                    ],
                    e
                );
        }),
        (r.MorrisCharts = new e()),
        (r.MorrisCharts.Constructor = e);
})(window.jQuery),
    (function () {
        "use strict";
        window.jQuery.MorrisCharts.init();
    })();
