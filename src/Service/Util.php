<?php

    namespace RatePAY\Service;

    class Util
    {

        /**
         * Changes value to negative if necessary
         *
         * @param $string
         * @return string
         */
        public static function changeValueToNegative($value)
        {
            $value = floatval($value);
            return ($value > 0) ? $value * -1 : $value;
        }

        /**
         * Changes the camel case notation to separation by underscore
         *
         * @param $string
         * @return string
         */
        public static function changeCamelCaseToUnderscore($string)
        {
            return self::changeCase($string, '_', 'upper');
        }

        /**
         * Changes the camel case notation to separation by dash
         *
         * @param $string
         * @return string
         */
        public static function changeCamelCaseToDash($string)
        {
            return self::changeCase($string, '-', 'lower');
        }

        /**
         * Changes the case by definition
         *
         * @param $string
         * @param $delimiter
         * @param string $case
         * @return string
         */
        private static function changeCase($string, $delimiter, $case = '') {
            $stringFormatted = preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);
            $stringFormatted = implode($delimiter, $stringFormatted);
            if ($case == 'upper') {
                $stringFormatted = strtoupper($stringFormatted);
            } elseif ($case == 'lower') {
                $stringFormatted = strtolower($stringFormatted);
            }

            return $stringFormatted;
        }

    }