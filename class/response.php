<?php
/**
 * Class for rendering XML Response
 */
class DiscussResponseHandler
{
	var $response = "";
	
	function DiscussResponseHandler(){
		$this->response = "";
	}
	
	function render() {
		// TODO : make sure if it's nessesary or not.
		//mb_http_output('utf-8');

		header('Content-type: text/xml; charset=utf-8');
		echo '<?xml version="1.0"?>';
		echo "<response>";
		echo $this->response;
		echo "</response>";
	}
	
	function addMessageXML($msgs, $client_message_id = 0){
		$this->response .= "<msgobjs>";
		foreach ($msgs as $msg) {
			
			$message_id = $msg['message_id'];
			if( $message_id <= intval($client_message_id) ) continue;
			
			$this->response .= "<msgobj>";
				$this->response .= "<mid>".$message_id."</mid>";
				$this->response .= "<uname>".$msg['uname']."</uname>";
				$this->response .= "<message>".$msg['message']."</message>";
				$this->response .= "<color>".$msg['color']."</color>";
			$this->response .= "</msgobj>";
		}
		$this->response .= "</msgobjs>";		
	}
	
	function addAttendeeXML($attendees)
	{
		// TODO : should write error handling code.
		$this->response .= "<attendeeobjs>";
			foreach($attendees as $attendee){
			$this->response .= "<attendeeobj>";
				$this->response .= "<attendee_id>".$attendee->getVar('attendee_id')."</attendee_id>";
				$this->response .= "<uid>".$attendee->getVar('uid')."</uid>";
				$this->response .= "<attendee_uname>".DiscussEncoder::toUtf8($attendee->getVar('uname'))."</attendee_uname>";
			$this->response .= "</attendeeobj>";
			}
		$this->response .= "</attendeeobjs>";		
	}
}

?>