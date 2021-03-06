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
class Accounting_Model_Groupmemberrecurringtransaction extends Zend_Db_Table {
     protected $_name = 'ourbank_groupmember_recurringtransaction';


    public function Addtransaction($memberfirstname,$transaction_id,$account_id,$savings_amount,$createdby) {
        foreach($memberfirstname as $memberfirstname) {
        $data = array('groupmemberrecurringtransaction_id' => '',
                      'groupmembertransaction_id' => $transaction_id,
                      'groupaccount_id' => $account_id,
                      'groupmemberaccount_id' => $memberfirstname,
                      'groupmembertransaction_date' => date("Y-m-d"),
                      'groupmembertransaction_type' => 1,
                      'groupmembertransaction_amount' => $savings_amount,
                      'groupmembertransaction_interest' => '',
                      'groupmembertransaction_by' => 7,
                      'groupmembercreateddate' => date("Y-m-d"));
       $this->insert($data);
       }
    }
}
