<?php

use Litipk\BigNumbers\Decimal;

define('CRYPTO_PRECISION', 8);
define('FIAT_PRECISION', 2);
define('PRECISION', CRYPTO_PRECISION);

define('DEC_SMALLEST_CRYPTO_AMOUNT', decpow(10, -CRYPTO_PRECISION));
define('DEC_SMALLEST_FIAT_AMOUNT', decpow(10, -FIAT_PRECISION));

function dec($n, $precision = PRECISION) {
    if ($n === NULL) return Decimal::create(0, $precision);
    if (!($n instanceof Decimal)) $n .= ''; // Library bug workaround: explicitly turn the passed value into a string
    return ($n instanceof Decimal) ? $n : Decimal::create($n, $precision);
}

function decvalid($value){
    return \preg_match(Decimal::CLASSIC_DECIMAL_NUMBER_REGEXP, $value, $captures) === 1;
}

function decadd($a, $b) {
    return dec($a)->add(dec($b), PRECISION)->asFloat();
}
function decsub($a, $b) {
    return dec($a)->sub(dec($b), PRECISION)->asFloat();
}
/**
 * @deprecated
 */
function decdiv($a, $b) {
//    throw new \Exception('Better not use decdiv, it will provide inconsistent results.');
    return dec($a)->div(dec($b), PRECISION)->asFloat();
}
function decmul($a, $b) {
    return dec($a)->mul(dec($b), PRECISION)->asFloat();
}
function decpow($a, $b) {
    return dec($a)->pow(dec($b), PRECISION)->asFloat();
}

define('DECCOMP_LEFT_IS_GREATER', 1);
define('DECCOMP_RIGHT_IS_LESS', 1);
define('DECCOMP_LEFT_IS_LESS', -1);
define('DECCOMP_RIGHT_IS_GREATER', -1);
define('DECCOMP_EQUAL', 0);
function deccomp($a, $b) {
    return dec($a)->comp(dec($b));
}
function decequal($a, $b) {
    return deccomp($a, $b) === DECCOMP_EQUAL;
}
function deczero($a) {
    return dec($a)->isZero(PRECISION);
}
function decnegative($a) {
    return dec($a)->isNegative();
}
function decpositive($a) {
    return dec($a)->isPositive();
}
function decmod($a, $b) {
    return dec($a)->mod(dec($b))->asInteger();
}

// Input: A decimal number as a String.
// Output: The equivalent hexadecimal number as a String.
function dec2hex($dec) {
    $dec = number_format($dec, 0, '', '');
    bcscale(20);
    $hex = '';
    do {
        $last = bcmod($dec, 16);
        $hex = dechex($last).$hex;
        $dec = bcdiv(bcsub($dec, $last), 16);
    } while($dec>0);
    return $hex;
}

// Input: A hexadecimal number as a String.
// Output: The equivalent decimal number as a String.
function hex2dec($number)
{
    // strip 0x
    if (strlen($number) >= 2) if (substr($number, 0, 2) === '0x') $number = substr($number, 2);

    $number = strtolower($number);
    $decvalues = array('0' => '0', '1' => '1', '2' => '2',
        '3' => '3', '4' => '4', '5' => '5',
        '6' => '6', '7' => '7', '8' => '8',
        '9' => '9', 'a' => '10', 'b' => '11',
        'c' => '12', 'd' => '13', 'e' => '14',
        'f' => '15');
    $decval = '0';
    $number = strrev($number);
    for($i = 0; $i < strlen($number); $i++)
    {
        $decval = decadd(decmul(decpow('16',$i),$decvalues[$number{$i}]), $decval);
    }
    return $decval;
}
