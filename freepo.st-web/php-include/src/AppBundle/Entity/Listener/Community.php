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

namespace AppBundle\Entity\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

class Community
{
    // The "exercise_html_purifier.default" service
    protected $htmlPurifierService;
    
    // The "freepost.string" service
    protected $stringService;
    
    public function __construct($htmlPurifierService, \AppBundle\Service\String $stringService)
    {
        $this->htmlPurifierService  = $htmlPurifierService;
        $this->stringService        = $stringService;
    }
    
    protected function purifyCommunity(\AppBundle\Entity\Community &$community)
    {
        $communityName        = $community->getName();
        $communityDescription = $community->getDescription();
        
        // A community name can NOT contain slashes!
        $communityName = str_replace(array('\\', '/'), '-', $communityName);
        
        // Limit how long a name can be
        $communityName = substr($communityName, 0, 255);
        
        /* Clear user input because these are posted as raw content in order
         * to maintain the html formatting
         */
        $communityName        = $this->htmlPurifierService->purify($communityName);
        $communityDescription = $this->htmlPurifierService->purify($communityDescription);
        
        $community->setName($communityName);
        $community->setDescription($communityDescription);
    }
    
    public function preFlush(\AppBundle\Entity\Community $community, PreFlushEventArgs $event)
    {
        $this->purifyCommunity($community);
    }
    
}





