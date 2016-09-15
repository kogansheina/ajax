Enter file contents here<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta content="text/html;charset=ISO-8859-1" http-equiv="Content-Type">
<link href="mine.css" rel="stylesheet" type="text/css">
<?php
    $debug = false;
	$fwdata = array();

class FWfield {
    var $name;  
    var $size; 
    var $from;
    var $ar;
    var $len;
    var $word;
    var $hword;
    var $dword;
   // a = name, b = size, d = is array, e = number of entries 
    function FWfield ($a,$b,$d,$e) 
    {
        global $debug;
        if (!$a) return;
        $this->name = $a;
        $this->size = $b;
        if (!$d) $this->ar = false;
        else
        {
            if ($d== "true") $this->ar = true;
            else $this->ar = false;
        }
        if (!$e) $this->len = 1;
        else $this->len = $e;
        $this->from = 0;
        $this->word = 0;
        $this->hword = 0;
        $this->dword = -1;
        if ($debug) 
            $this->FWfield_print();
    }
    function FWfield_print () 
    {
        if ($this->ar) $isa = " is array and ";
        else $isa = " is not array ";
        echo "Field : ".$this->name." from ".$this->from." has length = ".$this->size.$isa." has entries = ".$this->len." word = ".count($this->word)."<br>";
    }
}
class FWentry {
    var $name;  
    var $align; 
    var $fields = array();
    var $length;
	var $done = False;
  // a = name, b = alignement  
    function FWentry ($a) 
    {
        global $debug;
        if (!$a) return;
        $this->name = $a;
        $this->length = 0;
        if ($debug) $this->FWentry_print();
    }
    function add_field ($a) 
    {
        if (!$a) return;
        array_push($this->fields,$a);
    }
    function FWentry_print () 
    {
        echo "Entry : ".$this->name."<br>";
        $n = count($this->fields);
        for ($k=0; $k < $n; $k++)
        {
            $this->fields[$k]->FWfield_print();
        }
    }
}
class FWtable {
    var $name;  
    var $base; 
    var $shared;
    var $anchor;
    var $module;
    var $same;
    var $len, $len2, $len3;
    var $union;
    var $uniondone;
	var $length;
	var $at;
	var $generate, $prefix;
    var $entries = array();
	var $done = False;
    
    // a = name, b = base, d = anchor, e = shared, f = module, s = same, c - sizes 
    function FWtable ($a,$b,$d,$e,$f,$s,$c,$c2,$c3,$u,$at,$align,$gen,$p) 
    {
        global $debug;
        if (!$a) return;
        $this->name = $a;
        if (!$b) $this->base = 0;
        else
        {
            if ((substr($b,0,2) == "0x") || (substr($b,0,2) == "0X"))
                $this->base = intval(substr($b,2,strlen($b)-2),16);
            else
                $this->base = intval($b);
        }
        if (!$d) $this->anchor = "true";
        else
            $this->anchor = $d;
        if (!$e) $this->shared = 0;
        else
            $this->shared = $e;
        if (!$f) $this->module = "none";
        else
            $this->module = $f;    
        if (!$s) $this->same = "false";
        else
            $this->same = $s;
        if (!$c) $this->len = 1;
        else
            $this->len = $c;
        if (!$c2) $this->len2 = 1;
        else
            $this->len2 = $c2;
        if (!$c3) $this->len3 = 1;
        else
            $this->len3 = $c3;
        if (!$u) $this->union = "none";
        else
            $this->union = $u;
        $this->length = 0;
        if (!$at) $this->at = "regular";
        else
            $this->at = $at;
        if (!$gen) $this->generate = "all";
        else
            $this->generate = $gen;
        if (!$p) $this->prefix = "";
        else
            $this->prefix = $p;
        if (!$align) $this->align = 1;
        else
        {
            if ($align == 1) 
                $this->align = $align;
            else if ($align%2 != 0) {
                echo "Error !!! Table ".$a." has incorrect alignment value ".$align."<br>";
                $this->align = 1;
            }
            else $this->align = $align;
        }
        if ($debug) 
            $this->FWtable_print();
    }
    function add_entry ($a) 
    {
        if (!$a) return;
        array_push($this->entries,$a);
    }
    function FWtable_print () 
    {
        echo "Table : ".$this->name." has address = ".$this->base." shared = ".$this->shared." anchor = ".$this->anchor.
              " module = ".$this->module." same = ".$this->same." len = ".$this->len.
              " len2 = ".$this->len2." len3 = ".$this->len3." union=".$this->union." align=".$this->at."<br>";
        $n = count($this->entries);
        for ($k=0; $k < $n; $k++)
        {
            $this->entries[$k]->FWentry_print();
        }
    }
}
class FWsegment {
    var $name;
    var $tables = array();
    
