<?php

namespace MMWS\Handler;

class CaseHandler
{
    /**
     * Converts a case type to another
     * Default is to convert from snake_case to camelCase.
     * Converts snake_case to camelCase or CamelCase. A key => value array will have
     * its indexes converted not its values
     * 
     * @param String|Array<String[]> $content string or string to be converted
     * @param Int $model scheme to convert cases. 0 for snake to camel and 1 to camel to snake. Default is 0.
     * @param Bool $capitalize to capitalise cases into CaseType
     * @param Array<String[]> options. A custom separator can be set to $model = 1 as 'separator' => '[any_char]'
     * 
     * @return String|Array<String[]> 
     * 
     * String example
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::convert('user_name');
     * 
     * print_r($str) -> outputs: userName
     * 
     * Array example
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::convert(['userName' => 'garry'], 1, true);
     * 
     * print_r($str) -> outputs: ['user_name' => 'garry']
     * 
     * Options
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::convert(['userName' => 'garry'], 1, true, ['separator' => '-']);
     * 
     * print_r($str) -> outputs: ['user-name' => 'garry']
     * 
     * ----------     
     */
    static function convert($content, Int $model = 0, Bool $capitalize = false, array $options = [])
    {
        switch ($model) {
            case 0:
                return self::snakeToCamel($content, $capitalize);
            case 1:
                return self::camelToSnake($content, $capitalize, $options);
        }
    }

    /**
     * Converts snake_case to camelCase or CamelCase. A key => value array will have
     * its indexes converted not its values
     * 
     * String example
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::snakeToCamel('user_name');
     * 
     * print_r($str) will print userName
     * 
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::snakeToCamel('user_name', true);
     * 
     * print_r($str) will print UserName
     * 
     * Array example
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::snakeToCamel(['user_name' => 'garry'], true);
     * 
     * print_r($str) will print ['UserName' => 'garry']
     * 
     * ----------
     * 
     * @param String|Array<String[]> $content string or string to be converted
     * @param Bool $capitalize capitalize CamelCase or camelCase
     * 
     * @return String|Array<String[]>
     */
    private static function snakeToCamel($content, Bool $capitalize = false)
    {
        if (is_array($content)) {
            $output = array();
            /** Loops through the array to get the keys */
            foreach ($content as $key => $value) {

                if (is_array($value)) $value = self::snakeToCamel($value, $capitalize);

                $parts = explode('_', $key);

                $outVarName = '';
                /** Loops through every name part separated by underscore (_) */
                $i = 0;
                foreach ($parts as $part) {
                    if ($i === 0 && !$capitalize) {
                        $outVarName = strtolower($part);
                        $i++;
                        continue;
                    }
                    $outVarName .= ucfirst(strtolower($part));
                    $i++;
                }
                $output[$outVarName] = $value;
            }
            return $output;
        }

        $parts = explode('_', $content);

        $outVarName = '';
        /** Loops through every name part separated by underscore (_) */
        $i = 0;
        foreach ($parts as $part) {
            if ($i === 0 && !$capitalize) {
                $outVarName = strtolower($part);
                $i++;
                continue;
            }
            $outVarName .= ucfirst(strtolower($part));
            $i++;
        }
        return $outVarName;
    }

    /**
     * Converts camelCase or CamelCase to snake_case or any separator. 
     * A key => value array will have its indexes converted not its values
     * 
     * String example
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::snakeToCamel('userName');
     * 
     * print_r($str) will print user_name
     * 
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::snakeToCamel('userName', true);
     * 
     * print_r($str) will print User_Name
     * 
     * Array example
     * ----------
     * 
     * $str = new MMWS\Handler\CaseHandler::snakeToCamel(['userName' => 'garry'], true, '-');
     * 
     * print_r($str) will print ['User-Name' => 'garry']
     * 
     * ----------
     * 
     * @param String|Array<String[]> $content string or string to be converted
     * @param Bool $capitalize capitalize CamelCase or camelCase
     * @param String $separator case word separator. Default is snake_case.
     * 
     * @return String|Array<String[]>
     */
    private static function camelToSnake($content, $capitalize = false, array $options = [])
    {
        $separator = $options['separator'] ?? '_';
        if (is_array($content)) {
            $output = array();

            /** Loops through the array to get the keys */
            foreach ($content as $key => $value) {
                if (is_array($value)) $value = self::camelToSnake($value, $capitalize, $options);

                $parts = preg_split('/(?=[A-Z])/', $key, -1, PREG_SPLIT_NO_EMPTY);

                $outVarName = '';
                /** Loops through every name part separated by case */
                $i = 0;
                foreach ($parts as $part) {
                    $outVarName .= $separator . ucfirst(strtolower($part));
                    $i++;
                }
                $outVarName = trim($outVarName, $separator);
                if (!$capitalize) $outVarName = strtolower($outVarName);
                $output[$outVarName] = $value;
            }
            return $output;
        }

        $parts = preg_split('/(?=[A-Z])/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $outVarName = '';

        /** Loops through every name part separated by case */
        $i = 0;
        foreach ($parts as $part) {
            $outVarName .= $separator . ucfirst(strtolower($part));
            $i++;
        }
        if (!$capitalize) {
            $outVarName = strtolower($outVarName);
        }
        $outVarName = trim($outVarName, $separator);
        return $outVarName;
    }
}
