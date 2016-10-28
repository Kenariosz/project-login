<?php
/**
 * @author: Kenariosz
 * @date  : 2016-10-21
 */

/**
 * Flash
 *
 * @param $title
 * @param $message
 *
 * @return mixed
 */
function flash($title = null, $message = null)
{
	$flash = app('App\Http\Utilities\Flash');

	if(func_num_args() == 0)
	{
		return $flash;
	}

	return $flash->info($title, $message);
}