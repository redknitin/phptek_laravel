<?php

if ( ! function_exists('utimezone'))
{
	function utimezone($value)
	{
		return Carbon::parse($value)->timezone(Config::get('app.utimezone'));
	}
}

if ( ! function_exists('ndate'))
{
	function ndate($value, $format = 'Y-m-d H:i:s')
	{
		if (!empty($value))
		{
			return Carbon::parse($value)->format($format);
		}
		else
		{
			return null;
		}
	}
}

if ( ! function_exists('pad'))
{
	function pad($value, $length = 0, $string = '', $side = 'left')
	{
		return str_pad($value, $length, $string, ($side === 'left') ? STR_PAD_LEFT : STR_PAD_RIGHT);
	}
}

if ( ! function_exists('array_to_collection'))
{
	function array_to_collection($array)
	{
		return Illuminate\Support\Collection::make((array)json_decode(json_encode($array)));
	}
}

if ( ! function_exists('integer_to_shortcode'))
{
	/**
	 * Translates a number to a short alhanumeric version
	 *
	 * Translated any number up to 9007199254740992
	 * to a shorter version in letters e.g.:
	 * 9007199254740989 --> PpQXn7COf
	 *
	 * specifiying the second argument true, it will
	 * translate back e.g.:
	 * PpQXn7COf --> 9007199254740989
	 *
	 * this function is based on any2dec && dec2any by
	 * fragmer[at]mail[dot]ru
	 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
	 *
	 * If you want the alphaID to be at least 3 letter long, use the
	 * $pad_up = 3 argument
	 *
	 * In most cases this is better than totally random ID generators
	 * because this can easily avoid duplicate ID's.
	 * For example if you correlate the alpha ID to an auto incrementing ID
	 * in your database, you're done.
	 *
	 * The reverse is done because it makes it slightly more cryptic,
	 * but it also makes it easier to spread lots of IDs in different
	 * directories on your filesystem. Example:
	 * $part1 = substr($alpha_id,0,1);
	 * $part2 = substr($alpha_id,1,1);
	 * $part3 = substr($alpha_id,2,strlen($alpha_id));
	 * $destindir = "/".$part1."/".$part2."/".$part3;
	 * // by reversing, directories are more evenly spread out. The
	 * // first 26 directories already occupy 26 main levels
	 *
	 * more info on limitation:
	 * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
	 *
	 * if you really need this for bigger numbers you probably have to look
	 * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
	 * or: http://theserverpages.com/php/manual/en/ref.gmp.php
	 * but I haven't really dugg into this. If you have more info on those
	 * matters feel free to leave a comment.
	 *
	 * The following code block can be utilized by PEAR's Testing_DocTest
	 * <code>
	 * // Input //
	 * $number_in = 2188847690240;
	 * $alpha_in  = "SpQXn7Cb";
	 *
	 * // Execute //
	 * $alpha_out  = alphaID($number_in, false, 8);
	 * $number_out = alphaID($alpha_in, true, 8);
	 *
	 * if ($number_in != $number_out) {
	 *     echo "Conversion failure, ".$alpha_in." returns ".$number_out." instead of the ";
	 *     echo "desired: ".$number_in."\n";
	 * }
	 * if ($alpha_in != $alpha_out) {
	 *     echo "Conversion failure, ".$number_in." returns ".$alpha_out." instead of the ";
	 *     echo "desired: ".$alpha_in."\n";
	 * }
	 *
	 * // Show //
	 * echo $number_out." => ".$alpha_out."\n";
	 * echo $alpha_in." => ".$number_out."\n";
	 * echo alphaID(238328, false)." => ".alphaID(alphaID(238328, false), true)."\n";
	 *
	 * // expects:
	 * // 2188847690240 => SpQXn7Cb
	 * // SpQXn7Cb => 2188847690240
	 * // aaab => 238328
	 *
	 * </code>
	 *
	 * @author    Kevin van Zonneveld <kevin@vanzonneveld.net>
	 * @author    Simon Franz
	 * @author    Deadfish
	 * @author    SK83RJOSH
	 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
	 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
	 * @link      http://kevin.vanzonneveld.net/
	 *
	 * @param mixed   $in       String or long input to translate
	 * @param boolean $to_num   Reverses translation when true
	 * @param mixed   $pad_up   Number or boolean pads the result up to a specified length
	 * @param string  $pass_key Supplying a password makes it harder to calculate the original ID
	 *
	 * @return mixed string or long
	 */
	function integer_to_shortcode($in, $to_num = false, $pad_up = false, $pass_key = null)
	{
		$out = '';
		$index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$base = strlen($index);

		if ($pass_key !== null)
		{
			// Although this function's purpose is to just make the
			// ID short - and not so much secure,
			// with this patch by Simon Franz (http://blog.snaky.org/)
			// you can optionally supply a password to make it harder
			// to calculate the corresponding numeric ID

			for ($n = 0; $n < strlen($index); $n++)
			{
				$i[] = substr($index, $n, 1);
			}

			$pass_hash = hash('sha256', $pass_key);
			$pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) : $pass_hash);

			for ($n = 0; $n < strlen($index); $n++)
			{
				$p[] = substr($pass_hash, $n, 1);
			}

			array_multisort($p, SORT_DESC, $i);
			$index = implode($i);
		}

		if ($to_num)
		{
			// Digital number  <<--  alphabet letter code
			$len = strlen($in) - 1;

			for ($t = $len; $t >= 0; $t--)
			{
				$bcp = bcpow($base, $len - $t);
				$out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
			}

			if (is_numeric($pad_up))
			{
				$pad_up--;

				if ($pad_up > 0)
				{
					$out -= pow($base, $pad_up);
				}
			}
		}
		else
		{
			// Digital number  -->>  alphabet letter code
			if (is_numeric($pad_up))
			{
				$pad_up--;

				if ($pad_up > 0)
				{
					$in += pow($base, $pad_up);
				}
			}

			for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--)
			{
				$bcp = bcpow($base, $t);
				$a = floor($in / $bcp) % $base;
				$out = $out . substr($index, $a, 1);
				$in = $in - ($a * $bcp);
			}
		}

		return $out;
	}
}

