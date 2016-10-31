function getChartOption(divId, titleText) {
    var options = {
        chart: {
            renderTo: 'container',
            type: 'line',
        },
        title: {
            text: ''
        },
        xAxis: {
            title: {
                text: null,
                align: 'high'
            },
            type: 'datetime',
            tickInterval: 24 * 3600 * 1000,
            tickWidth: 1,
            gridLineWidth: 1,
            labels: {
                overflow: 'justify'
            },
            dateTimeLabelFormats: {
                day: '%e日',
            }
        },
        yAxis: {
            title: {
                text: null
            },
            labels: {
                overflow: 'justify',
                formatter: function () {
                    return Highcharts.numberFormat(this.value, 0);
                }
            },
            showFirstLabel: false
        },
        legend: {
        },
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                        }
                    }
                },
                marker: {
                    lineWidth: 1
                }
            }
        },
        tooltip: {
            shared: true,
            crosshairs: true,
            formatter: function () {
                var tips = Highcharts.dateFormat('%Y-%m-%d', this.x) + '<br/>';
                for (var i = 0; i < this.points.length; i++) {
                    tips += '<b>' + this.points[i].series.name + ':</b>' + this.points[i].y + '<br/>';
                }
                return tips;
            }
        },
        series: []
    };


    options.chart.renderTo = divId;
    options.title.text = titleText;

    return options;
}

function getPieChartOption(divId, titleText) {
    var options = {
        chart: {
            renderTo: '',
            type: 'pie'
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage}%</b>',
            percentageDecimals: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                showInLegend: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function () {
                        return '<b>' + this.point.name + '</b>:' + this.y;
                    }
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        series: []
    };
    options.chart.renderTo = divId;
    options.title.text = titleText;

    return options;
}

function getColumnChartOption(divId, titleText){
    var options = {
            chart: {
                renderTo: '',
                type: 'column'
            },
            title: {
                text: ''
            },
            xAxis: {
                 title: {
                    text: null,
                    align: 'high'
                },
                categories: [],
            },
            yAxis: {
              title: {
                text: null
                },
                labels: {
                    overflow: 'justify'
                },
                max:null
            },
            tooltip: {
                formatter: function() {
                    return this.x +': '+ this.y;
                }
            },
            plotOptions: {
                column: {
                        pointPadding: 0.2,
                        borderWidth: 0 ,
                        dataLabels: {
                            enabled: false,
                            color: '#ccc',
                            style: {
                                fontWeight: 'bold'
                            },
                            formatter: function() {
                                return '￥'+this.y ;
                            }
                        }
                    }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            series: []
    };
    options.chart.renderTo = divId;
    options.title.text = titleText;

    return options;
}