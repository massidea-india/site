<?php
/**
 *  AdminController -> Includes tools for site admins
 *
 *  Copyright (c) <2009>, Pekka Piispanen <pekka.piispanen@cs.tamk.fi>
 *
 *  This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License 
 *  as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied  
 *  warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for  
 *  more details.
 * 
 *  You should have received a copy of the GNU General Public License along with this program; if not, write to the Free 
 *  Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 *  License text found in /license/
 */
 
/**
 *  AdminController - class
 *
 *  @package        controllers
 *  @author         Pekka Piispanen
 *  @copyright      2009 Pekka Piispanen
 *  @license        GPL v2
 *  @version        1.0
 */ 
class AdminController extends Oibs_Controller_CustomController 
{
	public function init()
	{	
        parent::init();
		
        // Get authentication
        $auth = Zend_Auth::getInstance();
        // If user has identity
        if ($auth->hasIdentity()) 
        {   
            if(!in_array("admin", $this->view->logged_user_roles))
            {
                $message = 'admin-no-permission';
                $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                $this->flash($message, $url);
            }
        }
        else
        {
            $message = 'admin-no-permission';
                 $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
            $this->flash($message, $url);
        }
		$this->view->title = "OIBS";
	}
	
	public function indexAction()
	{
		$this->view->title = "OIBS";
	}
    
    public function editrolesAction()
    {
        $params = $this->getRequest()->getParams();
        $username = $params['user'];		
        
        if($username != "")
        {
            $user = new Default_Model_User();
            if($user->usernameExists($username))
            {
                $this->view->editrole_username = $username;
                
                $id_usr = $user->getIdByUsername($username);
                
                $userProfiles = new Default_Model_UserProfiles();
                $user_roles = $userProfiles->getUserRoles($id_usr);
                $this->view->user_roles = $user_roles;
                
                $userRoles = new Default_Model_UserRoles();
                $roles = $userRoles->getRoles();
                $this->view->roles = $roles;
            }
            else
            {
                $message = 'admin-editrole-invalid-username';
                $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                $this->flash($message, $url);
            }
        }
        else
        {
            $message = 'admin-editrole-missing-username';
            $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
            $this->flash($message, $url);
        }
    }
    
    public function addroleAction()
    {
        $params = $this->getRequest()->getParams();
        $username = $params['user'];
        $role = $params['role'];
        
        if($username != "" && $role != "")
        {
            $user = new Default_Model_User();
            if($user->usernameExists($username))
            {
                $id_usr = $user->getIdByUsername($username);
                
                $userRoles = new Default_Model_UserRoles();
                $roles = $userRoles->getRoles();
                
                $userProfiles = new Default_Model_UserProfiles();
                $user_roles = $userProfiles->getUserRoles($id_usr);
                
                if(in_array($role, $roles))
                {
                    array_push($user_roles, $role);
                    
                    if($userProfiles->setUserRoles($id_usr, $user_roles))
                    {
                        $message = 'admin-addrole-successful';
                        $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                        $this->flash($message, $url);
                    }
                    else
                    {
                        $message = 'admin-addrole-not-successful';
                        $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                        $this->flash($message, $url);
                    }
                }
                else
                {
                    $message = 'admin-addrole-invalid-role';
                    $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                    $this->flash($message, $url);
                }
            }
            else
            {
                $message = 'admin-editrole-invalid-user';
                $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                $this->flash($message, $url);
            }
        }
        else
        {
            $message = 'admin-editrole-missing-username-role';
            $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
            $this->flash($message, $url);
        }
    }
    
    public function removeroleAction()
    {
        $params = $this->getRequest()->getParams();
        $username = $params['user'];
        $role = $params['role'];
        
        if($username != "" && $role != "")
        {
            $user = new Default_Model_User();
            if($user->usernameExists($username))
            {
                $id_usr = $user->getIdByUsername($username);
                
                $userProfiles = new Default_Model_UserProfiles();
                $user_roles = $userProfiles->getUserRoles($id_usr);
                
                if(in_array($role, $user_roles))
                {
                    foreach ($user_roles as $key => $value) 
                    {
                        if($value == $role)
                        {
                            unset($user_roles[$key]);
                        }
                    }
                    
                    $user_roles = array_values($user_roles);
                    
                    if($userProfiles->setUserRoles($id_usr, $user_roles))
                    {
                        $message = 'admin-removerole-successful';
                        $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                        $this->flash($message, $url);
                    }
                    else
                    {
                        $message = 'admin-removerole-not-successful';
                        $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                        $this->flash($message, $url);
                    }
                }
                else
                {
                    $message = 'admin-removerole-role-not-found';
                    $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                    $this->flash($message, $url);
                }
            }
            else
            {
                $message = 'admin-editrole-invalid-user';
                $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
                $this->flash($message, $url);
            }
        }
        else
        {
            $message = 'admin-editrole-missing-username-role';
            $url = $this->_urlHelper->url(array('controller' => 'msg', 
                                                'action' => 'index',
                                                'language' => $this->view->language),
                                          'lang_default', true); 
            $this->flash($message, $url);
        }
    }
    
    public function managerolesAction()
    {
        $params = $this->getRequest()->getParams();

        if(isset($params['managerolesaction']))
        {
            $this->view->managerolesaction = $params['managerolesaction'];
        }
        else
        {
            $this->view->managerolesaction = "";
        }
    }
    
