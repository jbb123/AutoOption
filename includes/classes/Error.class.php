<?php
class Error
{
    static $errors = array();

    public static function raise($message)
    {
    	array_push(self::$errors, $message);
    }

    public static function numErrors()
    {
        return (int)count((array)self::$errors);
    }

    public static function hasErrors()
    {
    	return (bool)self::numErrors();
    }

    public static function getLastError()
    {
        return array_pop(self::$errors);
    }

    public static function getErrors()
    {
        $tmp = self::$errors;
        self::$errors = array();

        return $tmp;
    }

    public static function getErrorList()
    {
        $err_list = '';

        if (self::numErrors())
        {
            $err_list = '<div id="form-errors">Please correct the following errors:<ul>';

            foreach (self::$errors as $e)
            {
                $err_list .= '<li>' . $e . '</li>';
            }

            $err_list .= '</ul></div>';

            self::$errors = array();
        }

        return $err_list;
    }
}
?>