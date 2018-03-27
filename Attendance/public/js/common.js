function createChart(question,questionName, optionalAnswers, amountArray) {

    //Question ID
    var pollingQuestionID = 'pollingChart' + question;

    var location = document.getElementById(pollingQuestionID).getContext('2d');

    new Chart(location, {
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
                text: questionName,
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