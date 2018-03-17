function createChart(mainQuestion) {
    var myChart = document.getElementById('pollingChart').getContext('2d');

    myChart.canvas.width = 150;
    myChart.canvas.height = 30;

    var massPopChart = new Chart(myChart, {
        type: 'horizontalBar', // bar , horizontalBar, pie, line, doughnut, radar
        data: {
            labels: ['HELLO', 'THERE', 'WORLD'],
            datasets: [{
                label: 'amount',
                data: [
                    2,
                    4,
                    6,
                ],
                backgroundColor: 'green',
                borderWidth: 4,
                borderColor: '#777'
            }]
        },
        //Add title
        options: {

            title: {
                display: true,
                text: mainQuestion,
                fontSize: 25,
            },

            legend: {
                display: false,
            },

            layout:{
                padding: {
                    left:0,
                    right:150,
                }
            }

        }
    });
}