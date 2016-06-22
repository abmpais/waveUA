<?php
// Version: 2009-06-09
// última versão disponível em http://idp.ua.pt/common/SP-demo/
//
// Modificado por: Carlos Costa @ CICUA <costa@ua.pt>

// Specify your attribute-map.xml file and make sure it is readable by the web server
//$attribute_map_file = '/etc/shibboleth/attribute-map.xml';
$attribute_map_file = 'C:/opt/shibboleth-sp/etc/shibboleth\attribute-map.xml';

//Set header
//header('Content-type: text/html; charset=utf-8');
header('Content-type: text/html; charset=ISO-8859-1');

// Show source
if (isset($_REQUEST['source'])) {
    highlight_file(__FILE__);
    exit;
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
    <title>UA/Service Provider - Demo</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
<!--
a
{
    color: #1B3E93;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
}

a:hover
{
    color: #FE911B;
    text-decoration: underline;
}

body 
{
    background-color: white;
    font-family: Verdana, Arial, Helvetica, sans-serif;
}

h1
{
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
}

.logo
{
    color: white;
    text-decoration: none;
}

.border-blue
{
    border-style: solid;
    border-width: 1px;
    border-color: rgb(0,43,127);
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 13px;
}

.border-orange
{
    border-style: solid;
    border-width: 1px;
    border-color: rgb(226,140,5);
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 13px;
}

.blue
{
    color: rgb(0,43,127);
}

.orange
{
    color: rgb(226,140,5);
}
-->
</style>
</head>

<body>
<div align="center">

<table border="0" cellpadding="0" cellspacing="0">
<!-- top left corner + middle bar + right corner -->
<tr>
    <td class="top-left"></td>
    <td class="top-middle"></td>
    <td class="top-right"></td>
</tr>
<!-- left border + content + right border -->
<tr>
    <!-- left border -->
    <td class="middle-left"></td>
    <!-- content -->
    <td>
    <a class="logo" href="http://www.ua.pt">
        <img alt="UA logo" src="http://idp.ua.pt/common/images/logo_UA.gif" style="padding-bottom: 7px" /></a>
    <h1>UA/Service Provider - Demo</h1>

<!-- table content -->
<table width="100%">
<tr>
<td class="blue">Atributos/Campos</td><td class="orange">Valores</td>
</tr>
<?php 

$attribute_map_lines = file($attribute_map_file);
$attribute_map = '';
foreach($attribute_map_lines as $line){
    $attribute_map .= $line;
}

$p = xml_parser_create();
xml_parse_into_struct($p, $attribute_map, $vals, $index);
xml_parser_free($p);

$HTTP_SHIB_HEADERS = array();
foreach ($vals as $element){
    if ($element['tag'] == 'ATTRIBUTE' && isset($element['attributes']['ID'])){
        $HTTP_SHIB_HEADERS[$element['attributes']['ID']] = $element['attributes']['ID'];
    }
}

// Dump all received Shibboleth attributes
$status = '00';
foreach ($_SERVER as $key => $value){
    // Do we have any variables defined in attribute map
    if (isset($HTTP_SHIB_HEADERS[$key])){
        $status[1] = '1';
        echo '<tr valign="top">';
        echo '<td class="border-blue" valign="top">'.$HTTP_SHIB_HEADERS[$key].'</td>';
        $clean_value = ereg_replace('\$','<br>',htmlspecialchars(stripslashes($value)));
        if (ereg(';', $clean_value)){
            $clean_value = ereg_replace(";",'</tt></li><li><tt>',$clean_value); 
            $clean_value = '<ul><li><tt>'.$clean_value.'</tt></li></ul>';
        }
        else {
            $clean_value = '<tt>'.$clean_value.'</tt>';
        }
        
        echo '<td class="border-orange">'.$clean_value.'</td>';
        echo '</tr>';
    }
    // or any attributes starting with Shib-
    elseif (eregi('Shib', $key) ) {
        $status[0] = '1';
        echo '<tr>';
        echo '<td class="border-blue" valign="top"><span style="color: grey; font-style:italic;">'.$key.'</span></td>';
        echo '<td class="border-orange"><span style="color: grey; font-style:italic;"><tt>'.wordwrap(htmlspecialchars("aaaa"), 70, "<br/>\n", true).'</tt></span></td>';
        echo "</tr>\n";
           
    }
	elseif (eregi('SP', $key) ) {
	    $status = '11';
     
	}
	
	
}
if (isset($_REQUEST['assertions'])) {
      
    $counter = 1;
    foreach ($_SERVER as $key=>$value){
// by vodka-START
		if (eregi('SP', $key) ) {
	    $status = '11';
        echo '<tr>';
        echo '<td class="border-blue" valign="top"><span style="color: grey; font-style:italic;">'.$key.'</span></td>';
        echo '<td class="border-orange"><span style="color: grey; font-style:italic;"><tt>'.wordwrap(htmlspecialchars($value), 70, "<br/>\n", true).'</tt></span></td>';
        echo "</tr>\n";
     
    }
// by vodka-END
        // Check if it is an assertion
        if (ereg('Shib-Assertion-Count', $key)  || !eregi('Shib-Assertion', $key)) {
            continue;
        }
        
        // Download the assertion
        $value = ereg_replace('dieng.switch.ch','127.0.0.1',stripslashes($value));
        
        $assertion = '';
        $handle = fopen($value, 'rb');
        if ($handle){
            while (!feof($handle)) {
                $tmp = fread($handle, 8192);
                if (!$tmp){
                    break;
                }
                $assertion .= $tmp;
            }
            fclose($handle);
        }
        
        echo '<tr><td colspan="2" class="border-orange"><h4>Assertion '.$counter.':</h4>';
        $assertion = ereg_replace('<', "\n<", $assertion);
        $assertion = preg_replace('/>(.+)/', ">\n$1", $assertion);
        $assertion = preg_replace("/\s([\S]+)=\"([^\"]+)/", "\n$1=\"$2",$assertion);
        $elements = preg_split('/\n/',$assertion);
        echo '<pre>';
        $indent = -1;
        foreach ($elements as $element){
            if (ereg('</', $element) && ereg('/>', $element)){
                echo '';
            }
            else if (ereg('/>', $element)){
                $reduce_indent = true;
                echo '';
            }
            elseif (ereg('</', $element)){
                $reduce_indent = true;
            }
            elseif (ereg('<', $element)){
                $indent++;
            }
            else {
                echo '&nbsp;&nbsp;';
            }
            
            for($i = 0; $i < $indent; $i++)
                echo '    ';
            
            // Syntax highlighting
            $element = ereg_replace('<','&lt;',$element);
            $element = ereg_replace('>','&gt;',$element);
            
            $element = preg_replace('/(\w+)="(.+)"/', " <span style=\"color:green\">$1</span>=<span style=\"color:brown\">&quot;$2&quot;</span>", $element);
            $element = preg_replace('/(&lt;.+)/', "<span style=\"color:blue\">$1</span>", $element);
            $element = ereg_replace('span>&gt;', "span><span style=\"color:blue\">&gt;</span>", $element);
            
            
            if (!ereg('=', $element) && !ereg('&lt;', $element))
                echo  '<span style="color:black;">'.wordwrap($element."\n", 120, "\n", 1).'</span>';
            else
                echo  wordwrap($element."\n", 120, "\n", 1);
            
            if ($reduce_indent){
                $indent--;
                $reduce_indent = false;
            }
        }
        
        echo '</pre>';
        echo '</td></tr>';
        
        $counter++;
    }
    
}

// Check status

if ($status == '10' ) {
    echo '<tr>';
    echo '<td colspan=2><b>Sessão Shibboleth válida, mas não possui atributos de utilizador!</b></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan=2>Informe os administradores do IdP da sua Instituição/Organização:<br>Por favor verifique a metadata e os ficheiros de mapping e filtering.</td>';
    echo '</tr>';
}

elseif ($status == '00') {
    echo '<tr>';
    echo '<td colspan=2><b>Sessão Shibboleth não presente!</b></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan=2>Esta página web provavelmente não está protegida por Shibboleth.<br>Informe os administradores do IdP da sua Instituição/Organização:<br>Por favor verifique o servidor web a metadata e a configuração Shibboleth</td>';
    echo '</tr>';
}

//else {

// by vodka-END
?>
<tr>
    <td colspan="2" align="center">
<?php
	if ($status == '11' ) {
            header("Location: http://webexmobilie.web.ua.pt/registered.php"); 
                     ?>
            <script>
                var abv="<?php echo $_SERVER['HTTP_IUPINOSP'];  ?>";
                console.log(abv);
                </script>
	<?php
    $token= $_SERVER['HTTP_IUPINOSP'];
    $username= $_SERVER['HTTP_UAFULLNAMENOSP'];
        $con=mysqli_connect("mysql-sa.mgmt.ua.pt","deca-wmob-dbo","C5y7FHt3aM9Dqr7E","deca-wmob");
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

mysqli_query($con,"INSERT INTO `deca-wmob`.`users` (`idusers`, `username`, `type_idtype`, `token`) VALUES ('7', '$username', '1', '$token');");

mysqli_close($con);
        
        	if (isset($_REQUEST['assertions'])) {
			echo '<a href=".">Esconder atributos Shibboleth</a>'; 
		} 
		else {
			echo '<a href="?assertions">Mostrar atributos Shibboleth</a>'; 
		}
    }
    if (isset($_REQUEST['all_variables'])) {
        echo ' | <a href=".">Esconder todas as variáveis HTTP</a>'; 
    }
    else {
        echo ' | <a href="?all_variables">Mostrar todas as variáveis HTTP</a>'; 
    }
?>
 | <a href="?source">Mostrar código PHP</a>
    </td>
</tr>
<?php
//}
?>
</table>
<!-- end content -->
</td>
  <!-- right border -->
  <td class="middle-right"></td>
</tr>
<!-- bottom left corner + middle bar + right corner -->
<tr>
  <td class="bottom-left"></td>
  <td><img src="http://www.switch.ch/aai/design/images/bottomcenter.gif" height="14" width="100%" alt="bottomcenter" /></td>
  <td class="bottom-right"></td>
</tr>
</table>

<!-- all HTTP variables -->
<?php
    if (isset($_REQUEST['all_variables'])) {
        ?>
<p>&nbsp;</p>
    <table>
        <tr>
            <td><strong>Variáveis de ambiente HTTP</strong></td><td><strong>Valores não processados</strong></td></tr>
        <?php
        ksort($_SERVER);
        foreach ($_SERVER as $key => $value) {
            if ( ereg('^Shib-', $key)  || isset($HTTP_SHIB_HEADERS[$key])) 
            { 
                $class= "border-orange"; 
            }
            else {
                $class= "border-blue";
            }
            echo '<tr valign="top">';
            echo '<td class="'.$class.'">'.$key.'</td>';
            if (is_array($value)){
                echo '<td class="'.$class.'">';
                if (!empty($value)){
                    echo '<ul>';
                    foreach($value as $item){
                        echo '<li><tt>'.wordwrap($item, 70, '<br>', true).'</tt></li>';
                    }
                    echo '</ul>';
                }
                echo '</td>';
            } else {
                echo '<td class="'.$class.'"><tt>'.wordwrap(htmlspecialchars(stripslashes($value)), 70, '<br>', true).'</tt></td>';
            }
            echo "</tr>\n";
        }
    ?>
    </table>
    <?php
    } 
?>
</div>
</body>
</html>
