<?php 



function getFormOptions($options, $default=null, $with_empty=true)
{
  
  $s_options = '';

  if ($with_empty)
  {
      $s_options .= '<option value="">Select one...</option>';
  }

  foreach ($options as $k => $v)
  {
      $s_options .= '<option value="' . $k . '"' . getFormSelected($k, $default) . '>' . $v . '</option>';
  }
  
  return $s_options;
}

function htmlEncode($fields)
{
	if (is_array($fields))
	{
		$safe_fields = array();

		foreach ($fields as $k => $v)
		{
			$safe_fields[$k] = htmlEncode($v);
		}

		return $safe_fields;
	}
	else
	{
		$encodedHtml = htmlentities($fields, ENT_QUOTES, 'UTF-8');
		$encodedHtml = htmlentities($fields, ENT_QUOTES, 'ISO-8859-15');
		$encodedHtml = preg_replace('/(&amp;)(gt;|lt;|#(\d+);)/', '&$2', $encodedHtml);
		return $encodedHtml;
	}
}

function htmlDecode($fields)
{
	if (is_array($fields))
	{
		$safe_fields = array();

		foreach ($fields as $k => $v)
		{
			$safe_fields[$k] = htmlDecode($v);
		}

		return $safe_fields;
	}
	else
	{
		$decodedHtml = html_entity_decode($fields, ENT_QUOTES, 'UTF-8');
        $decodedHtml = html_entity_decode($fields, ENT_QUOTES, 'ISO-8859-15');
		return $decodedHtml;
	}
}

function boolToYesNo($value)
{
  return $value ? '<font color=\"00AA00\">Yes</font>' : '<font color=\"AA0000\">No</font>';
}

function getFormSelected($value, $default=false)
{
  if (strlen($default) > 0)
  {
      return $value == $default ? ' selected' : '';
  }
  else
  {
      return '';
  }
}

function getMessageBox($message)
{
  $msgbox  = '<div class="msgbox">';
  $msgbox .= $message;
  $msgbox .= '</div>';

  return $msgbox;
}

function showManageRedirect($message, $url)
{
  include_once(ADMIN_PATH_INCLUDE . 'header.inc.php');
  
  echo '<meta http-equiv="refresh" content="1; URL=' . $url . '">';
  echo getMessageBox($message);

  include_once(ADMIN_PATH_INCLUDE . 'footer.inc.php');
  exit;
}

function tsDisplay($timestamp)
{
	$dateTime = date('M d Y', $timestamp);
	
	return $dateTime;
}

function timeDrop($fieldName, $defaultValue)
{
	$startTime = strtotime('12:00am');
	$endTime = strtotime('11:45pm');
	
	$dropDown =  '<select name="'.$fieldName.'" style="width:100px;">';
	for ($i = $startTime; $i <= $endTime; $i += 900)
	{
		$selectedTxt = (date('g:i a', $i) == $defaultValue) ? 'selected' : '';
		$dropDown .= '<option value="'.date('g:i a', $i).'" ' . $selectedTxt . '>' . date('g:i a', $i);
	}
	$dropDown .= '</select>';
	return $dropDown;
}


?>