!function(t){"use strict";function a(){}a.prototype.init=function(){var a,e,r,c,o,n,l,d,i,s,u,p,h,b,f,g=["#dcdcdc","#4a81d4","#1abc9c"];u=new Date,p=u.getFullYear(),(a=t("#chart").data("colors"))&&(g=a.split(",")),f=t("#chart").data("plot")?t("#chart").data("plot").split(","):"Generation",d=(n=t("#chart").data("xvalues"))?n.split(","):0,h=(s=t("#chart").data("generation_max"))?s.split(","):0,console.log(d[1]),c3.generate({bindto:"#chart",data:{columns:[f],type:"bar"},axis:{x:{type:"category",categories:d,tick:{multiline:!1}},y:{tick:{values:h}}},tooltip:{format:{title:function(t){return d[t]+" "+p},value:function(t){return d3.format(",")(t)+" kWh"}}},color:{pattern:g}}),g=["#dcdcdc","#4a81d4","#1abc9c"],(a=t("#chart1").data("colors"))&&(g=a.split(",")),r=(e=t("#chart1").data("actual_generation"))?e.split(","):0,o=(c=t("#chart1").data("expected_generation"))?c.split(","):0,i=(n=t("#chart1").data("xvalues"))?n.split(","):0,h=(s=t("#chart1").data("expected_max"))?s.split(","):0,c3.generate({bindto:"#chart1",data:{columns:[r,o],type:"bar"},axis:{x:{type:"category",categories:i,tick:{multiline:!1}},y:{tick:{values:h}}},tooltip:{format:{title:function(t){return i[t]+" "+p},value:function(t){return d3.format(",")(t)+" kWh"}}},color:{pattern:g}}),g=["#dcdcdc","#4a81d4","#1abc9c"],(a=t("#chart2").data("colors"))&&(g=a.split(",")),s=t("#chart2").data("revenue")?t("#chart2").data("revenue").split(","):0,h=t("#chart2").data("xvalues")?t("#chart2").data("xvalues").split(","):0,i=(n=t("#chart2").data("revenue_max"))?n.split(","):0,c3.generate({bindto:"#chart2",data:{columns:[s],type:"bar"},axis:{x:{type:"category",categories:h,tick:{multiline:!1}},y:{tick:{values:i}}},tooltip:{format:{title:function(t){return i[t]+" "+p},value:function(t){return d3.format(",")(t)+" PKR"}}},color:{pattern:g}}),g=["#dcdcdc","#4a81d4","#1abc9c"],(a=t("#chart3").data("colors"))&&(g=a.split(",")),r=(e=t("#chart3").data("fault"))?e.split(","):0,o=(c=t("#chart3").data("warning"))?c.split(","):0,i=(n=t("#chart3").data("xvalues"))?n.split(","):0,c3.generate({bindto:"#chart3",data:{columns:[r,o],type:"bar"},axis:{x:{type:"category",categories:i,tick:{multiline:!1}},y:{tick:{values:[0,1,2,4,5]}}},tooltip:{format:{title:function(t){return i[t]+" "+p},value:function(t){return t}}},color:{pattern:g}}),g=["#dcdcdc","#4a81d4","#1abc9c"],(a=t("#chart4").data("colors"))&&(g=a.split(",")),s=t("#chart4").data("fault")?t("#chart4").data("fault").split(","):0,h=t("#chart4").data("xvalues")?t("#chart4").data("xvalues").split(","):0,l=(d=t("#chart4").data("fault_max"))?d.split(","):0,c3.generate({bindto:"#chart4",data:{columns:[s],type:"bar"},axis:{x:{type:"category",categories:h,tick:{multiline:!1}},y:{tick:{values:l}}},tooltip:{format:{title:function(t){return h[t]+" "+p},value:function(t){return t+" time(s)"}}},color:{pattern:g}}),g=["#1abc9c","#4a81d4"],(a=t("#chart-stacked").data("colors"))&&(g=a.split(",")),c3.generate({bindto:"#chart-stacked",data:{columns:[["Desktops",30,200,100,400,150,250],["Tablets",50,20,10,40,15,25]],types:{Yesterday:"area-spline",Today:"area-spline"}},color:{pattern:g}}),g=["#1abc9c","#4a81d4"],(a=t("#roated-chart").data("colors"))&&(g=a.split(",")),c3.generate({bindto:"#roated-chart",data:{columns:[["Desktops",30,200,100,400,150,250],["Tablets",50,20,10,40,15,25]],types:{Desktops:"bar"}},color:{pattern:g},axis:{rotated:!0,x:{type:"categorized"}}}),g=["#dcdcdc","#4a81d4","#36404a","#fb6d9d","#1abc9c"],(a=t("#combine-chart").data("colors"))&&(g=a.split(",")),c3.generate({bindto:"#combine-chart",data:{columns:[["Desktops",30,20,50,40,60,50],["Tablets",200,130,90,240,130,220],["Mobiles",300,200,160,400,250,250],["Watch",200,130,90,240,130,220],["iPad",130,120,150,140,160,150]],types:{Desktops:"bar",Tablets:"bar",Mobiles:"spline",Watch:"line",iPad:"bar"},groups:[["Desktops","Tablets"]]},color:{pattern:g},axis:{x:{type:"categorized"}}}),g=["#f4f8fb","#4a81d4","#1abc9c"],(a=t("#donut-chart").data("colors"))&&(g=a.split(",")),b=t("#donut-chart").data("title"),c3.generate({bindto:"#donut-chart",data:{columns:[["Online",t("#donut-chart").attr("data-online")?t("#donut-chart").attr("data-online"):0],["Offline",t("#donut-chart").attr("data-offline")?t("#donut-chart").attr("data-offline"):0],["Faults",t("#donut-chart").attr("data-fault")?t("#donut-chart").attr("data-fault"):0]],type:"donut"},donut:{title:b+" %",width:15,label:{show:!1}},color:{pattern:g}}),g=["#f4f8fb","#4a81d4"],(a=t("#donut-chart1").data("colors"))&&(g=a.split(",")),c3.generate({bindto:"#donut-chart1",data:{columns:[["Energy Bought",t("#donut-chart1").attr("data-bought")?t("#donut-chart1").attr("data-bought"):0],["Energy Sell",t("#donut-chart1").attr("data-sell")?t("#donut-chart1").attr("data-sell"):0]],type:"donut"},donut:{title:t("#donut-chart1").data("title"),width:15,label:{show:!1}},color:{pattern:g}}),g=["#f4f8fb","#4a81d4"],(a=t("#donut-chart2").data("colors"))&&(g=a.split(",")),c3.generate({bindto:"#donut-chart2",data:{columns:[["Designed Capacity",t("#donut-chart2").attr("data-capacity")?t("#donut-chart2").attr("data-capacity"):0],["Current Generation",t("#donut-chart2").attr("data-generation")?t("#donut-chart2").attr("data-generation"):0]],type:"donut"},donut:{title:"Current Generation 60% Designed Capacity 200KW",width:15,label:{show:!1}},color:{pattern:g}}),g=["#f4f8fb","#4a81d4","#1abc9c"],(a=t("#pie-chart").data("colors"))&&(g=a.split(",")),c3.generate({bindto:"#pie-chart",data:{columns:[["iPhone",46],["MI",24],["Samsung",30]],type:"pie"},color:{pattern:g},pie:{label:{show:!1}}}),g=["#4a81d4","#1abc9c","#4a81d4","#1abc9c"],(a=t("#scatter-plot").data("colors"))&&(g=a.split(",")),c3.generate({bindto:"#scatter-plot",data:{xs:{Setosa:"setosa_x",Versicolor:"versicolor_x"},columns:[["setosa_x",3.5,3,3.2,3.1,3.6,3.9,3.4,3.4,2.9,3.1,3.7,3.4,3,3,4,4.4,3.9,3.5,3.8,3.8,3.4,3.7,3.6,3.3,3.4,3,3.4,3.5,3.4,3.2,3.1,3.4,4.1,4.2,3.1,3.2,3.5,3.6,3,3.4,3.5,2.3,3.2,3.5,3.8,3,3.8,3.2,3.7,3.3],["versicolor_x",3.2,3.2,3.1,2.3,2.8,2.8,3.3,2.4,2.9,2.7,2,3,2.2,2.9,2.9,3.1,3,2.7,2.2,2.5,3.2,2.8,2.5,2.8,2.9,3,2.8,3,2.9,2.6,2.4,2.4,2.7,2.7,3,3.4,3.1,2.3,3,2.5,2.6,3,2.6,2.3,2.7,3,2.9,2.9,2.5,2.8],["Setosa",.2,.2,.2,.2,.2,.4,.3,.2,.2,.1,.2,.2,.1,.1,.2,.4,.4,.3,.3,.3,.2,.4,.2,.5,.2,.2,.4,.2,.2,.2,.2,.4,.1,.2,.2,.2,.2,.1,.2,.2,.3,.3,.2,.6,.4,.3,.2,.2,.2,.2],["Versicolor",1.4,1.5,1.5,1.3,1.5,1.3,1.6,1,1.3,1.4,1,1.5,1,1.4,1.3,1.4,1.5,1,1.5,1.1,1.8,1.3,1.5,1.2,1.3,1.4,1.4,1.7,1.5,1,1.1,1,1.2,1.6,1.5,1.6,1.5,1.3,1.3,1.3,1.2,1.4,1.2,1,1.3,1.2,1.3,1.3,1.1,1.3]],type:"scatter"},color:{pattern:g},axis:{x:{label:"Sepal.Width",tick:{fit:!1}},y:{label:"Petal.Width"}}}),g=["#4a81d4","#fb6d9d"],(a=t("#line-regions").data("colors"))&&(g=a.split(",")),c3.generate({bindto:"#line-regions",data:{columns:[["Desktops",30,200,100,400,150,250],["Tablets",50,20,10,40,15,25]],regions:{Desktops:[{start:1,end:2,style:"dashed"},{start:3}],Tablets:[{end:3}]}},color:{pattern:g}})},t.ChartC3=new a,t.ChartC3.Constructor=a}(window.jQuery),function(){"use strict";window.jQuery.ChartC3.init()}();