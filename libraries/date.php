<?php

class Date
{
	/**
	 * Unix timestamp for object.
	 */
	protected $time;
	
	/**
	 * Preset format strings.
	 */
	protected $formats = array(
		'datetime' => '%Y-%m-%d %H:%M:%S',
		'date' => '%Y-%m-%d',
		'time' => '%H:%M:%S',
	);
	
	/**
	 * Static constructor.
	 *
	 * param	string	$str
	 */
	public static function forge($str = null)
	{
		$class = __CLASS__;
		return new $class($str);
	}
	
	/**
	 * Constructor.
	 *
	 * param	string	$str
	 */
	public function __construct($str = null)
	{
		// if no given...
		if ($str === null)
		{
			// use now
			$this->time = time();
		}
		
		// if given...
		else
		{
			// if number...
			if (is_numeric($str))
			{
				// treat as unix time
				$this->time = $str;
			}
			
			// if NOT number...
			else
			{
				// treat as string
				$time = strtotime($str);
				
				// if conversion fails...
				if ($time === false or $time === null)
				{
					// set time as false
					$this->time = false;
				}
				else
				{
					// accept time value
					$this->time = $time;
				}
			}
		}
	}
	
	/**
	 * Retreive the timestamp.
	 */
	public function time()
	{
		return $this->time;
	}
	
	/**
	 * Format the timestamp to something useful.
	 *
	 * param	string	$str
	 */
	public function format($str)
	{
		// convert alias string
		if (in_array($str, array_keys($this->formats)))
		{
			$str = $this->formats[$str];
		}
	
		// if valid unix timestamp...
		if ($this->time !== false)
		{
			// return formatted value
			return strftime($str, $this->time);
		}
		else
		{
			// return false
			return false;
		}
	}
	
	/**
	 * Modify timestamp relative to existing.
	 *
	 * param	string	$str
	 */
	public function reforge($str)
	{
		// if not false...
		if ($this->time !== false)
		{
			// amend the time
			$this->time = strtotime($str, $this->time);
		}
		
		// return
		return $this;
	}
	
	/**
	 * Compare two date objects or timestamps.
	 *
	 * param	object/int	$date1
	 * param	object/int	$date2
	 */
	public static function compare($date1, $date2 = null)
	{
		// convert to objects, all
		if (!is_object($date1)) $date1 = self::forge($date1);
		if (!is_object($date2)) $date2 = self::forge($date2);
		
		// catch error
		if (!$date1->time() or !$date2->time()) return false;
		
		// perform comparison
		$date1 = date_create($date1->format('datetime'));
		$date2 = date_create($date2->format('datetime'));
		$diff = date_diff($date1, $date2);
		
		// catch error
		if ($diff === false) return false;
		
		// filter result
		$filter = array(
			'y' => 'years',
			'm' => 'months',
			'd' => 'days',
			'h' => 'hours',
			'i' => 'minutes',
			's' => 'seconds',
		);
		
		// convert keys
		$clean = array();
		$diff = get_object_vars($diff);
		foreach ($diff as $key=>$value)
		{
			if (in_array($key, array_keys($filter)))
			{
				$clean[$filter[$key]] = $value;
			}
		}
		$clean['invert'] = $diff['invert'];
		
		// return
		return $clean;
	}
	
	/**
	 * Get number of days in month.
	 */
	public static function days_in_month($date)
	{
		// convert to object
		if (!is_object($date)) $date = self::forge($date);
	
		// return
		return cal_days_in_month(CAL_GREGORIAN, $date->format('%m'), $date->format('%Y'));
	}
}