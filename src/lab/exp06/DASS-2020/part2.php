<html>
<?php
$exnum = $_POST["exnum"];
$changeflag = $_POST["changeflag"];
$swunit = $_POST["swunit"];

if($swunit == '') $swunit = 'syl';

/*
echo $exnum."<br>";
echo $changeflag . "<br>";
echo $swunit . "<br>";
*/


function displaysent($exnum,$swunit){
 if($exnum <1 | $exnum >4){
	echo "Selected example = ".$exnum."<br>";
 	echo "Invalid selection: Select an appropriat example...!";
 }

 $fname =  'wav/ex'.$exnum.'.txt';
 //echo nl2br(implode('',file($fname)));
 $fid = fopen($fname, 'r');
 $labels=array('Utterance','Transliteration','Subword units'); 
 for($i=0;$i<4 & ($buffer[$i] = fgets($fid, 4096)) !== false & !feof($fid);$i++);
 echo '<table border=1>';
 echo '<tr><td><b>'.$labels[0].'</b></td><td>'.$buffer[0].'</td></tr>';
 echo '<tr><td><b>'.$labels[1].'</b></td><td>'.$buffer[1].'</td></tr>';
 switch($swunit){
  case 'syl': 
 	echo '<tr><td><b>'.$labels[2].'</b></td><td>'.$buffer[2].'</td></tr>';
	break;
  case 'phn': 
 	echo '<tr><td><b>'.$labels[2].'</b></td><td>'.$buffer[3].'</td></tr>';
	break;
  case 'wrd': 
 	echo '<tr><td><b>'.$labels[2].'</b></td><td>'.$buffer[1].'</td></tr>';
	break;
 }
 echo '</table>';

 // echo nl2br(implode('',file('wav/ex'.$exnum.'.txt')));
}

function displaylabels($exnum,$swunit){

$fname = 'wav/ex'.$exnum.'.'.$swunit;
$fid = fopen($fname,'r');

if(! $fid){
 echo "Error: Cannot open file - ".$fname."<br>";
}
echo "<table border=1>";
echo "<tr><td><b>SYM</b></td><td><b>BEG</b></td><td><b>END</b></td></tr>";
$i = 1;
while (!feof($fid) & ($buffer = fgets($fid, 4096)) !== false){
 list($unit, $beg, $end) = sscanf($buffer,'%s %d %d');
 echo "<tr><td>$unit</td><td><input size=10 type=text value='' name='ubeg$i' onchange=jsverifylab(this,$beg) /></td><td><input size=10 type=text value='' name='uend$i' onchange=jsverifylab(this,$end) /></td></td></tr>";
 echo "<input type=hidden name='rbeg".$i."' value=".$beg." />";
 echo "<input type=hidden name='rend".$i."' value=".$end." />";
 $i = $i + 1;
}
echo "</table>";

/*
echo "Unit | Beg | End<br>";
echo "-----------------------------<br>";
 if($exnum <1 | $exnum >2){
	echo "Selected example = ".$exnum."<br>";
 	echo "Invalid selection: Select an appropriat example...!";
 }
 
 echo nl2br(implode('',file('wav/ex'.$exnum.'.lab')));
 //$fid = fopen("wav/ex".$exnum.".txt", 
*/
}

function selectswunit($sel,$swunit){
 if($sel == $swunit){
  return "selected=selected";
 }
 else{
  return "";
 }
}

?>

<script type=text/javascript>
function jschange(){
	document.myform.changeflag.value=1;
}

function jsreset(){
	document.myform.changeflag.value=0;
	document.myform.exnum.value=0;
}

function jsverifylab(ele,ref){
	//alert(ele.value + ' ' + ref);
	var uval = ele.value;
	var err = uval - ref;
	//alert('Error = ' + uval + ' - ' + ref + ' = ' + err);
	if (Math.abs(err) > 480){
		alert('Your marking is errorneous by ' + err + ' samples.');
		ele.value='';
	}
/*
	else{
		alert("Correct value = " + ref);
	}
*/
}

function loadwavfile(filename){
	var speechtooljs=document.getElementsByName('speechtool')[0];
	
	var filename = 'ex' + <?php echo $exnum;?> + '.wav';
	//alert(filename);
	speechtooljs.setAudioFile(filename);
	speechtooljs.loadAudio();

}

function jssubmitform(){
	document.myform.changeflag.value=1;
	document.myform.submit();
}

</script>

<table width=900><tr>
<td><h2>Part-2: Segmentation with feedback </h2></td>
<td width=200 align=right><a href=part1.php><b>&lt;&lt;Part-1</b></a> || <a href=part3.php><b>Part-3 &gt;&gt;</b></a></td>
</tr></table>

<form method=post name=myform id=myform>
<select name=exnum id=exnum onchange=jssubmitform()>
<option name=ex0 value=0 >Select an example</option>
<option name=ex1 value=1 <?php if($exnum=='1') echo 'selected=selected';?> >Ex-1: Hindi</option>
<option name=ex2 value=2 <?php if($exnum=='2') echo 'selected=selected';?> >Ex-2: Telugu</option>
<option name=ex3 value=3 <?php if($exnum=='3') echo 'selected=selected';?> >Ex-3: Kannada</option>
<option name=ex4 value=4 <?php if($exnum=='4') echo 'selected=selected';?> >Ex-4: Tamil</option>
</select>
<input type=submit name=go value=Reset onclick=jsreset() />

<input type=hidden name=changeflag value=0 />

<?php
if($changeflag == 1){

 echo "<font size=-1>Change subword unit: </font>";
 echo "<select name=swunit onchange=jssubmitform() >";
 echo "<option name=syl value=syl ".selectswunit('syl',$swunit).">Syllable</option>";
 echo "<option name=phn value=phn ".selectswunit('phn',$swunit).">Phoneme</option>";
 echo "<option name=wrd value=wrd ".selectswunit('wrd',$swunit).">Word</option>";
 echo "</select>";

 displaysent($exnum,$swunit);

 echo "<font size=-1>";
 echo "<ol>";
 echo "<li>Zoom, select and listen to selected portions of the speech waveform to identify subword boundaries.</li>";
 echo "<li>Enter the subword unit boundaries in the table provided.</li>";
 echo "<li>Repeat the experiment by changing the subword unit to word and phoneme.</li>";
 echo "</ol>";
 echo "</font>";

 echo "<table border=1><tr>";
 echo "<td width=620 valign=top>";
 echo "<applet code=audioTransport/SVLAudioTool.class archive='media/speechAnalysis.jar' name='speechtool' width=600 height=400>";
 echo "<param name=foo value='bar'>";
 echo "Please install the latest version of the Java plugin for your browser. <br>";
 echo "</applet><br>";
 echo "</td>";
 echo "<td width=200 align=center valign=top>";
 echo "<font size=-1>Subword unit boundaries</font>";
 displaylabels($exnum,$swunit);
 echo "</td>";
 echo "</tr></table>";

 echo '<script type="text/javascript">';
 echo "loadwavfile('ex".$exnum.".wav');";
 echo '</script>';

}
?>
</form>

</html>

