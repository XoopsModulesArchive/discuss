<?php
	class DiscussEncoder
	{		
	function fromUtf8($from_string)
	{
//		$local_string = mb_convert_encoding($utf8_string, "euc-jp", "utf-8");
		$from_enc = mb_detect_encoding($from_string);
		$local_string = ('euc-jp' == $from_enc) ? $from_string : mb_convert_encoding($from_string, "euc-jp", $from_enc);

		return $local_string;
	}

		function toUtf8($local_string)
		{
			$utf8_string = mb_convert_encoding($local_string, "utf-8", "euc-jp");
			return $utf8_string;
        }
	}
?>