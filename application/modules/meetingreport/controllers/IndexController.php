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
 *  create an meeting controller to search filtered values and pdf
 */

class Meetingreport_IndexController extends Zend_Controller_Action
{
	function init() { 
		$this->view->pageTitle = "Meeting Report";
		$this->view->type="others";
		//session
		$sessionName = new Zend_Session_Namespace('ourbank');
		$userid=$this->view->createdby = $sessionName->primaryuserid;
		$login=new App_Model_Users();
		$loginname=$login->username($userid);
		foreach($loginname as $loginname) {
// 			$this->view->username=$loginname['username'];
// 			$this->view->id=$loginname['id'];
// 			$this->view->primaryrole=$loginname['grantname'];

		}
	}
	//view action
	function indexAction() {
		$path = $this->view->baseUrl();
		$searchForm = new Meetingreport_Form_Search($path);
		$this->view->form = $searchForm;

		$fetchBanknames = new Meetingreport_Model_Meetingreport();
		$office = $fetchBanknames->getOffice();
		//load office name drop down
		foreach($office as $office) {
			$searchForm->field1->addMultiOption($office['id'],$office['name']);
		}

		$fetchGroupnames = new Meetingreport_Model_Meetingreport();

		$Groupname = $fetchBanknames->fetchGroupnamesForMeetingReport();
		//load groupname dropdown
		foreach($Groupname as $Groupname) {
			$searchForm->field2->addMultiOption($Groupname['id'],$Groupname['name']);
		}
			$this->view->bank_name=$this->_request->getParam('field1');
		
		$days = $fetchGroupnames->getDays();
		//load days dropdown
		foreach($days as $days) {
			$searchForm->field3->addMultiOption($days['day_value'],$days['days_name']);
		}
			$fetchMeetings=new Meetingreport_Model_Meetingreport();
			$result=$fetchMeetings->getMeetingsall(); 

				$page = $this->_getParam('page',1);
				$paginator = Zend_Paginator::factory($result);
				$paginator->setItemCountPerPage(5);
				$paginator->setCurrentPageNumber($page);
				$this->view->paginator = $paginator;

		if ($this->_request->isPost() && $this->_request->getPost('Search')) {
			$formData = $this->_request->getPost();
				
			$this->view->institute_bank_id=$institute_bank_id = $this->_request->getParam('field1');
			//$bankName=$fetchMeetings->getMeetings($this->_request->getParam('field1'));
			//foreach($bankName as $bankName){}
			//$this->view->bank_name=$bankName['bankname'];
			
			$this->view->group_id=$group_id = $this->_request->getParam('field2');
			$this->view->fromDate=$fromDate = $this->_request->getParam('field3');
			$this->view->toDate=$toDate = $this->_request->getParam('field4');
			
			if ($searchForm->isValid($formData)) {

				//validate form data
				$this->view->check=10;
				$fetchMeetings=new Meetingreport_Model_Meetingreport();
				$result=$fetchMeetings->getMeetings($formData); 
				$page = $this->_getParam('page',1);
				$paginator = Zend_Paginator::factory($result);
				$paginator->setItemCountPerPage(5);
				$paginator->setCurrentPageNumber($page);
				$this->view->paginator = $paginator;
			}
		}
	}

