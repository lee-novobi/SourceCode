<?php
require_once 'phpexcel/PHPExcel.php';
require_once 'phpexcel/PHPExcel/IOFactory.php';


class Oda_php_excel{
	var $strSourceFileName 	= '';
	var $objPHPExcelReader	= null;
	var $objPHPExcelWriter	= null;
	var $objPHPExcel 		= null;

	function __construct($strSourceFileName='' /* Full path to file */) {
		$this->objPHPExcel = new PHPExcel();
		
		if(!empty($strSourceFileName)){
			$this->strSourceFileName = $strSourceFileName;
			$this->InitPhpExcelObj();
		}
	}

	/* -------------------------------------------------------------------------
	 * InitPhpExcelObj
	 * -------------------------------------------------------------------------
	 * Initialize PHPExcelReader object
	 *
	 */
	protected function InitPhpExcelObj(){
		if(is_null($this->objPHPExcelReader) && !empty($this->strSourceFileName)){
			$inputFileType = PHPExcel_IOFactory::identify($this->strSourceFileName);
			$this->objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
			$this->objPHPExcelReader->setReadDataOnly(true);
		}
	}
	
	/* -------------------------------------------------------------------------
	 * ReadAllActiveSheet
	 * -------------------------------------------------------------------------
	 * Read all sheet data
	 *
	 */
	public function ReadAllActiveSheet(){
		$result = array();
		if(is_null($this->objPHPExcelReader)){
			$this->InitPhpExcelObj();
		}

		if(!is_null($this->objPHPExcelReader)){
			$objPHPExcel = $this->objPHPExcelReader->load($this->strSourceFileName);
			$result = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		}
		return $result;
	}
	
	/* -------------------------------------------------------------------------
	 * AppendRichText
	 * -------------------------------------------------------------------------
	 * Create a custom formatted text
	 *
	 */
	public function AppendRichText($objRichText=null, $strText='', $bBold=false, $bItalic=false, $strColor= 'FF000000')
	{
		if (is_null($objRichText)) {
			$objRichText = new PHPExcel_RichText();
		} 
		$objTextRun = $objRichText->createTextRun($strText);
		$objTextRun->getFont()->setBold($bBold);
		$objTextRun->getFont()->setItalic($bItalic);
		$objTextRun->getFont()->setColor( new PHPExcel_Style_Color( $strColor ) );	
		return $objRichText;
	}
	
	/* -------------------------------------------------------------------------
	 * AppendNormalText
	 * -------------------------------------------------------------------------
	 * Create a normal text
	 *
	 */
	public function AppendNormalText($objRichText=null, $strText='')
	{
		if (is_null($objRichText)) {
			$objRichText = new PHPExcel_RichText();
		}
		$objRichText->createText($strText);
		return $objRichText;
	}
	
	/* -------------------------------------------------------------------------
	 * FillBackgroundCell
	 * -------------------------------------------------------------------------
	 * Fill background cell with a color
	 *
	 */
	public function FillBackgroundCell($strRange=null, $strColor='FFFFFFFF')
	{
		if ($strRange != null && $strRange != '') 
		{
			$this->objPHPExcel->getActiveSheet()->getStyle($strRange)->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB($strColor);
		}
	}
	
	/* -------------------------------------------------------------------------
	 * SetValueToCell
	 * -------------------------------------------------------------------------
	 * Set value to cell
	 *
	 */
	public function SetValueToCell($iActiveSheetIndex=0, $strCell=null, $objText=null) {
		if (!is_null($strCell) && !is_null($objText)) 
		{
			$this->objPHPExcel->setActiveSheetIndex($iActiveSheetIndex);
			$this->objPHPExcel->getActiveSheet()->getCell($strCell)->setValue($objText);
			
			$this->objPHPExcel->getActiveSheet()->getStyle($strCell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			
			$this->objPHPExcel->getActiveSheet()->getStyle($strCell)->getAlignment()->setWrapText(true);
		}
	}
	
	/* -------------------------------------------------------------------------
	 * SetColumnSize
	 * -------------------------------------------------------------------------
	 * Set column width, can be custom or auto
	 *
	 */
	public function SetColumnSize($strColumn, $bIsAutoSize=true, $iCustomWidth=100) {
		if ($bIsAutoSize)
		{
			$this->objPHPExcel->getActiveSheet()
								->getColumnDimension($strColumn)
								->setAutoSize(true);
		}
		else {
			$this->objPHPExcel->getActiveSheet()
							->getColumnDimension($strColumn)
							->setWidth($iCustomWidth);
		}
	}
	
	/* -------------------------------------------------------------------------
	 * WriteAll
	 * -------------------------------------------------------------------------
	 * Write PHPExcel Object to a output location
	 *
	 */
	public function WriteAll($strOutputLocation){
		if(is_null($this->objPHPExcel)){
			$this->objPHPExcel = new PHPExcel();
		}
		$this->objPHPExcelWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, "Excel2007");
		if(!is_null($this->objPHPExcelWriter)){
			$this->objPHPExcelWriter->save($strOutputLocation);
		}
	}

	/* -------------------------------------------------------------------------
	 * SetSourceFile
	 * -------------------------------------------------------------------------
	 * Init source file
	 *
	 */
	public function SetSourceFile($strSourceFileName='' /* Full path to file */){
		$this->strSourceFileName = $strSourceFileName;
	}
}
?>
