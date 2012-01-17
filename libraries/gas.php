<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Gas ORM Packages
 *
 * A lighweight and easy-to-use ORM for CodeIgniter
 * 
 * This packages intend to use as semi-native ORM for CI, 
 * based on the ActiveRecord pattern. This ORM uses CI stan-
 * dard DB utility packages also validation class.
 *
 * @package     Gas ORM
 * @category    ORM
 * @version     2.0.0
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD
 *
 * =================================================================================================
 * =================================================================================================
 * Copyright 2011 Taufan Aditya a.k.a toopay. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 * 
 * 1. Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * 
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY Taufan Aditya a.k.a toopay ‘’AS IS’’ AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Taufan Aditya a.k.a toopay OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * The views and conclusions contained in the software and documentation are those of the
 * authors and should not be interpreted as representing official policies, either expressed
 * or implied, of Taufan Aditya a.k.a toopay.
 * =================================================================================================
 * =================================================================================================
 */

/**
 * Gas Class.
 *
 * @package     Gas ORM
 * @category    Libraries
 * @version     2.0.0
 */

class Gas {
	
	/**
	 * @var bool  Gas ORM initialization status
	 */
	protected static $init = FALSE;

	/**
	 * Constructor
	 *
	 * @throws Exception if `default` group connection fails
	 * @return void
	 */
	public function __construct() 
	{
		if (static::$init == FALSE)
		{
			// Access current instance singleton
			$CI =& get_instance();

			// Load necessary configuration(s)
			$CI->config->load('gas', TRUE, TRUE);
			$CI->config->load('migration', TRUE, TRUE);	

			// Set temporary config handler
			$config = $CI->config->item('gas');

			// Validate DB instance
			if ( ! class_exists('CI_DB'))
			{
				$DB = $CI->load->database('default', TRUE);

				// Register into CI super object, so we could monitore it
				$CI->db = $DB;
			}
			else
			{
				$DB = $CI->db;
			}

			// Include the bootstrap
			include_once APPPATH.'third_party'.DIRECTORY_SEPARATOR.'gas'.DIRECTORY_SEPARATOR.'bootstrap.php';

			// Set initialization flag
			static::$init = TRUE;
		}
    }

    /**
     * Exception handler
     * 
     * @param  Exception  
     * @return response   CI show_error method  
     */
    public function exception(Exception $e)
    {
		// Access current instance singleton
		$CI      =& get_instance();
		$speaker = $CI->lang;
		$speaker->load('gas');

		// Get the exception message
		$parser    = NULL;
		$exception = explode(':', $e->getMessage());

		// Parse the point and the identifier
		$point  = $exception[0];

		if (count($exception) == 2)
		{
			$parser = $exception[1];
		}

		// Is there something to parse?
		if (FALSE === ($msg = $speaker->line($point)))
		{
			$msg = $point;
		}

		// Finalize the error message
		$error = (is_string($parser)) ? sprintf($msg, $parser) : $msg;

		// Check whether we need to generate tracer
		if (preg_match('/^\[(.*?)\]([^\n]+)$/', $error, $m) && count($m) == 3)
		{
			// Initial values
			$function = '';
			$file     = '';
			$line     = '';
			$snap     = '';

			// Capture the method which trigerring an exception
			$trigger = $m[1];
			$error   = $m[2];

			// Build the snapshot
			$traces  = $e->getTrace();
			krsort($traces);

			foreach ($traces as $trace)
			{
				if ($trace['function'] == $trigger)
				{
					$function = '  '.str_pad('Function', 10).': '.$trigger."\n";

					if (array_key_exists('file', $trace))
					{
						// Get as needed
						$errFile = $trace['file'];
						$errLine = (int) $trace['line'];
						// Build the trace
						$file    = '  '.str_pad('File', 10).': '.$errFile."\n";
						$line    = '  '.str_pad('Line', 10).': '.$errLine."\n";
					}

					break;
				}
			}

			unset($trace);

			// Build the output fragment of trace section
			if (isset($errLine) && isset($errFile))
			{
				$lines   = array();

				if (file_exists($errFile) && $handle = fopen($errFile, 'r')) {

			        while (($each_line = fgets($handle, 4096)) !== FALSE) {
			        	$lines[] = $each_line;
				    }

				    if ( ! feof($handle)) {
				    	return $lines;
				    }

				    fclose($handle);
				}

				$snap  = '  '.str_pad('Snapshot', 10).': '."\n";
				$snap .= '<code>';
				$snap .= '<small>'.($errLine-2).'</small>'.$lines[$errLine-2];
				$snap .= '<small>'.($errLine-1).'</small>'.$lines[$errLine-1];
				$snap .= '<small>'.($errLine-0).'</small>'.$lines[$errLine-0];
				$snap .= '<small>'.($errLine+1).'</small>'.$lines[$errLine+1];
				$snap .= '</code>';
			}

			$trace = '<pre class="exception">'."\n\n".$function.$file.$line.$snap.'</pre>';
		}
		else
		{
			$trace = '';
		}

		// Output it with CI `show_error`
		$error .= $trace;
		show_error($error);
    }
}