    public function commentflagsAction()
    {
    	// Get all POST-parameters
    	$posts = $this->_request->getPost();
    	// Get models for the job
    	$flagmodel = new Default_Model_CommentFlags();
    	$commentmodel = new Default_Model_Comments();
    	$contentmodel = new Default_Model_Content();
    	
        if($posts)
    	{
			// Remove comment
    		if($posts['rm'] == "comment")
    		{
    		    foreach($posts as $key => $post)
    			{
    				if($key != "rm" && $key != "selectall")
    				{
    					$cmf_ids = $flagmodel->getFlagsByCommentId($key);
	    				foreach($cmf_ids as $cmf_id)
	    				{
	    					$flagmodel->removeFlag($cmf_id);
	    				}
    					$commentmodel->removeComment($key);
    				} 
    			}
    		}

    	    // Remove flags
    	    if($posts['rm'] == "flag")
    		{
    		    foreach($posts as $key => $post)
    			{
    				if($key != "rm" && $key != "selectall")
    				{
	    				$cmf_ids = $flagmodel->getFlagsByCommentId($key);
	    				foreach($cmf_ids as $cmf_id)
	    				{
	    					$flagmodel->removeFlag($cmf_id);
	    				}
    				}
    			}
    		}
    		
    	}
    	
    	$flagItems = $flagmodel->getAllFlags();
    	
    	// Awesome algorithm for counting how many flags each flagged comment has
    	$tmpCount = array();
    	foreach($flagItems as $flagItem)
    	{
    		$tmpCount[$flagItem['id_comment_cmf']]++;
    	}
    	arsort($tmpCount);
    	$data = array();
    	$count = 0;
    	
    	// Loop and re-arrange our variables
    	foreach($tmpCount as $cmt_id => $cmt_count)
    	{
    		$comment = $commentmodel->getById($cmt_id);
    		$comment = $comment['Data']['body_cmt'];
    		$content_id = $commentmodel->getContentIdsByCommentId($cmt_id);
    		$content_id = $content_id[0]['id_cnt_cmt'];
    		$content_url = $this->_urlHelper->url(array('controller' => 'view', 
														'action' => $content_id,
														'language' => $this->view->language),
														'lang_default', true); 
    		
    		$data[$count]['cnt_id'] = $content_id;
    		$data[$count]['cnt_title'] = $contentmodel->getContentHeaderByContentId($content_id);
    		$data[$count]['cnt_url'] = $content_url;
    		$data[$count]['cmt_id'] = $cmt_id;
    		$data[$count]['cmt_body'] = $comment;
    		$data[$count]['cmt_count'] = $cmt_count;
    		$count++;
    	}
    	
		// Go!
    	$this->view->comments = $data;
    }
    public function contentflagsAction()
    {
    	// Get all POST-parameters
    	$posts = $this->_request->getPost();
    	
    	// Get models for the job
    	$flagmodel = new Default_Model_ContentFlags();
    	$contentmodel = new Default_Model_Content();

        if($posts)
    	{
			// Remove content
    		if($posts['rm'] == "content")
    		{
    		    foreach($posts as $key => $post)
    			{
    				if($key != "rm" && $key != "selectall")
    				{
    					$cfl_ids = $flagmodel->getFlagsByContentId($key);
	    				foreach($cfl_ids as $cfl_id)
	    				{
	    					$flagmodel->removeFlag($cfl_id);
	    				}
    					$contentmodel->removeContent($key);
    				} 
    			}
    		}
    		
			// Unpublish content
    		if($posts['rm'] == "pubflag")
    		{
    		    foreach($posts as $key => $post)
    			{
    				if($key != "rm" && $key != "selectall")
    				{
    					$cfl_ids = $flagmodel->getFlagsByContentId($key);
	    				foreach($cfl_ids as $cfl_id)
	    				{
	    					$flagmodel->removeFlag($cfl_id);
	    				}
    					$contentmodel->publishContent($key,0);
    				} 
    			}
    		}

    	    // Remove flags
    	    if($posts['rm'] == "flag")
    		{
    		    foreach($posts as $key => $post)
    			{
    				if($key != "rm" && $key != "selectall")
    				{
    					$cfl_ids = $flagmodel->getFlagsByContentId($key);
	    				foreach($cfl_ids as $cfl_id)
	    				{
	    					$flagmodel->removeFlag($cfl_id);
	    				}
    				}
    			}
    		}
    		
    	}

    	// Awesome algorithm for counting how many flags each flagged content has
    	$flagItems = $flagmodel->getAllFlags();
    	$tmpCount = array();
    	foreach($flagItems as $flagItem)
    	{
    		$tmpCount[$flagItem['id_content_cfl']]++;
    	}
    	arsort($tmpCount);
    	$data = array();
    	$count = 0;
    	
    	// Loop and re-arrange our variables
    	foreach($tmpCount as $cnt_id => $cnt_count)
    	{
    		$content = $contentmodel->getById($cnt_id);
    		$data[$count]['id'] = $cnt_id;
        	$data[$count]['ctype'] = $content['Content']['Data']['id_cty_cnt'];
    		$data[$count]['title'] = $content['Content']['Data']['title_cnt'];
    		$data[$count]['lead'] = $content['Content']['Data']['lead_cnt'];
    		$data[$count]['body'] = $content['Content']['Data']['body_cnt'];
    		$data[$count]['count'] = $cnt_count;
    		$data[$count]['url'] = $this->_urlHelper->url(array('controller' => 'view', 
														'action' => $cnt_id,
														'language' => $this->view->language),
														'lang_default', true); 
    		$count++;
    	}
		// Go!
    	$this->view->contents = $data;
    }
}