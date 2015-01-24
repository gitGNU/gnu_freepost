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

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Community;
use AppBundle\Entity\VotePost;

class CommunityController extends Controller
{
    // Load default community page
    public function postsAction($communityName, $sort = '')
    {
        $em            = $this->getDoctrine()->getManager();
        $communityRepo = $em->getRepository('AppBundle:Community');
        $postRepo      = $em->getRepository('AppBundle:Post');
        
        $user          = $this->getUser();
        $asset         = $this->get('freepost.asset');
        $community     = $communityRepo->findOneByName($communityName);
        
        // This community doesn't exist
        if (is_null($community))
            // If user is not logged in, redirect to home page
            if (is_null($user))
                return $this->redirect($this->generateUrl('freepost_homepage'));
            // Otherwhise create the new community
            else
            {
                $community = $communityRepo->create($communityName, $user);
                
                // Set the default picture for this new community
                $asset->resetCommunityPicture($community);
            }
        
        $sort = strtoupper($sort);
        
        switch ($sort)
        {
            case 'NEW':
                $posts = $postRepo->findNew($community, $user);
                break;
            default:
                $sort = 'HOT';
                $posts = $postRepo->findHot($community, $user);
                break;
        }
        
        return $this->render(
            'AppBundle:Default:Community/Page/posts.html.twig',
            array(
                'community'     => $community,
                'posts'         => $posts,
                'postSorting'   => $sort,
                'view'          => 'COMPACT'
            )
        );
    }
    
    // Load community page, sort by HOT
    public function hotPostsAction($communityName)
    {
        return $this->forward('AppBundle:Community:posts', array(
            'communityName' => $communityName,
            'sort'          => 'HOT',
        ));
    }
    
    // Load community page, sort by NEW
    public function newPostsAction($communityName)
    {
        return $this->forward('AppBundle:Community:posts', array(
            'communityName' => $communityName,
            'sort'          => 'NEW',
        ));
    }
    
    // Create a new community
    public function createAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Retrieve POST data
        $communityName = $request->request->get('communityName');
        
        /* A community name can NOT contain slashes!
         * NB: I need this line here because I'm redirecting to "freepost_community" route
         */
        $communityName = str_replace(array('\\', '/'), '-', $communityName);
        
