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

class Membership_Model_Group extends Zend_Db_Table {
protected $_name = 'ourbank_members';

    public function groupDetails($group_id) {
        $db = $this->getAdapter();
        $sql = 'select * from  ourbank_groupmembers A,
                               ourbank_membername B 
                               where (A.group_id = ? &&
                                      A.member_id = B.member_id )';
        $result = $db->fetchAll($sql, array($group_id));
        return $result;
    }

    public function productDetails() {
        $db = $this->getAdapter();
        $sql = 'select * from ourbank_productdetails';
        $result = $db->fetchAll($sql);
        return $result;
    }


    public function getGroupList($office_id) {
        $db = $this->getAdapter();
        $sql = 'select * from  
                ourbank_groupaddress
                where
                groupoffice_id = ? AND 
                (groupaccountstatus = 3 OR groupaccountstatus = 1) AND
                (recordstatus_id = 3 OR recordstatus_id = 1)';

        $result = $db->fetchAll($sql, array($office_id));
        return $result;
    }
    public function getMember($office_id) {
        $db = $this->getAdapter();
        $sql = "select * from  
                      ourbank_members A,
                      ourbank_bankaddress  B,
                      ourbank_membername C
                      where
                      A.memberbranch_id = ? AND
                      A.memberbranch_id = B.bank_id AND
                      A.member_id = C.member_id AND
                      B.recordstatus_id = 3  AND
                      (A.member_status = 3 || A.member_status = 1) AND
                      C.recordstatus_id=3 ";
        $result = $db->fetchAll($sql, array($office_id));
        return $result;
    }
/*
 $sql = 'select D.memberfirstname from  ourbank_groupmembers A,
                               ourbank_groupaddress B,
			       ourbank_members C,
			       ourbank_membername D

                               where (B.groupname_id = ? &&
                                      B.group_id = A.group_id && 
				      B.groupoffice_id = C.memberbranch_id &&
				      A.member_id != C.member_id &&
				      C.member_id = D.member_id &&
				      D.recordstatus_id = 3)';*/


    public function offerDetails() {
        $db = $this->getAdapter();
        $sql = 'select * from ourbank_productsofferdetails';
        $result = $db->fetchAll($sql);
        return $result;
   }

    public function groupmemberDetails() {
        $db = $this->getAdapter();
        $sql = 'select * from 
		ourbank_members A,
		ourbank_groupaddress C,
		ourbank_bankaddress  D where
                (A.member_id = C.group_id &&
		 C.groupoffice_id = D.bank_id &&
		 C.recordstatus_id = 3 &&
                (A.member_status = 3 || A.member_status = 1)) ';
        $result = $db->fetchAll($sql);
        return $result;
   }

    public function groupView($groupname_id) {
        $db = $this->getAdapter();
        $sql = 'select * from 
		ourbank_members A,
		ourbank_groupaddress C,
		ourbank_bankaddress  D where
		(C.groupname_id = ?) &&
                (A.member_id = C.group_id &&
		 C.groupoffice_id = D.bank_id &&
		 C.recordstatus_id = 3 && 
 		 C.recordstatus_id = 3  
                ) ';

     return $db->fetchAll($sql,array($groupname_id));

   }

    public function groupmembersView($groupname_id) {
         $db = $this->getAdapter();
        $sql = 'select * from 
		ourbank_groupaddress A,
		ourbank_groupmembers B,
		ourbank_members C,
		ourbank_membername D where
		(A.groupname_id = ?) &&
                (A.group_id = B.group_id &&
		 B.member_id = C.member_id &&
		 C.member_id = D.member_id &&
		 A.recordstatus_id = 3 && 
 		 D.recordstatus_id = 3 ) ';
        $result = $db->fetchAll($sql,array($groupname_id));
        return $result;
    }

    public function groupSearch($input) {
        $db = $this->getAdapter();

        $sql = "select * from 
		    ourbank_members A,
		    ourbank_groupaddress C,
		    ourbank_bankaddress  D 
                    where (
                    A.membercode like ? '%' AND
                    C.groupname like ? '%' AND
                    C.groupaddress_location like ? '%' AND 
                    D.bank_id like ? '%') AND 
                    A.member_id = C.group_id AND
		    C.groupoffice_id = D.bank_id AND
		    C.recordstatus_id = 3";

         return $db->fetchAll($sql,array($input['membercode'],$input['groupname'],
                                                   $input['groupaddress_location'],$input['bank_id']));

    }

    public function clickProductDetails($o_id) {
        $db = $this->getAdapter();
        $sql = "select * from ourbank_productsofferdetails A,
                              ourbank_productsloan B,
                              ourbank_interesttypes C,
                              ourbank_frequencyofpayment D
                              where (A.offerproduct_id = ? &&
                                     A.offerproductupdate_id = B.offerproductupdate_id &&
                                     B.interesttype_id= C.interesttype_id &&
                                     B.installmenttype_id =D.timefrequncy_id)";
        $result = $db->fetchAll($sql,array($o_id));
        return $result;
        }

