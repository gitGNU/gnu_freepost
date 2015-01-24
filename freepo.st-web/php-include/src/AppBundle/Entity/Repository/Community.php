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

class Community extends EntityRepository
{
    public function search($communityName)
    {
        $em = $this->getEntityManager();

        return $em->createQuery(
            'SELECT c
            FROM AppBundle:Community c
            WHERE c.name LIKE :communityName
            ORDER BY c.name'
        )
        ->setMaxResults(10)
        ->setParameter('communityName', '%' . $communityName . '%')
        ->getResult();
    }
    
    public function create($communityName, $user)
    {
        // Allow new community only if $user is logged in
        if (is_null($user))
            return NULL;
        
        $em = $this->getEntityManager();
        $datetime = new \DateTime();

        $c = new \AppBundle\Entity\Community();
        $c->setName($communityName);
        
        // Add the user to this community members
        $user->addCommunity($c);

        $em->persist($c);
        $em->persist($user);

        $em->flush();
        
        return $c;
    }
    
    // $user follow $community
    public function follow($user, $community)
    {
        $em = $this->getEntityManager();
        
        $user->addCommunity($community);

        $em->persist($user);
        $em->persist($community);
        $em->flush();
    }
    
    // $user stop following $community
    public function stopFollowing($user, $community)
    {
        $em = $this->getEntityManager();
        
        $user->removeCommunity($community);

        $em->persist($user);
        $em->persist($community);
        $em->flush();
    }
}



