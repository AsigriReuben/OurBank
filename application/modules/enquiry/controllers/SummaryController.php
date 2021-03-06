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
class Enquiry_SummaryController extends Zend_Controller_Action
{

     function init()
     { 
	   $this->view->title = "Summary of MFI's";	
     }
     function indexAction()
     {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('index/login');
        }
        $this->view->title = "Summary of MFI's";

        $savings= new Enquiry_Model_Summary();
	$members = $savings->inactiveMemberDetails();
        
        $savings1= new Enquiry_Model_Summary();
	$allmembers = $savings1->allMembers();
        
        $activeMembers = new Enquiry_Model_Summary();
        $activemember = $activeMembers->activeMembers();
        
        $inactiveMembers = new Enquiry_Model_Summary();
        $inactivemembers = $inactiveMembers->inactiveMembers();

        $savingAccounts = new Enquiry_Model_Summary();
        $savingaccounts = $savingAccounts->savingAccounts();
        
        $totalSavings = new Enquiry_Model_Summary();
        $totalsavings = $totalSavings->totalSavings();

        $loanAccounts = new Enquiry_Model_Summary();
        $loanaccounts = $loanAccounts->loanAccounts();

        $totalLoans = new Enquiry_Model_Summary();
        $totalloans = $totalLoans->totalLoans();

        $loanDisburse = new Enquiry_Model_Summary();
        $loandisburse = $loanDisburse->loanDisburse();

        $loanRepay = new Enquiry_Model_Summary();
        $loanrepay = $loanRepay->loanRepay();
        
        $rateLoan = new Enquiry_Model_Summary();
        $rateloan = $rateLoan->rateLoan();

        $funders = new Enquiry_Model_Summary();
        $Funders = $funders->funders();

        $fundings = new Enquiry_Model_Summary();
        $Fundings = $fundings->fundings();

        $totalFundings = new Enquiry_Model_Summary();
        $totalfundings = $totalFundings->totalFundings();

          $interestAmount = new Enquiry_Model_Summary();
          $interestamount = $interestAmount->interestAmount();

        $con=mysql_connect('localhost','newbank','new_bank');
        mysql_select_db('ourbank',$con);
        $sql=mysql_query( 'SELECT SUM(amount_disbursed) - (SELECT SUM( loaninstallmentpaid_amount )
                   FROM ourbank_loan_repayment ) test FROM ourbank_loan_disbursement');
        $this->view->sql=$sql;

        $con=mysql_connect('localhost','newbank','new_bank');
        mysql_select_db('ourbank',$con);
          $sql1=mysql_query( 'SELECT ROUND(100 * SUM( loaninstallmentpaid_amount ) / (SELECT SUM( amount_disbursed )
                              FROM ourbank_loan_disbursement )) rate FROM ourbank_loan_repayment');
        $this->view->sql1=$sql1;


        $con=mysql_connect('localhost','newbank','new_bank');
        mysql_select_db('ourbank',$con);
          $sql2=mysql_query( 'SELECT (SUM( amount_disbursed ) - 
                              (SELECT SUM( loaninstallmentpaid_amount ) FROM `ourbank_loan_repayment` )) *100 / SUM( amount_disbursed ) paid
                              FROM `ourbank_loan_disbursement`');
        $this->view->sql2=$sql2;

        



        $this->view->savings = $members;
        $this->view->savings1 = $allmembers;
        $this->view->activeMembers = $activemember;
        $this->view->inactiveMembers = $inactivemembers;
        $this->view->savingAccounts = $savingaccounts;
        $this->view->totalSavings = $totalsavings;
        $this->view->loanAccounts = $loanaccounts;
        $this->view->totalLoans = $totalloans;
        $this->view->loanDisburse = $loandisburse;
        $this->view->rateLoan = $rateloan;
        $this->view->funders = $Funders;
        $this->view->fundings = $Fundings;
        $this->view->totalFundings = $totalfundings;
        $this->view->loanRepay = $loanrepay;
        $this->view->interestAmount = $interestamount;
        
    }
					
    
}