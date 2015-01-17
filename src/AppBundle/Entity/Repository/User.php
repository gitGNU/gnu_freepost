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

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class User extends EntityRepository implements UserProviderInterface
{

    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin AcmeUserBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }
    
    public function isFollowingCommunity($user, $community)
    {
        if (is_null($user) || is_null($community))
            return FALSE;
        
        return $user->getCommunities()->contains($community);
    }
    
    public function usernameExists($username)
    {
        $em = $this->getEntityManager();

        $count = $em->createQuery(
            'SELECT COUNT(u)
            FROM AppBundle:User u
            WHERE u.username = :username'
        )
        ->setParameter('username', $username)
        ->getSingleScalarResult();
        
        return $count > 0;
    }
    
    public function createNew($username, $password)
    {
        $em = $this->getEntityManager();

        $newUser = new \AppBundle\Entity\User();
        $newUser->setUsername($username);
        $newUser->setPassword($password);

        /* Add these communities as default for new users
         * We assume that these exist by default. We do not check them here!
         */
        $newUser->addCommunity($em->getRepository('AppBundle:Community')->findOneByName('freepost'));
        
        $em->persist($newUser);
        $em->flush();

        return $newUser;
    }
}







