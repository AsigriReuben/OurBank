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
class Address_Form_Address extends Zend_Form {
    public function init(){}
    public function __construct($id,$subId) 
    {
        parent::__construct($id);
        parent::__construct($subId);
        //$id= record id
        //$subId=submodule id
        //create address form elements...
        //$fieldtype,$fieldname,$table,$columnname,$cssname,$labelname,$required,$validationtype,$min,$max,$rows,$cols,$decorator,$value
        $formfield = new App_Form_Field ();
        $addressline1 = $formfield->field('Text','address1','','','mand','Address line1',true,'','','','','',1,0);
        $addressline2 = $formfield->field('Text','address2','','','','Address line2',false,'','','','','',1,0);
        $addressline3 = $formfield->field('Text','address3','','','','Address line3',false,'','','','','',1,0);
        $city = $formfield->field('Text','city','','','mand','City',true,'','','','','',1,0);
        $state = $formfield->field('Text','state','','','','State',false,'','','','','',1,0);
        $country = $formfield->field('Text','country','','','','Country',false,'','','','','',1,0);
        $pincode = $formfield->field('Text','zipcode','','','','Pincode',false,'','','','','',1,0);
        $address_id = $formfield->field('Hidden','id','','','','',false,'','','','','',0,$id);
        $sub_id = $formfield->field('Hidden','submodule_id','','','','',false,'','','','','',0,$subId);
        $created_by = $formfield->field('Hidden','created_by','','','','',false,'','','','','',0,1);
        $created_date = $formfield->field('Hidden','created_date','','','','',false,'','','','','',0,date("y/m/d H:i:s"));
        $this->addElements(array($sub_id,$address_id,$addressline1,$addressline2,$addressline3,$city,$state,$country,$pincode,$created_date,$created_by));
    }
}
