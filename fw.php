
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta content="text/html;charset=ISO-8859-1" http-equiv="Content-Type">
<link href="../common/mine.css" rel="stylesheet" type="text/css">
<?php

    include "../common/parser.php";
    function write_temp($filename,$last)
    {
        global $fwdata;
		$doc = new XMLWriter();
		$doc->openMemory();
		$doc->startDocument("1.0","ISO-8859-1");
        $doc->setIndent(2);
        $doc->startElement( "datastruct" );
        // every segment
		for ($sn=0; $sn < count($fwdata); $sn++) 
        {
            $seg = $fwdata[$sn];
            $doc->startElement( "data_segment" );
			$doc->writeAttribute("name",$seg->name);
			$doc->writeAttribute("end_address","0x".base_convert($seg->end,10,16));
            // every table in segment
            $tab = $seg->tables;
            for ($tn=0; $tn<count($tab); $tn++)
            {
				if ($tab[$tn]->name != "undef") 
                {
                    $doc->startElement( "table" );
                    $doc->writeAttribute("name",$tab[$tn]->name);
                    $doc->writeAttribute("address","0x".base_convert($tab[$tn]->base,10,16));
                    $doc->writeAttribute("anchor",$tab[$tn]->anchor);
                    $doc->writeAttribute("shared_id",$tab[$tn]->shared);
                    $doc->writeAttribute("module_name",$tab[$tn]->module);
                    $doc->writeAttribute("same_table",$tab[$tn]->same);
                    $doc->writeAttribute("size",$tab[$tn]->len);
                    $doc->writeAttribute("size2",$tab[$tn]->len2);
                    $doc->writeAttribute("size3",$tab[$tn]->len3);
                    $doc->writeAttribute("union_name",$tab[$tn]->union);
                    $doc->writeAttribute("align_type",$tab[$tn]->at);
                    $doc->writeAttribute("alignment",$tab[$tn]->align);
                    $ent = $tab[$tn]->entries;
                    for ($en=0; $en<count($ent);$en++) 
                    {
                       $doc->startElement( "entry" );
                        $doc->writeAttribute("name",$ent[$en]->name);
                        $fld = $ent[$en]->fields;
                        for ($fn=0; $fn<count($fld);$fn++)
                        {
                            $doc->startElement( "field" );
                            $doc->writeAttribute("name",$fld[$fn]->name);
                            if ($fld[$fn]->ar==true)
                            {
                                $doc->writeAttribute("is_array","true");
                                $doc->writeAttribute("array_num_entries",$fld[$fn]->len);
                            }

                            $doc->writeAttribute("size",$fld[$fn]->size);
                            // append field to entry
                            $doc->fullEndElement();
                        }
                        // append entry to table 
                        $doc->fullEndElement();
                    }
                    // append table to segment
                    $doc->fullEndElement();
                }
				else
                {
                    $ent = $tab[$tn]->entries;
                    for ($en=0; $en<count($ent);$en++) 
                    {
                       $doc->startElement( "entry" );
                        $doc->writeAttribute("name",$ent[$en]->name);
                        $doc->writeAttribute("size",$ent[$en]->length);
                        $fld = $ent[$en]->fields;
                        for ($fn=0; $fn<count($fld);$fn++)
                        {
                            $doc->startElement( "field" );
                            $doc->writeAttribute("name",$fld[$fn]->name);
                            if ($fld[$fn]->ar==true)
                            {
                                $doc->writeAttribute("is_array","true");
                                $doc->writeAttribute("array_num_entries",$fld[$fn]->len);
                            }
                            $doc->writeAttribute("size",$fld[$fn]->size);
                            // append field to entry
                            $doc->fullEndElement();
                        }
                    }
                    // append table to segment
                    $doc->fullEndElement();
                }
            }
            // append segment to datastruct
			$doc->fullEndElement();
        }
		$doc->fullEndElement();
        $doc->endDocument();
        $file = fopen($filename,'w+');
        if ($file)
        {
            fwrite($file,$doc->outputMemory(true));
            fclose($file);
            if ($last) 
            {
                $fl = substr($filename,strlen($_SERVER['DOCUMENT_ROOT']));
                echo "Saved file : "."<a href=".$fl.">".$fl."</a><br><br>";
            }
        }
    }
	global $fwdata;
