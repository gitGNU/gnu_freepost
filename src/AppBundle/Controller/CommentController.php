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
use AppBundle\Entity\VoteComment;

class CommentController extends Controller
{
    public function upvoteAction($commentHashId)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Bad request data...
        if (is_null($commentHashId))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $comment = $em->getRepository('AppBundle:Comment')->findOneByHashId($commentHashId);
        $vote    = $em->getRepository('AppBundle:VoteComment')->findOneBy(array(
            'user'    => $user,
            'comment' => $comment
        ));
        
        // If user hasn't yet voted this comment
        if (is_null($vote))
        {
            $vote = new VoteComment();
            
            $vote->upvote($user, $comment);
            $comment->upvote();
            
            $em->persist($vote);
        } else
        
        // If user has already voted this comment
        {
            // Already voted earlier. Remove upvote
            if ($vote->upvoted())
            {
                $em->remove($vote);
                $comment->downvote();
            }
            // Switch from downvote to upvote
            else
            {
                $vote->setUpvoted();
                $comment->doubleUpvote();
                
                $em->persist($vote);
            }
        }
        
        $em->persist($comment);
        $em->flush();
        
        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    public function downvoteAction($commentHashId)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Bad request data...
        if (is_null($commentHashId))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $comment = $em->getRepository('AppBundle:Comment')->findOneByHashId($commentHashId);
        $vote    = $em->getRepository('AppBundle:VoteComment')->findOneBy(array(
            'user'    => $user,
            'comment' => $comment
        ));
        
        // If user hasn't yet voted this comment
        if (is_null($vote))
        {
            $vote = new VoteComment();
            
            $vote->downvote($user, $comment);
            $comment->downvote();
            
            $em->persist($vote);
        } else
        
        // If user has already voted this comment
        {
            // Already voted earlier. Remove downvote
            if ($vote->downvoted())
            {
                $em->remove($vote);
                $comment->upvote();
            }
            // Switch from upvote to downvote
            else
            {
                $vote->setDownvoted();
                $comment->doubleDownvote();
                
                $em->persist($vote);
            }
        }
        
        $em->persist($comment);
        $em->flush();
        
        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    /* Submit a new comment.
     * This is POST called when the form from submitAction() is sent.
     */
    public function submitNewAction($postHashId)
    {
        $em             = $em = $this->getDoctrine()->getManager();
        $request        = $this->getRequest();
        $commentRepo    = $em->getRepository('AppBundle:Comment');

        $user           = $this->getUser();

        // Data for the new comment
        $comment = (object) array(
            'post'          => $em->getRepository('AppBundle:Post')->findOneByHashId($postHashId),
            'parentComment' => $commentRepo->findOneByHashId($request->request->get('parentHashId')),
            'text'          => $request->request->get('text')
        );

        // If user is not signed in, or POST data is not valid
        if (is_null($user) || is_null($comment->post) || strlen($comment->text) < 1)
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        // Create the new comment
        $newComment = $commentRepo->submitNew(
            $comment->post,
            $comment->parentComment,
            $user,
            $comment->text
        );

        // Now we send back the formatted code for the new comment to be displayed
        if (is_null($comment->parentComment))
            $parentCommentId = 'root';
        else
            $parentCommentId = $comment->parentComment->getId();
        
        $templateData = array(
            'comments'       => array($parentCommentId => array($newComment)),
            'currentDate'    => new \DateTime(),
            'depth'          => 0,
            'op'             => $comment->post->getUser(),
            'parent'         => $comment->parentComment,
            'post'           => $comment->post
        );
        
        $html = $this->renderView('AppBundle:Default:Post/comment.html.twig', $templateData);
        
        return new JsonResponse(array(
            'done' => TRUE,
            'html' => $html
        ));
    }
    
    // Edit a user comment
    public function editAction($commentHashId)
    {
        $em             = $em = $this->getDoctrine()->getManager();
        $request        = $this->getRequest();

        $user           = $this->getUser();
        $comment        = $em->getRepository('AppBundle:Comment')->findOneBy(array(
                            'hashId'    => $commentHashId,
                            'user'      => $user
                          ));
        $commentText    = $request->request->get('text');
        
        // If user is not signed in, or POST data is not valid
        if (is_null($user) || is_null($comment) || is_null($commentText) || strlen($commentText) < 1)
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $comment->setText($commentText);
        
        $em->persist($comment);
        $em->flush();
        
        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
}


