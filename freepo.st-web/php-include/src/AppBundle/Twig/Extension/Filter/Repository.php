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

/* Static files such as communities and users pictures are retrieved from a
 * repository slave servers. This filter is used to generate the correct URLs
 * to a repository file.
 * 
 * See also http://symfony.com/doc/current/cookbook/templating/twig_extension.html
 */

namespace AppBundle\Twig\Extension\Filter;

class Repository extends \Twig_Extension
{
    // The freepost.asset service
    protected $asset;
    
    public function __construct($assetService)
    {
        $this->asset = $assetService;
    }
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('communityPicture', array($this, 'communityPictureFilter')),
            new \Twig_SimpleFilter('userPicture', array($this, 'userPictureFilter')),
        );
    }

    public function communityPictureFilter(\AppBundle\Entity\Community $community)
    {
        return $this->asset->retrieveCommunityPicture($community);
    }

    public function userPictureFilter(\AppBundle\Entity\User $user)
    {
        return $this->asset->retrieveUserPicture($user);
    }

    public function getName()
    {
        return 'Repository';
    }
}






