//////////
// Main //
//////////

window.onload = discuss_init;

var discuss;

function discuss_init(){
    // initialize discuss client    
    discuss = new Discuss(discuss_id);
}

/////////////
// Discuss //
/////////////

var Discuss = Class.create();
Discuss.prototype = {

	initialize: function(discuss_id){
	
        // initialize global HTML elements
        main_w     = $('messageWindow');
        input_w    = $('inputWindow');
        attendee_w = $('attendeeList');
        sndbtn     = document.discuss_form.submitButton;
        
        // initialize global variables
        attendees = Array;
                
        // init variables
        this.discuss_id      = discuss_id;
        this.message_id      = 0;
        this.refreshInterval = 1500;

        // initialize properties
        this._ajax = new DiscussAjaxController(this.discuss_id);
        this._attendees = new DiscussAttendeeList();
                
        // register some local properties to global scope (for Ajax response handling)
        scrollDown = this.scrollDown;
        _attendees = this._attendees;
        
        // initialize elements status
        input_w.value = "";
        input_w.focus();
        this.changeButtonStatus();        

        // set event listener
        document.onkeyup    = this.changeButtonStatus.bindAsEventListener(this);
        document.onkeypress = this.sendMessageByKey.bindAsEventListener(this);
        document.discuss_form.submitButton.onclick = this.sendMessageByButton.bindAsEventListener(this);
        
        // get initial messages
        this._ajax.init(this.responseHandler);
        this.refresh();
         
    },
	
	refresh: function(){
	    //@TODO function name (first argument) should be in global scope.
        setTimeout('discuss.refresh()', this.refreshInterval);
        try{
            this._ajax.get(this.message_id, this.responseHandler);
        } catch(e){
            // do nothing?
        }
    },
		
	scrollDown: function(){
        //@TODO stop scrolling when user is reading a log.
        main_w.scrollTop = main_w.scrollHeight - main_w.offsetHeight;
	},
	
	changeButtonStatus: function(){
    	if(input_w.value != ""){
    	    sndbtn.disabled = false;
        } else {
            sndbtn.disabled = true;
	    }
	},
	
    sendMessageByKey: function(e){
        var element = Event.element(e);
    	if (e.keyCode == 13 && element.name == 'inputWindow' && input_w.value != ""){
    	    //@TODO first enter-hit is not captured though input area is blank. not critical.
    	    this._ajax.post(this.messgage_id, this.responseHandler);
            input_w.value = "";
            this.changeButtonStatus();
            Event.stop(e);
    	}
    },
    
    sendMessageByButton: function(){
        this._ajax.post(this.message_id, this.responseHandler);
        input_w.value = '';
        input_w.focus();
        this.changeButtonStatus();        
    },
    
	responseHandler: function(request){
	
	    //for( var i in this ){ document.write(i); document.writeln("<br>"); }
	    var xmlDoc  = request.responseXML;
	    
        if (xmlDoc.documentElement) {
            
            /*¡¡Message Handling */
            
            var messages = xmlDoc.documentElement.childNodes[0];
            var len      = messages.childNodes.length;
            
            for(i = 0; i < len; i++){
            
                var _message = new DiscussMessage();

                _message.id      = messages.childNodes[i].childNodes[0].firstChild.data;
                _message.uname   = messages.childNodes[i].childNodes[1].firstChild.data;
                _message.message = String(messages.childNodes[i].childNodes[2].firstChild.data);
                _message.color   = messages.childNodes[i].childNodes[3].firstChild.data;
                
                if($('dm_'+_message.id) == null){
                    _message.show();
                    //@TODO think why this doesn't work. what's 'this' in this context?
                    //main_w.scrollTop = main_w.offsetHeight; // dirty code
                    scrollDown(); // still dirty but no way.
                }
                this.message_id = _message.id;
                //@TODO think how to destroy the message object!
            }

            /* Attendee Handling */

            var attendees = xmlDoc.documentElement.childNodes[1];
            _tmpAttendees = new DiscussAttendeeList();
            len = attendees.childNodes.length;
            for(i = 0; i < len; i++){
                var _attendee = new DiscussAttendee();
                _attendee.id    = attendees.childNodes[i].childNodes[0].firstChild.data;
                _attendee.uname = attendees.childNodes[i].childNodes[2].firstChild.data;
                _attendees.appendAttendee(_attendee);

                var index = _attendees.isNew(_attendee);             
                if(index == -1){
                    _attendees.show(_attendee);
                } else {
                    _attendees.attendees.splice(index, 1);
                }
		    }
            _attendees.refresh();
        }        
    }   
}

//////////
// Ajax //
//////////

