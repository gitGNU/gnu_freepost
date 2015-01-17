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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;

use AppBundle\Entity\Community;
use AppBundle\Utility\EmailMessage;

class UserController extends Controller
{
    public function signinAction(Request $request)
    {
        $user = $this->getUser();
        
        if (!is_null($user))
            return $this->redirect($this->generateUrl('freepost_user', array('userName' => $user->getUsername())));
        
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render(
            'AppBundle:Default:Home/signin.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }
    
    public function signupAction()
    {
        $user = $this->getUser();
        
        // If user is signed in, don't create a new one! Must be signed out first!
        if (!is_null($user))
            return $this->redirect($this->generateUrl('freepost_user', array('userName' => $user->getUsername())));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        
        // A user name can NOT contain slashes!
        $username = str_replace(array('\\', '/'), '-', $username);
        
        $usernameExists = $userRepo->usernameExists($username);
        
        // Check input data
        if (strlen($username) < 5 || strlen($password) < 5 || $usernameExists)
            return $this->redirect($this->generateUrl('freepost_user_signin'));
        
        // Create the new user
        $newUser = $userRepo->createNew($username, $password);
        
        // Automatically signin the new user
        $token = new UsernamePasswordToken($newUser, null, 'secured_area', $newUser->getRoles());
        $this->get('security.token_storage')->setToken($token);
        
        return $this->redirect($this->generateUrl('freepost_user', array('userName' => $newUser->getUsername())));
    }
    
    // Load default "Communities" page
    public function communitiesAction($userName)
    {
        $user = $this->getUser();
        
        // Only allow to see my homepage (not that of other users)
        if (is_null($user) || strtolower($user->getUsername()) != strtolower($userName))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $em = $this->getDoctrine()->getManager();
        
        return $this->render(
            'AppBundle:Default:User/Page/communities.html.twig',
            array(
                'page'  => 'COMMUNITIES',
                'user'  => $user
            )
        );
    }
    
    // "My posts" page
    public function myPostsAction($userName)
    {
        $user = $this->getUser();
        
        // Only allow to see my homepage (not that of other users)
        if (is_null($user) || strtolower($user->getUsername()) != strtolower($userName))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $em = $this->getDoctrine()->getManager();
        
        $myPosts = $em->getRepository('AppBundle:Post')->findMyPosts($user);
        
        return $this->render(
            'AppBundle:Default:User/Page/myposts.html.twig',
            array(
                'page'  => 'MYPOSTS',
                'posts' => $myPosts,
                'user'  => $user
            )
        );
    }
    
    // "My comments" page
    public function commentsAction($userName)
    {
        $user = $this->getUser();
        
        // Only allow to see my homepage (not that of other users)
        if (is_null($user) || strtolower($user->getUsername()) != strtolower($userName))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $em = $this->getDoctrine()->getManager();
        
        $comments = $em->getRepository('AppBundle:Comment')->findComments($user);
        
        return $this->render(
            'AppBundle:Default:User/Page/mycomments.html.twig',
            array(
                'comments'  => $comments,
                'page'      => 'MYCOMMENTS',
                'user'      => $user
            )
        );
    }
    
    // "My comments replies" page
    public function repliesAction($userName)
    {
        $user = $this->getUser();
        
        // Only allow to see my homepage (not that of other users)
        if (is_null($user) || strtolower($user->getUsername()) != strtolower($userName))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $em          = $this->getDoctrine()->getManager();
        $commentRepo = $em->getRepository('AppBundle:Comment');
        
        // Load user replies
        $comments = $commentRepo->findReplies($user);
        
        // Set replies as "read"
        $commentRepo->setRepliesAsRead($user);
        
        return $this->render(
            'AppBundle:Default:User/Page/myreplies.html.twig',
            array(
                'comments'  => $comments,
                'page'      => 'REPLIES',
                'user'      => $user
            )
        );
    }
    
    // Return number of user unread replies
    public function unreadRepliesAction()
    {
        $user = $this->getUser();
        
        // Only allow to see my homepage (not that of other users)
        if (is_null($user))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $em = $this->getDoctrine()->getManager();
        
        $unreadReplies = $em->getRepository('AppBundle:Comment')->findNumberOfUnreadReplies($user);
        
        return new JsonResponse(array(
            'count' => $unreadReplies
        ));
    }
    
    // Load user preferences page
    public function preferencesAction($userName)
    {
        $user = $this->getUser();
        
        if (is_null($user) || strtolower($user->getUsername()) != strtolower($userName))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $em = $this->getDoctrine()->getManager();
        
        return $this->render(
            'AppBundle:Default:User/Page/preferences.html.twig',
            array(
                'page' => 'PREFERENCES',
                'user' => $user
            )
        );
    }
    
    // Check if username exists
    public function checkUsernameAction($userName)
    {
        $em = $this->getDoctrine()->getManager();
        
        $exists = $em->getRepository('AppBundle:User')->usernameExists($userName);
        
        return new JsonResponse(array(
            'exists' => $exists
        ));
    }
    
    // Ajax-load a community posts to show in user homepage
    public function readCommunityPostsAction($communityHashId)
    {
        $user = $this->getUser();
        
        // Only allow to see my homepage (not that of other users)
        if (is_null($user))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $em = $this->getDoctrine()->getManager();
        $communityRepo = $em->getRepository('AppBundle:Community');
        $postRepo = $em->getRepository('AppBundle:Post');
        
        $community = $communityRepo->findOneByHashId($communityHashId);
        $posts = $postRepo->findHot($community, $user);
        
        return $this->render(
            'AppBundle:Default:User/communityPosts.html.twig',
            array(
                'community'     => $community,
                'posts'         => $posts,
                'postSorting'   => 'HOT',
                'view'          => 'COMPACT'
            )
        );
    }
    
    // Ajax-load a list of communities to show in user homepage
    public function searchCommunitiesAction()
    {
        $user = $this->getUser();
        
        // Only allow to see my homepage (not that of other users)
        if (is_null($user))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $em = $this->getDoctrine()->getManager();
        
        $communities = $em->getRepository('AppBundle:Community')->findAll();
        
        return new JsonResponse(array(
            'html' => $this->renderView(
                'AppBundle:Default:User/searchCommunities.html.twig',
                array('communities' => $communities)
            )
        ));
    }
    
    // Check if $user is following $communityHashId
    public function followsCommunityAction($communityHashId)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'follows' => FALSE
            ));
        
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $communityRepo = $em->getRepository('AppBundle:Community');
        
