function createChart(question, optionalAnswers, amountArray) {

    //Question ID
    var pollingQuestionID = 'pollingChart' + question.id;

    var myChart = document.getElementById(pollingQuestionID).getContext('2d');

    var massPopChart = new Chart(myChart, {
        type: 'horizontalBar', // bar , horizontalBar, pie, line, doughnut, radar
        data: {
            labels: optionalAnswers,
            datasets: [{
                label: 'amount',
                data: amountArray,
                backgroundColor: 'blue',
                borderWidth: 1,
                borderColor: '#777'
            }]
        },

        //Add title
        options: {
            responsive: true,
            maintainAspectRatio: false,
            //Scale
            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Answer',
                        fontSize: 15,
                    },
                }],
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Amount',
                        fontSize: 15,
                    },
                    ticks: {
                        min: 0,
                        beginAtZero: true,
                        stepSize: 1,
                    },
                    display: true,

                }]
            },

            title: {
                display: true,
                text: question.question,
                fontSize: 25,
                fontColor: 'red',
            },

            legend: {
                display: false,
                position: 'right',
            },
        }
    });
}