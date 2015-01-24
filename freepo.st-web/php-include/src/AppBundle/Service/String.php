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

namespace AppBundle\Service;

class String
{
    /* Replace text links to anchor tags
     * Example: http://example.com => <a targe="_blank" href="http://example.com">http://example.com</a>
     */
    public function linksToAnchors($string = '', $target = '_blank')
    {
        return preg_replace(
            "/((?:http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,2}(?:\/\S*)?)/i",
            '<a href="${1}">${1}</a>',
            $string,
            -1,
            $count
        );
        return preg_replace(
            "/((?:http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,2}(?:\/\S*)?)/i",
            '<a href="${1}" target="' . $target . '">${1}</a>',
            $string
        );
    }

}






