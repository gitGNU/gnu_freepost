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
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use AppBundle\Utility\Crypto;

/**
 * User
 */
class User implements AdvancedUserInterface, \Serializable
{
    // User properties
    
    private $email;
    private $emailToConfirm;
    private $emailToConfirmCode;
    private $hashId;
    private $id;
    private $isActive;
    /* Show posts list as
     *   - compact: header only
     *   - default: header + post content
    */
    private $feedFormat;
    private $password;
    private $passwordResetCode;
    private $registered;
    private $salt;
    private $username;
    
    // Relations
    
    private $posts;
    private $comments;
    private $communities;
    private $commentVotes;
    private $postVotes;
    
    /* When a new email is added for validation, a new code is generated.
     * The code is stored to emailToConfirmCode field, and sent by email
     * for verification.
     */
    protected function makeEmailToConfirmCode()
    {
        if (is_null($this->emailToConfirm))
            return NULL;
        
        return Crypto::randomString(36, 64);
    }
    
    public function __construct()
    {
        $this->email                 = NULL;
        $this->emailToConfirm        = NULL;
        $this->emailToConfirmCode    = NULL;
        $this->hashId                = Crypto::randomString(36, 8);
        $this->id                    = NULL;
        $this->isActive              = TRUE;
        $this->feedFormat            = $this->setFeedFormat();
        $this->password              = '';
        $this->passwordResetCode     = NULL;
        $this->registered            = new \DateTime();
        $this->salt                  = '';
        $this->username              = '';
        
        $this->comments              = new ArrayCollection();
        $this->communities           = new ArrayCollection();
        $this->commentVotes          = new ArrayCollection();
        $this->postVotes             = new ArrayCollection();
        $this->posts                 = new ArrayCollection();
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

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function deleteEmail()
    {
        $this->email = NULL;

        return $this;
    }
    
    public function setEmailToConfirm($email)
    {
        $this->emailToConfirm = $email;
        $this->setEmailToConfirmCode($this->makeEmailToConfirmCode());

        return $this;
    }
    
    public function deleteEmailToConfirm()
    {
        $this->emailToConfirm = NULL;

        return $this;
    }

    public function getEmailToConfirm()
    {
        return $this->emailToConfirm;
    }
    
    public function setEmailToConfirmCode($code)
    {
        $this->emailToConfirmCode = $code;

        return $this;
    }

    public function deleteEmailToConfirmCode()
    {
        $this->emailToConfirmCode = NULL;

        return $this;
    }
    
    public function getEmailToConfirmCode()
    {
        return $this->emailToConfirmCode;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = Crypto::sha512($password);

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setRegistered($registered)
    {
        $this->registered = $registered;

        return $this;
    }

    public function getRegistered()
    {
        return $this->registered;
    }

    public function setPasswordResetCode($passwordResetCode = NULL)
    {
        $this->passwordResetCode = $passwordResetCode;

        return $this;
    }
    
    public function autosetPasswordResetCode()
    {
        return $this->setPasswordResetCode(Crypto::randomString(36, 128));
    }
    
    public function deletePasswordResetCode()
    {
        return $this->setPasswordResetCode();
    }

    public function getPasswordResetCode()
    {
        return $this->passwordResetCode;
    }
    
    public function getPasswordResetCodeHead()
    {
        return substr($this->getPasswordResetCode(), 0, floor(strlen($this->passwordResetCode) / 2));
    }
    
    public function getPasswordResetCodeTail()
    {
        return substr($this->getPasswordResetCode(), floor(strlen($this->passwordResetCode) / 2));
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    // Make sure we have a valid value for this input
    public function setFeedFormat($feedFormat = '')
    {
        if (is_null($feedFormat))
            $feedFormat = '';
        
        $feedFormat = strtoupper($feedFormat);
        
        switch ($feedFormat)
        {
            case 'COMPACT': break;
            default:        $feedFormat = 'DEFAULT';
        }
        
        $this->feedFormat = $feedFormat;
    }
    
    public function setDefaultFeedFormat()
    {
        $this->setFeedFormat();
        
        return $this;
    }
    
    public function setCompactFeedFormat()
    {
        $this->setFeedFormat('COMPACT');
        
        return $this;
    }
    
    public function getFeedFormat()
    {
        return is_null($this->feedFormat) || $this->feedFormat == '' ? 'DEFAULT' : $this->feedFormat;
    }
    
    public function getPosts()
    {
        return $this->posts;
    }
    
    public function getCommunities()
    {
        return $this->communities;
    }
    
    public function addCommunity(Community $community)
    {
        $community->addUser($this); // synchronously updating inverse side
        
        if (!$this->communities->contains($community))
            $this->communities->add($community);
    }
    
    public function removeCommunity(Community $community)
    {
        $community->removeUser($this); // synchronously updating inverse side
        
        if ($this->communities->contains($community))
            $this->communities->removeElement($community);
    }
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
    
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->hashId,
            $this->username,
            $this->password,
            $this->email
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->hashId,
            $this->username,
            $this->password,
            $this->email
        ) = unserialize($serialized);
    }
    
    
}









