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
class Account_IndexController extends Zend_Controller_Action {
    public function init() {
       $this->view->pageTitle='Account';
	$storage = new Zend_Auth_Storage_Session();
	$data = $storage->read();
	if(!$data){
		$this->_redirect('index/login');
	}
        $sessionName = new Zend_Session_Namespace('ourbank');
        $userid=$this->view->createdby = $sessionName->primaryuserid;
        $login=new App_Model_Users();
        $loginname=$login->username($userid);
        foreach($loginname as $loginname) {
           $this->view->username=$loginname['username'];
        }
    }

    public function indexAction() {
	$storage = new Zend_Auth_Storage_Session();
	$data = $storage->read();
	if(!$data){
		$this->_redirect('index/login');
	}
	$this->view->title = "Accounting";
	$accountsForm = new Account_Form_Accounts();
	$this->view->form = $accountsForm;

	if ($this->_request->isPost() && $this->_request->getPost('Submit')) {
		$formData = $this->_request->getPost();
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($accountsForm->isValid($formData)) {
 				$accounts = new Account_Model_Accounts();
				$membercode=$this->_request->getParam('membercode');
				$this->view->result = $accounts->searchMembercode($membercode);
                                if($this->view->result) { 
                                    if(COUNT($this->view->result)=='1') {
                                        foreach($this->view->result as $memberid) {
                                            $membercodeid=$memberid->code;
                                            $memberid=$memberid->id;
                                        }
                                      $this->_redirect("/account/index/loans/memberId/$memberid/membercode/$membercodeid");
                                    } else {
				        $this->view->result = $accounts->searchMembercode($membercode);
                                    }
                                } else { 
                                    echo "Enter a valid member code or name";
                                }
			}
		}
	}
    }

	public function loansAction() {
                $storage = new Zend_Auth_Storage_Session();
                $data = $storage->read();
                if(!$data){
                        $this->_redirect('index/login');
                }
		$memberId=$this->_request->getParam('memberId');
		$this->view->memberId = $memberId;
		$membercodeid=$this->_request->getParam('membercode');
		$this->view->membercodeid = $membercodeid;

                $convertdate = new Creditline_Model_dateConvertor();
		$productDetails = new Account_Model_Accounts();
                $membertypeId=$this->view->membertype_ID=substr($membercodeid, 4, -5);

		if($membertypeId==3) {
			$fetchGroupDetails= $productDetails->fetchGroupDetails($memberId);
			foreach($fetchGroupDetails as $fetchGroupDetails1) {
			     $branchID=$this->view->branch=$fetchGroupDetails1->name;
			     $memberCode=$this->view->membercode=$fetchGroupDetails1->groupcode;
			     $groupName=$this->view->groupname=$fetchGroupDetails1->group_name;
			}
			$this->view->label='Group name';
			$this->view->pageTitle='Group account';
		}

		if($membertypeId == 4) {
			$fetchmemberName= $productDetails->fetchMemberDetails($memberId);
			foreach($fetchmemberName as $fetchmemberName1) {
			     $branchID=$this->view->branch=$fetchmemberName1->name;
			     $memberCode=$this->view->membercode=$fetchmemberName1->membercode;
			     $memberName=$this->view->memberfirstname=$fetchmemberName1->member_name;
			}
			$this->view->label='Name'; 
			$this->view->pageTitle='Individual account';
		} 

		$accountsSearchFetch = $productDetails->accountsSearch($memberId);
		$this->view->account = $accountsSearchFetch;

		$app = $this->view->baseUrl();
		$loan = new Account_Form_Loan($app);
		$this->view->loan=$loan;


		$groupMembers= $productDetails->groupMembers($memberId);
		$this->view->groupMembers = $groupMembers;
		foreach($groupMembers as $groupMembers1) {
			$loan->members->addMultiOption($groupMembers1->id,$groupMembers1->member_name);
		}

		$activityName= $productDetails->activityname();
		$this->view->activityname = $activityName;
		foreach($activityName as $activityName1) {
			$loan->activity_id->addMultiOption($activityName1->id,$activityName1->name);
		}

		$interestcategory= $productDetails->interestcategory();
		$this->view->interestcategory = $interestcategory;
		foreach($interestcategory as $interestcategory1) {
			$loan->interest_category->addMultiOption($interestcategory1->id,$interestcategory1->name);
		}


		for($i=1;$i<=36;$i++)  {
			$loan->installments->addMultiOption($i,$i);
		}

		if ($this->_request->isPost() && $this->_request->getPost('Submit')) {
		  $formData = $this->_request->getPost();
		  if ($this->_request->isPost()) {
                      if($membertypeId == 4) { 
                         $loan->members->setRequired(false); 
                      }
		      if ($loan->isValid($formData)) {
		          $creditline_id=$this->view->creditline_id = $this->_request->getParam('creditline_id');
			  $loanAccountdate=$this->view->loanAccountdate = $convertdate->phpmysqlformat($this->_request->getParam('loanAccountdate'));
			  $amount=$this->view->amount = $this->_request->getParam('amount');
                	  $interest=$this->view->interest = $this->_request->getParam('interest');
                	  $ballet=$this->view->ballet = $this->_request->getParam('ballet');
			  $installments=$this->view->installments = $this->_request->getParam('installments');
                          $activityId=$this->view->activityId = $this->_request->getParam('activity_id');
			  $creditlineamount=$this->view->creditlineamount=$this->_request->getParam('cteditlineamount');
			  $fee=$this->_request->getParam('fee');
                          if($fee) {
                             $feeamount=$fee;
                          } else {
                             $feeamount='0';
                          }
                          $currentdate=date("Y-m-d");
                          $sessionName = new Zend_Session_Namespace('ourbank');
                          $userId = $sessionName->primaryuserid;

                           $activitydate= $productDetails->activitydate($activityId);
                           foreach($activitydate as $activitydate1) {
                                $activitycredateddate=substr($activitydate1['created_date'], 0, -9);
                           }


                           $activitynormalcreateddate=$convertdate->phpnormalformat($activitycredateddate);
                           $currentnormaldate=$convertdate->phpnormalformat($currentdate);

                          if($activitycredateddate > $loanAccountdate) {
                             $this->view->activitydate = "Date must be greater than or equal to activity created date - '$activitynormalcreateddate'";
                          } elseif($currentdate < $loanAccountdate) {
                             $this->view->currentloandate = "Date must be lesser than or equal to current date - '$currentnormaldate'";
                          }  elseif($amount > $creditlineamount) {
                             $this->view->creditlineremainingamount = "Amount must be less than or equal to creditline remaining amount - '$creditlineamount'";
                          }else{
		              $accounts = new Account_Model_Accounts();
		              $accounts->insertAccounts();
		              $account_id = Zend_Db_Table::getDefaultAdapter()->lastInsertId('ob_accounts','account_id');

			      if($membertypeId==4) {
                                $branchidfetch = $accounts->fetchbranchid($memberId);
                                foreach($branchidfetch as $branchidfetch) {
                                    $branchId = $this->view->branchId = $branchidfetch->bank_id;
                                }
                              } else {
                                $branchidgroupfetch = $accounts->fetchgroupbranchid($memberId);
                                foreach($branchidgroupfetch as $branchidgroupfetch) {
                                    $branchId = $this->view->branchId = $branchidgroupfetch->bank_id;
                                }
                              }

                              $productcode="L";
			      if($membertypeId==3) {
			      $IndividualNumber=3; 
			      } else { 
			         $IndividualNumber=4;
			      }
			      $b=str_pad($branchId,3,"0",STR_PAD_LEFT); 
			      $t=str_pad($IndividualNumber,2,"0",STR_PAD_LEFT);
			      $p=str_pad($productcode,3,"0",STR_PAD_LEFT);
			      $a=str_pad($account_id,6,"0",STR_PAD_LEFT);
			      $date=date("y/m/d H:i:s");
			      $accountNumber=$b.$t.$p.$a;

			      $accounts->UpDateAccounts($account_id,$accountNumber,$memberId,$activityId,$IndividualNumber,$creditline_id,$date,$userId);

			      $accounts->Addloanaccounts($account_id,$loanAccountdate,$amount,$creditline_id,$interest,$installments,$date,$ballet,$feeamount,$userId);

			      if($membertypeId==3){
				$member= $this->_request->getParam('members');
				$accounts->Addgroupmembers($member,$account_id,$activityId,$userId,$date);
			      }
			      $this->_redirect("/account/index/accountsview/accountNumber/$accountNumber");
                          }
		      }
	           }
		}
	}

	function accountsviewAction() {
                $storage = new Zend_Auth_Storage_Session();
                $data = $storage->read();
                if(!$data){
                   $this->_redirect('index/login');
                }
                $this->view->pageTitle='Account';
		$accountNumber =$this->view->accountNumber= $this->_request->getParam('accountNumber');
	}




	function interestcategoryAction() {
		$this->_helper->layout()->disableLayout();
		$app = $this->view->baseUrl();
		$loan = new Account_Form_Loan($app);
		$this->view->loan=$loan;
		$productDetails = new Account_Model_Accounts();
		$interestcategory = $this->_request->getParam('interestcategory');
		$fetchcreditlineDetails= $productDetails->fetchcreditline($interestcategory);
		foreach($fetchcreditlineDetails as $fetchcreditlineDetails) {
                        $this->view->loan->fee->setValue($fetchcreditlineDetails['fee']);
			$this->view->creditline_id = $loan->creditline_id->addMultiOption($fetchcreditlineDetails['id'],
			$fetchcreditlineDetails['name']."-".$fetchcreditlineDetails['portfolio']);
		}
	}
	function interestsrangesAction() {
		$this->_helper->layout()->disableLayout();
		$creditlineId = $this->_request->getParam('creditline_id');
		$interestcategory= $this->_request->getParam('interest_category');
		$productDetails = new Account_Model_Accounts();
		$interestsFetch= $this->view->interests= $productDetails->interestsranges($creditlineId,$interestcategory);
                if($interestsFetch) { 
                    $this->view->interests= $productDetails->interestsranges($creditlineId,$interestcategory);
                } else {
                    echo "Loan ranges does not exisist";
                }
	}


	function creditlineAction() {
		$this->_helper->layout()->disableLayout();
		$creditlineId=$this->view->creditline = $this->_request->getParam('creditline_id');
		$app = $this->view->baseUrl();
		$loan = new Account_Form_Loan($app);
		$this->view->loan=$loan;
		$productDetails = new Account_Model_Accounts();
		$creditlineamountFetch= $this->view->creditlineamount= $productDetails->creditlineamount($creditlineId);
                foreach($creditlineamountFetch as $creditlineamountFetch) {
                    $totalloansamount=$creditlineamountFetch->totalloansamount;
                }

		$creditlineamount= $this->view->creditline= $productDetails->creditline($creditlineId);
                foreach($creditlineamount as $creditlineamount) {
                   $creditlineamount=$creditlineamount->portfolio;
                }
                if($creditlineamountFetch) {
                    $loanamountremaining=$creditlineamount-$totalloansamount;
                    $this->view->loan->cteditlineamount->setValue($loanamountremaining);
                } else {
                    $loanamountremaining=$creditlineamount;
                    $this->view->loan->cteditlineamount->setValue($loanamountremaining);
                }
	}

	function interestsAction() {
		$this->_helper->layout()->disableLayout();
		$app = $this->view->baseUrl();
		$loan = new Account_Form_Loan($app);
		$this->view->loan=$loan;
		$amountrange = $this->_request->getParam('amount');
		$creditlineId = $this->_request->getParam('creditlineId');
		$interest_id = $this->_request->getParam('interestId');

		$productDetails = new Account_Model_Accounts();
		$this->view->selectedInterest = $productDetails->interestFromUrl($amountrange,$creditlineId,$interest_id);

                foreach($this->view->selectedInterest as $selectedinterest1) {
                   $interest=$selectedinterest1->rate;
                }
                if($this->view->selectedInterest) {
                    $this->view->loan->interest->setValue($interest);
                } else {
                    echo "Amount is grater than Loan range";
                }
	}

	function balletAction() {
		$this->_helper->layout()->disableLayout();
		$app = $this->view->baseUrl();
		$loan = new Account_Form_Loan($app);
		$this->view->loan=$loan;
		$amountrange = $this->_request->getParam('amount');
		$creditlineId = $this->_request->getParam('creditlineId');
		$interest_id = $this->_request->getParam('interestId');

		$productDetails = new Account_Model_Accounts();
		$this->view->selectedInterest = $productDetails->interestFromUrl($amountrange,$creditlineId,$interest_id);

                foreach($this->view->selectedInterest as $selectedinterest1) {
                   $ballet=$selectedinterest1->ballet;
                }
                if($this->view->selectedInterest) {
                    $this->view->loan->ballet->setValue($ballet);
                } else {
                    echo "Amount is grater than interest range";
                }
	}


	function feepercentAction() {
		$this->_helper->layout()->disableLayout();
		$app = $this->view->baseUrl();
		$loan = new Account_Form_Loan($app);
		$this->view->loan=$loan;
		$amountrange = $this->_request->getParam('amount');
		$creditlineId = $this->_request->getParam('creditlineId');
		$interest_id = $this->_request->getParam('interestId');

		$productDetails = new Account_Model_Accounts();
		$this->view->selectedfeerate= $productDetails->interestFromUrl($amountrange,$creditlineId,$interest_id);

                foreach($this->view->selectedfeerate as $selectedfeerate) {
                   $feerate=$selectedfeerate->fee;
                }
                if($this->view->selectedfeerate) {
                    $this->view->loan->feepercent->setValue($feerate);
                } else {
                    $this->view->loan->feepercent->setValue(0);
                }
	}

	function feeAction() {
		$this->_helper->layout()->disableLayout();
		$app = $this->view->baseUrl();
		$loan = new Account_Form_Loan($app);
		$this->view->loan=$loan;
		$amount = $this->_request->getParam('amount');
		$feepercent = $this->_request->getParam('feepercent');
                $feeamount= sprintf("%4.2f", $amount*($feepercent/100));
                $this->view->loan->fee->setValue($feeamount);
	}


}

