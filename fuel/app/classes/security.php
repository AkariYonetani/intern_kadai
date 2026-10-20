<?php

/**
 * FuelPHPのSecurityクラスを拡張する
 * 自動エスケープがnullの値でエラーになるため、nullはそのまま返す
 */
class Security extends \Fuel\Core\Security
{
    public static function htmlentities($value, $flags = null, $encoding = null, $double_encode = null)
    {
        if ($value === null) {
            return null;
        }
        return parent::htmlentities($value, $flags, $encoding, $double_encode);
    }
}