        /* Redirect to the default community page.
         * If the community doesn't exist, it's automatically created in
         * 'AppBundle:Community:posts'. This is the default behaviour as
         * if user visited the URL freepo.st/community/[communityName]
         */
        return $this->redirect($this->generateUrl('freepost_community', array(
            'communityName' => $communityName
        )));
    }
    
    // Load community about page
    public function aboutAction($communityName)
    {
        $em = $this->getDoctrine()->getManager();
        
        $community = $em->getRepository('AppBundle:Community')->findOneByName($communityName);
        
        return $this->render(
            'AppBundle:Default:Community/Page/about.html.twig',
            array('community' => $community)
        );
    }
    
    // Load community preferences page
    public function preferencesAction($communityName)
    {
        $em          = $this->getDoctrine()->getManager();
        
        $user        = $this->getUser();
        $community   = $em->getRepository('AppBundle:Community')->findOneByName($communityName);
        
        $isFollowing = $em->getRepository('AppBundle:User')->isFollowingCommunity($user, $community);
        
        return $this->render(
            'AppBundle:Default:Community/Page/preferences.html.twig',
            array(
                'community'   => $community,
                'isFollowing' => $isFollowing
            )
        );
    }
    
    /* Update a community name. A user CAN NOT CHANGE the community name, but he
     * is allowed to change the name CaMeLcAsE.
     */
    public function updateDisplayNameAction($communityHashId)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Retrieve POST data
        $communityName = $request->request->get('displayName');

        // The community
        $community = $em->getRepository('AppBundle:Community')->findOneByHashId($communityHashId);

        // Bad request data...
        if (is_null($communityName) || is_null($community) || strtolower($community->getName()) != strtolower($communityName))
            return new JsonResponse(array(
                'done' => FALSE
            ));

        // Update community name
        $community->setName($communityName);

        $em->persist($community);
        $em->flush();

        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    // Update a community description. This is basically the "About" page
    public function updateDescriptionAction($communityHashId)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Retrieve POST data
        $communityDescription = $request->request->get('description');

        // The community
        $community = $em->getRepository('AppBundle:Community')->findOneByHashId($communityHashId);

        // Bad request data...
        if (is_null($communityDescription) || is_null($community))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        // Update community description
        $community->setDescription($communityDescription);

        $em->persist($community);
        $em->flush();

        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    public function updatePictureAction($communityHashId)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return $this->render(
                'AppBundle:Default:Etc/postMessage.html.twig',
                array('message' => json_encode(array(
                    'action'    => 'updateCommunityPicture',
                    'status'    => 'error'
                )))
            );
        
        $request    = $this->getRequest();
        $asset      = $this->get('freepost.asset');
        $em         = $this->getDoctrine()->getManager();

        // Retrieve POST data
        $communityPicture = $request->files->get('pictureFile');

        // The community
        $community = $em->getRepository('AppBundle:Community')->findOneByHashId($communityHashId);

        // Bad request data...
        if (is_null($community))
            return $this->render(
                'AppBundle:Default:Etc/postMessage.html.twig',
                array('message' => json_encode(array(
                    'action'    => 'updateCommunityPicture',
                    'status'    => 'error'
                )))
            );

        // Save the new picture
        if (!is_null($communityPicture))
            $asset->updateCommunityPicture($community, $communityPicture);

        return $this->render(
            'AppBundle:Default:Etc/postMessage.html.twig',
            array('message' => json_encode(array(
                'action'    => 'updateCommunityPicture',
                'status'    => 'done'
            )))
        );
    }
    
    /* Submit a new post.
     * This is POST called when the form from submitAction() is sent.
     */
    public function submitNewPostAction($communityHashId)
    {
        $user = $this->getUser();
        
        // If user is not signed in, user can't submit anything...
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));

        $em = $this->getDoctrine();
        $request = $this->getRequest();
        
        // Data for the new post
        $post = (object) array(
            'community' => $em->getRepository('AppBundle:Community')->findOneByHashId($communityHashId),
            'title'     => $request->request->get('title'),
            'text'      => $request->request->get('text')
        );

        // If POST data is not valid
        if (is_null($post->community) || is_null($post->title) || strlen($post->title) < 1)
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        // Create the new post
        $newPost = $em->getRepository('AppBundle:Post')->submitNew(
            $post->community,
            $user,
            $post->title,
            $post->text
        );
        
        // Send back the formatted code of the new post to be display
        $html = $this->renderView(
            'AppBundle:Default:Etc/PostsList/post.html.twig',
            array(
                'community'     => $post->community,
                'currentDate'   => new \DateTime(),
                'aPost'         => $newPost
            )
        );
        
        return new JsonResponse(array(
            'done' => TRUE,
            'html' => $html
        ));
    }
    
    // Search communities
    public function searchAction($communityName)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array());
        
        $em = $this->getDoctrine()->getManager();
        
        $communities = $em->getRepository('AppBundle:Community')->search($communityName);
        
        return new JsonResponse(array(
            'html' => $this->renderView(
                'AppBundle:Default:Etc/communitiesSearchResults.html.twig',
                array('communities' => $communities)
            )
        ));
    }
    
    // Follow this community
    public function followAction($communityHashId)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $request        = $this->getRequest();
        $em             = $this->getDoctrine()->getManager();
        $userRepo       = $em->getRepository('AppBundle:User');
        $communityRepo  = $em->getRepository('AppBundle:Community');
        
        // The community
        $community      = $communityRepo->findOneByHashId($communityHashId);

        // Bad request data...
        if (is_null($community))
            return new JsonResponse(array(
                'done' => FALSE
            ));

        // Already following this community
        if ($userRepo->isFollowingCommunity($user, $community))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $communityRepo->follow($user, $community);
        
        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    // Stop following this community
    public function stopFollowingAction($communityHashId)
    {
        $user = $this->getUser();
        
        // Must be signed in to stop following a community
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $em             = $this->getDoctrine()->getManager();
        $communityRepo  = $em->getRepository('AppBundle:Community');
        $community      = $communityRepo->findOneByHashId($communityHashId);

        $communityRepo->stopFollowing($user, $community);
        
        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
}


