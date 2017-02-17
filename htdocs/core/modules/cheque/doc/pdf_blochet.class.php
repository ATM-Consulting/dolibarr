<?php
/* Copyright (C) 2006      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2009-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2016      Juanjo Menent		<jmenent@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/core/modules/cheque/doc/pdf_blochet.class.php
 *	\ingroup    banque
 *	\brief      File to build cheque deposit receipts
 */

require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/cheque/modules_chequereceipts.php';


/**
 *	Class of file to build cheque deposit receipts
 */
class BordereauChequeBlochet extends ModeleChequeReceipts
{
	var $emetteur;	// Objet societe qui emet

	/**
	 *	Constructor
	 *
	 *	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("main");
		$langs->load("bills");

		$this->db = $db;
		$this->name = "blochet";

		$this->tab_top = 93;

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray=pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = $formatarray['height'];
		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$this->marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$this->marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$this->marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;

        // Recupere emmetteur
        $this->emetteur=$mysoc;
        if (! $this->emetteur->country_code) $this->emetteur->country_code=substr($langs->defaultlang,-2);    // By default if not defined

        // Defini position des colonnes
        $this->line_height = 5;
		$this->line_per_page = 35;
		$this->tab_height = 175;	//$this->line_height * $this->line_per_page;
	}

	/**
	 *	Fonction to generate document on disk
	 *
	 *	@param	RemiseCheque	$object			Object RemiseCheque			
	 *	@param	string			$_dir			Directory
	 *	@param	string			$number			Number
	 *	@param	Translate		$outputlangs	Lang output object
     *	@return	int     						1=ok, 0=ko
	 */
	function write_file($object, $_dir, $number, $outputlangs)
	{
		global $user,$conf,$langs,$hookmanager;

        if (! is_object($outputlangs)) $outputlangs=$langs;
        // For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
        $sav_charset_output=$outputlangs->charset_output;
        if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("main");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("products");
        $outputlangs->load("compta");

		$dir = $_dir . "/".get_exdir($number,0,1,0,$object,'cheque').$number;

		if (! is_dir($dir))
		{
			$result=dol_mkdir($dir);

			if ($result < 0)
			{
				$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
				return -1;
			}
		}

		$file = $dir . "/bordereau-".$number.".pdf";

		// Add pdfgeneration hook
		if (! is_object($hookmanager))
		{
			include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
			$hookmanager=new HookManager($this->db);
		}
		$hookmanager->initHooks(array('pdfgeneration'));
		$parameters=array('file'=>$file, 'outputlangs'=>$outputlangs);
		global $action;
		$reshook=$hookmanager->executeHooks('beforePDFCreation',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks

		// Create PDF instance
        $pdf=pdf_getInstance($this->format);
        $heightforinfotot = 50;	// Height reserved to output the info and total part
        $heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	// Height reserved to output the free text on last page
        $heightforfooter = $this->marge_basse + 8;	// Height reserved to output the footer (value include bottom margin)
        $pdf->SetAutoPageBreak(1,0);

        if (class_exists('TCPDF'))
        {
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
        }
        $pdf->SetFont(pdf_getPDFFont($outputlangs));

		$pdf->Open();
		$pagenb=0;
		$pdf->SetDrawColor(128,128,128);

		$pdf->SetTitle($outputlangs->transnoentities("CheckReceipt")." ".$number);
		$pdf->SetSubject($outputlangs->transnoentities("CheckReceipt"));
		$pdf->SetCreator("Dolibarr ".DOL_VERSION);
		$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
		$pdf->SetKeyWords($outputlangs->transnoentities("CheckReceipt")." ".$number);
		if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

		$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right

		$nboflines=count($this->lines);

		// Define nb of page
		$pages = intval($nboflines / $this->line_per_page);
		if (($nboflines % $this->line_per_page)>0)
		{
			$pages++;
		}
		if ($pages == 0)
		{
			// force to build at least one page if report has no lines
			$pages = 1;
		}

		$pdf->AddPage();
        $pagenb++;
		$this->Header($pdf, $pagenb, $pages, $outputlangs);

		$this->Body($pdf, $pagenb, $pages, $outputlangs);

		// Pied de page
		$this->_pagefoot($pdf,'',$outputlangs);
		if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

		$pdf->Close();

		$pdf->Output($file,'F');

		// Add pdfgeneration hook
		if (! is_object($hookmanager))
		{
			include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
			$hookmanager=new HookManager($this->db);
		}
		$hookmanager->initHooks(array('pdfgeneration'));
		$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
		global $action;
		$reshook=$hookmanager->executeHooks('afterPDFCreation',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks

		if (! empty($conf->global->MAIN_UMASK))
			@chmod($file, octdec($conf->global->MAIN_UMASK));

        $outputlangs->charset_output=$sav_charset_output;
	    return 1;   // Pas d'erreur
	}


	/**
	 *	Generate Header
	 *
	 *	@param  PDF			$pdf        	Pdf object
	 *	@param  int			$page        	Current page number
	 *	@param  int			$pages       	Total number of pages
	 *	@param	Translate	$outputlangs	Object language for output
	 *	@return	void
	 */
	function Header(&$pdf, $page, $pages, $outputlangs)
	{
		global $langs;
		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$outputlangs->load("compta");
		$outputlangs->load("banks");

		$title = $outputlangs->transnoentities("CheckReceipt");
		$pdf->SetFont('','B', $default_font_size);
        $pdf->SetXY(10,8);
        $pdf->MultiCell(0,2,$title,0,'L');

		$pdf->SetFont('','', $default_font_size);
        $pdf->SetXY(10,15);
		$pdf->MultiCell(22,2,$outputlangs->transnoentities("Ref"),0,'L');
        $pdf->SetXY(32,15);
		$pdf->SetFont('','', $default_font_size);
        $pdf->MultiCell(60, 2, $outputlangs->convToOutputCharset($this->ref.($this->ref_ext?" - ".$this->ref_ext:'')), 0, 'L');

		$pdf->SetFont('','', $default_font_size);
        $pdf->SetXY(10,20);
        $pdf->MultiCell(22,2,$outputlangs->transnoentities("Date"),0,'L');
        $pdf->SetXY(32,20);
        $pdf->SetFont('','', $default_font_size);
        $pdf->MultiCell(60, 2, dol_print_date($this->date,"day",false,$outputlangs));

		$pdf->SetFont('','', $default_font_size);
        $pdf->SetXY(10,26);
        $pdf->MultiCell(22,2,$outputlangs->transnoentities("Owner"),0,'L');
		$pdf->SetFont('','', $default_font_size);
        $pdf->SetXY(32,26);
        $pdf->MultiCell(60,2,$outputlangs->convToOutputCharset($this->account->proprio),0,'L');

		$pdf->SetFont('','', $default_font_size);
        $pdf->SetXY(10,32);
        $pdf->MultiCell(19,2,$outputlangs->transnoentities("Account"),0,'L');
        $this->pdf_bank($pdf,$outputlangs,32,32,$this->account,1);


		$pdf->SetFont('','', $default_font_size);
        $pdf->SetXY(102,15);
		$pdf->MultiCell(40, 2, $outputlangs->transnoentities("Signature"), 0, 'L');

		$pdf->SetFont('','', $default_font_size);
        $pdf->SetXY(102,44);
        $pdf->MultiCell(96,2,$outputlangs->transnoentities("Address"),0,'L');

		$pdf->SetFont('','B',$default_font_size + 1);
		$pdf->SetXY(102, 52);
		$pdf->MultiCell(100, 6, $this->account->bank, 0, 'L', 0);

		$pdf->SetFont('','',$default_font_size + 1);
		$pdf->SetXY(102, 59);
		$pdf->MultiCell(100, 6, $this->account->domiciliation, 0, 'L', 0);
        

        $pdf->Rect(9, 14, 192, 68);
        $pdf->line(9, 19, 100, 19);
        $pdf->line(9, 25, 100, 25);
        //$pdf->line(9, 31, 201, 31);
        $pdf->line(9, 31, 100, 31);

        
        $pdf->line(100, 42, 201, 42);

        $pdf->line(30, 14, 30, 82);
        $pdf->line(100, 14, 100, 82);

		// Number of cheques
		$posy=84;
		$pdf->Rect(9, $posy, 192, 6);
		$pdf->line(55, $posy, 55, $posy+6);
		$pdf->line(140, $posy, 140, $posy+6);
		$pdf->line(170, $posy, 170, $posy+6);

		$pdf->SetFont('','', $default_font_size);
		$pdf->SetXY(10,$posy+1);
		$pdf->MultiCell(40, 2, $outputlangs->transnoentities("NumberOfCheques"), 0, 'L');

		$pdf->SetFont('','B', $default_font_size);
        $pdf->SetXY(57,$posy+1);
        $pdf->MultiCell(40, 2, $this->nbcheque, 0, 'L');

		$pdf->SetFont('','', $default_font_size);
        $pdf->SetXY(148,$posy+1);
		$pdf->MultiCell(40, 2, $langs->trans("Total"));

		$pdf->SetFont('','B', $default_font_size);
		$pdf->SetXY(170, $posy+1);
		$pdf->MultiCell(31, 2, price($this->amount), 0, 'C', 0);

		// Tableau
		$pdf->SetFont('','', $default_font_size - 2);
		$pdf->SetXY(11, $this->tab_top+2);
		$pdf->MultiCell(40,2,$outputlangs->transnoentities("Num"), 0, 'L');
		$pdf->line(40, $this->tab_top, 40, $this->tab_top + $this->tab_height + 10);

		$pdf->SetXY(41, $this->tab_top+2);
        $pdf->MultiCell(40,2,$outputlangs->transnoentities("Bank"), 0, 'L');
		$pdf->line(100, $this->tab_top, 100, $this->tab_top + $this->tab_height + 10);

        $pdf->SetXY(101, $this->tab_top+2);
        $pdf->MultiCell(40,2,$outputlangs->transnoentities("CheckTransmitter"), 0, 'L');
		$pdf->line(180, $this->tab_top, 180, $this->tab_top + $this->tab_height + 10);

		$pdf->SetXY(180, $this->tab_top+2);
		$pdf->MultiCell(20,2,$outputlangs->transnoentities("Amount"), 0, 'R');
		$pdf->line(9, $this->tab_top + 8, 201, $this->tab_top + 8);

		$pdf->Rect(9, $this->tab_top, 192, $this->tab_height + 10);
	}


	/**
	 *	Output array
	 *
	 *	@param	PDF			$pdf			PDF object
	 *	@param	int			$pagenb			Page nb
	 *	@param	int			$pages			Pages
	 *	@param	Translate	$outputlangs	Object lang
	 *	@return	void
	 */
	function Body(&$pdf, $pagenb, $pages, $outputlangs)
	{
		// x=10 - Num
		// x=30 - Banque
		// x=100 - Emetteur
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		$pdf->SetFont('','', $default_font_size - 1);
		$oldprowid = 0;
		$pdf->SetFillColor(220,220,220);
		$yp = 0;
		$lineinpage=0;
		$num=count($this->lines);
		for ($j = 0; $j < $num; $j++)
		{
		    $lineinpage++;

			$pdf->SetXY(1, $this->tab_top + 10 + $yp);
			$pdf->MultiCell(8, $this->line_height, $j+1, 0, 'R', 0);

			$pdf->SetXY(10, $this->tab_top + 10 + $yp);
			$pdf->MultiCell(30, $this->line_height, $this->lines[$j]->num_chq?$this->lines[$j]->num_chq:'', 0, 'L', 0);

			$pdf->SetXY(40, $this->tab_top + 10 + $yp);
			$pdf->MultiCell(70, $this->line_height, dol_trunc($outputlangs->convToOutputCharset($this->lines[$j]->bank_chq),44), 0, 'L', 0);

			$pdf->SetXY(100, $this->tab_top + 10 + $yp);
			$pdf->MultiCell(80, $this->line_height, dol_trunc($outputlangs->convToOutputCharset($this->lines[$j]->emetteur_chq),50), 0, 'L', 0);

			$pdf->SetXY(180, $this->tab_top + 10 + $yp);
			$pdf->MultiCell(20, $this->line_height, price($this->lines[$j]->amount_chq), 0, 'R', 0);

			$yp = $yp + $this->line_height;

			if ($lineinpage >= $this->line_per_page && $j < (count($this->lines)-1))
			{
			    $lineinpage=0; $yp=0;

                // New page
                $pdf->AddPage();
                $pagenb++;
                $this->Header($pdf, $pagenb, $pages, $outputlangs);
                $pdf->SetFont('','', $default_font_size - 1);
                $pdf->MultiCell(0, 3, '');      // Set interline to 3
                $pdf->SetTextColor(0,0,0);
			}
		}
	}


	/**
	 *   	Show footer of page. Need this->emetteur object
     *
	 *   	@param	PDF			$pdf     			PDF
	 * 		@param	Object		$object				Object to show
	 *      @param	Translate	$outputlangs		Object lang for output
	 *      @param	int			$hidefreetext		1=Hide free text
	 *      @return	void
	 */
	function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		global $conf;
		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$showdetails=$conf->global->MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS;
		return pdf_pagefoot($pdf,$outputlangs,'BANK_CHEQUERECEIPT_FREE_TEXT',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,$showdetails,$hidefreetext);
		$paramfreetext='BANK_CHEQUERECEIPT_FREE_TEXT';
		$marge_basse=$this->marge_basse;
		$marge_gauche=$this->marge_gauche;
		$page_hauteur=$this->page_hauteur;

		// Line of free text
		$line=(! empty($conf->global->$paramfreetext))?$outputlangs->convToOutputCharset($conf->global->$paramfreetext):"";

		$pdf->SetFont('','', $default_font_size - 3);
		$pdf->SetDrawColor(224,224,224);

		// The start of the bottom of this page footer is positioned according to # of lines
    	$freetextheight=0;
    	if ($line)	// Free text
    	{
    		//$line="eee<br>\nfd<strong>sf</strong>sdf<br>\nghfghg<br>";
    	    if (empty($conf->global->PDF_ALLOW_HTML_FOR_FREE_TEXT))   // by default
    		{
    			$width=20000; $align='L';	// By default, ask a manual break: We use a large value 20000, to not have automatic wrap. This make user understand, he need to add CR on its text.
        		if (! empty($conf->global->MAIN_USE_AUTOWRAP_ON_FREETEXT)) {
        			$width=200; $align='C';
        		}
    		    $freetextheight=$pdf->getStringHeight($width,$line);
    		}
    		else
    		{
                $freetextheight=pdfGetHeightForHtmlContent($pdf,dol_htmlentitiesbr($line, 1, 'UTF-8', 0));      // New method (works for HTML content)
                //print '<br>'.$freetextheight;exit;
    		}
    	}

		$marginwithfooter=$marge_basse + $freetextheight;
    	$posy=$marginwithfooter+0;
    
    	if ($line)	// Free text
    	{
    		$pdf->SetXY($dims['lm'],-$posy);
    		if (empty($conf->global->PDF_ALLOW_HTML_FOR_FREE_TEXT))   // by default
    		{
                $pdf->MultiCell(0, 3, $line, 0, $align, 0);
    		}
    		else
    		{
                $pdf->writeHTMLCell($pdf->page_largeur - $pdf->margin_left - $pdf->margin_right, $freetextheight, $dims['lm'], $dims['hk']-$marginwithfooter, dol_htmlentitiesbr($line, 1, 'UTF-8', 0));
    		}
    		$posy-=$freetextheight;
    	}
    	
		// On positionne le debut du bas de page selon nbre de lignes de ce bas de page
		/*
		$nbofline=dol_nboflines_bis($line,0,$outputlangs->charset_output);
		//print 'e'.$line.'t'.dol_nboflines($line);exit;
		$posy=$marge_basse + ($nbofline*3);

		if ($line)	// Free text
		{
			$pdf->SetXY($marge_gauche,-$posy);
			$pdf->MultiCell(20000, 3, $line, 0, 'L', 0);	// Use a large value 20000, to not have automatic wrap. This make user understand, he need to add CR on its text.
			$posy-=($nbofline*3);	// 6 of ligne + 3 of MultiCell
		}*/

		$pdf->SetY(-$posy);
		$pdf->line($marge_gauche, $page_hauteur-$posy, 200, $page_hauteur-$posy);
		$posy--;

		/*if ($line1)
		{
			$pdf->SetXY($marge_gauche,-$posy);
			$pdf->MultiCell(200, 2, $line1, 0, 'C', 0);
		}

		if ($line2)
		{
			$posy-=3;
			$pdf->SetXY($marge_gauche,-$posy);
			$pdf->MultiCell(200, 2, $line2, 0, 'C', 0);
		}*/

        // Show page nb only on iso languages (so default Helvetica font)
        if (pdf_getPDFFont($outputlangs) == 'Helvetica')
        {
    		$pdf->SetXY(-20,-$posy);
            $pdf->MultiCell(11, 2, $pdf->PageNo().'/'.$pdf->getAliasNbPages(), 0, 'R', 0);
        }
	}


	/**
	 *  Show bank informations for PDF generation
	 *
	 *  @param	PDF			$pdf            		Object PDF
	 *  @param  Translate	$outputlangs     		Object lang
	 *  @param  int			$curx            		X
	 *  @param  int			$cury            		Y
	 *  @param  Account		$account         		Bank account object
	 *  @param  int			$onlynumber      		Output only number (bank+desk+key+number according to country, but without name of bank and domiciliation)
	 *  @param	int			$default_font_size		Default font size
	 *  @return	float                               The Y PDF position
	 */
	function pdf_bank(&$pdf,$outputlangs,$curx,$cury,$account,$onlynumber=0,$default_font_size=10)
	{
		global $mysoc, $conf;
	
		require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbank.class.php';
		
		$diffsizetitle=(empty($conf->global->PDF_DIFFSIZE_TITLE)?3:$conf->global->PDF_DIFFSIZE_TITLE);
		$diffsizecontent=(empty($conf->global->PDF_DIFFSIZE_CONTENT)?4:$conf->global->PDF_DIFFSIZE_CONTENT);
		$pdf->SetXY($curx, $cury);
	
		if (empty($onlynumber))
		{
			$pdf->SetFont('','B',$default_font_size - $diffsizetitle);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities('PaymentByTransferOnThisBankAccount').':', 0, 'L', 0);
			$cury+=4;
		}
	
		$outputlangs->load("banks");
	
		// Use correct name of bank id according to country
		$bickey="BICNumber";
		if ($account->getCountryCode() == 'IN') $bickey="SWIFT";
	
		// Get format of bank account according to its country
		$usedetailedbban=$account->useDetailedBBAN();
	
		//$onlynumber=0; $usedetailedbban=1; // For tests
		if ($usedetailedbban)
		{
			$savcurx=$curx;
	
			if (empty($onlynumber))
			{
				$pdf->SetFont('','',$default_font_size - $diffsizecontent);
				$pdf->SetXY($curx, $cury);
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Bank").': ' . $outputlangs->convToOutputCharset($account->bank), 0, 'L', 0);
				$cury+=3;
			}
	
			if (empty($conf->global->PDF_BANK_HIDE_NUMBER_SHOW_ONLY_BICIBAN))    // Note that some countries still need bank number, BIC/IBAN not enought for them
			{
			    // Note:
			    // bank = code_banque (FR), sort code (GB, IR. Example: 12-34-56)
			    // desk = code guichet (FR), used only when $usedetailedbban = 1
			    // number = account number
			    // key = check control key used only when $usedetailedbban = 1
	    		if (empty($onlynumber)) $pdf->line($curx+1, $cury+1, $curx+1, $cury+6);
	    
	
				foreach ($account->getFieldsToShow() as $val)
				{
					$pdf->SetXY($curx, $cury+4);
					$pdf->SetFont('','',$default_font_size - 3);
	
					if ($val == 'BankCode') {
						// Bank code
						$tmplength = 16;
						$content = $account->code_banque;
					} elseif ($val == 'DeskCode') {
						// Desk
						$tmplength = 16;
						$content = $account->code_guichet;
					} elseif ($val == 'BankAccountNumber') {
						// Number
						$tmplength = 22;
						$content = $account->number;
					} elseif ($val == 'BankAccountNumberKey') {
						// Key
						$tmplength = 10;
						$content = $account->cle_rib;
					} else {
						dol_print_error($this->db, 'Unexpected value for getFieldsToShow: '.$val);
						break;
					}
	
					$pdf->MultiCell($tmplength, 3, $outputlangs->convToOutputCharset($content), 0, 'C', 0);
					$pdf->SetXY($curx, $cury + 1);
					$curx += $tmplength;
					$pdf->SetFont('', 'B', $default_font_size - 4);
					$pdf->MultiCell($tmplength, 3, $outputlangs->transnoentities($val), 0, 'C', 0);
					if (empty($onlynumber)) {
						$pdf->line($curx, $cury + 1, $curx, $cury + 7);
					}
	    		}
	    
	    		$curx=$savcurx;
	    		$cury+=8;
			}
		}
		else
		{
			$pdf->SetFont('','B',$default_font_size - $diffsizecontent);
			$pdf->SetXY($curx, $cury);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("Bank").': ' . $outputlangs->convToOutputCharset($account->bank), 0, 'L', 0);
			$cury+=3;
	
			$pdf->SetFont('','B',$default_font_size - $diffsizecontent);
			$pdf->SetXY($curx, $cury);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("BankAccountNumber").': ' . $outputlangs->convToOutputCharset($account->number), 0, 'L', 0);
			$cury+=3;
	
