<?php
/**
 * Simple SVN changelog formatter
 *
 * Accepted tags:
 * -NEW- new feature
 * -CHG- change
 * -BUG- bug
 * -REL- release
 * -EXC- exclude from log
 * none exlude from log
 *
 */


// various
$nl = "\n";
$relString = ' [ %s ] %s';
$itemFirstLine = "    [%s]    %s";
$itemOtherLine = "             %s";

// allowed tags - commits comments with any other tag, or without any tag
// will not be included in the changelog
$tags = array ('[NEW]', '[CHG]', '[BUG]', '[REL]');

// prevent timezone not set warnings to appear all over,
// for PHP 5.3.3+
$oldLevel = error_reporting(0);
$serverTimezone = date_default_timezone_get();
error_reporting($oldLevel);
date_default_timezone_set( $serverTimezone);

// arguments
$args = arguments($argv);
//var_dump($args);

// source and target, replace by read from command line
$source = 'changelog/jChangelog.xml';
$target = 'changelog/jChangelog.log';

// changelog title and build, to read from cli
if(empty( $args['options'])) {
	echo 'Bad arguments, syntax: --title="JInbound changelog" --build=880. Exiting';
	exit();
}

foreach( $args['options'] as $option) {
	$$option[0] = $option[1];
}

if(empty($title) || empty( $build)) {
	echo 'Bad arguments, syntax: --title="JInbound changelog" --build=880. Exiting';
	exit();

}

if(empty($linelength)) {
	$linelength = 80;
}
$sep = str_pad( '-', $linelength, '-', STR_PAD_BOTH) . $nl;

// read xml file
$logxml = simplexml_load_file( $source);
if(empty($logxml->logentry)) {
	echo 'No <log> entry found in ' . $source;
	exit();
}

$out = '';

// title
$out .= str_pad( $title, $linelength, ' ', STR_PAD_BOTH) . $nl . $nl;
//date + build
$date = new DateTime();
$dateBuild = $date->format( 'Y-m-d H:i') . ' - build #' . $build;
$out .= str_pad( '(' . $dateBuild . ')', $linelength, ' ', STR_PAD_BOTH) . $nl . $nl;

$started = false;
foreach($logxml->logentry as $entry) {
	$msgs = explode("\n", $entry->msg);
	foreach ($msgs as $msg) {
		$dateString = $entry->date;
		$date = new DateTime( $dateString);
		$type = strtoupper(substr( $msg, 0, 5));
	
		// commit messages starts with a tag?
		$toLog = in_array( $type, $tags);
		if($toLog) {
			// remove tag from message
			$msg = substr( $msg, 5);
			
			// just add a fake release for now
			if ('[REL]' != $type && !$started) {
					$out .= $nl . $nl . $sep;
					$out .= sprintf($relString, $date->format( 'Y-m-d'), 'DEVELOPMENT RELEASE');
					$out .= $nl . $sep . $nl;
			}
			
			$started = true;
			switch($type) {
				case '[REL]':
					// if release, adjust message
					$out .= $nl . $nl . $sep;
					$out .= sprintf( $relString, $date->format( 'Y-m-d'), $msg);
					$out .= $nl . $sep . $nl;
					break;
				default:
					$type = trim($type, '[]');
					$msg = formatMsg( $msg, $linelength - strlen( $itemOtherLine));
					foreach( $msg as $nbr => $line) {
						if( $nbr == 0) {
							$out .= $nl . sprintf( $itemFirstLine, strtolower($type), $msg[$nbr]);
						} else {
							$out .= $nl . sprintf( $itemOtherLine,	$msg[$nbr]);
						}
					}
					break;
			}
		}
	}

}

// write to target file
file_put_contents( $target, $out);
echo 'done';

// breaks down a commit message into a array of strings
// readu

/**
 * Breaks down a commit message into an array of strings
 * ready for inclusion into a changelog file
 * Message can be multiline, so first it is broken into
 * each individual lines. Then each line is broken down
 * to individual words. These words are in turn
 * aggregated again to form lines that are $length 
 * characters in lenght at most.
 * 
 * @param $msg
 * @param $length
 */
function formatMsg( $msg, $length) {
	$newMsg = array();

	$msgs = explode( "\n", $msg);
	$line = 0;
	foreach( $msgs as $subMsg) {
		$subMsg = trim( $subMsg);
		if(!empty($subMsg)) {
			$bits = explode( ' ', $subMsg);

			$done = false;
			$bitNbr = 0;
			do {
				$newMsg[$line] = '';
				do {
					$bits[$bitNbr] = trim($bits[$bitNbr]);
					if (!empty($bits[$bitNbr])) {
						$newMsg[$line] .= trim($bits[$bitNbr]);
					};
					$next = empty( $bits[$bitNbr+1]) ? '' : trim($bits[$bitNbr+1]);
					if( empty( $next) || strlen($newMsg[$line] . ' ' . $next) > $length) {
						$nextLine = true;
					} else {
						$newMsg[$line] .= ' ';
						$nextLine = false;
					}
					$bitNbr++;
				} while ( !$nextLine);
				$line++;
				$done = empty( $bits[$bitNbr]);
			} while (!$done);
		}
	}

	return $newMsg;
}

// read arguments from command line
// taken from php.net
function arguments($args ) {
	$ret = array(
				'exec'			=> '',
				'options'	 => array(),
				'flags'		 => array(),
				'arguments' => array(),
	);

	$ret['exec'] = array_shift( $args );

	while (($arg = array_shift($args)) != NULL) {
		// Is it a option? (prefixed with --)
		if ( substr($arg, 0, 2) === '--' ) {
			$option = substr($arg, 2);

			// is it the syntax '--option=argument'?
			if (strpos($option,'=') !== FALSE)
			array_push( $ret['options'], explode('=', $option, 2) );
			else
			array_push( $ret['options'], $option );
			 
			continue;
		}

		// Is it a flag or a serial of flags? (prefixed with -)
		if ( substr( $arg, 0, 1 ) === '-' ) {
			for ($i = 1; isset($arg[$i]) ; $i++)
			$ret['flags'][] = $arg[$i];

			continue;
		}

		// finally, it is not option, nor flag
		$ret['arguments'][] = $arg;
		continue;
	}
	return $ret;
}//function arguments
