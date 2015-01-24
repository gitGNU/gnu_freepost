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

/* This class is used to manage uploaded files, such as pictures.
 */

namespace AppBundle\Service;

class Asset
{
    const PATH_COMMUNITY_PICTURE    = 'community/picture/';
    const PATH_USER_PICTURE         = 'user/picture/';
    
    const DEFAULT_COMMUNITY_PICTURE = 'default.png';
    const DEFAULT_USER_PICTURE      = 'default.png';
    
    // Size of Community picture
    const COMMUNITY_PICTURE_WIDTH	= 256;
    const COMMUNITY_PICTURE_HEIGHT	= 256;

    // Size of user picture
    const USER_PICTURE_WIDTH		= 256;
    const USER_PICTURE_HEIGHT		= 256;
    
    protected $repositoryMasterPath;    // Repository master server path. Set in "app/config/parameters.yml"
    protected $repositorySlaveHost;     // Repository slave server host. Set in "app/config/parameters.yml"
    
    public function __construct($repositoryMasterPath, $repositorySlaveHost)
    {
        if (is_null($repositoryMasterPath))
            $repositoryMasterPath = './';
        
        if (is_null($repositorySlaveHost))
            $repositorySlaveHost = './';
        
        $this->repositoryMasterPath = $repositoryMasterPath . 'asset/';
        $this->repositorySlaveHost = $repositorySlaveHost . 'asset/';
    }
    
    protected static function createThumbnail($filePath, $width, $height)
    {
        try {
            $imagick = new \Imagick();
            $imagick->readImage($filePath);
            $imagick->thumbnailImage($width, $height);
            $imagick->writeImage($filePath);
        } catch (Exception $e) {}
    }
    
    
    /***   USER   ***/
    
    
    // Return the name of $user picture
    protected static function getUserPictureFilename($user)
    {
        return is_null($user) ? NULL : $user->getHashId() . '.png';
    }
    
    // Return the path to $user picture on the MASTER server
    protected function getUserPictureMasterPath($user)
    {
        return is_null($user) ? NULL : $this->repositoryMasterPath . Asset::PATH_USER_PICTURE;
    }
    
    // Return the full path to $user picture on the MASTER server
    protected function getUserPictureMasterFullPath($user)
    {
        return is_null($user) ? NULL : $this->repositoryMasterPath . Asset::PATH_USER_PICTURE . Asset::getUserPictureFilename($user);
    }
    
    // Return the full path to $user picture on the SLAVE server
    protected function getUserPictureSlaveFullPath($user)
    {
        return is_null($user) ? '' : $this->repositorySlaveHost . Asset::PATH_USER_PICTURE . Asset::getUserPictureFilename($user);
    }
    
    /* $userPicture is a instance of
     *   Symfony\Component\HttpFoundation\File\UploadedFile
     * which corresponds to a form <input type="file" />.
     * 
     * If $userPicture is NULL, the default one is set.
     * 
     * Note: a user picture is update on the MASTER server
     */
    public function updateUserPicture($user, $userPicture)
    {
        if (is_null($user))
            return FALSE;
        
        if (is_null($userPicture))  // Set default picture
        {
            $this->resetUserPicture($user);
        }
        else                        // Move user picture to repository
        {
            $fileName     = Asset::getUserPictureFilename($user);
            $filePath     = $this->getUserPictureMasterPath($user);
            $fileFullPath = $this->getUserPictureMasterFullPath($user);

            $userPicture->move($filePath, $fileName);
            
            Asset::createThumbnail($fileFullPath, Asset::USER_PICTURE_WIDTH, Asset::USER_PICTURE_HEIGHT);
        }
        
        return TRUE;
    }
    
    // Delete a user picture from the MASTER server
    public function deleteUserPicture($user)
    {
        // unlink(Asset::getUserPictureMasterFullPath($user));
        $this->resetUserPicture($user);
    }

