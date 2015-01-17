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

class VotePost
{
    private $user;
    private $post;
    private $vote;
    private $datetime;

    public function __construct()
    {
        $this->post     = NULL;
        $this->user     = NULL;
        $this->vote     = 0;
        $this->datetime = new \DateTime();
    }
    
    public function getId()
    {
        return $this->id;
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
    
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    public function getPost()
    {
        return $this->post;
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

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }
    
    public function setUpvoted()
    {
        $this->vote = 1;

        return $this;
    }
    
    public function setDownvoted()
    {
        $this->vote = -1;

        return $this;
    }
    
    public function upvote($user, $post)
    {
        $this->datetime = new \DateTime();
        $this->post     = $post;
        $this->user     = $user;
        $this->vote     = 1;
    }
    
    public function downvote($user, $post)
    {
        $this->datetime = new \DateTime();
        $this->post     = $post;
        $this->user     = $user;
        $this->vote     = -1;
    }
    
    public function upvoted()
    {
        return $this->vote == 1;
    }
    
    public function downvoted()
    {
        return $this->vote == -1;
    }
    
    
}
