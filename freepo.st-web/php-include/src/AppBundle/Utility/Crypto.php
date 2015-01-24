<?php

/* freepost
 * http://freepo.st
 *
 * Copyright © 2014-2015 zPlus
 * 
 * This file is part of freepost.
 * freepost is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * freepost is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with freepost. If not, see <http://www.gnu.org/licenses/>.
 */

namespace AppBundle\Utility;

class Crypto
{
    public static function sha512($string)
    {
        return hash("sha512", $string);
    }

    public static function randomString($base = 62, $length = 0)
    {
        if($length < 1)	return "";

        if($base < 1)	$base = 1;
        if($base > 62)	$base = 62;

        $srnd = '';
        $sbase = substr('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 0, $base);

        for($i = 0, --$base; $i < $length; $i++)
            $srnd .= $sbase[mt_rand(0, $base)];

        return $srnd;
    }

}
