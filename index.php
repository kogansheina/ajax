
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta content="text/html;charset=ISO-8859-1" http-equiv="Content-Type">
<link href="../common/mine.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="fw_classes.js">
</script>
<script type="text/javascript" src="fw_parser.js">
</script>
<script type="text/javascript" src="fw_tables.js">
</script>
<html>

<body>
<input type="button" value="Validate" onclick="valid_data_base();"&#160;&#160;&#160;&#160;&#160;&#160;>
<input type="button" value="Save" onclick="save(0);"&#160;&#160;&#160;&#160;&#160;&#160;>
<input type="button" value="Generate" onclick="save(1);"><br><br>
<table id="segments" border="1" cellpadding=2 cellspacing=5>
<tr><th align=middle><b>Name</b></th><th align=middle><b>EndAddress</b></th><th align=middle colspan=3><b>Commands</b></th></tr>
</table><br>
<table id="tables" border="1" cellpadding=2 cellspacing=5>
<tr><th align=middle><b>Name</b></th><th align=middle><b>Base</b></th><th align=middle><b>Anchor</b></th>
	<th align=middle><b>Shared</b></th>
	<th align=middle><b>Module</b></th><th align=middle><b>Same Address</b></th>
	<th align=middle><b>Size</b></th><th align=middle><b>Size2</b></th>
	<th align=middle><b>Size3</b></th><th align=middle><b>Union</b></th>
	<th align=middle><b>AlignType</b></th><th align=middle><b>Align</b></th><th align=middle><b>Length(bytes)</b>
	<th align=middle colspan=3><b>Commands</b></th></tr>
</table><br>
<table id="entries" border="1" cellpadding=2 cellspacing=5>
<tr><th align=middle><b>Name</b></th><th align=middle><b>Length(bytes)</b></th>
	<th align=middle colspan=3><b>Commands</b></th></tr>
</table><br>
<table id="fields" border="1" cellpadding=2 cellspacing=5>
<tr><th align=middle><b>Name</b></th><th align=middle><b>Word</b></th><th align=middle><b>From</b></th><th align=middle><b>Size(bits)</b></th><th align=middle><b>Array Num Entries</b></th>
	<th align=middle colspan=2><b>Commands</b></th></tr>
</table><br>
</body>
</html>
<script type="text/javascript">
var fwdata = [];
var filename = '';
var errors = [];
var file_output = "";

