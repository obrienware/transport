<?php
require_once 'class.fpdf.php';
/**
 * The following has been extensively modified from the original to adhere
 * as far as possible to current coding standards AND to work with PHP8+
 */


//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec ($couleur = "#000000")
{
  $R = substr($couleur, 1, 2);
  $rouge = hexdec($R);
  $V = substr($couleur, 3, 2);
  $vert = hexdec($V);
  $B = substr($couleur, 5, 2);
  $bleu = hexdec($B);
  $tbl_couleur = array();
  $tbl_couleur['R'] = $rouge;
  $tbl_couleur['G'] = $vert;
  $tbl_couleur['B'] = $bleu;
  return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm ($px)
{
  return $px * 25.4 / 72;
}

function txtentities ($html)
{
  $trans = get_html_translation_table(HTML_ENTITIES);
  $trans = array_flip($trans);
  return strtr($html, $trans);
}
////////////////////////////////////






class PDF extends FPDF
{
	protected $widths;
	protected $aligns;
	public $row_height = 17;
	public $lm = 30;
	public $rm = 30;

	protected $col = 0;
	protected $y0;
	protected $doColumns = false;
	protected $columns = 2;
	protected $gutter = 5;
	protected $colWidth = 90;


	//variables of html parser
	protected $B;
	protected $I;
	protected $U;
	protected $HREF;
	protected $fontList;
	protected $issetfont;
	protected $issetcolor;

  protected $gradients;

  public $fontlist;
  public $footnote;


	function __construct ($orientation = 'P', $unit = 'pt', $format = 'letter')
	{
		//Call parent constructor
		parent::__construct($orientation, $unit, $format);

		//Initialization
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
		$this->fontlist = ["arial","times","courier","helvetica","symbol"];
		$this->issetfont=false;
		$this->issetcolor=false;
	
    $this->gradients=array();
		$this->AliasNbPages();
	}

	function Footer()
	{
		$this->SetLeftMargin($this->lm);
		$this->SetRightMargin($this->rm);

		// From bottom...
		$this->SetY(-50);
		$this->SetX(-1 * $this->rm);
		$this->setDrawColor(127, 127, 127);
		$this->Line($this->lm, $this->GetY()+2, $this->GetX(), $this->GetY()+2);
		$this->SetX($this->lm);

		$this->SetFont('Helvetica', 'I', 8);

		//Print centered page number
		$this->Cell(0, 20, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
		$this->Cell(0, 20, 'powered by obrienware.com', 0, 0, 'R');
		$this->SetX(10); $this->Cell(0, 20, $this->footnote, 0, 0, 'L');
	}

	function Header()
	{
		if ($this->doColumns) {
			$this->y0 = 10;
			$this->SetCol(0);
		}
	}


  function LinearGradient($x, $y, $w, $h, $col1 = [], $col2 = [], $coords = [0, 0, 1, 0]){
    $this->Clip($x, $y, $w, $h);
    $this->Gradient(2, $col1, $col2, $coords);
  }

  function RadialGradient($x, $y, $w, $h, $col1 = [], $col2 = [], $coords = [0.5, 0.5, 0.5, 0.5, 1]){
    $this->Clip($x, $y, $w, $h);
    $this->Gradient(3, $col1, $col2, $coords);
  }

    function CoonsPatchMesh($x, $y, $w, $h, $col1=array(), $col2=array(), $col3=array(), $col4=array(), $coords=array(0.00,0.0,0.33,0.00,0.67,0.00,1.00,0.00,1.00,0.33,1.00,0.67,1.00,1.00,0.67,1.00,0.33,1.00,0.00,1.00,0.00,0.67,0.00,0.33), $coords_min=0, $coords_max=1){
        $this->Clip($x,$y,$w,$h);        
        $n = count($this->gradients)+1;
        $this->gradients[$n]['type']=6; //coons patch mesh
        //check the coords array if it is the simple array or the multi patch array
        if(!isset($coords[0]['f'])){
            //simple array -> convert to multi patch array
            if(!isset($col1[1]))
                $col1[1]=$col1[2]=$col1[0];
            if(!isset($col2[1]))
                $col2[1]=$col2[2]=$col2[0];
            if(!isset($col3[1]))
                $col3[1]=$col3[2]=$col3[0];
            if(!isset($col4[1]))
                $col4[1]=$col4[2]=$col4[0];
            $patch_array[0]['f']=0;
            $patch_array[0]['points']=$coords;
            $patch_array[0]['colors'][0]['r']=$col1[0];
            $patch_array[0]['colors'][0]['g']=$col1[1];
            $patch_array[0]['colors'][0]['b']=$col1[2];
            $patch_array[0]['colors'][1]['r']=$col2[0];
            $patch_array[0]['colors'][1]['g']=$col2[1];
            $patch_array[0]['colors'][1]['b']=$col2[2];
            $patch_array[0]['colors'][2]['r']=$col3[0];
            $patch_array[0]['colors'][2]['g']=$col3[1];
            $patch_array[0]['colors'][2]['b']=$col3[2];
            $patch_array[0]['colors'][3]['r']=$col4[0];
            $patch_array[0]['colors'][3]['g']=$col4[1];
            $patch_array[0]['colors'][3]['b']=$col4[2];
        }
        else{
            //multi patch array
            $patch_array=$coords;
        }
        $bpcd=65535; //16 BitsPerCoordinate
        //build the data stream
        for($i=0;$i<count($patch_array);$i++){
            $this->gradients[$n]['stream'].=chr($patch_array[$i]['f']); //start with the edge flag as 8 bit
            for($j=0;$j<count($patch_array[$i]['points']);$j++){
                //each point as 16 bit
                $patch_array[$i]['points'][$j]=(($patch_array[$i]['points'][$j]-$coords_min)/($coords_max-$coords_min))*$bpcd;
                if($patch_array[$i]['points'][$j]<0) $patch_array[$i]['points'][$j]=0;
                if($patch_array[$i]['points'][$j]>$bpcd) $patch_array[$i]['points'][$j]=$bpcd;
                $this->gradients[$n]['stream'].=chr(floor($patch_array[$i]['points'][$j]/256));
                $this->gradients[$n]['stream'].=chr(floor($patch_array[$i]['points'][$j]%256));
            }
            for($j=0;$j<count($patch_array[$i]['colors']);$j++){
                //each color component as 8 bit
                $this->gradients[$n]['stream'].=chr($patch_array[$i]['colors'][$j]['r']);
                $this->gradients[$n]['stream'].=chr($patch_array[$i]['colors'][$j]['g']);
                $this->gradients[$n]['stream'].=chr($patch_array[$i]['colors'][$j]['b']);
            }
        }
        //paint the gradient
        $this->_out('/Sh'.$n.' sh');
        //restore previous Graphic State
        $this->_out('Q');
    }

    function Clip($x,$y,$w,$h){
        //save current Graphic State
        $s='q';
        //set clipping area
        $s.=sprintf(' %.2f %.2f %.2f %.2f re W n', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k);
        //set up transformation matrix for gradient
        $s.=sprintf(' %.3f 0 0 %.3f %.3f %.3f cm', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k);
        $this->_out($s);
    }

    function Gradient($type, $col1, $col2, $coords){
        $n = count($this->gradients)+1;
        $this->gradients[$n]['type']=$type;
        if(!isset($col1[1]))
            $col1[1]=$col1[2]=$col1[0];
        $this->gradients[$n]['col1']=sprintf('%.3f %.3f %.3f',($col1[0]/255),($col1[1]/255),($col1[2]/255));
        if(!isset($col2[1]))
            $col2[1]=$col2[2]=$col2[0];
        $this->gradients[$n]['col2']=sprintf('%.3f %.3f %.3f',($col2[0]/255),($col2[1]/255),($col2[2]/255));
        $this->gradients[$n]['coords']=$coords;
        //paint the gradient
        $this->_out('/Sh'.$n.' sh');
        //restore previous Graphic State
        $this->_out('Q');
    }

    function _putshaders(){
        foreach($this->gradients as $id=>$grad){  
            if($grad['type']==2 || $grad['type']==3){
                $this->_newobj();
                $this->_out('<<');
                $this->_out('/FunctionType 2');
                $this->_out('/Domain [0.0 1.0]');
                $this->_out('/C0 ['.$grad['col1'].']');
                $this->_out('/C1 ['.$grad['col2'].']');
                $this->_out('/N 1');
                $this->_out('>>');
                $this->_out('endobj');
                $f1=$this->n;
            }
            
            $this->_newobj();
            $this->_out('<<');
            $this->_out('/ShadingType '.$grad['type']);
            $this->_out('/ColorSpace /DeviceRGB');
            if($grad['type']=='2'){
                $this->_out(sprintf('/Coords [%.3f %.3f %.3f %.3f]',$grad['coords'][0],$grad['coords'][1],$grad['coords'][2],$grad['coords'][3]));
                $this->_out('/Function '.$f1.' 0 R');
                $this->_out('/Extend [true true] ');
                $this->_out('>>');
            }
            elseif($grad['type']==3){
                //x0, y0, r0, x1, y1, r1
                //at this this time radius of inner circle is 0
                $this->_out(sprintf('/Coords [%.3f %.3f 0 %.3f %.3f %.3f]',$grad['coords'][0],$grad['coords'][1],$grad['coords'][2],$grad['coords'][3],$grad['coords'][4]));
                $this->_out('/Function '.$f1.' 0 R');
                $this->_out('/Extend [true true] ');
                $this->_out('>>');
            }
            elseif($grad['type']==6){
                $this->_out('/BitsPerCoordinate 16');
                $this->_out('/BitsPerComponent 8');
                $this->_out('/Decode[0 1 0 1 0 1 0 1 0 1]');
                $this->_out('/BitsPerFlag 8');
                $this->_out('/Length '.strlen($grad['stream']));
                $this->_out('>>');
                $this->_putstream($grad['stream']);
            }
            $this->_out('endobj');
            $this->gradients[$id]['id']=$this->n;
        }
    }

    // function _putresourcedict(){
    //     parent::_putresourcedict();
    //     $this->_out('/Shading <<');
    //     foreach($this->gradients as $id=>$grad)
    //          $this->_out('/Sh'.$id.' '.$grad['id'].' 0 R');
    //     $this->_out('>>');
    // }

    function _putresources(){
        $this->_putshaders();
        parent::_putresources();
    }




    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' or $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }


	function AcceptPageBreak()
	{
		if( !$this->doColumns ) return true;		// Default action if we are not doing columns
	
		//Method accepting or not automatic page break
		if( $this->col < ($this->columns-1) )
		{
		    //Go to next column
		    $this->SetCol($this->col+1);
		    //Set ordinate to top
		    $this->SetY($this->y0);
		    //Keep on page
		    return false;
		}
		else
		{
		    //Page break
		    return true;
		}
	}

	function SetCol($col)
	{
		//Set position at a given column
		$this->col = $col;
		$x = $this->lm + ( $col * ($this->colWidth+$this->gutter) );
		$rm = $this->rm + ( ($this->columns-1 -$col) * ($this->colWidth+$this->gutter) );
		$this->SetLeftMargin($x);
		$this->SetRightMargin( $rm );
		$this->SetX($x);
	}

	function startColumns()
	{
		$this->y0 = $this->GetY();
		$this->doColumns = true;
		$this->colWidth = ((210-$this->lm-$this->rm) - ( ($this->columns-1) * $this->gutter )) / $this->columns;
		$this->SetCol(0);
	}

	function stopColumns()
	{
		$this->rm = 10;
		$this->lm = 10;
		$this->doColumns = false;
		$this->SetRightMargin( $this->rm );
		$this->SetLeftMargin( $this->lm );
	}





	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}

	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns=$a;
	}

	function Row($data)
	{
		//Calculate the height of the row
		$nb = 0;
		for($i = 0; $i < count($data); $i++) {
		  $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    }
		$h = $this->row_height * $nb;

		//Issue a page break first if needed
		$this->CheckPageBreak($h);

		//Draw the cells of the row
		for ($i = 0; $i < count($data); $i++) {
		  $w=$this->widths[$i];
		  $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

		  //Save the current position
		  $x=$this->GetX();
		  $y=$this->GetY();

		  //Draw the border
		  $this->Rect($x,$y,$w,$h);
			//$this->setDrawColor( 127, 127, 127 );
			//$this->Line($x, $y+$h, $x+$w, $y+$h );
		  
      //Print the text
		  $this->MultiCell($w, $this->row_height, $data[$i], 0, $a);
		  //Put the position to the right of the cell
		  $this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function HRow($data)
	{
		//Calculate the height of the row
		$nb = 0;
		for ($i = 0; $i < count($data); $i++) {
		  $nb = max($nb, $this->NbLines($this->widths[$i],$data[$i]));
    }
		$h = $this->row_height * $nb;

		//Issue a page break first if needed
		$this->CheckPageBreak($h);

		//Draw the cells of the row
		for ($i = 0; $i < count($data); $i++) {
		  $w = $this->widths[$i];
		  $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

		  //Save the current position
		  $x = $this->GetX();
		  $y = $this->GetY();

		  //Draw the border
			$this->setDrawColor(255, 255, 255);
			$this->setFillColor(230, 230, 230);
		  $this->Rect($x, $y, $w, $h, "FD");
			//$this->Line($x, $y+$h, $x+$w, $y+$h );
		  
      //Print the text
		  $this->MultiCell($w, $this->row_height, $data[$i], 0, $a);
		  
      //Put the position to the right of the cell
		  $this->SetXY($x + $w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
		    $this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
		    $w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s = ($txt) ? str_replace("\r",'',$txt) : '';
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
		    $nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
		    $c=$s[$i];
		    if($c=="\n")
		    {
		        $i++;
		        $sep=-1;
		        $j=$i;
		        $l=0;
		        $nl++;
		        continue;
		    }
		    if($c==' ')
		        $sep=$i;
		    $l+=$cw[$c];
		    if($l>$wmax)
		    {
		        if($sep==-1)
		        {
		            if($i==$j)
		                $i++;
		        }
		        else
		            $i=$sep+1;
		        $sep=-1;
		        $j=$i;
		        $l=0;
		        $nl++;
		    }
		    else
		        $i++;
		}
		return $nl;
	}




	//////////////////////////////////////
	//html parser

	function WriteHTML($html)
	{
		$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //remove all unsupported tags
		$html=str_replace("\n",' ',$html); //replace carriage returns by spaces
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explodes the string
		foreach($a as $i=>$e)
		{
		    if($i%2==0)
		    {
		        //Text
		        if($this->HREF)
		            $this->PutLink($this->HREF,$e);
		        else
		            $this->Write(5,stripslashes(txtentities($e)));
		    }
		    else
		    {
		        //Tag
		        if($e[0]=='/')
		            $this->CloseTag(strtoupper(substr($e,1)));
		        else
		        {
		            //Extract attributes
		            $a2=explode(' ',$e);
		            $tag=strtoupper(array_shift($a2));
		            $attr=array();
		            foreach($a2 as $v)
                  // if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                  if (preg_match('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
		                    $attr[strtoupper($a3[1])]=$a3[2];
		            $this->OpenTag($tag,$attr);
		        }
		    }
		}
	}

	function OpenTag($tag,$attr)
	{
		//Opening tag
		switch($tag){
		    case 'STRONG':
		        $this->SetStyle('B',true);
		        break;
		    case 'EM':
		        $this->SetStyle('I',true);
		        break;
		    case 'B':
		    case 'I':
		    case 'U':
		        $this->SetStyle($tag,true);
		        break;
		    case 'A':
		        $this->HREF=$attr['HREF'];
		        break;
		    case 'IMG':
		        if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
		            if(!isset($attr['WIDTH']))
		                $attr['WIDTH'] = 0;
		            if(!isset($attr['HEIGHT']))
		                $attr['HEIGHT'] = 0;
		            $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
		        }
		        break;
		    case 'TR':
		    case 'BLOCKQUOTE':
		    case 'BR':
		        $this->Ln(5);
		        break;
		    case 'P':
		        $this->Ln(10);
		        break;
		    case 'FONT':
		        if (isset($attr['COLOR']) and $attr['COLOR']!='') {
		            $coul=hex2dec($attr['COLOR']);
		            $this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
		            $this->issetcolor=true;
		        }
		        if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
		            $this->SetFont(strtolower($attr['FACE']));
		            $this->issetfont=true;
		        }
		        break;
		}
	}

	function CloseTag($tag)
	{
		//Closing tag
		if($tag=='STRONG')
		    $tag='B';
		if($tag=='EM')
		    $tag='I';
		if($tag=='B' or $tag=='I' or $tag=='U')
		    $this->SetStyle($tag,false);
		if($tag=='A')
		    $this->HREF='';
		if($tag=='FONT'){
		    if ($this->issetcolor==true) {
		        $this->SetTextColor(0);
		    }
		    if ($this->issetfont) {
		        $this->SetFont('arial');
		        $this->issetfont=false;
		    }
		}
	}

	function SetStyle($tag,$enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
		    if($this->$s>0)
		        $style.=$s;
		$this->SetFont('',$style);
	}

	function PutLink($URL,$txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}



}
