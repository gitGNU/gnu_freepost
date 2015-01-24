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

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\VoteComment;
use AppBundle\Utility\Crypto;

class Comment
{
    private $id;
    private $hashId;
    private $created;
    private $dateCreated;
    private $read;              // If this comment has been read
    private $text;
    private $vote;
    private $votes;
    
    private $children;
    private $parent;
    private $parentUser;
    private $post;
    private $user;

    public function __construct()
    {
        $this->hashId       = Crypto::randomString(36, 8);
        $this->created      = new \DateTime();
        $this->dateCreated  = new \DateTime();
        $this->read         = FALSE;
        $this->text         = '';
        $this->vote         = 0;
        $this->votes        = new ArrayCollection();
        
        $this->parent       = NULL;
        $this->parentUser   = NULL;
        $this->children     = new ArrayCollection();
        $this->post         = NULL;
        $this->user         = NULL;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setHashId($hashId)
    {
        $this->hashId = $hashId;

        return $this;
    }

    public function getHashId()
    {
        return $this->hashId;
    }

    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }
    
    public function getVote()
    {
        return $this->vote;
    }
    
    public function getVotes()
    {
        return $this->votes;
    }
    
    public function addToVotes(VoteComment $vote)
    {
        return $this->votes->add($vote);
    }
    
    public function upvote()
    {
        $this->vote++;
    }
    
    public function downvote()
    {
        $this->vote--;
    }
    
    // Used when switching from downvote to upvote
    public function doubleUpvote()
    {
        $this->vote += 2;
    }
    
    // Used when switching from upvote to downvote
    public function doubleDownvote()
    {
        $this->vote -= 2;
    }
    
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    public function getPost()
    {
        return $this->post;
    }
    
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
    
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
    public function setParentUser($parentUser)
    {
        $this->parentUser = $parentUser;

        return $this;
    }
    
    public function getParentUser()
    {
        return $this->parentUser;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    public function hasParent()
    {
        return !is_null($this->parent);
    }
    
    public function setRead()
    {
        $this->read = TRUE;

        return $this;
    }
    
    public function setUnread()
    {
        $this->read = FALSE;

        return $this;
    }
    
    public function hasBeenRead()
    {
        return $this->read;
    }
}







