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

use AppBundle\Utility\Crypto;

class Community
{
    private $id;
    private $name;
    private $description;
    private $hashId;
    private $created;
    
    private $posts;
    private $users;
    
    public function __construct()
    {
        $this->hashId       = Crypto::randomString(36, 8);
        $this->name         = '';
        $this->description  = '';
        $this->created      = new \DateTime();
        
        $this->posts = new ArrayCollection();
        $this->users = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
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
    
    public function getPosts()
    {
        return $this->posts;
    }
    
    public function getUsers()
    {
        return $this->users;
    }
    
    public function addUser(User $user)
    {
        if (!$this->users->contains($user))
            $this->users->add($user);
    }
    
    public function removeUser(User $user)
    {
        if ($this->users->contains($user))
            $this->users->removeElement($user);
    }
}