function print_errors()
{
    if (errors.length) 
    {
        for (var i=0; i<errors.length; i++) 
        {
            document.write(i+" : "+errors[i]+"<br>");
        }
        alert("Data Base has errors !!!<br>See at the bottom of the page.");
    }
    else alert("Valid Data Base - no errors !!!");
}
function instead_reghex(a)
{
    if (a.length) 
    {
        if (a.charAt(0) != '0') return 4;
        if ((a.charAt(1) != 'x') && (a.charAt(1) != 'X')) return 2;
        for (var i=2; i<a.length; i++) 
        {
            if (
            ((a.charAt(i) >= 'a') && (a.charAt(i) <= 'f')) ||
            ((a.charAt(i) >= 'A') && (a.charAt(i) <= 'F')) ||
            ((a.charAt(i) >= '0') && (a.charAt(i) <= '9'))) continue;
            else return 3;
        }
        return true;
    }
    return false;
}
function instead_regexp(a)
{
    if (a.length) 
    {
        for (var i=0; i<a.length; i++) 
        {
            if (i==0) 
            {
                if (
                    ((a.charAt(i) >= 'a') && (a.charAt(i) <= 'z')) ||
                    ((a.charAt(i) >= 'A') && (a.charAt(i) <= 'Z')) ||
                    (a.charAt(i) == '_')
                   ) continue;
                else return false;
            }
            else
            {
                if (((a.charAt(i) >= 'a') && (a.charAt(i) <= 'z'))
                     || ((a.charAt(i) >= 'A') && (a.charAt(i) <= 'Z'))
                     || (a.charAt(i) == '_')
                     || ((a.charAt(i) >= '0') && (a.charAt(i) <= '9'))) continue;
                else return false;
            }
        }
        return true;
    }
    return false;
}
function valid_name(arnames,txt,s,t,e)
{
    var ret = true;
    for (i=0; i < arnames.length; i++) 
    {
        if (!instead_regexp(arnames[i]))            
            errors.push(txt+" '"+arnames[i]+"', is not a correct name");
        for (j=0; j < arnames.length; j++) 
        {
            if (j == i) continue;
            if (arnames[j] == arnames[i]) 
            {
                errors.push(txt+" "+arnames[j]+" is not unique name");
                ret = false;
            }
        }
    }
    return ret;
}
function valid_data_base()
{
    var ret = true;
    var arsegnames = new Array();
    for (var s=0; s<fwdata.length-1;s++)
    {
        var ts = fwdata[s].tables;
        var artabnames = new Array();
        var artablens = new Array();
        arsegnames.push(fwdata[s].name);
        for (var t=0; t<ts.length-1;t++)
        {
			if ((ts[t].name != "undef" ) && (ts[t].name != "none")) {
                tname = fwdata[s].name+'_'+ts[t].name;
                artabnames.push(tname);
                if (!instead_regexp(ts[t].module))            
                    errors.push("Table"+" '"+ts[t].name+"', has an incorrect module "+ts[t].module);
                if (!instead_regexp(ts[t].union))            
                    errors.push("Table"+" '"+ts[t].name+"', has an incorrect munion "+ts[t].union);
            }
            var es = ts[t].entries;
            var arentrynames = new Array();
            var arentrylen = 0;
            for (var e=0; e<es.length-1;e++)
            {
                tname = fwdata[s].name+'_'+ts[t].name+'_'+es[e].name;
                arentrynames.push(tname);
                var fs = es[e].fields;
                var arnames = new Array();
				for (var f=fs.length-1; f >=0; f--) 
                {
                    tname = fwdata[s].name+'_'+ts[t].name+'_'+es[e].name+'_'+fs[f].name;
                    arnames.push(tname);
                    if (isNaN(fs[f].size)) {
                        errors.push("Field '"+tname+"' has size not numeric.");
                        ret = false;
                    }
                    if (isNaN(fs[f].len)) {
                        errors.push("Field '"+tname+"' has length not numeric.");
                        ret = false;
                    }
                    var intsize = parseInt(fs[f]['size']);
                    if ((intsize > 32) || (intsize <=0))  
                    {
                        errors.push("Field '"+tname+"' has incorrect size.");
                        ret = false;
                    }
                    if (fs[f].ar && (intsize != 8) && (intsize != 16) && (intsize != 32))
                    {
                        errors.push("Field '"+tname+" has incorrect size. It is an array and must have size 8/16/32'");
                        ret = false;
                    }
                    arentrylen +=intsize;
                }
                ret |= valid_name(arnames,"Field",s,t,e);
            }
            ret |= valid_name(arentrynames,"Entry",s,t,-1);
            artablens.push(arentrylen);
            if (isNaN(ts[t].base)) {
                errors.push("Table '"+tname+"' has base not numeric.");
                ret = false;
            }
        }
        ret |= valid_name(artabnames,"Table",s,-1,-1);
    }
    ret |= valid_name(arsegnames,"Segment",-1,-1,-1);
    print_errors();
    return ret;
}
// send to php all the data as a JSON string
function save(action)
{
    if (!fwdata.length) {
        alert("Empty file OR no submit");
        return;
    }
    updatePHP(null,action);
}