    function FWsegment ($a,$end) 
    {
        global $debug;
        if (!$a) return;
        $this->name = $a;
        $this->start = 0x0000;
        if ($end == null) $this->end = 0xffff;
        else 
        {
            if ((substr($end,0,2) == "0x") || (substr($end,0,2) == "0X"))
                $this->end = intval(substr($end,2,strlen($end)-2),16);
            else
                $this->end = intval($end);
        }
        if ($debug) $this->FWsegment_print();
    }
    function add_table ($a) 
    {
        if (!$a) return;
        array_push($this->tables,$a);
		for ($i=0; $i<count($this->tables);$i++) 
        {
            if ($i==0) 
            {
                if ($this->start < $this->tables[$i]->base) 
                    $this->start = $this->tables[$i]->base;
            }
            else
            {
                if ($this->start > $this->tables[$i]->base) 
                    $this->start = $this->tables[$i]->base;
            }
        }
    }
    function FWsegment_print () 
    {
        echo "Segment : ".$this->name."<br>";
        $n = count($this->tables);
        for ($k=0; $k < $n; $k++)
        {
            $this->tables[$k]->FWtable_print();
        }
    }

    function calculate_from_field($all)
    {
        for ($i=0; $i < count($this->tables); $i++) 
        {
            $tables = $this->tables[$i];
            for ($j=0; $j < count($tables); $j++) 
            {
                if ($all) 
                {
                    $ents = $tables->entries;
                    for ($k=0; $k < count($ents); $k++) 
                    {
                        $ent = $ents[$k];
                        $n = count($ent->fields);
                        $len = 0;

                        for ($kk=0; $kk < $n; $kk++) 
                        {
                            $len += $ent->fields[$kk]->size * $ent->fields[$kk]->len;
                        }
                        $ent->length = ceil($len/8);
                        $maxl = 32;
                        switch($ent->length)
                        {
                        case 1:
                            $maxl = 8;
                            break;
                        case 2:
                            $maxl = 16;
                            break;
                        default:
                            break;
                        }
                        if ($n > 0)
                        {
                            if ($ent->fields[0]->ar) 
                            {
                                $ent->fields[0]->from = 0;
                            }
                            else
                            {
                                $ent->fields[0]->from = $maxl-$ent->fields[0]->size;
                            }
                        }
                        $len=$ent->fields[0]->size;
                        for($c=1;$c < $n;$c++)
                        {
                            if (!$ent->fields[$c-1]->ar) 
                            {
                                if ($ent->fields[$c]->size < $ent->fields[$c-1]->from) 
                                {
                                    $ent->fields[$c]->from = $ent->fields[$c-1]->from - $ent->fields[$c]->size;
                                    $ent->fields[$c]->word=$ent->fields[$c-1]->word;
                                }
                                else if ($ent->fields[$c]->size > $ent->fields[$c-1]->from) 
                                {
                                    $ent->fields[$c]->from = $maxl - ($ent->fields[$c]->size - $ent->fields[$c-1]->from);
                                    $ent->fields[$c]->word=$ent->fields[$c-1]->word+1;
                                    if ($ent->fields[$c-1]->from > 0) 
                                        $ent->fields[$c]->dword=$ent->fields[$c]->word-1;                            }
                                else
                                {
                                    $ent->fields[$c]->from = 0;
                                    $ent->fields[$c]->word = $ent->fields[$c-1]->word;
                                }
                                $ent->fields[$c]->hword =  floor($len/8);
                            }
                            else
                            {
                                $t = $ent->fields[$c-1]->size*$ent->fields[$c-1]->len;
                                $ent->fields[$c]->word = $ent->fields[$c-1]->word+ceil($t/32);
                                $ent->fields[$c]->hword = $ent->fields[$c-1]->hword+ceil($t/8);
                                $ent->fields[$c]->from = $maxl - ($ent->fields[$c]->hword%4+1)*8;
                            }
                            $len += $ent->fields[$c]->size*$ent->fields[$c]->len;
                        }
                    }
                }
                $calclen = 0;
                if (count($tables->entries)>0) 
                    $calclen = $tables->entries[0]->length;
                if ($tables->union == "none" ) 
                {
                    for($c=1; $c<count($tables->entries); $c++)
                    {
                        $calclen += $tables->entries[$c]->length;
                    }
                }
				else
                    $calclen = $this->maxlen($tables);
				//echo $tables->name.", table length=".$calclen."<br>";
                $tables->length = $calclen*$tables->len*$tables->len2*$tables->len3;
            }
        }
    }
    function maxlen($tab)
    {
        $m = $tab->entries[0]->length;
        for($c=1; $c<count($tab->entries); $c++)
        {
            $m = max($m,$tab->entries[$c]->length);
        }
        return $m;
    }
}

