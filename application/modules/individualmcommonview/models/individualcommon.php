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
class Individualmcommonview_Model_individualmcommon extends Zend_Db_Table {
      protected $_name = 'ourbank_member';

    //get member details
    public function getmember($id)
    {

        $select=$this->select()
                ->setIntegrityCheck(false)
                ->join(array('a'=>'ourbank_member'),array('a.id'),array('a.name as membername','a.dateofbirth','a.mobile'))
                ->join(array('b'=>'gender'),'b.id=a.gender',array('b.sex'))
                ->join(array('c'=>'ourbank_office'),'c.id =a.office_id',array('c.name'))
                ->where('a.id=?',$id);
        $result=$this->fetchAll($select);
        return $result->toArray();
    }

    //get module id and submodule id
    public function getmodule($modulename)
    {
        $select=$this->select()
                        ->setIntegrityCheck(false)
                        ->join(array('ob_modules'),array('module_id'))
                        ->where('module_description=?',$modulename);
        $result=$this->fetchAll($select);
        return $result->toArray();
    }

    //getting family details
    public function getfamilydetails($mebmerid)
    {
        $select=$this->select()
                        ->setIntegrityCheck(false)
                        ->join(array('a'=>'ourbank_family'),array('a.id'),array('a.name','a.age'))
                        ->where('a.member_id=?',$mebmerid)
                        ->join(array('b'=>'gender'),'b.id=a.gender',array('b.sex'))
                        ->join(array('c'=>'ourbank_familyrelationship'),'c.id=a.relationship',array('c.relationship'))
                        ->join(array('d'=>'ourbank_physicalstatus'),'d.id=a.physicalstatus',array('d.physicalstatus'))
                        ->join(array('e'=>'ourbank_maritalstatus'),'e.id=a.maritalstatus',array('e.maritalstatus'));
       $result=$this->fetchAll($select);
        return $result->toArray();
    }

    //getting family health details
    public function gethealthdetails($mebmerid)
    {
        $select=$this->select()
                        ->setIntegrityCheck(false)
                        ->join(array('a'=>'ourbank_familyhealth'),array('a.id'))
                        ->where('a.member_id=?',$mebmerid)
                        ->join(array('b'=>'ourbank_family'),'b.member_id=a.member_id && b.id=a.familymember_id',array('b.name'))
                        ->join(array('c'=>'ourbank_health_problem'),'c.id=a.health_problem',array('c.health_problem'));
       $result=$this->fetchAll($select);
       return $result->toArray();
    }

    //getting economic details
    public function geteconomicdetails($mebmerid)
    {
        $select=$this->select()
                        ->setIntegrityCheck(false)
                        ->join(array('a'=>'ourbank_familyeconomic'),array('a.id'))
                        ->where('a.member_id=?',$mebmerid)
                        ->join(array('b'=>'ourbank_family'),'b.member_id=a.member_id && b.id=a.familymember_id',array('b.name'))
                        ->join(array('c'=>'ourbank_profession'),'c.id=a.profession',array('c.profession'));
       $result=$this->fetchAll($select);
       return $result->toArray();
    }

    //getting education details
    public function geteducationdetails($mebmerid)
    {
        $select=$this->select()
                        ->setIntegrityCheck(false)
                        ->join(array('a'=>'ourbank_familyeducation'),array('a.id'))
                        ->where('a.member_id=?',$mebmerid)
                        ->join(array('b'=>'ourbank_family'),'b.member_id=a.member_id && b.id=a.familymember_id',array('b.name'))
                        ->join(array('c'=>'ourbank_qualification'),'c.id=a.qualification',array('c.qualification'));
       $result=$this->fetchAll($select);
       return $result->toArray();
    }




}
