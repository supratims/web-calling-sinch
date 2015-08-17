var global_username = '';

var sinchClient;

//*** Set up sinchClient ***/
sinchClient = new SinchClient({
	applicationKey: $('#sinchApplicationKey').val(),
	capabilities: {calling: true},
	//startActiveConnection: true, /* NOTE: This is only required if application is to receive calls / instant messages. */ 
	//Note: For additional loging, please uncomment the three rows below
	onLogMessage: function(message) {
		console.log(message);
	},
});

function startSinch(userTicket, userName){
	if (userTicket && userTicket.length>0){
		/* start sinch client */
		sinchClient.start({'username': userName, 'userTicket': userTicket}, function() {
			console.log('Started successfully !!!');
		}).fail(handleError);
	}
}
startSinch($('#userTicket').val(), $('#username_auth').val());

/*** Define listener for managing calls ***/

var callListeners = {
	onCallProgressing: function(call) {
		$('audio#ringback').prop("currentTime", 0);
		$('audio#ringback').trigger("play");
		$('div#callLog').append('<div id="stats">Ringing...</div>');
	},
	onCallEstablished: function(call) {
		$('audio#incoming').attr('src', call.incomingStreamURL);
		$('audio#ringback').trigger("pause");

		//Report call stats
		var callDetails = call.getDetails();
		$('div#callLog').append('<div id="stats">Answered at: '+(callDetails.establishedTime)+'</div>');
	},
	onCallEnded: function(call) {
		$('audio#ringback').trigger("pause");
		$('audio#incoming').attr('src', '');

		$('button').removeClass('incall');
		$('input#phoneNumber').removeAttr('disabled');

		//Report call stats
		var callDetails = call.getDetails();
		$('div#callLog').append('<div id="stats">Ended: '+callDetails.endedTime+'</div>');
		$('div#callLog').append('<div id="stats">Duration (s): '+callDetails.duration+'</div>');
		$('div#callLog').append('<div id="stats">End cause: '+call.getEndCause()+'</div>');
		if(call.error) {
			$('div#callLog').append('<div id="stats">Failure message: '+call.error.message+'</div>');
		}
	}
}

/*** Make a new PSTN call ***/

var callClient = sinchClient.getCallClient();
callClient.initStream().then(function() { // Directly init streams, in order to force user to accept use of media sources at a time we choose
	$('div.frame').not('#chromeFileWarning').show();
}); 
var call;

$('button#call').click(function(event) {
	event.preventDefault();

	if(!$(this).hasClass("incall")) {
		$('button').addClass('incall');
		$('input#phoneNumber').attr('disabled', 'disabled');

		$('div#callLog').append('<div id="title">Calling ' + $('input#phoneNumber').val()+'</div>');

		call = callClient.callPhoneNumber($('input#phoneNumber').val());

		call.addEventListener(callListeners);
	}
});

$('button#hangup').click(function(event) {
	event.preventDefault();

	if($(this).hasClass("incall")) {
		call && call.hangup();
	}
});

$('#login').on('click', function(e){
	e.preventDefault();
	/*
	$.post('/ajaxlogin.php', {'type':'login','username':$('#username').val(), 'password':$('#password').val()},
		function(response){
			//console.log(response);
			var resp = response.split(',');
			if (resp && resp[0]=='success'){
				startSinch(resp[1]);
				$('.error').html('').hide();
				$('#loginform').hide();
				$('#callframe').show();
			}
			else {
				if (resp[1]) {
					$('.error').html(resp[1]).show();
				}
			}
		}
	);
	*/
	$('#signin').attr('action', '/ajaxlogin.php?type=login').submit();
});

$('#register').on('click', function(e){
	e.preventDefault();
	
	/*
	$.post('/ajaxlogin.php', {'type':'register','username':$('#username').val(), 'password':$('#password').val()},
		function(response){
			//console.log(response);
			var resp = response.split(',');
			if (resp && resp[0]=='success'){
				startSinch(resp[1]);
				$('.error').html('').hide();
			}
			else {
				if (resp[1]) {
					$('.error').html(resp[1]).show();
				}
			}
		}
	);
	*/
	$('#signin').attr('action', '/ajaxlogin.php?type=register').submit();
});

$('#logout').on('click', function(e){
	e.preventDefault();
	sinchClient.terminate();
	$.post('/logout.php', function(response){
			window.location.reload();
		}
	);
});

/*** Handle errors, report them and re-enable UI ***/

var handleError = function(error) {
	//Enable buttons
	$('button#createUser').prop('disabled', false);
	$('button#loginUser').prop('disabled', false);

	//Show error
	$('div.error').text(error.message);
	$('div.error').show();
}

/** Always clear errors **/
var clearError = function() {
	$('div.error').hide();
}

/** Chrome check for file - This will warn developers of using file: protocol when testing WebRTC **/
if(location.protocol == 'file:' && navigator.userAgent.toLowerCase().indexOf('chrome') > -1) {
	$('div#chromeFileWarning').show();
}

$('button').prop('disabled', false); //Solve Firefox issue, ensure buttons always clickable after load






