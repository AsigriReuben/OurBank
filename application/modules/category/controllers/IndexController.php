<?php
/*
############################################################################
#  This file is part of OurBank.
############################################################################
#  OurBank is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as
#  published by the Free Software Foundation, either version 3 of the
#  License, or (at your option) any later version.
############################################################################
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
############################################################################
#  You should have received a copy of the GNU Affero General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
############################################################################
*/
?>



<?php
class Category_IndexController extends Zend_Controller_Action
{
	public function init() 
	{
		$this->view->pageTitle='Category';
        $globalsession = new App_Model_Users();
        $this->view->globalvalue = $globalsession->getSession();
		$this->view->createdby = $this->view->globalvalue[0]['id'];
//		$this->view->username = $this->view->globalvalue[0]['username'];
//        if (($this->view->globalvalue[0]['id'] == 0)) {
//            $this->_redirect('index/logout');
//        }
		$this->view->adm = new App_Model_Adm();   	
	}

	public function indexAction() 
	{
               
		$this->view->title = "Category";
//		$storage = new Zend_Auth_Storage_Session();
//		$data = $storage->read();
//		if(!$data){
//			$this->_redirect('index/login');
//		}


//calling the category model
		$category = new Category_Model_Category();
		$categorydetails=$category->getCategoryinformation();
		$searchForm = new Category_Form_Search();
		$this->view->form = $searchForm;
		$result = $category->getCategoryDetails();
//pagination  -   for set of 5 at a time
		$page = $this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);
		$paginator->setItemCountPerPage(5);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator = $paginator;
//search action
		if ($this->_request->isPost() && $this->_request->getPost('Search')){
			if ($this->_request->isPost()){
				if ($searchForm->isValid($this->_request->getPost())){
					$result = $category->SearchCategory($searchForm->getValues());
                                         $this->view->errormsg="Record not found";
//pagination for the search result
					$page = $this->_getParam('page',1);
					$paginator = Zend_Paginator::factory($result);
                                         if(!$paginator){
                                            $this->view->errormsg="Record not found";
            }
					$paginator->setItemCountPerPage(5);
					$paginator->setCurrentPageNumber($page);
					$this->view->paginator = $paginator;
				}
			}
		}
	}
	public function categoryaddAction() 
	{
//calling the form		
		$categoryForm = new Category_Form_category();
		$this->view->form = $categoryForm;
//submit action
		if ($this->_request->isPost() && $this->_request->getPost('Submit')) {
				$formData = $this->_request->getPost();
				if ($this->_request->isPost()) {
					if ($categoryForm->isValid($formData)) {  
//adding the datas to the table
					$id = $this->view->adm->addRecord("ourbank_category",$categoryForm->getValues());
						$this->_redirect('/category/index/categoryview/id/'.$id);
				}
			}
		}
		
	}
	
	public function categoryeditAction() 
	{
//calling the form
			$categoryForm = new Category_Form_category();
			$this->view->form = $categoryForm;
//getting the id
			$id=$this->_getParam('id');
			$this->view->id = $id;
//calling the category model
			$category = new Category_Model_Category;
			$categorydetails = $category->getCategory($id);
			$categoryForm->populate($categorydetails[0]);
//update action
			if ($this->_request->isPost() && $this->_request->getPost('Update')) {  
				$id = $this->_getParam('id');
				$formData = $this->_request->getPost();
				//print_r($formData);
				if ($categoryForm->isValid($formData)) { 
//editing the record
					$previousdata = $this->view->adm->editRecord("ourbank_category",$id);
//updating the previous data
					$this->view->adm->updateLog("ourbank_category_log",$previousdata[0],1);
					//update 					
					$this->view->adm->updateRecord("ourbank_category",$id,$categoryForm->getValues());
					$this->_redirect('category/index/');
				}
 			}
    }		
	public function categoryviewAction() 
	{
		//Acl
        $id=$this->_request->getParam('id');
//calling the category model			
			$category = new Category_Model_Category;
			$this->view->categorydetails=$category->getCategory($id);
	}
	public function categorydeleteAction() 
	{
//calling the delete form
		$delform = new Category_Form_Delete();
		$this->view->deleteform = $delform;
//getting the id
		$id = $this->_getParam('id');
		$this->view->id = $id;
//calling the category model
		$category = new Category_Model_Category;
		$this->view->categorydetails=$category->getCategory($id);
// 				$this->view->sectorname =  $Sectordetails['name'];
// 			}
//delete action
		if($this->_request->isPost() && $this->_request->getPost('Delete')) {
		$formdata = $this->_request->getPost();
				if ($delform->isValid($formdata)) { 
//deleting  record
       $redirect = $this->view->adm->deleteRecord("ourbank_category",$id);
					//update
            $this->_redirect('/category');
				}
			}
		
	}
}
