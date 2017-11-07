<?php
namespace AppBundle\Helper;

class String
{
	const UNIQUE_ID_SIZE = 10;
	const CHAR_LIST = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	public static function uniqid()
	{
		$chrRepeatMin = 1;
		$chrRepeatMax = 10;
 		return substr(str_shuffle(
 			str_repeat(self::CHAR_LIST, mt_rand($chrRepeatMin,$chrRepeatMax))
		),1,self::UNIQUE_ID_SIZE);
	}
}