	function viewtransactionAction() {
	}
	//pdf action
	function pdfgenerationAction() {
		$fetchMeetings=new Meetingreport_Model_Meetingreport();

		$pdf = new Zend_Pdf();
		$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
		$pdf->pages[] = $page;
		// Image
		$app = $this->view->baseUrl();
		$word=explode('/',$app);
		$projname='';
		for($i=0; $i<count($word); $i++) {
			if($i>0 && $i<(count($word)-1)) { $projname.='/'.$word[$i]; }
		}
		$image_name = "/var/www".$app."/images/logo.jpg";
		$image = Zend_Pdf_Image::imageWithPath($image_name);
	
		$page->drawImage($image, 30, 770, 130, 820);
		$page->setLineWidth(1)->drawLine(25, 25, 570, 25); //bottom horizontal
		$page->setLineWidth(1)->drawLine(25, 25, 25, 820); //left vertical
		$page->setLineWidth(1)->drawLine(570, 25, 570, 820); //right vertical
		$page->setLineWidth(1)->drawLine(570, 820, 25, 820); //top horizontal
		//set the font
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
	
		$text = array("Meeting List","Bank Name","Group Head","Day","Time","Place");
		
		$x =array(55,180,320,390,450);

			$this->view->institute_bank_id=$institute_bank_id= $this->_request->getParam('field1'); 
			$this->view->group_id=$group_id = $this->_request->getParam('field2');
			$this->view->fromDate=$fromDate = $this->_request->getParam('field3'); 
			$this->view->toDate=$toDate = $this->_request->getParam('field4');


			$result=$fetchMeetings->getMeetingsall(); 
		
		if($this->_request->getParam('field1') || $this->_request->getParam('field2') ||$this->_request->getParam('field3') ||$this->_request->getParam('field4')) {
			$post=array('field1'=>$this->_request->getParam('field1'),'field2'=>$this->_request->getParam('field2'),'field3'=>$this->_request->getParam('field3'),'field4'=>$this->_request->getParam('field4'));
			$result=$fetchMeetings->getMeetings($post); 
		}

				$page1 = $this->_getParam('page',1);
				$paginator = Zend_Paginator::factory($result);
				$paginator->setItemCountPerPage(10);
				$paginator->setCurrentPageNumber($page1);
				$this->view->paginator = $paginator;
		$y1 = 740;
		$xx=50; $xy=550;
			$page->drawText($text[0], 250, $y1); $y1-=20;//Title of PDF

		if($this->_request->getParam('field1')) {
			$page->drawText("Bank Name : ", $xx, $y1);

			$getBankName=new Meetingreport_Model_Meetingreport();
			$bankName=$getBankName->getBankName($this->_request->getParam('field1'));
			foreach($bankName as $bankName){}
			$page->drawText($bankName['name'], $xx+100, $y1);$y1-=15;
			}
		if($this->_request->getParam('field2')) {
			$page->drawText("Group Name : ", $xx, $y1);
			$getGroupName=new Meetingreport_Model_Meetingreport();
			$GroupName=$getGroupName->getGroupName($this->_request->getParam('field2'));
			foreach($GroupName as $GroupName){}
			$page->drawText($GroupName['name'], $xx+100, $y1);$y1-=15;
		}
		if($this->_request->getParam('field3')) {
			$page->drawText("Day : ", $xx, $y1);
			$page->drawText($this->_request->getParam('field3'), $xx+100, $y1);$y1-=15;
		}
		
		$page->drawLine($xx,$y1,$xy,$y1); $y1-=15;//first line
			$page->drawText($text[1], $x[0], $y1);//Header
			$page->drawText($text[2], $x[1], $y1);
			$page->drawText($text[3], $x[2], $y1);
			$page->drawText($text[4], $x[3], $y1);
			$page->drawText($text[5], $x[4], $y1);
			
			$y1-=10;
	        $page->drawLine($xx,$y1,$xy,$y1); $y1-=15;//second line

		foreach($result as $result1) {
			if($y1>40){

			$st=16;
			$text = $result1['bankname'];
				$newtext = wordwrap($text, 15, "<br />\n");
				$pieces = explode("<br />", $newtext);

				$ommittedspace=0;
				foreach($pieces as $pieces0) {
					if($pieces0!="") {
						$page->drawText(substr($pieces0,0,$st),$x[0], $y1);
						$y1-=15;  $ommittedspace+=15;
					}
				} $y1+=$ommittedspace;

			$st1=30;
			$text1 = $result1['grouphead_name'];
				$newtext1 = wordwrap($text1, 23, "<br />\n");
				$pieces1 = explode("<br />", $newtext1);

				$ommittedspace1=0;
				foreach($pieces1 as $pieces2) {
					if($pieces2!="") {
						$page->drawText(substr($pieces2,0,$st1),$x[1], $y1);
						$y1-=15;  $ommittedspace1+=15;
					}
				} $y1+=$ommittedspace1;

				$page->drawText($result1['day'], $x[2], $y1);
				$page->drawText($result1['time'], $x[3], $y1);
				$page->drawText($result1['place'], $x[4], $y1);
				$y1-=15;
				$space=max($ommittedspace,$ommittedspace1);$y1-=$space;
			}
			else {

			$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
			$pdf->pages[] = $page;
			// Image
			$app = $this->view->baseUrl();
			$word=explode('/',$app);
			$projname = $word[1];
	
			$image_name = "/var/www/".$projname."/public/images/logo.jpg";
			$image = Zend_Pdf_Image::imageWithPath($image_name);

		
			$page->drawImage($image, 30, 770, 130, 820);
			$page->setLineWidth(1)->drawLine(25, 25, 570, 25); //bottom horizontal
			$page->setLineWidth(1)->drawLine(25, 25, 25, 820); //left vertical
			$page->setLineWidth(1)->drawLine(570, 25, 570, 820); //right vertical
			$page->setLineWidth(1)->drawLine(570, 820, 25, 820); //top horizontal
			//set the font
			$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
		
			$text = array("Meeting List","Bank Name","Group Head","Day","Time","Place");
			
			$x =array(55,180,320,390,450);

			$y1 = 740;
			$st=16;
			$text = $result1['bankname'];
				$newtext = wordwrap($text, 15, "<br />\n");
				$pieces = explode("<br />", $newtext);

				$ommittedspace=0;
				foreach($pieces as $pieces0) {
					if($pieces0!="") {
						$page->drawText(substr($pieces0,0,$st),$x[0], $y1);
						$y1-=15;  $ommittedspace+=15;
					}
				} $y1+=$ommittedspace;


			$st1=30;
			$text1 = $result1['grouphead_name'];
				$newtext1 = wordwrap($text1, 23, "<br />\n");
				$pieces1 = explode("<br />", $newtext1);

				$ommittedspace1=0;
				foreach($pieces1 as $pieces2) {
					if($pieces2!="") {
						$page->drawText(substr($pieces2,0,$st1),$x[1], $y1);
						$y1-=15;  $ommittedspace1+=15;
					}
				} $y1+=$ommittedspace1;

				$page->drawText($result1['day'], $x[2], $y1);
				$page->drawText($result1['time'], $x[3], $y1);
				$page->drawText($result1['place'], $x[4], $y1);
				$y1-=15;
				$space=max($ommittedspace,$ommittedspace1);$y1-=$space;
			}
			}

		$pdfData = $pdf->render();
		
		$pdf->save('/var/www'.$projname.'/reports/meeting'.date('Y-m-d').'.pdf');
		$path = '/var/www'.$projname.'/reports/meeting'.date('Y-m-d').'.pdf';
		chmod($path,0777);
	}
	//pdf report display action
	function reportdisplayAction() {
		$this->_helper->layout->disableLayout();
		$file1 = $this->_request->getParam('file');
		
		$app = $this->view->baseUrl();
		$word=explode('/',$app);
		$projname='';
		for($i=0; $i<count($word); $i++) {
			if($i>0 && $i<(count($word)-1)) { $projname.='/'.$word[$i]; }
		}
                $this->view->filename = $projname."/reports/".$file1;
	}
	//search group action
	public function fetchgroupsAction() {
		$this->_helper->layout->disableLayout();

		$path = $this->view->baseUrl();
		
		$meetingreportForm = new Meetingreport_Form_Search($path);
		$this->view->meetingreportForm = $meetingreportForm;
		
		$bank_id=$this->_request->getParam('bank_id');
		
		$meeting = new Meetingreport_Model_Meetingreport();
		$office=$meeting->fetchGroupnames($bank_id);
		//office dropdown
		foreach($office as $office) {
			$meetingreportForm->field2->addMultiOption($office['id'],$office['name']);
		}
	}
}