			if ($diffsizecontent <= 2) $cury+=1;
		}
	
		$pdf->SetFont('','',$default_font_size - $diffsizecontent);
	
		if (empty($onlynumber) && ! empty($account->domiciliation))
		{
			$pdf->SetXY($curx, $cury);
			$val=$outputlangs->transnoentities("Residence").': ' . $outputlangs->convToOutputCharset($account->domiciliation);
			$pdf->MultiCell(100, 3, $val, 0, 'L', 0);
			//$nboflines=dol_nboflines_bis($val,120);
			//$cury+=($nboflines*3)+2;
			$tmpy=$pdf->getStringHeight(100, $val);
			$cury+=$tmpy;
		}
	
		if (! empty($account->proprio))
		{
			$pdf->SetXY($curx, $cury);
			$val=$outputlangs->transnoentities("BankAccountOwner").': ' . $outputlangs->convToOutputCharset($account->proprio);
			$pdf->MultiCell(100, 3, $val, 0, 'L', 0);
			$tmpy=$pdf->getStringHeight(100, $val);
			$cury+=$tmpy;
		}
	
		else if (! $usedetailedbban) $cury+=1;
	
		// Use correct name of bank id according to country
		$ibankey = FormBank::getIBANLabel($account);
	
		if (! empty($account->iban))
		{
			//Remove whitespaces to ensure we are dealing with the format we expect
			$ibanDisplay_temp = str_replace(' ', '', $outputlangs->convToOutputCharset($account->iban));
			$ibanDisplay = "";
	
			$nbIbanDisplay_temp = dol_strlen($ibanDisplay_temp);
			for ($i = 0; $i < $nbIbanDisplay_temp; $i++)
			{
				$ibanDisplay .= $ibanDisplay_temp[$i];
				if($i%4 == 3 && $i > 0)	$ibanDisplay .= " ";
			}
	
			$pdf->SetFont('','B',$default_font_size - 3);
			$pdf->SetXY($curx, $cury);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities($ibankey).': ' . $ibanDisplay, 0, 'L', 0);
			$cury+=3;
		}
	
		if (! empty($account->bic))
		{
			$pdf->SetFont('','B',$default_font_size - 3);
			$pdf->SetXY($curx, $cury);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities($bickey).': ' . $outputlangs->convToOutputCharset($account->bic), 0, 'L', 0);
		}

		return $pdf->getY();
	}
}