        $community = $communityRepo->findOneByHashId($communityHashId);
        
        $follows = $userRepo->isFollowingCommunity($user, $community);
        
        return new JsonResponse(array(
            'follows' => $follows
        ));
    }
    
    /* Update a user name. A user CAN NOT CHANGE his username, but he
     * is allowed to change the name CaMeLcAsE.
     */
    public function updateDisplayNameAction()
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Retrieve POST data
        $displayName = $request->request->get('displayName');

        // Bad request data...
        if (is_null($displayName) || strtolower($user->getUsername()) != strtolower($displayName))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        // Update username
        $user->setUsername($displayName);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    /* Validate user email.
     * This is called when user insert a new email. This function sends a confirmation
     * code to the user new email for validation.
     */
    public function validateEmailAction()
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Retrieve POST data
        $email = $request->request->get('email');
        
        // Update user email
        $user->setEmailToConfirm($email);
        
        // Send email to $user with a code to confirm the email
        $this->get('mailer')->send(
            \Swift_Message::newInstance()
                ->setSubject('Email confirmation')
                ->setFrom(array('noreply@freepo.st' => 'freepost'))
                ->setTo(array($user->getEmailToConfirm() => $user->getUsername()))
                ->setBody(
                    $this->renderView(
                        'AppBundle:Default:Email/emailValidation.txt.twig',
                        array('user' => $user)
                    )
                )
        );

        $em->persist($user);
        $em->flush();

        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    // This is called when user follows the validation link sent by email
    public function validateEmailCodeAction($code)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Validate code
        if (   !is_null($user->getEmailToConfirm())
            && !is_null($user->getEmailToConfirmCode())
            && !is_null($code)
            && $user->getEmailToConfirmCode() == $code)
        {
            $user->setEmail($user->getEmailToConfirm());
            $user->deleteEmailToConfirm();
            $user->deleteEmailToConfirmCode();
        }

        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('freepost_homepage'));
    }
    
    // Delete user email
    public function deleteEmailAction()
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $em = $this->getDoctrine()->getManager();

        // Update user email
        $user->deleteEmail();
        $user->deleteEmailToConfirm();
        $user->deleteEmailToConfirmCode();

        $em->persist($user);
        $em->flush();

        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    /* Send an email with a code to reset user password
     * 
     * - The code is split in half. The second-half (codeTail) of the code is emailed
     *   to the user.
     * - Then the user is forwarded to changePasswordAction() which loads a <form/>
     *   with the first-half (codeHead) of the code.
     * - The user must copy-paste the codeTail into the form
     * - The form is then submitted for verification and the 2 halves combined to
     *   match the user property passwordResetCode
     */
    public function resetPasswordAction()
    {
        $user = $this->getUser();
        
        // User must be signed out before she can reset her password
        if (!is_null($user))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Retrieve POST data
        $email = $request->request->get('email');
        
        // Find the user associated with this email
        $user = $em->getRepository('AppBundle:User')->findOneByEmail($email);
        
        // The email is not valid
        if (is_null($user))
            return $this->redirect($this->generateUrl('freepost_user_signin'));
        
        // Update user with the secret token to be sent to her email
        $user->autosetPasswordResetCode();
        
        // Send email to $user with a code to reset her password
        $this->get('mailer')->send(
            \Swift_Message::newInstance()
                ->setSubject('Change account password')
                ->setFrom(array('noreply@freepo.st' => 'freepost'))
                ->setTo(array($user->getEmail() => $user->getUsername()))
                ->setBody(
                    $this->renderView(
                        'AppBundle:Default:Email/resetPassword.txt.twig',
                        array(
                            'codeTail' => $user->getPasswordResetCodeTail(),
                            'user'     => $user
                        )
                    )
                )
        );

        $em->persist($user);
        $em->flush();

        return $this->forward('AppBundle:User:changePassword', array(
            'codeHead' => $user->getPasswordResetCodeHead()
        ));
    }
    
    /* This function is forwarded to from resetPasswordAction().
     * This function does not have a route defined.
     */
    public function changePasswordAction($codeHead)
    {
        $user = $this->getUser();
        
        // User must be signed out before she can reset her password
        if (!is_null($user))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        
        // Show the form to input the new password
        return $this->render(
            'AppBundle:Default:User/resetPassword.html.twig',
            array(
                'codeHead'  => $codeHead,
                'user'      => $user
            )
        );
    }
    
    /* After the user has received the confirmation code via email, and she has
     * inserted it into the <form/> (see controller changePasswordAction()),
     * this function is finally called to update the user password in the database.
     */
    public function updatePasswordAction()
    {
        $user = $this->getUser();
        
        // User must be signed out before she can reset her password
        if (!is_null($user))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        
        // Retrieve POST data
        $password = $request->request->get('password');
        $code = (object) array(
            'head' => $request->request->get('head'),
            'tail' => $request->request->get('tail')
        );
        
        // Validate <form/> input data
        if (is_null($password) || strlen($password) < 5 || is_null($code->head) || is_null($code->tail))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        /* Combine the confirmation code from (head + tail).
         * - head has been added to the <form/> in the user browser
         * - tail has been email to the user, and manually entered by user
         */
        $passwordResetCode = $code->head . $code->tail;
        
        // Find the user who requested the new password
        $user = $em->getRepository('AppBundle:User')->findOneByPasswordResetCode($passwordResetCode);
        
        // The code is not valid
        if (is_null($user))
            return $this->redirect($this->generateUrl('freepost_homepage'));
        
        // Update user password
        $user->setPassword($password);
        $user->deletePasswordResetCode();
        
        $em->persist($user);
        $em->flush();
        
        return $this->redirect($this->generateUrl('freepost_user_signin'));
    }
    
    /* Update feed format. "feed format" is the way posts are displayed to the user,
     * either "title only" or "title + post content"
     */
    public function updateFeedFormatAction($feedFormat)
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // Bad request data...
        if (is_null($feedFormat))
            return new JsonResponse(array(
                'done' => FALSE
            ));
        
        // Update user
        $user->setFeedFormat($feedFormat);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(array(
            'done' => TRUE
        ));
    }
    
    public function updatePictureAction()
    {
        $user = $this->getUser();
        
        if (is_null($user))
            return $this->render(
                'AppBundle:Default:Etc/postMessage.html.twig',
                array('message' => json_encode(array(
                    'action'    => 'updateUserPicture',
                    'status'    => 'error'
                )))
            );
        
        $request = $this->getRequest();
        $asset = $this->get('freepost.asset');
        $em = $this->getDoctrine()->getManager();

        // Retrieve POST data
        $userPicture = $request->files->get('pictureFile');

        // Save the new picture, or reset to default if none is specified
        if (is_null($userPicture))
            $asset->deleteUserPicture($user);
        else
            $asset->updateUserPicture($user, $userPicture);

        return $this->render(
            'AppBundle:Default:Etc/postMessage.html.twig',
            array('message' => json_encode(array(
                'action'    => 'updateUserPicture',
                'status'    => 'done'
            )))
        );
    }
}