// call from php; bring the file, parse it, display tables
// fout = filename
function disp(fname,fout,sep)
{
    //document.write("sep="+sep+"<br>");
    if (sep != "win") 
    {
        filename = "backups/"+fout;
    }
    else
    {
        filename = "backups\\"+fout;
    }
    file_output = fname+"=files="+fout;
	if (fout.length) 
    {
        try
        {
            var xhttp=new XMLHttpRequest(); //filename
            xhttp.open("GET",filename,false);
            xhttp.send(null);
            if(xhttp.readyState == 4 && xhttp.status == 200) 
            {
                var xmlDoc=xhttp.responseXML;
                {
                    var segvar=xmlDoc.getElementsByTagName("data_segment"); 
                    var nsegments = segvar.length;
                    if (nsegments==0) {
                        document.write("No segments defined\n");
                        return;
                    }
                    for (var i=0;i<nsegments;i++)
                    {
                        var seg = new FWsegment(segvar[i].getAttribute("name"),segvar[i].getAttribute("end_address"));
                        if (seg)
                        {
                            fwdata[i]= seg;
                            var tt = segvar[i].childNodes;
                            var ntt = tt.length;
                            for (var j=0; j < ntt; j++) 
                            {
                                parse_table(seg,tt[j]);
                            }
                        } // segment
                        else
                        {
                            document.write("Segment error<br>");
                        }
                    } // loop on segments	
                    for (var s=1; s<fwdata.length;s++)
                    {
                        var ts = fwdata[s].tables;
                        for (var t=0; t<ts.length;t++)
                        {
                            var es = ts[t].entries;
                            for (var e=0; e<es.length;e++)
                            {
                                calculate_from_field(s,t,e,false);
                                var fs = es[e].fields;
                                fs[fs.length] = new FWfield("none","32","0","1");
                            }
                            es[es.length] = new FWentry("none");
                        }
                        ts[ts.length] = new FWtable("none",null,"true","0",null,"false",null,null,null,null,null,null);
                    }
                    fwdata[fwdata.length]=new FWsegment("none",null);
                }
            }//server answer
            else
            {
                document.write("Server error reading file : "+filename+"<br>");
                if (xhttp.status == 404) 
                    document.write("File not found<br>");
                return;
            }
        }
        catch( err ) 
        { document.writeln( "ERROR: " + err.description ); return; } 
    }
	else
    {
        fwdata[0]=new FWsegment("none");
		add_table(fwdata[0],new FWtable("none",null,"false","0",null,"false",null,null,null,null,null,null));
        add_entry(fwdata[0].tables[0],new FWentry("none"));
        add_field(fwdata[0].tables[0].entries[0],new FWfield("none","32","0","1"));
    }
    table_of_segments();
    table_of_tables(0)
    table_of_entries(0,0);
    table_of_fields(0,0,0);
}
</script>
<?php
     // BEGIN !!!!
    $useragent = $_SERVER['SERVER_SOFTWARE'];
    $br = $_SERVER['HTTP_USER_AGENT'];
    if (stristr($useragent,"win32"))
    {
        $os_sep = '\\';
        $os = 'win';
    	$path = getcwd()."\\backups";
    	if (!is_dir($path)) mkdir($path, 0777);
    	$path = getcwd()."\\files";
    	if (!is_dir($path))  mkdir($path, 0777);
    }
    else
    {
        $os_sep = '/';
        $os = 'linux';
    	$path = getcwd()."/backups";
    	if (!is_dir($path)) mkdir($path, 0777);
    	$path = getcwd()."/files";
    	if (!is_dir($path))  mkdir($path, 0777);
    }
	$br = $_SERVER['HTTP_USER_AGENT']; 
    $qq = strpos($br,"MSIE");
    if ( $qq== false) 
    {
        if (strpos($br,"Firefox") >=0) $os = 'mozilla';
        else if (strpos($br,"Mozilla") >=0) $os = 'mozilla';
        else if (strpos($br,"Chrome") >=0) $os = 'chrome';
    }
    if (isset($_REQUEST['from'])) 
    {
        if (isset($_REQUEST['path']) && (strlen($_REQUEST['path']) > 0)) 
        {
            $pp = $_REQUEST['path'];
            $path = buildpath($os_sep,$pp);
            $pos = strpos($path,'..');
            while ($pos != False) 
            {
                $path = buildpath($os_sep,$path);
                $pos = strpos($path,'..');
            }
            if (($os != "win") && (substr($path,0,1) != "."))
                $path = $os_sep.$path;
            if (is_dir($path))
            {
                printdir($path,$os_sep);
            }
            else  
            {
                //$path = "C:\\xampp\\htdocs\\xampp\\xml\\input\\fw0.xml";
                if (is_file($path)) 
                {
                    //if (strrpos($path,'.xml') == false) 
                    //    exit("File must have sufix '.xml' : ".$path."<br>");
                    $origfile = $path;
					$ff = getfile($os_sep,$origfile);
					if ($os != "win")
					{
						$file = getcwd()."/backups/".$ff;
						$fileo = getcwd()."/files/".$ff;
					}
					else
					{
						$file = getcwd()."\\backups\\".$ff;
						$fileo = getcwd()."\\files\\".$ff;
					}
                    copy($origfile,$file); 
                    copy($origfile,$fileo); 
                    $f1 = str_replace($os_sep,"=",getcwd()); 
                    echo '<script text=text/javascript>
                        disp(\''.$f1.'\',\''.$ff.'\',\''.$os.'\');
                       </script>';
                }
				else if (!is_link($path))
					echo "Wrong file name : ".$path."<br>";
            }
        }
        else
        {
            echo '<script text=text/javascript>
               disp("","",\''.$os.'\');
               </script>';
        }
    }
    else
    {
        echo "<form action='index.php' method='post' target='_top'>";
        echo "<br><br>&#160;&#160;&#160;&#160;&#160;Files select&#160;&#160;<input type=text size=120 name='path' value=''><br><br>";
        echo "<input type=hidden name=from value=files>";
        echo "<br><br><br>&#160;&#160;<input type='submit' value='Submit' >";
        echo "</form>";
    }

    function printdir($path,$os_sep)
    {
        $dir_handle = @opendir($path) or  die("Unable to open ".$path);

        //running the while loop
        while ($file = readdir($dir_handle))
        {
            $newfile = $path.$os_sep.$file; 
            if ( $file != "." )
            {
                if (is_dir($newfile))
                {
                    $newfile .= $os_sep;
                    //echo 'newfile='.$newfile.'<br>';
                    echo "<a style = 'color:green' href=index.php?from=files&path=".$newfile.">[+] ".$file."</a><br/>";
                }
                else if (!is_link($newfile))
                {
                    if (strrpos($newfile,'.xml') == false) 
                        echo $file."<br/>";
                    else
                        echo "<a style = 'color:red' href=index.php?from=files&path=".$newfile."> -> ".$file."</a><br/>";
                }
            }
        }
        closedir($dir_handle);
    }
    function buildpath($os_sep,$pp)
    {
        $path = '';
        $u = explode($os_sep,$pp);
        $lim = count($u);
        for ($j=0; $j<$lim-2; $j++)
        {
            if (strlen($u[$j]) > 0) 
            {
                if ($u[$j+1] != '..') 
                    $path .= $u[$j].$os_sep;
                else
                    $j ++;
            }
        }
		if ($lim>=2) 
        {
            if (strlen($u[$lim-2]) > 0)
            {
                if ($u[$lim-1] != '..') 
                    if ($os_sep == '/') 
                        $path .= $u[$lim-2].$os_sep.$u[$lim-1];
                    else
                        $path .= $u[$lim-2].$u[$lim-1];
                else
                    $path .= $u[$lim-2];
            }
            else
                $path .= $u[$lim-1];
        }
        else if ($lim>=1) 
            $path .= $u[$lim-1];
     return $path;
    }
    function pathstrip($os_sep,$pp)
    {
        $path = '';
        $u = explode($os_sep,$pp);
        $lim = count($u);
        for ($j=0; $j<$lim-1; $j++)
        {
            if (strlen($u[$j]) > 0) 
            {
                if ($u[$j+1] != '..') 
                    $path .= $u[$j].$os_sep;
                else
                    $j ++;
            }
        }
     return $path;
    }
    function getfile($os_sep,$pp)
    {
        $u = explode($os_sep,$pp);
        return $u[count($u)-1];
    }
?>



