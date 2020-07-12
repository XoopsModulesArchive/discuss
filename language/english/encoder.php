<?php
	class DiscussEncoder
	{		
		function fromUtf8($string)
		{	
			return utf8_decode($string);
		}

		function toUtf8($string)
		{
			return utf8_encode($string);
        }
	}
?>