var DiscussAjaxController = Class.create();
DiscussAjaxController.prototype = {

	initialize: function(disucuss_id){
        this.discuss_id = discuss_id;
	},
	
	init: function(handler){
        var url = 'init.php';
        var pars = 'did=' + this.discuss_id;	
        var discussAjax = new Ajax.Request(url, {method: 'get', parameters: pars, onComplete: handler});
	},
	
	get: function(message_id, handler){
    	var url = 'get.php';
    	var pars = 'mid=' + message_id + '&did=' + this.discuss_id;	
    	var discussAjax = new Ajax.Request(url, {method: 'get', parameters: pars, onComplete: handler});
	},
	
	post: function(message_id, handler){
    	var message = encodeURIComponent($F('inputWindow'));
	    var color = '000000';
	    for (i=1;;i++) {
		    if (!$('color' + i)) break;
	    	if ( $('color' + i).checked == true){
    			color = $('color' + i).value;
	    	}
	    }
    	var url = 'post.php';
	    var pars = 'msg=' + message + '&mid=' + message_id + '&did=' + this.discuss_id + '&color=' +color;
	    var discussAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete: handler});
	}
}

/////////////
// Message //
/////////////

var DiscussMessage = Class.create();
DiscussMessage.prototype = {

	initialize: function(){
    	this.message;
    	this.uname;
    	this.color;
	},
	
	show: function(){
	    var messageContainer = document.createElement('p');
	    messageContainer.id = 'dm_'+this.id;
	    if (this.color!='' && this.color!='00000') {
		    messageContainer.style.color = "#"+this.color;
        }
	    var usernameContainer = document.createElement('b');
	    var usernameText = document.createTextNode(this.uname+": ");
	    usernameContainer.insertBefore(usernameText, null);
	    messageContainer.insertBefore(usernameContainer, null); 
  	    var url = this.message.match(/((?:https?|ftp|news):\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+)/g);
      	if(url){
            var messageText = this.hyperlink(String(url))
      	} else {
    		var messageText = document.createTextNode(this.message);
    	}
        messageContainer.insertBefore(messageText, null);
    	main_w.insertBefore(messageContainer, null);
	},
	
	hyperlink: function(url){
	    var url_len = 50;
	    var linkHref = document.createElement("a");
    	linkHref.setAttribute('href', url);
    	linkHref.setAttribute('target', '_blank');
    	if(url.length > url_len){
    	    url = url.substr(0, 40) + '...';
    	}
    	var linkText = document.createTextNode(url);
    	linkHref.appendChild(linkText);	
    	return linkHref;
	}
}

//////////////
// Attendee //
//////////////

var DiscussAttendee = Class.create();
DiscussAttendee.prototype = {

	initialize: function(id, uname){
	    this.id    = id;
	    this.uname = uname;
	}
}

var DiscussAttendeeList = Class.create();
DiscussAttendeeList.prototype = {

    initialize: function(){
        this.attendees = new Array();
        this.current = new Array();
    },
    
    isNew: function(_attendee){
        for(var i = 0; i < this.attendees.length; i++){
            if(this.attendees[i].id == _attendee.id) return i;
        }
        return -1;
    },

    appendAttendee: function(_attendee) {
        this.current[this.current.length] = _attendee;
    },

    getLength: function() {
        return this.last;
    },
    
    show: function(_attendee){
        var attendeeContainer = document.createElement("span");
        attendeeContainer.id = 'da_' + _attendee.id;
        var attendeeText = document.createTextNode(_attendee.uname + " ");
        attendeeContainer.insertBefore(attendeeText, null);
        attendee_w.appendChild(attendeeContainer);
    },
    
    refresh: function(){    
	    var len = this.attendees.length;
	    if(len > 0){
		    for(i = 0; i < len; i++){
			    var node = $('da_' + this.attendees[i].id);
			    attendee_w.removeChild(node);
		    }
	    }
	    this.attendees = this.current;
	    this.current = new Array();
    }
    
}

/////////////
// Command //
/////////////

var DiscussCommand = Class.create();
DiscussCommand.prototype = {

	initialize: function(){
	    // Not implemented yet.
	}
}

///////////
// Block //
///////////

var DiscussBlock = Class.create();
DiscussBlock.prototype = {

	initialize: function(){
	    // we offer you a chance here to show your creativity.
	}
}

///////////
// Debug //
///////////

var DiscussDebug = Class.create();
DiscussDebug.prototype = {

	initialize: function(mode){
	    if(mode == true){
            var debugContainer = document.createElement("div");
            debugContainer.id = 'discussDebug';
            document.body.appendChild(debugContainer); 
            debug_w = $('discussDebug');   
	    }
	},
	
	show: function(message){
            var messageContainer = document.createElement("div");
            var messageText = document.createTextNode(message);
            messageContainer.insertBefore(messageText, null);
            var br = document.createElement("br");   
            messageContainer.insertBefore(br, null);         
            debug_w.appendChild(messageContainer);
	}
	
}