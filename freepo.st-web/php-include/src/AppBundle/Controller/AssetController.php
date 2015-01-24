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

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AssetController extends Controller
{
    public function communityPictureAction($communityHashId)
    {
        $em = $this->getDoctrine()->getManager();
        $asset = $this->get('freepost.asset');
        $community = $em->getRepository('AppBundle:Community')->findOneByHashId($communityHashId);

        return new Response(
            $asset->retrieveCommunityPicture($community),   // Raw file
            200,                                            // Response code
            array(                                          // Headers
                'Content-Type' => 'image/png',
                'X-Robots-Tag' => 'noindex',
                'Cache-Control' => 'max-age=2592000',       // 30 days
                'Content-Disposition' => 'inline; filename="' . $communityHashId . '"'
            )
        );
    }

    public function userPictureAction($userHashId)
    {
        $em = $this->getDoctrine()->getManager();
        $asset = $this->get('freepost.asset');
        $user = $em->getRepository('AppBundle:User')->findOneByHashId($userHashId);

        return new Response(
            $asset->retrieveUserPicture($user),             // Raw file
            200,                                            // Response code
            array(                                          // Headers
                'Content-Type' => 'image/png',
                'X-Robots-Tag' => 'noindex',
                'Cache-Control' => 'max-age=2592000',       // 30 days
                'Content-Disposition' => 'inline; filename="' . $userHashId . '"'
            )
        );
    }
}
