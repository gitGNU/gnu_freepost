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

class Post
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
    
    protected function purifyPost(\AppBundle\Entity\Post &$post)
    {
        $postTitle = $post->getTitle();
        $postText  = $post->getText();
        
        // A post title can NOT contain slashes!
        $postTitle = str_replace(array('\\', '/'), '-', $postTitle);
        
        // Limit how long a title can be
        $postTitle = substr($postTitle, 0, 255);
        
        // Replace textual links to clickable anchors
        // $postText = $this->stringService->linksToAnchors($postText);
        
        /* Clear user input because these are posted as raw content in order
         * to maintain the html formatting
         */
        $postTitle = $this->htmlPurifierService->purify($postTitle);
        $postText  = $this->htmlPurifierService->purify($postText);
        
        $post->setTitle($postTitle);
        $post->setText($postText);
    }
    
    public function preFlush(\AppBundle\Entity\Post $post, PreFlushEventArgs $event)
    {
        $this->purifyPost($post);
    }
    
    /*
    public function prePersist(\AppBundle\Entity\Post $post, LifecycleEventArgs $event)
    {
        $this->purifyPost($post);
    }
    
    public function preUpdate(\AppBundle\Entity\Post $post, LifecycleEventArgs $event)
    {
        //$this->purifyPost($post);
    }
    */
}





