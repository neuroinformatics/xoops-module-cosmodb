<?php

   // making clone cosmoDB
   // University of Hyogo, Ikeno Lab.

   function makeClone($oldName, $newName)
   {
       $dir = dir('.');
       while (($ent = $dir->read()) != false) {
           if (is_file($ent) && $ent != 'clone.php' && $ent != 'clone.php~') {
               echo  "Modified: $ent <br>";
               $orgFile = '_'.$ent;
               copy($ent, $orgFile);

               $fp = fopen($orgFile, 'r');
               $fq = fopen($ent, 'w');
               while (!feof($fp)) {
                   $line = fgets($fp);
                   if ((strpos($line, $oldName) != 0) &&
            (strpos($line, $newName) == 0)) {
                       $line2 = str_replace($oldName, $newName, $line);
                   } else {
                       $line2 = $line;
                   }
                   fwrite($fq, $line2);
               }
               fclose($fp);
               fclose($fq);
               unlink($orgFile);
           }
       }
   }

   // detect newdb name

   $oldName = 'newdb';

   $dir = getcwd();
   $str = strstr($dir, 'newdb');
   $pos = strpos($str, 'extension');
   $newName = substr($str, 0, $pos - 1);
   $newNumber = substr($newName, 5);

   // newdb/extension dir
   chdir('.'); makeClone($oldName, $newName);
   chdir('convert'); makeClone($oldName, $newName); chdir('..');

   // newdb
   chdir('..'); makeClone($oldName, $newName);

   // newdb/admin
   chdir('admin'); makeClone($oldName, $newName);
   chdir('upgrade'); makeClone($oldName, $newName);
   chdir('..');
   chdir('..');

   // newdb/blocks
   chdir('blocks'); makeClone($oldName, $newName); chdir('..');

   // newdb/class
   chdir('class'); makeClone($oldName, $newName); chdir('..');

   // newdb/include
   chdir('include'); makeClone($oldName, $newName); chdir('..');

   // newdb/sql
   chdir('sql'); makeClone($oldName, $newName); chdir('..');

   // newdb/templates/blocks
   chdir('templates'); chdir('blocks');
   makeClone($oldName, $newName);
   chdir('..'); chdir('..');

   echo  'Modified database name<br>';

   // newdb/language
   chdir('language');
   chdir('english');
   $newModule = 'CosmoDB'.$newNumber;
   makeClone('CosmoDB', $newModule);
   chdir('..');
   chdir('japanese');
   $newModule = 'CosmoDB'.$newNumber;
   makeClone('CosmoDB', $newModule);
   chdir('..');
   chdir('..');

   echo  'Modified module name <br>';

   // newdb/images

   chdir('images');
   copy('logo.gif', 'logo00.gif');
   if ($newNumber + 0 < 10) {
       $newLogo = 'logo0'.$newNumber.'.gif';
   } else {
       $newLogo = 'logo'.$newNumber.'.gif';
   }
   copy($newLogo, 'logo.gif');
   chdir('..');

   echo  'Changed logo image<br>';

   chdir('extension');

   echo  '<br>Modification was done. <br>';
