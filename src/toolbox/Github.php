<?php
namespace CxQ\Toolbox;
class Github{
          function downloadArchive($owner,$repo,$username,$password, $outFileName,$ref='main'){  
               //Downloads private archive
               $url="https://api.github.com/repos/{$owner}/{$repo}/zipball/$ref";
               $ch = curl_init();
               
               curl_setopt($ch, CURLOPT_URL,$url);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
               curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
               curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
               
               curl_setopt($ch, CURLOPT_HTTPHEADER, [
                   "Authorization: Bearer {$password}",
                   'Accept: application/vnd.github+json',
                   'X-GitHub-Api-Version: 2022-11-28',
                   'User-Agent: '.get_class()
               ]);
               
               $result=curl_exec ($ch);
               $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
               curl_close ($ch);

               file_put_contents($outFileName, $result);
               return $httpcode;
          }
}