// receives from client side (browser) the data; decode it
// write it to a file or generate files
    $useragent = $_SERVER['SERVER_SOFTWARE'];
    $opath="";
    $dirp = date("d.m.Y").date("_H_i");
    if (stristr($useragent,"win32"))
    {
        $GLOBALS['os_sep']= '\\';
        $opath ="files\\".$dirp;
    }
    else
    {
        $GLOBALS['os_sep']= '/';
        $opath ="files/".$dirp;
    }

    $ff = str_replace("=",$GLOBALS['os_sep'],$_REQUEST['filen']);
    FWparse($ff);
    switch ($_REQUEST['action']) 
    {
    case 0:
        write_temp($ff,true);
        break;
    case 1:
		require_once "../common/generateH.php";
        if (!is_dir($opath)) mkdir($opath, 0777);
        $ffg = getcwd().$GLOBALS['os_sep'].$opath.$GLOBALS['os_sep'];
        gen($ffg);
        break;
    case 2:
    case 3:
        $data = $_REQUEST['data'];
        $data = str_replace('\"','"',$data);
		//echo "data=>".$data."<br>";
        $fwchg = json_decode($data,true);
        $keys = array_keys($fwchg);
        $seg = strtok($keys[0],"&");
        $tab = strtok("&");
        $ent = strtok("&");
        $fld = strtok("&");
        if ($_REQUEST['action'] == 2) // change
        {
            $vals = $fwchg[$keys[0]];
            //print_r($vals);echo "<br>";
            if ($tab != "-1") 
            {
                if ($ent != "-1") 
                { 
                    if ($fld != "-1") 
                    { // update field
                        if ($vals['is_array'] == 'true') 
                            $ar=true;  
                        else
                            $ar=false; 
                        if (count($fwdata[$seg]->tables[$tab]->entries[$ent]->fields) > $fld) 
                        {
                            $field=$fwdata[$seg]->tables[$tab]->entries[$ent]->fields[$fld];
                            $field->name=$vals['name'];
                            $field->size=$vals['size'];
                            $field->len=$vals['array_num_entries'];
                            $field->ar = $ar;
                        }
                        else
                        {
                            $field = new FWfield($vals['name'],$vals['size'],$ar,$vals['array_num_entries']);
                            $fwdata[$seg]->tables[$tab]->entries[$ent]->add_field($field);  
                        }
                        //$field->FWfield_print();
                    }
                    else
                    { // update entry
                        if (count($fwdata[$seg]->tables[$tab]->entries) > $ent) 
                        {
                            $entry=$fwdata[$seg]->tables[$tab]->entries[$ent];
                            $entry->name=$vals['name'];
                        }
                        else
                        {
                            $entry = new FWentry($vals['name']);
                            $fwdata[$seg]->tables[$tab]->add_entry($entry);
                        }
                        //$entry->FWentry_print();
                    }
                }
                else
                { // update table
                    if (count ($fwdata[$seg]->tables) > $tab) 
                    {
                        $table=$fwdata[$seg]->tables[$tab];
                        $table->name=$vals['name'];
                        $table->base=$vals['base'];
                        $table->shared=$vals['shared_id'];
                        $table->anchor=$vals['anchor'];
                        $table->module=$vals['module'];
                        $table->same=$vals['same'];
                        $table->len=$vals['size'];
                        $table->len2=$vals['size2'];
                        $table->len3=$vals['size3'];
                        $table->union=$vals['union'];
                        $table->at=$vals['align_type'];
                        $table->align=$vals['alignment'];
                    }
                    else
                    {
                        $table = new FWtable($vals['name'],$vals['base'],$vals['anchor'],$vals['shared_id'],
                                             $vals['module'],$vals['same'],
                                             $vals['size'],$vals['size2'],$vals['size3'],
                                             $vals['union'],$vals['align_type'],$vals['alignment']);
                        $fwdata[$seg]->add_table($table);
                    }
                    //$table->FWtable_print();
                }
            }
            else
            {
                if (count($fwdata) > $seg) 
                {
                    // update segment
                    $segment=$fwdata[$seg];
                    $segment->name=$vals['name'];
                    $segment->end=$vals['end_address'];
                    //$segment->FWsegment_print();
                }
                else
                {
                    $segment = new FWsegment($vals['name'],$vals['end_address']);
                    array_push($fwdata,$segment);
                }
            }
        }
        else
        { // delete
            if ($tab != "-1") 
            {
                if ($ent != "-1") 
                { 
                    if ($fld != "-1") 
                    { // update field
                        $entry=$fwdata[$seg]->tables[$tab]->entries[$ent];
                        array_splice($entry->fields,$fld,1);
                        //$entry->FWentry_print();
                    }
                    else
                    { // update entry
                        $table=$fwdata[$seg]->tables[$tab];
                        array_splice($table->entries,$ent,1);
                    }
                }
                else
                {// update table
                    $seg=$fwdata[$seg];
                    array_splice($seg->tables,$tab,1);
                }
            }
            else
            {// upate segment
                array_splice($fwdata,$tseg,1);
            }
        }
        write_temp($ff,false);
        //echo '<script text=text/javascript>
        //    self.close();
        //      </script>';
        break;
    }


?>
