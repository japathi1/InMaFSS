<?php
/*=================================================================================*\
|* This file is part of InMaFSS                                                    *|
|* InMaFSS - INformation MAnagement for School Systems - Keep yourself up to date! *|
|* ############################################################################### *|
|* Copyright (C) flx5                                                              *|
|* E-Mail: me@flx5.com                                                             *|
|* ############################################################################### *|
|* InMaFSS is free software; you can redistribute it and/or modify                 *|
|* it under the terms of the GNU Affero General Public License as published by     *|
|* the Free Software Foundation; either version 3 of the License,                  *|
|* or (at your option) any later version.                                          *|
|* ############################################################################### *|
|* InMaFSS is distributed in the hope that it will be useful,                      *|
|* but WITHOUT ANY WARRANTY; without even the implied warranty of                  *|
|* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                            *|
|* See the GNU Affero General Public License for more details.                     *|
|* ############################################################################### *|
|* You should have received a copy of the GNU Affero General Public License        *|
|* along with InMaFSS; if not, see http://www.gnu.org/licenses/.                   *|
\*=================================================================================*/


class core {
       public static function SystemError($title, $text)
        {
                echo '<div style="width: 80%; padding: 15px 15px 15px 15px; margin: 50px auto; background-color: #F6CECE; font-family: arial; font-size: 12px; color: #000000; border: 1px solid #FF0000;">';
                echo '<img src="' . WWW . '/images/exclamation.png" style="float: left;" title="Error">&nbsp;';
                echo '<b>' . $title. '</b><br />';
                echo '&nbsp;' . $text;
                echo '<hr size="1" style="width: 100%; margin: 15px 0px 15px 0px;" />';
                echo 'Script execution was aborted. We apoligize for the possible inconvenience. If this problem is persistant, please contact an Administrator.';
                echo '</div>';
                exit;
        }

        public function generatePW($username, $password) {
            return sha1(config("salt").md5($password.config("salt").$username));
        }

        public function filter($input) {
                 return mysql_real_escape_string(stripslashes(trim($input)));
        }

        function FormatJson($json) {

            $result      = '';
            $pos         = 0;
            $strLen      = strlen($json);
            $indentStr   = '  ';
            $newLine     = "\n";
            $prevChar    = '';
            $outOfQuotes = true;

            for ($i=0; $i<=$strLen; $i++) {

                // Grab the next character in the string.
                $char = substr($json, $i, 1);

                // Are we inside a quoted string?
                if ($char == '"' && $prevChar != '\\') {
                    $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
                } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                    $result .= $newLine;
                    $pos --;
                    for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
                    }
                }

                // Add the character to the result string.
                $result .= $char;

                // If the last character was the beginning of an element,
                // output a new line and indent the next line.
                if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                    $result .= $newLine;
                    if ($char == '{' || $char == '[') {
                        $pos ++;
                    }

                    for ($j = 0; $j < $pos; $j++) {
                        $result .= $indentStr;
                    }
                }

                $prevChar = $char;
            }

            return $result;
        }
}
?>