function FWparse($file)
{
	global $fwdata;
	$data = implode("", file($file));
	$xml_parser = xml_parser_create();
	xml_parse_into_struct($xml_parser, $data, $values, $tags);
	xml_parser_free($xml_parser);

    //echo "tags<br>";print_r($tags); echo "<br>";
    //echo "values<br>";print_r($values); echo "<br>";
    $segments=array();
    $tables=array();
    $entries=array();
    for ($i=1; $i<count($values)-1;$i++) 
    {
        $a=$values[$i];
        //print_r($a); echo "<br>";
        switch ($a['tag']) 
        {
        case "DATA_SEGMENT":
            if ($a['type'] == 'open') 
            {
                $b=$a['attributes'];
                $end = null;
                if (isset($b['END_ADDRESS'])) $end = $b['END_ADDRESS'];
                array_push($fwdata,new FWsegment($b['NAME'],$end));
                array_push($segments,true);
            }
            if ($a['type'] == 'close')
            {
                if (count($segments) > 0) 
                    array_pop($segments);
                else echo "ERROR: segment not open<br>";
            }
            break;
        case "TABLE":
            if ($a['type'] == 'open') 
            {
                $b=$a['attributes'];
                $anchor=null;
                $shared=null;
                $module = null;
                $same = null;
                $size = null;
                $size2 = null;
                $size3 = null;
				$union = null;
                $at = null;
                $align = null;
                $p = null;
                $gen = null;
                if (isset($b['UNION_NAME'])) $union = $b['UNION_NAME'];
                if (isset($b['ANCHOR'])) $anchor = $b['ANCHOR'];
                if (isset($b['SHARED_ID'])) $shared = $b['SHARED_ID'];
                if (isset($b['MODULE_NAME'])) $module = $b['MODULE_NAME'];
                if (isset($b['SAME_TABLE'])) $same = $b['SAME_TABLE'];
                if (isset($b['SIZE'])) $size = $b['SIZE'];
                if (isset($b['SIZE2'])) $size2 = $b['SIZE2'];
                if (isset($b['SIZE3'])) $size3 = $b['SIZE3'];
                if (isset($b['ALIGN_TYPE'])) $at = $b['ALIGN_TYPE'];
                if (isset($b['ALIGNMENT'])) $align = $b['ALIGNMENT'];
                if (isset($b['GENERATE'])) $gen = $b['GENERATE'];
                if (isset($b['ADDRESS_PREFIX'])) $p = $b['ADDRESS_PREFIX'];
                $t=new FWtable($b['NAME'],$b['ADDRESS'],$anchor,$shared,$module,$same,$size,$size2,
                               $size3,$union,$at,$align,$gen,$p);
                $fwdata[count($fwdata)-1]->add_table($t);
                array_push($tables,true);
            }
            if ($a['type'] == 'close') 
            {
                if (count($tables) > 0) 
                    array_pop($tables);
                else echo "ERROR: table not open<br>";
            }
            break;
        case "ENTRY":
            if ($a['type'] == 'open') 
            {
                $b=$a['attributes'];
                if ($fwdata[count($fwdata)-1]->name == "entries") 
                {
                    $e=new FWentry($b['NAME']);
                    $ts = $fwdata[count($fwdata)-1]->tables;
                    $t=null;
                    if (count($ts) > 0) 
                    {
                        $t=$ts[count($ts)-1];
                    }
                    else
                    {
                        $t=new FWtable("undef",null,null,null,null,null,null,null,null,null,null,null,null,null);
                        $fwdata[count($fwdata)-1]->add_table($t);
                    }
                    $t->add_entry($e);
                }
                array_push($entries,true);
            }
            if ($a['type'] == 'close')
            {
                if (count($entries) > 0) 
                    array_pop($entries);
                else echo "ERROR: entry not open<br>";
            }
            if ($a['type'] == 'complete')
            {
                $b=$a['attributes'];
                $e = null;
                $ts = $fwdata[count($fwdata)-1]->tables;
                $t=$ts[count($ts)-1]; // last table defined
                $ts0 = $fwdata[0]->tables[0]; // suppose first segment is 'entries'
                for ($ii=0; $ii<count($ts0->entries); $ii++) 
                {
                    if ($ts0->entries[$ii]->name == $b['NAME']) 
                    {
                        $e = $ts0->entries[$ii];
                        break;
                    }
                }
                if ($e == null) 
                {
                    $e=new FWentry($b['NAME']);
                }
                $t->add_entry($e);
            }
            break;
        case "FIELD":
            //print_r($a);
            //echo "<br>";
            if ($a['type'] == 'complete') 
            {
                $b=$a['attributes'];
                $ar=null;
                $nument=null;
                if (isset($b['IS_ARRAY'])) $ar = $b['IS_ARRAY'];
                if (isset($b['ARRAY_NUM_ENTRIES'])) $nument = $b['ARRAY_NUM_ENTRIES'];
                $f=new FWfield($b['NAME'],$b['SIZE'],$ar,$nument);
                $ts = $fwdata[count($fwdata)-1]->tables;
                $t=$ts[count($ts)-1];
                $es=$t->entries;
                $e=count($es);
                $es[$e-1]->add_field($f);
            }
            break;
        }
    }
    if (count($segments) > 0) 
        echo "ERROR: segments not closed: ".count($segments)."<br>";
    if (count($tables) > 0) 
        echo "ERROR: tables not closed: ".count($tables)."<br>";
    if (count($entries) > 0) 
        echo "ERROR: entries not closed: ".count($entries)."<br>";
    for ($j=0;$j<count($fwdata);$j++) 
    {
		if ($j == 0)
			$fwdata[$j]->calculate_from_field(True);
        else
            $fwdata[$j]->calculate_from_field(False);        
        $aa = array();
        for ($i=0; $i < count($fwdata[$j]->tables); $i++) 
        {
            array_push($aa,$fwdata[$j]->tables[$i]->base);
        }
        sort($aa);
        $bb = $fwdata[$j]->tables;
        $fwdata[$j]->tables = array();
        for ($i=0; $i < count($aa); $i++) 
        {
            $bbindex = findIn($bb,$aa[$i]);
            array_push($fwdata[$j]->tables,$bb[$bbindex]);
            $bb[$bbindex] = null;
        }
        //$fwdata[$j]->FWsegment_print();
    }
}
function findIn($a1,$v)
{
    for ($t=0; $t < count($a1); $t++) 
    {
        if ($a1[$t] != null) 
            if ($a1[$t]->base == $v) return $t;
    }
    return -1;
}
?> 

