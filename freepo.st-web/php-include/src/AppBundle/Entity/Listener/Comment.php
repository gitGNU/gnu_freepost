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

class Comment
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
    
    protected function purifyComment(\AppBundle\Entity\Comment &$comment)
    {
        $commentText  = $comment->getText();
        
        // Replace textual links to clickable anchors
        // $postText = $this->stringService->linksToAnchors($postText);
        
        /* Clear user input because these are posted as raw content in order
         * to maintain the html formatting
         */
        $commentText  = $this->htmlPurifierService->purify($commentText);
        
        $comment->setText($commentText);
    }
    
    public function preFlush(\AppBundle\Entity\Comment $comment, PreFlushEventArgs $event)
    {
        $this->purifyComment($comment);
    }
}





