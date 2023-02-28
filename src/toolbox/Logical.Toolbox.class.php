<?php
namespace CxQ\Toolbox;
class Logical{
    static function toBool($var) {

        if (!is_string($var)) return (bool) $var;

        switch (strtolower($var)) {
          case '1':
          case 'true':
          case 'on':
          case 'yes':
          case 'y':
            return true;
          default:
            return false;
        }
    }
}