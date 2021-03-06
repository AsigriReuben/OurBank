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
/*
 *  create common view of funder details
 */
class Fundercommonview_IndexController extends Zend_Controller_Action
{

    public function init() 
    {
	//choose page title language
        $this->view->pageTitle = $this->view->translate("Funder");
        $globalsession = new App_Model_Users();
        $this->view->globalvalue = $globalsession->getSession();
        $this->view->username = $this->view->globalvalue[0]['username'];
//         if (($this->view->globalvalue[0]['id'] == 0)) {
//             $this->_redirect('index/logout');
//         }
        $this->view->adm = new App_Model_Adm();
        $this->view->funder = new Fundercommonview_Model_fundercommon();
    }

    public function indexAction() 
    {}

    // view and edit common funder details
    public function commonviewAction()
    {
        //Acl
/*        $access = new App_Model_Access();
        $checkaccess = $access->accessRights('Funder',$this->view->globalvalue[0]['name'],'viewfunderAction');
        if (($checkaccess != NULL)) {*/	

	//choose page title language
            $this->view->title = $this->view->translate("Edit Funder Common");
            $id=$this->_request->getParam('id');
            $this->view->id = $id;		
            $this->view->fundername=$this->view->funder->getfunder($id);
            $this->view->address = $this->view->adm->getModule("address",$id,"Funder");
            $this->view->contact = $this->view->adm->getModule("contact",$id,"Funder");

	// get module id
            $module=$this->view->funder->getmodule('Funder');
            foreach($module as $module_id){ }
            $this->view->mod_id=$module_id['parent'];
            $this->view->sub_id=$module_id['module_id'];
//         }else {$this->_redirect('index/index');}
    } 
}	
	


