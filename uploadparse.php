<?php
// In PHP version earlier than 4.1.09 $HTTP_POST_FILES should be used instead
// of $_FILES.

$uploaddir = './models/';
//does file exist already?
if (file_exists($uploaddir . basename($_FILES['userfile']['name']))) {
     echo "A file already exists under that name, please rename your file";
}
else {
//create new model file
$uploadfile = fopen($uploaddir . basename($_FILES['userfile']['name']),"a");
//open uploaded file to read
$temp = fopen($_FILES['userfile']['tmp_name'],"r");

$vertindex = 1;
//open bounding array tag
fputs($uploadfile, "[");
while(!feof($temp))
{
     // get a line 
     $line = fgets($temp, 3000);
     // Split the input string into words as an array
     $arrWords = explode( ' ', $line );  
     // Count the words in the string
     $numWords = count( $arrWords ); 
     // if first word is v - vertex
     if($arrWords[0] == "v"){
          $arrWords[1]*=10;
          $arrWords[2]*=10;
          $arrWords[3]*=10;
          $vertex[$vertindex]="[$arrWords[1], $arrWords[2], $arrWords[3]],";
          $vertindex++;
     }
     // if first word is f - face 
     if($arrWords[0] == "f"){
          //open polygon array tag
          fputs($uploadfile, "[");
          //cycle through verticies of the polygon
          for ($i=1; $i<$numWords; $i++){
               $vert = $arrWords[$i];
               $vert=trim($vert);
               if (strpos ($vert, "/")){
                    $vert=substr($vert,0,strpos ($vert, "/"));
               }
               fputs($uploadfile, "{$vertex[$vert]}");
          }
          //ensure closing of polygons
          $vert = $arrWords[1];
          $vert=trim($vert);
          fputs($uploadfile, "{$vertex[$vert]}");
          //close polygon array tag
          fputs($uploadfile, "],");
     }
}
//close bounding array tag
fputs($uploadfile, "]");
//close files
fclose($uploadfile);
fclose($temp);
echo "File is valid, and was successfully uploaded.\n";
chmod ("./models/".$_FILES['userfile']['name'],0644);
}

echo "<script language=javascript>";
echo "setTimeout(\"window.location='./index.php?mod=".$_FILES['userfile']['name']."'\", 2000);";
?>
</script>
