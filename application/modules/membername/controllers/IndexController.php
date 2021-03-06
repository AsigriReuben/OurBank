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
class Membername_IndexController extends Zend_Controller_Action 
{
    public function init() 
    {
        $test = new DH_ClassInfo(APPLICATION_PATH . '/modules/activity/controllers/');
        $this->view->pageTitle=$this->view->translate('Individual member');
        $globalsession = new App_Model_Users();
        $this->view->globalvalue = $globalsession->getSession();
        $this->view->createdby = $this->view->globalvalue[0]['id'];
        $this->view->username = $this->view->globalvalue[0]['username'];
//         if (($this->view->globalvalue[0]['id'] == 0)) {
//         $this->_redirect('index/logout');
//         }
//      getting module id and submodule id for delete operation
        $individualcommon=new Individualmcommonview_Model_individualmcommonview();
        $module=$individualcommon->getmodule('Individual');
        foreach($module as $module_id){ }
        $this->view->mod_id=$module_id['parent'];
        $this->view->sub_id=$module_id['module_id'];
        $this->view->adm = new App_Model_Adm();
    }

    public function indexAction() 
    {}

//adding primary data for individual member...
    public function addmembernameAction()
    {
        //Acl
        //$access = new App_Model_Access();20/01/2012
        //$checkaccess = $access->accessRights('Individual',$this->view->globalvalue[0]['name'],'addmembernameAction');
        //if (($checkaccess != NULL)) {
        //add Action
        $this->view->title = $this->view->translate("Individual details");
        //load form for member add...
        $addForm = new Membername_Form_membername();
        $this->view->form=$addForm;
        $convertdate = new Creditline_Model_dateConvertor();
        $gender = $this->view->adm->viewRecord("gender","id","DESC");
        foreach($gender as $genderresult){
        $addForm->gender_id->addMultiOption($genderresult['id'],$genderresult['sex']);
        }
        $bankname = $this->view->adm->viewRecord("ob_bank","id","DESC");
        foreach($bankname as $bankresult){
        $addForm->office->addMultiOption($bankresult['id'],$bankresult['name']);
        }
        //insert the member details
        if ($this->_request->isPost() && $this->_request->getPost('Submit')) 
        {
            $formData = $this->_request->getPost();
            if($addForm->isValid($formData))
            {
                $subOffice = $this->_request->getParam('office');
                $member_dob=$this->_request->getParam('memberdateofbirth');
                $memberfirstname = $this->_request->getParam('memberfirstname');
                $membergender = $this->_request->getParam('gender_id');
                $mobile=$this->_request->getParam('mobile');
                $date=date("y/m/d H:i:s");
                $lastid = $this->view->adm->addRecord("ob_member",array('id' => '',
                                                'bank_id'=>$subOffice,
                                                'member_name' => $memberfirstname,
                                                'member_dateofbirth'=>$convertdate->phpmysqlformat($member_dob),
                                                'member_gender' => $membergender, 'mobile'=>$mobile,
                                                'created_date' =>$date,'created_by'=>$this->view->createdby));
            //create member code...
                $o=str_pad($subOffice,3,"0",STR_PAD_LEFT);
                $p = "04";
                $u=str_pad($lastid,5,"0",STR_PAD_LEFT);
                echo $membercode=$o.$p.$u;
                $this->view->adm->updateRecord("ob_member",$lastid,array('membercode'=>$membercode));
                $this->_redirect('/individualcommonview/index/commonview/id/'.$lastid);
            }
        }	
// 	} else {
//             $this->_redirect('index/index');
// 	}	

    }

// editing member details
    public function editmembernameAction()
    {
        //Acl
        //$access = new App_Model_Access();
        //$checkaccess = $access->accessRights('Individual',$this->view->globalvalue[0]['name'],'editmembernameAction');
        //if (($checkaccess != NULL)) {

        $member_id=$this->_getParam('id');
        $this->view->memberid=$member_id;
        $this->view->title = $this->view->translate("Edit member details"); 
        //load individual form for editing member
        $addForm = new Membername_Form_membername();
        $convertdate = new Creditline_Model_dateConvertor();
        $this->view->form=$addForm;
        $gender = $this->view->adm->viewRecord("gender","id","DESC");
        foreach($gender as $genderresult){
            $addForm->gender_id->addMultiOption($genderresult['id'],$genderresult['sex']);
        }
        $bankname = $this->view->adm->viewRecord("ob_bank","id","DESC");
        foreach($bankname as $bankresult){
            $addForm->office->addMultiOption($bankresult['id'],$bankresult['name']);
        }
        //set the member details value in the edit form...
        $edit_member = $this->view->adm->editRecord("ob_member",$member_id);
        foreach($edit_member as $editmembername)
        {
            $this->view->form->memberdateofbirth->setValue($convertdate->phpnormalformat($editmembername['member_dateofbirth']));
            $this->view->form->office->setValue($editmembername['bank_id']);
            $this->view->form->memberfirstname->setValue($editmembername['member_name']);
            $this->view->form->gender_id->setValue($editmembername['member_gender']);
            $this->view->form->mobile->setValue($editmembername['mobile']);
        }
        //update the member details
        if ($this->_request->isPost() && $this->_request->getPost('Update')) 
        {
            $formData = $this->_request->getPost();
            if($addForm->isValid($formData))
            {
            $olddate = $this->view->adm->editRecord("ob_member",$member_id);
            $this->view->adm->updateLog("ob_member_log",$olddate[0],$this->view->createdby);
            $office=$this->_request->getParam('office');
            $member_dob=$this->_request->getParam('memberdateofbirth');
            $memberfirstname = $this->_request->getParam('memberfirstname');
            $membergender = $this->_request->getParam('gender_id');
            $mobile=$this->_request->getParam('mobile');
            $date=date("y/m/d H:i:s");
            foreach($olddate as $olddate)
            $olddate['membercode'];
            $this->view->adm->updateRecord("ob_member",$member_id,array('id' => $member_id,
                            'bank_id'=>$office,
                                    'membercode'=>$olddate['membercode'],
                            'member_name' => $memberfirstname,
                            'member_dateofbirth'=>$convertdate->phpmysqlformat($member_dob),
                            'member_gender' => $membergender,'mobile'=>$mobile,
                            'created_date' =>$date,'created_by'=>$this->view->createdby));

            $this->_redirect('/individualcommonview/index/commonview/id/'.$member_id);
            }
        }
        // // 	} else {
        // //             $this->_redirect('index/index');}
    }

