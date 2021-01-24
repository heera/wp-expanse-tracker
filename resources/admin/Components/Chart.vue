<script>
    import { Bar } from 'vue-chartjs';

    export default {
        extends: Bar,
        props: ['chartLabels', 'chartData'],
        data() {
            return {
                legendTitle: 'Expense',
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            },
                            gridLines: {
                                display: true
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    tooltips: {
                      enabled: true,
                      callbacks: {
                            label: ((tooltipItems, data) => {
                              return 'Total: ' + this.formatMoney(tooltipItems.yLabel);
                              // return this.chartLabel + "\n" + tooltipItems.yLabel;
                            })
                        }
                    },
                    legend: {
                        display: true
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    hover: {
                        onHover: function(e) {
                            const point = this.getElementAtEvent(e);
                            e.target.style.cursor = point.length ? 'pointer' : 'default';
                        }
                    },
                    onClick: (evt, array) => {
                        if (array.length !== 0) {
                            this.$router.push({
                                name: 'reports',
                                query: {
                                    date: array[0]._model.label
                                }
                            });
                        }  
                    }
                }
            };
        },
        watch: {
            chartData(value) {
                this.legendTitle = value.reduce((payment, total) => {
                    return payment + total;
                }, 0);

                this.render();
            }
        },
        methods: {
            render() {
                this.renderChart({
                    labels: this.computedChartLabels,
                    datasets: [
                        {
                            label: this.chartLabel,
                            borderColor: '#ccc',
                            pointBackgroundColor: 'white',
                            borderWidth: 1,
                            pointBorderColor: '#249EBF',
                            backgroundColor: '#409EFF',
                            data: this.chartData
                        }
                    ]
                }, this.options);
            }
        },
        computed: {
            chartLabel: {
                get() {
                    return 'Total Expense: ' + this.formatMoney(this.legendTitle);
                },
                set(value) {
                    this.legendTitle = value;
                }
            },
            computedChartLabels() {
                return this.chartLabels.map(l => {
                    return l.split('-').reverse().join('-');
                });
            }
        }
    };
</script>
