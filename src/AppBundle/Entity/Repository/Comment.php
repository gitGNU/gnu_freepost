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

class Comment extends EntityRepository
{
    /* Retrieve a list of hottest comments sorted by vote
     * $user is used to find $user vote for each post
     */
    public function findHot($post = NULL, $user = NULL)
    {
        if (is_null($post))
            return NULL;
        
        $em = $this->getEntityManager();

        return $em->createQuery(
            'SELECT c, u, v
            FROM AppBundle:Comment c
            JOIN c.user u
            LEFT JOIN c.votes v WITH v.user = :user
            WHERE c.post = :post
            ORDER BY c.vote DESC, c.created DESC'
        )
        ->setParameter('user', $user)
        ->setParameter('post', $post)
        ->setMaxResults(1000)
        ->getResult();
    }
    
    // Get user comments
    public function findComments($user = NULL)
    {
        $em = $this->getEntityManager();

        return $em->createQuery(
            'SELECT c
            FROM AppBundle:Comment c
            WHERE c.user = :user
            ORDER BY c.created DESC'
        )
        ->setParameter('user', $user)
        ->setMaxResults(100)
        ->getResult();
    }
    
    // Get replies to user comments
    public function findReplies($user = NULL)
    {
        $em = $this->getEntityManager();

        return $em->createQuery(
            'SELECT c
            FROM AppBundle:Comment c
            WHERE c.parentUser = :user AND c.user != :user
            ORDER BY c.read ASC, c.created DESC'
        )
        ->setParameter('user', $user)
        ->setMaxResults(100)
        ->getResult();
    }
    
    // Set replies as "read"
    public function setRepliesAsRead($user = NULL)
    {
        $em = $this->getEntityManager();

        $em->createQuery(
            'UPDATE AppBundle:Comment c
            SET c.read = 1
            WHERE c.parentUser = :user AND c.read = 0'
        )
        ->setParameter('user', $user)
        ->execute();
    }
    
    // Find how many replies are still unread
    public function findNumberOfUnreadReplies($user = NULL)
    {
        $em = $this->getEntityManager();

        return $em->createQuery(
            'SELECT COUNT(c)
            FROM AppBundle:Comment c
            WHERE c.parentUser = :user AND c.user != :user AND c.read = 0'
        )
        ->setParameter('user', $user)
        ->getSingleScalarResult();
    }
    
    public function submitNew(&$post, &$parentComment, &$user, &$text)
    {
        $em = $this->getEntityManager();
        
        $datetime = new \DateTime();

        $c = new \AppBundle\Entity\Comment();
        $c->setPost($post);
        $c->setParent($parentComment);
        $c->setParentUser(is_null($parentComment) ? $post->getUser() : $parentComment->getUser());
        $c->setUser($user);
        $c->setText($text);
        $c->setCreated($datetime);
        $c->setDateCreated($datetime);
        // If it's a reply to myself, don't mark it as "unread"
        ($c->getParentUser()->getId() == $user->getId()) && $c->setRead();
        
        $post->increaseCommentsCount();
        
        // I need to flush() and create the new Comment before I can vote it (below)
        $em->persist($c);
        $em->persist($post);
        $em->flush();
        
        // Automatically upvote my post
        $vote = new \AppBundle\Entity\VoteComment();
        $vote->upvote($user, $c);
        $c->upvote();
        $c->addToVotes($vote);
        
        $em->persist($vote);

        $em->flush();
        
        return $c;
    }
}