    public function viewmemberAction()
    {
    }

    //delete member with respective to micro modules
    public function deletememberAction()
    {
        //Acl
        //$access = new App_Model_Access();
        //$checkaccess = $access->accessRights('Individual',$this->view->globalvalue[0]['name'],'editmembernameAction');
        //if (($checkaccess != NULL)) {
        //delete action
    
        $id=$this->_request->getParam('id');	  
        $this->view->memberid=$id;
        $individualcommon=new Individualcommonview_Model_individualcommon;
        $member_name=$individualcommon->getmember($id);
        $this->view->membername=$member_name;
        $delform=new Membername_Form_Delete();
        $this->view->delete=$delform;
        
        if ($this->_request->isPost() && $this->_request->getPost('Submit'))
        {
            $formdata = $this->_request->getPost();
            //print_r($formdata);
            if($delform->isValid($formdata)) 
            { 
            $membermodel=new Membername_Model_membername();
            $account=$membermodel->findaccount($id);
            if(!$account){
            $this->view->adm->deletemember("ob_member",$id); // delete member and update log tables
            $this->view->adm->deletemember("ob_member_family",$id);// delete family details and update log tables
            $this->view->adm->deleteSubmodule("contact",$id,$this->view->sub_id);// delete contact details and update log tables
            $this->view->adm->deleteSubmodule("address",$id,$this->view->sub_id);// delete address details and update log tables
            $this->_redirect('/individual');
            }
            else
                { echo $this->view->translate("<font color=red>This member having active accounts</font>");
                }
            }
        }	
        // 	} else {
        //             $this->_redirect('index/index');
        // 	}
    }


}

