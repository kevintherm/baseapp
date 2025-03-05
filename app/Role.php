<?php

namespace App;

enum Role: string
{
    use EnumToArray;

    case Admin = 'admin';
    case User = 'user';
    case Editor = 'editor';
}

trait EnumToArray
{

  public static function names(): array
  {
    return array_column(self::cases(), 'name');
  }

  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }

  public static function array(): array
  {
    return array_combine(self::values(), self::names());
  }

}