    public function savingDetails($o_id) {
        $db = $this->getAdapter();
        $sql = "select * from ourbank_productsofferdetails A,
                              ourbank_productssaving B
                              where (A.offerproduct_id = ? &&
                                     A.offerproductupdate_id = B.offerproductupdate_id )";
        $result = $db->fetchAll($sql,array($o_id));
        return $result;
        }

    public function insertloanAccount($input) {
        $db = $this->getAdapter();
        $db->insert('ourbank_loanaccounts',$input);
        return '1';
    }

    public function insertgroupAccount($input) {
        $db = $this->getAdapter();
        $db->insert('ourbank_groupmembers',$input);
        return '1';
    }

	public function fetchAllOffice()
    	{
		$db = $this->getAdapter();
		$sql = 'SELECT * FROM  ourbank_bankaddress  where recordstatus_id=3 || recordstatus_id=1';
		$result = $db->fetchAll($sql,array());
		return $result;
    	}

    public function ProductIdDetails($o_id) {
        $db = $this->getAdapter();
        $sql = "SELECT * FROM ourbank_productsofferdetails where offerproduct_id = ?";
        $result = $db->fetchAll($sql,array($o_id));
        return $result;
    }

    public function accountnumber($group_id) {
        $db = $this->getAdapter();
        $sql = "select * from  ourbank_groupaddress A,
                               ourbank_members B,
                               ourbank_productsofferdetails C 
                               where (group_id =? &&
                                      A.group_id=B.member_id)" ;
        $result = $db->fetchAll($sql,array($group_id));
        return $result;
        }

    public function productnumber($product_id) {
        $db = $this->getAdapter();
        $sql = "select * from  ourbank_productdetails where product_id= ? " ;
        $result = $db->fetchAll($sql,array($product_id));
        return $result;
        }

    public function insertAccount($input) {
        $db = $this->getAdapter();
        $db->insert('ourbank_accounts',$input);
        $result = $db->lastInsertId('ourbank_accounts');
        return $result;
    } 

    public function updateRow($mFirstInsertedId,$input) {
        $db = $this->getAdapter();
        $where[] = "account_id = '".$mFirstInsertedId."'";
        $result = $db->update('ourbank_accounts',$input,$where);
    }

    public function branch()
    {
        $db = $this->getAdapter();
        $sql = 'select * from  ourbank_bankaddress  where officetype_id =4 && recordstatus_id=3';
        $result = $db->fetchAll($sql,array());
        return $result;
    }


    public function member($office_id)
    {
        $db = $this->getAdapter();
        $sql = 'select * from  ourbank_members A,
                               ourbank_membername B
                               where (A.memberbranch_id =? && 
                                     A.member_id = B.member_id &&
                                     B.recordstatus_id=3)';
        $result = $db->fetchAll($sql,array($office_id));
        return $result;
    }

    public function groupName($office_id)
    {
        $db = $this->getAdapter();
        $sql = 'select * from  ourbank_groupaddress  where groupoffice_id =? && groupaccountstatus = 3';
        $result = $db->fetchAll($sql,array($office_id));
        return $result;
    }


    public function insertMember($input) {
        $db = $this->getAdapter();
        $db->insert('ourbank_members',$input);
        $result = $db->lastInsertId('ourbank_members');
        return $result;
    }


        public function insertRow($input) {
            $db = $this->getAdapter();
            $db->insert('ourbank_members',$input);
            $result = $db->lastInsertId('ourbank_members');
            return $result;
        } 

        public function updateRow1($memberId,$input) {
        $db = $this->getAdapter();
        $where[] = "member_id = '".$memberId."'";
        $result = $db->update('ourbank_members',$input,$where);
        } 

        public function insertgroupaddress($input) {
        $db = $this->getAdapter();
            $db->insert('ourbank_groupaddress',$input);
            return 1;
        }

       public function groupUpdate($groupname_id,$input) {
        $db = $this->getAdapter();
        $where[] = "groupname_id = '".$groupname_id."'";
	$result = $db->update('ourbank_groupaddress',$input,$where);
      }

      public function insertUpdates($previous,$current,$groupname_id) {
         $db = $this->getAdapter();
         $result=array_keys (array_diff($previous,$current));
         foreach($result as $group) {
            $table_name='ourbank_groupupdates';
            $groupupdates = array('groupupdates_id'=>'',
                 		    'groupname_id'=>$groupname_id,
            			    'fieldname'=>$group,
            		   	    'table_name'=>$table_name,
           			    'previous_data'=>$previous[$group],
            			    'current_data'=>$current[$group],
            			    'modified_date'=>date("Y-m-d"));
            $db->insert('ourbank_groupupdates',$groupupdates);
	}
	return;
    } 
 


}
