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

use AppBundle\Entity\VotePost;
use AppBundle\Utility\Crypto;

class Post
{
    private $id;
    private $hashId;
    private $created;
    private $dateCreated;
    private $commentsCount;
    private $title;
    private $text;
    private $user;
    private $vote;
    private $votes;
    private $comments;
    private $community;

    public function __construct()
    {
        $this->id            = 0;
        $this->hashId        = Crypto::randomString(36, 8);
        $this->community     = 0;
        $this->commentsCount = 0;
        $this->title         = '';
        $this->text          = '';
        $this->user          = NULL;
        $this->created       = new \DateTime();
        $this->dateCreated   = new \DateTime();
        $this->vote          = 0;
        $this->votes         = new ArrayCollection();
        $this->comments      = new ArrayCollection();
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
    
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
    
    public function getTitle()
    {
        return $this->title;
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
    
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setCommunity($community)
    {
        $this->community = $community;

        return $this;
    }
    
    public function getCommunity()
    {
        return $this->community;
    }
    
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;

        return $this;
    }
    
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }
    
    public function increaseCommentsCount() {
        $this->commentsCount++;
    }
    
    public function decreaseCommentsCount() {
        $this->commentsCount--;
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
    
    public function addToVotes(VotePost $vote)
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
}
