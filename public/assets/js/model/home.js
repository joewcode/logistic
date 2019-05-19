'use strict';

// Init function
function __construct() {
	try {
		
	} catch (e) {  }
}

//
function getDataLine(arrData) {
	let lineData = { labels: ["Вторник", "Среда", "Читверг", "Пятница", "Суббота"], datasets: [
                    {
                        label: "Тоннаж",
                        backgroundColor: "rgba(26,179,148,0.5)",
                        borderColor: "rgba(26,179,148,0.7)",
                        pointBackgroundColor: "rgba(26,179,148,1)",
                        pointBorderColor: "#fff",
                        data: arrData['tonn']
                    },
                    {
                        label: "Заказы",
                        backgroundColor: "rgba(147, 176, 93, 0.6)",
                        borderColor: "rgba(147, 176, 93, 1)",
                        pointBackgroundColor: "rgba(147, 176, 93, 1)",
                        pointBorderColor: "#fff",
                        data: arrData['ordc']
                    },
                    {
                        label: "Сумма отгрузки",
                        backgroundColor: "rgba(147, 78, 93, 0.6)",
                        borderColor: "rgba(147, 78, 93, 0.1)",
                        pointBackgroundColor: "rgba(147, 78, 93, 1)",
                        pointBorderColor: "#fff",
                        data: arrData['summ']
                    },
                    {
                        label: "Расстояние",
                        backgroundColor: "rgba(255, 93, 17, 0.5)",
                        borderColor: "rgba(255, 93, 17, 1)",
                        pointBackgroundColor: "rgba(255, 93, 17, 1)",
                        pointBorderColor: "#fff",
                        data: arrData['kmtr']
                    },
                    {
                        label: "UPS",
                        backgroundColor: "rgba(220,220,220,0.5)",
                        borderColor: "rgba(220,220,220,1)",
                        pointBackgroundColor: "rgba(220,220,220,1)",
                        pointBorderColor: "#fff",
                        data: arrData['upsp']
                    }
				]};
	return lineData;
}

function chengedStatus(id) {
	$.post('/chsbp', {'_token': csrftoken, 'id': id}, function(r){
		if ( r.success ) {
			
		}
	});	
}


