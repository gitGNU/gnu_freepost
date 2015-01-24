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

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class Post extends EntityRepository
{
    /* Retrieve a list of posts.
     * $user is used to find $user vote for each post
     */
    protected function findPosts($community = NULL, $user = NULL, $sort = '')
    {
        if (is_null($community))
            return NULL;
        
        $em = $this->getEntityManager();

        switch(strtoupper($sort))
        {
            case 'NEW':
                $query = $em->createQuery(
                    'SELECT p, u, v
                    FROM AppBundle:Post p
                    JOIN p.user u
                    LEFT JOIN p.votes v WITH v.user = :user
                    WHERE p.community = :community
                    ORDER BY p.created DESC'
                )
                ->setParameter('user', $user)
                ->setParameter('community', $community);
                break;
            
            default: // HOT
                $query = $em->createQuery(
                    'SELECT p, u, v
                    FROM AppBundle:Post p
                    JOIN p.user u
                    LEFT JOIN p.votes v WITH v.user = :user
                    WHERE p.community = :community
                    ORDER BY p.dateCreated DESC, p.vote DESC, p.created DESC'
                )
                ->setParameter('user', $user)
                ->setParameter('community', $community);
        }
        
        return $query->setMaxResults(32)->getResult();
    }
    
    // Retrieve a list of newest posts sorted by vote
    public function findHot($community = NULL, $user = NULL)
    {
        return $this->findPosts($community, $user, 'hot');
    }
    
    // Retrieve a list of newest posts sorted by date (newest first)
    public function findNew($community = NULL, $user = NULL)
    {
        return $this->findPosts($community, $user, 'new');
    }
    
    // Get $user posts
    public function findMyPosts($user = NULL)
    {
        $em = $this->getEntityManager();

        return $em->createQuery(
            'SELECT p
            FROM AppBundle:Post p
            WHERE p.user = :user
            ORDER BY p.created DESC'
        )
        ->setParameter('user', $user)
        ->setMaxResults(100)
        ->getResult();
    }
    
    public function submitNew(&$community, &$user, &$title, &$text)
    {
        $em = $this->getEntityManager();
        
        $datetime = new \DateTime();

        $p = new \AppBundle\Entity\Post();
        $p->setCommunity($community);
        $p->setUser($user);
        $p->setTitle($title);
        $p->setText($text);
        $p->setCreated($datetime);
        $p->setDateCreated($datetime);
        
        // I need to flush() and create the new Post before I can vote it (below)
        $em->persist($p);
        $em->flush();
        
        // Automatically upvote my post
        $vote = new \AppBundle\Entity\VotePost();
        $vote->upvote($user, $p);
        $p->upvote();
        $p->addToVotes($vote);
        
        $em->persist($vote);

        $em->flush();
        
        return $p;
    }
}