if ( ! function_exists('add_time_string'))
{
	/**
	 * Adds timestamp to date string if needed for comparison in database.
	 *
	 * @param string $date
	 * @param bool $endOfDay
	 * @return string
	 */
	function add_time_string($date = '', $endOfDay = false)
	{
		$date = (string) $date;

		if ($endOfDay === false)
		{
			$time = ' 00:00:00';
		}
		else
		{
			$time = ' 24:00:00';
		}

		return (stripos($date, ':') === false) ? $date.$time : $date;
	}
}

if ( ! function_exists('get_currency_value'))
{
	function get_currency_value($value, $decimalPlaces = null)
	{
		$number = '';
		$length = mb_strlen($value);

		for ($i = 0; $i < $length; $i++ )
		{
			if (is_numeric($value[$i]) || $value[$i] === '.')
			{
				$number .= $value[$i];
			}
		}

		if ($decimalPlaces)
		{
			$number = number_format($number, 2, '.', '');
		}

		if (strlen($number) === 0)
		{
			$number = null;
		}

		return $number;
	}
}

if ( ! function_exists('value'))
{
	/**
	 * Gets value from an unknown variable
	 *
	 * @param $value
	 * @param $default
	 * @return mixed
	 */
	function value($value, $default)
	{
		return (!empty($value)) ? $value : $default;
	}
}

if ( ! function_exists('is_true'))
{
	/**
	 * Checks if value is truthy ('1', 'yes', 'on', true, 1)
	 *
	 * @param $value
	 * @return bool
	 */
	function is_true($value)
	{
		return ($value === true || $value === 1 || $value === '1' || $value === 'true' || $value === 'yes' || $value === 'on');
	}
}