    // This method returns a link to a user picture in the SLAVE server.
    public function retrieveUserPicture($user)
    {
        return $this->getUserPictureSlaveFullPath($user);
    }
    
    // The path to the default picture to use for a user
    public function getUserDefaultPictureMasterFullPath($user)
    {
        return is_null($user) ? NULL : $this->repositoryMasterPath . Asset::PATH_USER_PICTURE . Asset::DEFAULT_USER_PICTURE;
    }
    
    // Set the user picture to the default one
    public function resetUserPicture($user)
    {
        $defaultPicture = $this->getUserDefaultPictureMasterFullPath($user);
        $destination    = $this->getUserPictureMasterFullPath($user);
        
        if (!file_exists($defaultPicture))
            return;
        
        copy($defaultPicture, $destination);
    }
    
    /***   COMMUNITY   ***/
    
    
    // Return the name of $community picture
    protected static function getCommunityPictureFilename($community)
    {
        return is_null($community) ? NULL : $community->getHashId() . '.png';
    }
    
    // Return the path to $community picture on the MASTER server
    protected function getCommunityPictureMasterPath($community)
    {
        return is_null($community) ? NULL : $this->repositoryMasterPath . Asset::PATH_COMMUNITY_PICTURE;
    }
    
    // Return the full path to $community picture on the MASTER server
    protected function getCommunityPictureMasterFullPath($community)
    {
        return is_null($community) ? NULL : $this->repositoryMasterPath . Asset::PATH_COMMUNITY_PICTURE . Asset::getCommunityPictureFilename($community);
    }
    
    // Return the full path to $community picture on the SLAVE server
    protected function getCommunityPictureSlaveFullPath($community)
    {
        return is_null($community) ? '' : $this->repositorySlaveHost . Asset::PATH_COMMUNITY_PICTURE . Asset::getCommunityPictureFilename($community);
    }
    
    /* $communityPicture is a instance of
     *   Symfony\Component\HttpFoundation\File\UploadedFile
     * which corresponds to a form <input type="file" />.
     * 
     * Note: a community picture is update on the MASTER server
     */
    public function updateCommunityPicture($community, $communityPicture)
    {
        if (is_null($community))
            return FALSE;
        
        if (is_null($communityPicture))  // Set default picture
        {
            $this->resetCommunityPicture($community);
        }
        else                             // Move community picture to repository
        {
            $fileName     = Asset::getCommunityPictureFilename($community);
            $filePath     = $this->getCommunityPictureMasterPath($community);
            $fileFullPath = $this->getCommunityPictureMasterFullPath($community);

            $communityPicture->move($filePath, $fileName);

            Asset::createThumbnail($fileFullPath, Asset::COMMUNITY_PICTURE_WIDTH, Asset::COMMUNITY_PICTURE_HEIGHT);
        }
        
        return TRUE;
    }
    
    // Delete a community picture from the MASTER server
    public function deleteCommunityPicture($community)
    {
        // unlink(Asset::getCommunityPictureMasterFullPath($community));
    }

    // This method returns a link to a community picture in the SLAVE server.
    public function retrieveCommunityPicture($community)
    {
        return $this->getCommunityPictureSlaveFullPath($community);
    }

    // The path to the default picture to use for $community
    public function getCommunityDefaultPictureMasterFullPath($community)
    {
        return is_null($community) ? NULL : $this->repositoryMasterPath . Asset::PATH_COMMUNITY_PICTURE . Asset::DEFAULT_COMMUNITY_PICTURE;
    }
    
    // Set $community picture to the default one
    public function resetCommunityPicture($community)
    {
        $defaultPicture = $this->getCommunityDefaultPictureMasterFullPath($community);
        $destination    = $this->getCommunityPictureMasterFullPath($community);
        
        if (!file_exists($defaultPicture))
            return;
        
        copy($defaultPicture, $destination);
    }

}


