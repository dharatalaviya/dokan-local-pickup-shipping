/**
 * Load general.js
 */
 jQuery(document).ready(function($){
 	
 	$("body").on("click", ".datepicker", function(){
 		var id = "#"+$(this).attr('id');
 		var storedata = $(this).data('storedata');
 		var sellerid = $(this).data('sellerid');
 		var closedays = [];
 		var opendays = [];
 		var days = {'sunday':0, 'monday':1, 'tuesday':2, 'wednesday':3, 'thursday':4, 'friday':5, 'saturday':6};
 		$.each(storedata, function(key, value){
 			if(value.status == 'close'){
 				closedays.push(days[key]);
 			}else{
 				opendays.push(days[key]);
 			}
 		});
		$(id).datepicker({ 
	 		beforeShowDay: nonWorkingDates,
	 		minDate: 0, 
	 		maxDate:7,
	 		dateFormat: "dd-mm-yy",
	 		onSelect: function(){
	 			var date = $(this).datepicker('getDate');
	    		var dayOfWeek = date.getDay();
	    		var nameofweek = '';
	    		$.each(days, function(key, value) {
					if(dayOfWeek == value){
					    nameofweek = key;
					}				  
				});
				var sel = "#timedur_"+sellerid;
				var timeboxid = "#timedur_"+sellerid;
				$(timeboxid).attr("data-pickupdate", $(this).val());
				$.ajax({
		            url : ajax_custom.ajaxurl,
		            type : 'post',
		            data : {
		                action : 'update_pickup_datetime_session',
		                sellerid : sellerid,
		                pickupdate : $(this).val(),
		            },
		            success : function( response ) {
		            	return true;
		            },error: function (error) {
	    				console.log('error: ' + eval(error));
					}
		        });
				var storeopening_time = storedata[nameofweek].opening_time;
				var storeclosing_time = storedata[nameofweek].closing_time; 
				if (storeopening_time == ''){ storeopening_time = '10:00 am'; }
				if(storeclosing_time == ''){  storeclosing_time = '6:00 pm'; }
				if((storedata[nameofweek].status == 'open' || opendays.length <= 0) && storeopening_time.length > 0 && storeclosing_time.length > 0){
					var openingtime = onehourslist(date, storeopening_time, storeclosing_time );	
					$(sel).empty().append($("<option>Choose Pickup Time</option>"));
					$(openingtime).each(function(key, value) {
						$("<option>").attr('value',value).text(value).appendTo(sel);
					});
					$(sel).show();
					return true;
				}else{
					$(sel).after("<p class='error'>Store Pickup time is not availble. Contact to store or admin.</p>")
				}
				
	 		},
 		});	
	
 		function nonWorkingDates(date){
        	var day = date.getDay();
      		var show = true;
      	 	var display = '';
      		if(opendays.length <= 0){
      			display = 'No Weekends';
      		}else{
      			for (var i = 0; i < closedays.length; i++) {
	            	if (day == closedays[i]) {
	                	show = false; 
	            	}
	        	}
	        	var display = [show,'',(show)?'':'No Weekends'];//With Fancy hover tooltip!
      		}
        	return display;
   		}
  	});
  	$("body").on("change", ".selecttime", function(){
 		var id = $(this).attr("id");
 		var sellerid = $(this).attr("data-sellerid");
 		var pickupdate = $(this).attr("data-pickupdate");
 		$.ajax({
	        url : ajax_custom.ajaxurl,
	        type : 'post',
	        data : {
	            action : 'update_pickup_datetime_session',
	            sellerid : sellerid,
	            pickupdate : pickupdate,
	            pickuptime : $(this).val(),
	        },
	        success : function( response ) {
	           	return true;
	        },error: function (error) {
    			console.log('error: ' + eval(error));
			}
	    });
 	});	
 	
 	function onehourslist(date, starttime, endtime){
	   	var opentime = AMPMtohours(starttime);
	   	var endtime = AMPMtohours(endtime);
	   	var d = new Date(); //get a date object
	    var todayDate = d.getDate();
	    var timeArr = [];
	    var hours = opentime.split(':')[0];
	    var minutes = opentime.split(':')[1];
	    var chours = endtime.split(":")[0];
	    var cminutes = endtime.split(":")[1];   
		var x = 60; //minutes interval
		var times = []; // time array
		var tt = parseInt(hours*60,10) + parseInt(minutes,10); // start time
		var ct =  parseInt(chours*60) +  parseInt(cminutes);
		var ap = ['AM', 'PM']; // AM-PM
	 	//loop to increment the time and push results in array
		for (var i=0;tt<ct; i++) {
			  var hh = Math.floor(tt/60); // getting hours of day in 0-24 format
			  var mm = (tt%60); // getting minutes of the hour in 0-55 format
			  times[i] = ("" + ((hh==12)?12:hh%12)).slice(-2) + ':' + ("0" + mm).slice(-2) +" "+ ap[Math.floor(hh/12)]; // pushing data in array in [00:00 - 12:00 AM/PM format]
			  tt = tt + x;
		}
		return times;
   }

 	function AMPMtohours(starttime){
		var time = starttime;
		var hours = Number(time.match(/^(\d+)/)[1]);
		var minutes = Number(time.match(/:(\d+)/)[1]);
		var AMPM = time.match(/\s(.*)$/)[1];
		if(AMPM == "pm" && hours<12) hours = hours+12;
		if(AMPM == "am" && hours==12) hours = hours-12;
		var sHours = hours.toString();
		var sMinutes = minutes.toString();
		if(hours<10) sHours = "0" + sHours;
		if(minutes<10) sMinutes = "0" + sMinutes;
		return sHours + ":" + sMinutes;
   }
});