Enter file contents here
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta content="text/html;charset=ISO-8859-1" http-equiv="Content-Type">
<link href="mine.css" rel="stylesheet" type="text/css">
<?php
define ("PREFIX"     , "LILAC_RDD_");
define ("HEAD"       , "\ntypedef struct\n{\n");
define ("END"        , "}\n__PACKING_ATTRIBUTE_STRUCT_END__ ");
define ("FIELD"      , "\t__PACKING_ATTRIBUTE_FIELD_LEVEL__;\n");
define ("COMMENT"    , "/* This is an automated file. Do not edit its contents. */\n\n");
 $GLOBALS['output'] = COMMENT;
 //$GLOBALS['output_macro'] = COMMENT;
 $GLOBALS['output_fw'] = COMMENT;
 $GLOBALS['output_runner'] = COMMENT;
 $GLOBALS['output_map'] = COMMENT;
 $GLOBALS['unions']=array();

 function generate_table_struct($tabentry)
 {
     if (($tabentry->len > 1) || ($tabentry->len2 > 1) || ($tabentry->len3 > 1)) 
     {
		 //$tabentry->FWtable_print();
         $entarray = $tabentry->entries;
         $n = PREFIX.strtoupper($tabentry->name);
         writeToFile(HEAD);
         //foreach ($entarray as $e)
		 if (count($entarray) > 1) 
             $txt = sprintf("\t%s_DTS\tentry","BL_".PREFIX.strtoupper($tabentry->union));
         else
             $txt = sprintf("\t%s_DTS\tentry",PREFIX.strtoupper($entarray[0]->name));
         if ($tabentry->len > 1) 
         {
             $txt .= sprintf("[ %s_SIZE ]",$n);
         }
         if ($tabentry->len2 > 1) 
         {
             $txt .= sprintf("[ %s_SIZE2 ]",$n);
         }
         if ($tabentry->len3 > 1) 
         {
             $txt .= sprintf("[ %s_SIZE3 ]",$n);
         }
         writeToFile($txt.";\n");
         writeToFile(END.PREFIX.strtoupper($tabentry->name)."_DTS;\n\n");
         //echo "Entry ".$tabentry->name.".".$entarray[0]->name." length=".$entarray[0]->length."<br>";
     }
 }
 function regular16($field,$txtR,$txtW)
 {
     $I = "";
     $i = "";
	 if ($field->ar == true) 
     {
         $I = "I_";
         $i = "i, ";
     }
     $txtRB = "BL_FIELD_MREAD_".$I;
     $txtWB = "BL_FIELD_MWRITE_".$I;
     $o = $field->hword;
     if ($o == 0) 
     {
         $tmpr = sprintf("16((stt_uint8 *)p, %d, %d, %sr )\n",$field->from,$field->size,$i);
         $tmpw = sprintf("16((stt_uint8 *)p, %d, %d, %sv )\n",$field->from,$field->size,$i);
     }
	 else
     {
         $tmpr = sprintf("16((stt_uint8 *)p + %d, %d, %d, %sr )\n",$o,$field->from,$field->size,$i);
         $tmpw = sprintf("16((stt_uint8 *)p + %d, %d, %d, %sv )\n",$o,$field->from,$field->size,$i);
     }
     if ($field->size == 8) 
     {
         if ($field->from%8==0) // aligned to byte
         {
             $txtRB = "BL_MREAD_".$I;
             $txtWB = "BL_MWRITE_".$I;
             if ($o == 0) 
             {
                 $tmpr = sprintf("8((stt_uint8 *)p, %sr )\n",$i);
                 $tmpw = sprintf("8((stt_uint8 *)p, %sv )\n",$i);
             }
             else
             {
                 $tmpr = sprintf("8((stt_uint8 *)p + %d, %sr )\n",$o,$i);
                 $tmpw = sprintf("8((stt_uint8 *)p + %d, %sv )\n",$o,$i);
             }
             writeToFile($txtR);
             writeToFile($txtRB.$tmpr);
             writeToFile($txtW);
             writeToFile($txtWB.$tmpw);
             return;
         }
     }
     if ($field->size < 8) 
     {
         if (floor($field->from/8)==floor(($field->from+$field->size-1)/8)) // aligned to byte
         {
             if ($o == 0) 
             {
                 $tmpr = sprintf("8((stt_uint8 *)p, %d, %d, %sr )\n",$field->from%8,$field->size,$i);
                 $tmpw = sprintf("8((stt_uint8 *)p, %d, %d, %sv )\n",$field->from%8,$field->size,$i);
             }
             else
             {
                 $tmpr = sprintf("8((stt_uint8 *)p + %d, %d, %d, %sr )\n",$o,$field->from%8,$field->size,$i);
                 $tmpw = sprintf("8((stt_uint8 *)p + %d, %d, %d, %sv )\n",$o,$field->from%8,$field->size,$i);
             }
             writeToFile($txtR);
             writeToFile($txtRB.$tmpr);
             writeToFile($txtW);
             writeToFile($txtWB.$tmpw);
             return;
         }
     }
     if ($field->size == 16) 
     {
         $txtRB = "BL_MREAD_".$I;
         $txtWB = "BL_MWRITE_".$I;
         if ($o == 0) 
         {
             $tmpr = sprintf("16((stt_uint8 *)p, %sr )\n",$i);
             $tmpw = sprintf("16((stt_uint8 *)p, %sv )\n",$i);
         }
         else
         {
             $tmpr = sprintf("16((stt_uint8 *)p + %d, %sr )\n",$o,$i);
             $tmpw = sprintf("16((stt_uint8 *)p + %d, %sv )\n",$o,$i);
         }
     }
     writeToFile($txtR);
     writeToFile($txtRB.$tmpr);
     writeToFile($txtW);
     writeToFile($txtWB.$tmpw);
     return;
 }
 function regular32($field,$txtR,$txtW)
 {
     $I = "";
     $i = "";
	 if ($field->ar == true) 
     {
         $I = "I_";
         $i = "i, ";
     }
     $txtRB = "BL_FIELD_MREAD_".$I;
     $txtWB = "BL_FIELD_MWRITE_".$I;

     if ($field->hword == 0) 
     {
         $tmpr = sprintf("32((stt_uint8 *)p, %d, %d, %sr )\n",$field->from,$field->size,$i);
         $tmpw = sprintf("32((stt_uint8 *)p, %d, %d, %sv )\n",$field->from,$field->size,$i);
     }
	 else
     {
         $tmpr = sprintf("32((stt_uint8 *)p + %d, %d, %d, %sr )\n",$field->word*4,$field->from,$field->size,$i);
         $tmpw = sprintf("32((stt_uint8 *)p + %d, %d, %d, %sv )\n",$field->word*4,$field->from,$field->size,$i);
     }
     if ($field->size == 8) 
     {
         if ($field->from%8==0) // aligned to byte
         {
             $txtRB = "BL_MREAD_".$I;
             $txtWB = "BL_MWRITE_".$I;
             if ($field->hword == 0) 
             {
                 $tmpr = sprintf("8((stt_uint8 *)p, %sr )\n",$i);
                 $tmpw = sprintf("8((stt_uint8 *)p, %sv )\n",$i);
             }
             else
             {
                 $tmpr = sprintf("8((stt_uint8 *)p + %d, %sr )\n",$field->hword,$i);
                 $tmpw = sprintf("8((stt_uint8 *)p + %d, %sv )\n",$field->hword,$i);
             }
             writeToFile($txtR);
             writeToFile($txtRB.$tmpr);
             writeToFile($txtW);
             writeToFile($txtWB.$tmpw);
             return;
         }
     }
     if ($field->size == 16) 
     {
         if ($field->from%16==0) // aligned to byte
         {
             $txtRB = "BL_MREAD_".$I;
             $txtWB = "BL_MWRITE_".$I;
             $o = floor($field->hword/2)*2;
             if ($o == 0) 
             {
                 $tmpr = sprintf("16((stt_uint8 *)p, %sr )\n",$i);
                 $tmpw = sprintf("16((stt_uint8 *)p, %sv )\n",$i);
             }
             else
             {
                 $tmpr = sprintf("16((stt_uint8 *)p + %d, %sr )\n",$o,$i);
                 $tmpw = sprintf("16((stt_uint8 *)p + %d, %sv )\n",$o,$i);
             }
             writeToFile($txtR);
             writeToFile($txtRB.$tmpr);
             writeToFile($txtW);
             writeToFile($txtWB.$tmpw);
             return;
         }
     }
     if ($field->size < 8) 
     {
         if (floor($field->from/8)==floor(($field->from+$field->size-1)/8)) // aligned to byte
         {
             if ($field->hword == 0) 
             {
                 $tmpr = sprintf("8((stt_uint8 *)p, %d, %d, %sr )\n",$field->from%8,$field->size,$i);
                 $tmpw = sprintf("8((stt_uint8 *)p, %d, %d, %sv )\n",$field->from%8,$field->size,$i);
             }
             else
             {
                 $tmpr = sprintf("8((stt_uint8 *)p + %d, %d, %d, %sr )\n",$field->hword,$field->from%8,$field->size,$i);
                 $tmpw = sprintf("8((stt_uint8 *)p + %d, %d, %d, %sv )\n",$field->hword,$field->from%8,$field->size,$i);
             }
             writeToFile($txtR);
             writeToFile($txtRB.$tmpr);
             writeToFile($txtW);
             writeToFile($txtWB.$tmpw);
             return;
         }
     }
     if ($field->size < 16) 
     {
         if (floor($field->from/16)==floor(($field->from+$field->size-1)/16)) // aligned to byte
         {
             $o = floor($field->hword/2)*2;
             if ($o == 0) 
             {
                 $tmpr = sprintf("16((stt_uint8 *)p, %d, %d, %sr )\n",$field->from%16,$field->size,$i);
                 $tmpw = sprintf("16((stt_uint8 *)p, %d, %d, %sv )\n",$field->from%16,$field->size,$i);
             }
             else
             {
                 $tmpr = sprintf("16((stt_uint8 *)p + %d, %d, %d, %sr )\n",$o,$field->from%16,$field->size,$i);
                 $tmpw = sprintf("16((stt_uint8 *)p + %d, %d, %d, %sv )\n",$o,$field->from%16,$field->size,$i);
             }
             writeToFile($txtR);
             writeToFile($txtRB.$tmpr);
             writeToFile($txtW);
             writeToFile($txtWB.$tmpw);
             return;
         }
     }
     if ($field->size == 32) 
     {
         $txtRB = "BL_MREAD_".$I;
         $txtWB = "BL_MWRITE_".$I;
		 $o = $field->word*4;
         if ($o == 0) 
         {
             $tmpr = sprintf("32((stt_uint8 *)p, %sr )\n",$i);
             $tmpw = sprintf("32((stt_uint8 *)p, %sv )\n",$i);
         }
         else
         {
             $tmpr = sprintf("32((stt_uint8 *)p + %d, %sr )\n",$o,$i);
             $tmpw = sprintf("32((stt_uint8 *)p + %d, %sv )\n",$o,$i);
         }
     }
     writeToFile($txtR);
     writeToFile($txtRB.$tmpr);
     writeToFile($txtW);
     writeToFile($txtWB.$tmpw);
     return;
 }
 // entire structure is 8/16/32 bits long
 function regular($field,$txtR,$txtW,$len)
 {
     $I = "";
     $i = "";
	 if ($field->ar == true) 
     {
         $I = "I_";
         $i = "i, ";
     }
     if ($field->size == $len) 
     {
         $txtRB = "BL_MREAD_".$I;
         $txtWB = "BL_MWRITE_".$I;
         $tmpr = sprintf("%d((stt_uint8 *)p, %sr )\n",$len,$i);
         $tmpw = sprintf("%d((stt_uint8 *)p, %sv )\n",$len,$i);
     }
     else 
     {
        $txtRB = "BL_FIELD_MREAD_".$I;
        $txtWB = "BL_FIELD_MWRITE_".$I;
        $tmpr = sprintf("%d((stt_uint8 *)p, %d, %d, %sr )\n",$len,$field->from,$field->size,$i);
        $tmpw = sprintf("%d((stt_uint8 *)p, %d, %d, %sv )\n",$len,$field->from,$field->size,$i);
     }
     writeToFile($txtR);
     writeToFile($txtRB.$tmpr);
     writeToFile($txtW);
     writeToFile($txtWB.$tmpw);
 }
 function composed_macro($field,$txtR,$txtW)
 {
     $I = "";
     $i = "";
     $len = "32";
	 if ($field->ar == true) 
     {
         $I = "I_";
         $i = "i, ";
         switch ($field->size) 
         {
         case 8:
             $len="8";
             break;
         case 16:
             $len="16";
             break;
         }
     }
     if ($field->dword < 0) // aligned to 32 bits
     {
         regular32($field,$txtR,$txtW);
     }
     else
     {
        $off = $field->dword*4;
        $off1 = $off+4;
        $size2 = 32 - $field->from;
        $size1 = $field->size - $size2;
        $mm = 1;
        for ($i=1; $i < $size2; $i++) 
        {
            $mm = (($mm << 1) | 1);
        }
        $m = "0x".base_convert($mm,10,16);
        if ($off == 0) 
        {
            $txtRB = "{ stt_uint32 temp; BL_FIELD_MREAD_32(((stt_uint8 *)p), 0, ".$size1.", temp ); r = temp << ".$size1."; ";
            $tmpr = "BL_FIELD_MREAD_32(((stt_uint8 *)p + ".$off1."), ".$field->from.", ".$size2.", temp ); r = r | temp; }\n";
            $txtWB = "{ BL_FIELD_MWRITE_32(((stt_uint8 *)p), 0, ".$size1.", (v >> ".$size2."); ";
            $tmpw = "BL_FIELD_MWRITE_32(((stt_uint8 *)p + ".$off1."), ".$field->from.", ".$size2.", (v & ".$m.") ); }\n";
        }
        else
        {
            $txtRB = "{ stt_uint32 temp; BL_FIELD_MREAD_32(((stt_uint8 *)p + ".$off."), 0, ".$size1.", temp ); r = temp << ".$size1."; ";
            $tmpr = "BL_FIELD_MREAD_32(((stt_uint8 *)p + ".$off1."), ".$field->from.", ".$size2.", temp ); r = r | temp; }\n";
            $txtWB = "{ BL_FIELD_MWRITE_32(((stt_uint8 *)p + ".$off."), 0, ".$size1.", (v >> ".$size2."); ";
            $tmpw = "BL_FIELD_MWRITE_32(((stt_uint8 *)p + ".$off1."), ".$field->from.", ".$size2.", (v & ".$m.") ); }\n";
        }
        writeToFile($txtR);
        writeToFile($txtRB.$tmpr);
        writeToFile($txtW);
        writeToFile($txtWB.$tmpw);
     }
 }
 function generate_entry_struct($tabname,$entry)
 {
     $l=count($entry->fields);
     if ($l == 0) return;
     
     $res = 0;
     $maxl = strlen("reserved")+2;
     $qq = "";
     for ($index=0; $index<$l; $index++) 
     {
         $field = $entry->fields[$index];
		 if ($field->ar==true)
         {
             $qq = PREFIX.strtoupper($entry->name)."_".strtoupper($field->name)."_NUMBER";
             $txt = sprintf("#define %s\t%d\n",$qq,$field->len);
             writeToFile($txt);
         }
     }
     for ($index=0; $index<$l; $index++) 
     {
         $field = $entry->fields[$index];
		 if ($field->ar==false)
         {
             if (strlen($field->name) > $maxl) 
                 $maxl = strlen($field->name);
         }
         else
         {
             $qq = PREFIX.strtoupper($entry->name)."_".strtoupper($field->name)."_NUMBER";
             $q = sprintf("%s[%s]",$field->name,$qq);
             if (strlen($q) > $maxl) 
                 $maxl = strlen($q);
         }
     }
     writeToFile(HEAD);
     $myarray = array();
     $mytemp = array();
     $cur=$entry->fields[0]->from;
     $curw = $entry->fields[0]->word;
     for ($index=0; $index<$l; $index++) 
     {
         $field = $entry->fields[$index];
         //$field->FWfield_print();
         if ($field->word != $curw) 
         {
             //$myarray = array_merge($myarray,array_reverse($mytemp));
             $myarray = array_merge($myarray,$mytemp);
             $mytemp = array();
             $curw = $field->word;
         }
         switch ($field->size)
         {
         case 8:
         case 16:
         case 32:

             if (($cur % $field->size == 0) || ($field->ar == true)) 
             {
                 $stt = sprintf("stt_uint%d",$field->size);
             }
             break;
         default:
             $stt = "stt_uint32";
             break;
         }
         $d = 32;
         if ($entry->length == 1) 
             $d = 8;
         else if ($entry->length == 2) 
             $d = 16;
         if ($cur<$field->from) 
         {
             $len = $field->from-$cur;
             $r = str_pad(sprintf("reserved%d",$res),$maxl);
             $txt = sprintf("\tstt_uint%d\t%s\t:%d%s",$d,$r,$len,FIELD);
             array_push($mytemp,$txt);
             $res++;
             $cur += $len;
         }
         if ($field->ar==false) 
         {
             $cur += $field->size;
             $txt = sprintf("\tstt_uint%d\t%s\t:%d%s",$d,str_pad(strtolower($field->name),$maxl),$field->size,FIELD);
         }
         else
         {
             $cur += $field->size*$field->len;
             $qq = PREFIX.strtoupper($entry->name)."_".strtoupper($field->name)."_NUMBER";
             $q = sprintf("%s[%s]",strtolower($field->name),$qq);
             $txt = sprintf("\t%s\t%s;\n",$stt,$q);
         }
         array_push($mytemp,$txt);
     }
     
     //$myarray = array_merge($myarray,array_reverse($mytemp));
     $myarray = array_merge($myarray,$mytemp);
     $ll = count($myarray);
     for ($i=0; $i<$ll; $i++) 
     {
         writeToFile($myarray[$i]);
     }
     writeToFile(END.PREFIX.strtoupper($entry->name)."_DTS;\n\n");
     $field = $entry->fields[0];
	 $nn = strlen(strtoupper($entry->name)."_".strtoupper($field->name));
     $reglen = $field->size * $field->len;
     for ($i=1; $i<$l;$i++) 
     {
         $field = $entry->fields[$i];
         $nn1 = strlen(strtoupper($entry->name)."_".strtoupper($field->name));
         if ($nn1 > $nn) $nn = $nn1;
         $reglen += $field->size * $field->len;
     }
     // $reglen is the entire structure length (in bits)
     //if ($reglen <= 8) $reglen = 8;
     //else if ($reglen <= 16) $reglen = 16;
     //else if ($reglen <= 32) $reglen = 32;
     // $nn is the max length of the name
     $nn1 = $nn + 18 + 14 + 4; //#define LILAC_RDD_ + _WRITE( v, p ) + space
     for ($i=0; $i<$l;$i++) 
     {
         $field = $entry->fields[$i];
         if (strtolower(substr($field->name,0,strlen("reserved")))=="reserved") continue;
         
         $txt = "#define ".PREFIX.strtoupper($entry->name)."_".strtoupper($field->name);
         if ($field->ar == true) 
         {
             $txtR = str_pad($txt."_READ( r, p, i )",$nn1);
             $txtW = str_pad($txt."_WRITE( v, p, i )",$nn1);
         }
         else
         { 
             $txtR = str_pad($txt."_READ( r, p )",$nn1);
             $txtW = str_pad($txt."_WRITE( v, p )",$nn1);
         }
// choose right macro
         $macroRead = "";
         $macroWrite = "";
         switch ($reglen) 
         {
         case 8:
             regular($field,$txtR,$txtW,8);
             break;
         case 16:
             regular16($field,$txtR,$txtW);
             break;
         case 32:
             regular32($field,$txtR,$txtW);
             break;
         default:
             composed_macro($field,$txtR,$txtW);
             break;
         }
     }
     //writeToFile("\n\n");
     $nn2 = $nn + 8 + 9 + 1 + 6; //#define + _F_OFFSET + space + _MOD16
     $en = str_replace("_"," ",$entry->name);
     $writecomment = False;
     for ($i=0; $i<$l;$i++) 
     {
         $field = $entry->fields[$i];
         if (strtolower(substr($field->name,0,strlen("reserved")))=="reserved") continue;
         else 
         {
             $writecomment = True;
             break;
         }
     }
     if ($writecomment) 
         writeToFileRunner("/**** ".strtoupper($en)." ****/\n");
     for ($i=0; $i<$l;$i++) 
     {
         $field = $entry->fields[$i];
         if (strtolower(substr($field->name,0,strlen("reserved")))=="reserved") continue;
		 $ttxt = "#define ".strtoupper($entry->name)."_".strtoupper($field->name);
         $txt = $ttxt."_F_OFFSET ";
         $txt = str_pad($txt,$nn2);
         writeToFileRunner($txt."d.".$field->from."\n");
         $txt = $ttxt."_F_WIDTH ";
         $txt = str_pad($txt,$nn2);
         writeToFileRunner($txt."d.".$field->size."\n");
         $txt = $ttxt."_OFFSET";
         $txt = str_pad($txt,$nn2);
         $ti=0;
		 if ($entry->length >= 4) 
         {
             if ($field->size<=8) 
                 $ti = $field->hword;
             else if ($field->size <=16) 
                 $ti = floor($field->hword/2)*2;
             else
                 $ti = floor($field->hword/4)*4;
         }
         else if (($entry->length <= 2) && ($entry->length > 1)) 
                $ti = $field->hword;
         else
             $ti = 0;
		 if ($field->ar) 
             $ti = floor($field->hword/4)*4;	
         writeToFileRunner($txt."d.".$ti."\n");
         if (($field->size<8) && ($field->from > 7)) 
         {
             $txt = $ttxt."_F_OFFSET_MOD8";
             $txt = str_pad($txt,$nn2);
             $tii = $field->from%8;
             writeToFileRunner($txt."d.".$tii."\n");
         }
         if (($field->size<16) && ($field->from > 15))
         {
             $txt = $ttxt."_F_OFFSET_MOD16";
             $txt = str_pad($txt,$nn2);
             $tii = $field->from%16;
             writeToFileRunner($txt."d.".$tii."\n");
         }
     }
     if ($writecomment) 
         writeToFileRunner("\n");
 }

 function writeToFile($data)
 {
    $GLOBALS['output'] .= $data;
    //echo "data=".$data."<br>";
 }
 //function writeToFile($data)
 //{
 //   $GLOBALS['output_macro'] .= $data;
    //echo $data."<br>";
 //}
 function writeToFileRunner($data)
 {
    $GLOBALS['output_runner'] .= $data;
    //echo $data."<br>";
 }
 function closeFile($fp,$n,$t,$a)
 {
     $f = $fp.$n;
    /* remove the old file */
    if (! strlen($f))
    {
        if ( file_exists($f) && ($t == 'w+'))
        {
            chmod($f,0666); 
            unlink($f);
        }
    }
    $file = fopen($f,$t);
    if ($file)
    {
        fwrite($file,$a);
        fclose($file);
        //echo "file=".$f."<br>";
        $fl = substr($f,strlen($_SERVER['DOCUMENT_ROOT']));
        echo "<br><a href=".$fl.">".$fl."</a><br>";
    }
    else
        echo ("Cannot open file ".$f."<br>");
    $a = "";
 }
 function dotab($text,$tn)
 {
     $txt = $text;
     $txt .= $tn->prefix.strtoupper($tn->name)."_ADDRESS"; 
     $t = sprintf("%s 0x%04x",str_pad($txt,100),$tn->base);
	 if ((strtoupper($tn->generate) == "ALL") ||
         (strtoupper($tn->generate) == "ADDRESS_ONLY")) 
         $GLOBALS['output_fw'] .= $t."\n";
	 if (strtoupper($tn->generate) == "ALL") 
     {
     $ents = $tn->entries;
     if ($tn->len > 1) 
     {
         $txt = sprintf("\n#define LILAC_RDD_%s_SIZE     %d",
                        strtoupper($tn->name),
                        $tn->len);
         writeToFile($txt);
     }
     if ($tn->len2 > 1) 
     {
         $txt = sprintf("\n#define LILAC_RDD_%s_SIZE2    %d",
                        strtoupper($tn->name),
                        $tn->len2);
         writeToFile($txt);
     }
     if ($tn->len3 > 1) 
     {
         $txt = sprintf("\n#define LILAC_RDD_%s_SIZE3    %d",
                        strtoupper($tn->name),
                        $tn->len3);
         writeToFile($txt);
     }
     $entarray = array();
     for ($en=0; $en<count($ents); $en++)
     {
         if (!$ents[$en]->done) 
         {
             generate_entry_struct($tn->name,$ents[$en]);
             $ents[$en]->done = True;
         }
         array_push($entarray,$ents[$en]->name);
     }
     $found = False;
     for ($r=0; $r < count($GLOBALS['unions']);$r++) 
     {
         if ($tn->union == $GLOBALS['unions'][$r]) 
         {
             $found=True;
             break;
         }
     }
     $nn = 0;
     for ($i=0; $i<count($entarray); $i++)
     {
         if (strlen($entarray[$i]) > $nn)
             $nn = strlen($entarray[$i]);
     }
     $nn = $nn+5;
     if ((count($ents) > 1) && !$found)
     {
         $entarrayr = array_reverse($entarray);
         writeToFile("\ntypedef union\n{\n");
         while (count($entarrayr) > 0) 
         {
             $tt = array_pop($entarrayr);
             $txt = sprintf("\t%s%s%s;\n",PREFIX,str_pad(strtoupper($tt)."_DTS",$nn),strtolower($tt));
             writeToFile($txt);
         }
         writeToFile(END."BL_".PREFIX.strtoupper($tn->union)."_DTS;\n\n");
         array_push($GLOBALS['unions'],$tn->union);
     }
     //else writeToFile("\n");
     generate_table_struct($tn);
     }
 }
 function gen($filename)
 {
     global $fwdata;

     $t = "Data Item, Size(DEC), Start Address(HEX), End Address(HEX), color\n";
     $GLOBALS['output_map'] .= $t."\n";
     //validate_data_base();
     for ($sn=0; $sn<count($fwdata); $sn++) 
     {
         $seg = $fwdata[$sn];
         writeToFile("/* ".strtoupper($seg->name)." */\n");
         $GLOBALS['output_fw'] .="/* ".strtoupper($seg->name)." */\n";
         $tabs = $seg->tables;
         $nn = 0;
         for ($tn=0; $tn<count($tabs); $tn++) 
         {
			 //if (($tabs[$tn]->name == "none") || ($tabs[$tn]->name == "undef")) continue;
             $txt = "#define ";
             if (strtolower($tabs[$tn]->same) == "true")
             {
                 $txt .= strtoupper($seg->name)."_";
                 if (!$tabs[$tn]->done) 
                 {
                     dotab($txt,$tabs[$tn]);
                     $tabs[$tn]->done = True;
                 }
             }
             else dotab($txt,$tabs[$tn]);
         }
		 $prevEnd = 0;
         $totalfree=0;
         if (($seg->start != 0) && ($sn > 0))
         {
             $totalfree += $seg->start;
             $t = sprintf(",%d, 0x0000, 0x%04X, 1",$totalfree,$seg->start-1);
             $GLOBALS['output_map'] .= $t."\n";
            //$txt = sprintf("Debug Free : length(bytes)=%d, start=0x0000, end=0x%04X, color=1",$totalfree,$seg->start-1);
            //echo $txt."<br>";
         }
         for ($tn=0; $tn<count($tabs); $tn++) 
         {
             $end = $tabs[$tn]->base+$tabs[$tn]->length-1;
             if ($tn > 0) 
             {
                 if (($tabs[$tn]->base > $prevEnd) && ($sn>0))
                 {
                     $l = $tabs[$tn]->base - $prevEnd;
                     $totalfree += $l;
                     $t = sprintf(",%d, 0x%04X, 0x%04X, 1",$l,$prevEnd,$tabs[$tn]->base-1);
                     $GLOBALS['output_map'] .= $t."\n";
                    //$txt = sprintf("Debug Free : length(bytes)=%d, start=0x%04X, end=0x%04X, color=1",$l,$prevEnd,$tabs[$tn]->base);
                    //echo $txt."<br>";
                 }
			     else 
                     if ($tabs[$tn]->base < $prevEnd)
                         if ($tabs[$tn]->shared == "0") 
			    		    echo "<div style='color:red'>ERROR !!! Table ".$seg->name.".".$tabs[$tn]->name.
                            " overlap ( previous=0x".base_convert($prevEnd,10,16)." current=0x".base_convert($tabs[$tn]->base,10,16).
                            " )<br><div style='color:black'>"; 
                         else
                             echo "<div style='color:blue'>Info !!! Table ".$seg->name.".".$tabs[$tn]->name.
                             " has shared and overlap ( previous=0x".base_convert($prevEnd,10,16)." current=0x".base_convert($tabs[$tn]->base,10,16).
                             " )<br><div style='color:black'>"; 
             }
             if (($tabs[$tn]->name != 'none') && ($tabs[$tn]->name != 'undef'))
             {
                 $atv = 0;
                 switch ($tabs[$tn]->at) 
                 {
                 case "table":
                     $atv = 2;
                     break;
                 case "cyclic":
                     $atv = 3;
                     break;
                 }
                 $t = sprintf("%s.%s, %d, 0x%04X, 0x%04X, %d",
                              $seg->name,$tabs[$tn]->name,
                              $tabs[$tn]->length,
                              $tabs[$tn]->base,
                              $end,
                              $atv);
                 $GLOBALS['output_map'] .= $t."\n";
                //$txt = sprintf("Debug Table %s.%s : length(bytes)=%d, start=0x%04X, end=0x%04X, color=%s(%d)",
                //               $seg->name,$tabs[$tn]->name,$tabs[$tn]->length,$tabs[$tn]->base,
                //               $end,$tabs[$tn]->at,$atv);
                //echo $txt."<br>";
             }
             $prevEnd = $end+1;
         }
         if (($prevEnd < $seg->end)  && ($sn>0))
         {
             $l = $seg->end-$prevEnd;
             $totalfree += $l;
             $t = sprintf(",%d, 0x%04X, 0x%04X, 1",$l,$prevEnd,$seg->end-1);
             $GLOBALS['output_map'] .= $t."\n";
            //$txt = sprintf("Debug Free : length(bytes)=%d, start=0x%04X, end=0x%04X, color=1",$l,$prevEnd,$seg->end);
            //echo $txt."<br>";
         }
		 if ($sn > 0) 
             $GLOBALS['output_map'] .= "\nTotal Free Space, ".$totalfree."\n\n";
     }
     closeFile($filename,"bl_lilac_drv_runner_data_structures_auto.h","w+",$GLOBALS['output']);
     //closeFile($filename,"bl_lilac_drv_macros.h","w+",$GLOBALS['output_macro']);
     closeFile($filename,"bl_lilac_drv_runner_defs_auto.h","w+",$GLOBALS['output_fw']);
     closeFile($filename,"bl_lilac_fw_defs_auto.h","w+",$GLOBALS['output_runner']);
     closeFile($filename,"maps.csv","w+",$GLOBALS['output_map']);
 }
?>


