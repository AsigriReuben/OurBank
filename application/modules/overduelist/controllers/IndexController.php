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

class Overduelist_IndexController extends Zend_Controller_Action
{
    function init() { 
        $this->view->pageTitle = "Overdue list";
	$sessionName = new Zend_Session_Namespace('ourbank');
	$userid=$this->view->createdby = $sessionName->primaryuserid;
	$login=new App_Model_Users();
$this->view->type = "others";

	$loginname=$login->username($userid);
	foreach($loginname as $loginname) {
	$this->view->username=$loginname['username'];
    }
		$this->view->adm = new App_Model_Adm();

}
    function indexAction() {
       	 	$searchForm = new Overduelist_Form_Search();
        	$this->view->form = $searchForm;
		$bankdetail = new Cbtransaction_Model_Cbtransaction();

       		$institution = $this->view->adm->viewRecord("ob_institution","id","DESC");

			foreach($institution as $institution) {
				$searchForm->bank_id->addMultiOption($institution['id'],$institution['name']);
			}
$loanofficer = $this->view->adm->viewRecord("ourbank_user","id","DESC");
			foreach($loanofficer as $loanofficer){
				$searchForm->loanofficer ->addMultiOption($loanofficer['id'],$loanofficer['name']);
			}
         	if ($this->_request->isPost() && $this->_request->getPost('Search')) {
	   
            	if ($this->_request->isPost()) {
                 $officer = $this->_request->getParam('loanofficer');
                 $bank = $this->_request->getParam('bank_id');
                 $date = $this->_request->getParam('dater');


	 $loandue = new Overduelist_Model_Overduelist();
			$arrayLoan=$loandue->search($bank,$date,$officer);
			$this->view->loanView = $arrayLoan;	


    }}}

   
}
