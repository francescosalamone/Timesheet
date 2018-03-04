
    var start = new Date();
	var now = 0;
	var timerId = 0;
	var startedTime = '';

	startedTime = document.currentScript.getAttribute('starttime');

	if(startedTime != ''){

      	var timestamp = new Date(startedTime.replace(' ', 'T'));
    	start = timestamp;
    }
	timerId = setInterval(count,1000);

	function count(){
		now = new Date();
      	var timeDiff = new Date(now - start);
      	var secs = timeDiff.getSeconds();

      	var mins = timeDiff.getMinutes();
      	var hours = timeDiff.getHours()-1;

      	if(secs < 10){
          secs = "0" + secs;
        }
      	if(mins < 10){
          mins = "0" + mins;
        }
      	if(hours < 10){
          hours = "0" + hours;
        }

      	document.getElementById("message").innerHTML = hours + ":" + mins + ":" + secs;
    }