if ( ! function_exists('no_cache_headers'))
{
	function no_cache_headers()
	{
		header("Expires: Mon, 1 Jan 1990 01:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
}

if ( ! function_exists('force_download'))
{
	function force_download($content, $fileName = 'report.csv', $sendHeaders = true)
	{
		if ($sendHeaders)
		{
			$mimeTypes = [
				'pdf' => 'application/pdf',
				'xls' => 'application/vnd.ms-excel',
				'xlsx'=> 'application/vnd.ms-excel',
				'csv' => 'application/vnd.ms-excel',
				'txt' => 'text/plain',
			];

			$fileName = basename($fileName);
			$fileNameParsed = explode('.', $fileName);
			$ext = strtolower(array_pop($fileNameParsed));

			@ob_end_clean();

			header('Content-type: '.$mimeTypes[$ext]);
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Transfer-Encoding: binary');

			if($ext === 'pdf')
			{
				session_cache_limiter("none");
				header('Content-Length: '.strlen($content));
				header('Cache-Control: maxage=1');
				header('Accept-Ranges: bytes');
				header('Content-Disposition: inline; filename="'.$fileName.'"');
			}
			else
			{
				header('Content-Disposition: attachment; filename="'.$fileName.'"');
			}

			header('Pragma: public');
			header('Expires: 0');
		}

		echo $content;
	}
}

if ( ! function_exists('array_to_csv'))
{
	function array_to_csv($array = [], $hasHeader = true, $columnDelimiter = ',', $rowDelimiter = "\r\n", $quoteValues = true)
	{
		$output = '';

		if ($quoteValues === true)
		{
			$open = '"';
			$close = '"';
		}
		else
		{
			$open = '';
			$close = '';
		}

		$rowCounter = 0;

		foreach ($array as $row)
		{
			$row = (array) $row;

			if ($hasHeader === true && $rowCounter === 0)
			{
				$output .= str_replace(['" '], ['"'], ucwords(str_replace(['_','"'], [' ','" '], $open.implode($close.$columnDelimiter.$open, array_keys($row)).$close.$rowDelimiter)));
			}

			$output .= $open.implode($close.$columnDelimiter.$open,$row).$close.$rowDelimiter;

			$rowCounter++;
		}

		return $output;
	}
}

if ( ! function_exists('csv_to_array'))
{
	function csv_to_array($csv = '', $hasHeader = true, $columnDelimiter = ',', $rowDelimiter = "\r")
	{
		$csv = explode($rowDelimiter, $csv);

		$output = [];

		foreach ($csv as $i => $row)
		{
			if (substr($row, 0, 1) === '"')
			{
				$row = str_replace(['"'.$columnDelimiter.'"', $columnDelimiter, '"""'], ['"""', '[delimiter]', '"'.$columnDelimiter.'"'], $row);

				$row = str_replace(['\"', '"', '[doublequote]'], ['[doublequote]', '', '"'], $row);
			}

			if(trim($row) !== '')
			{
				$csv[$i] = explode($columnDelimiter, $row);

				foreach ($csv[$i] as $i2 => $column)
				{
					if ($hasHeader && $i > 0)
					{
						$name = str_ireplace(' ', '_', strtolower($csv[0][$i2]));

						$output[($i-1)][$name] = str_replace('[delimiter]', $columnDelimiter, $column);
					}
					elseif ( ! $hasHeader)
					{
						$output[$i][$i2] = str_replace('[delimiter]', $columnDelimiter, $column);
					}
				}
			}
		}

		return $output;
	}
}

if ( ! function_exists('string_to_utf8'))
{
	function string_to_utf8($str)
	{
		if (mb_detect_encoding($str, "UTF-8, ISO-8859-1") !== "UTF-8")
		{
			return iconv("ISO-8859-1", "UTF-8", $str);
		}
		else
		{
			return $str;
		}
	}
}

if ( ! function_exists('string_to_utf8'))
{
	function string_to_iso($str)
	{
		if (mb_detect_encoding($str, "UTF-8, ISO-8859-1") !== "ISO-8859-1")
		{
			return iconv("UTF-8", "ISO-8859-1", $str);
		}
		else
		{
			return $str;
		}
	}
}

if ( ! function_exists('is_string_integer'))
{
	function is_string_integer($value)
	{
		$integerValue = $value * 1;

		return ($value === '0' || ($integerValue > 0 && mb_strlen($value) === strlen((string) $integerValue)));
	}
}

if ( ! function_exists('prefix'))
{
	function prefix($value, $prefix = '')
	{
		return ( ! empty($value)) ? $prefix . $value : $value;
	}
}

if ( ! function_exists('postfix'))
{
	function postfix($value, $postfix = '')
	{
		return ( ! empty($value)) ? $value . $postfix : $value;
	}
}

if ( ! function_exists('std_dev'))
{
	function std_dev($sample)
	{
		if ( ! is_array($sample)) $sample = [$sample];

		$mean = array_sum($sample) / count($sample);
		$devs = [];

		foreach ($sample as $key => $num) $devs[$key] = pow($num - $mean, 2);

		$avg = (count($devs) > 1) ? array_sum($devs) / (count($devs) - 1) : array_sum($devs) / 1;

		return (float) number_format(sqrt($avg), 2, '.', '');
	}
}

if ( ! function_exists('twig_split'))
{
	function twig_split($value, $delimiter = ',')
	{
		$value = explode($delimiter, $value);

		return array_map('trim', $value);
	}
}

if ( ! function_exists('twig_join'))
{
	function twig_join($value, $delimiter = ',')
	{
		return implode($delimiter, array_map('trim', $value));
	}
}