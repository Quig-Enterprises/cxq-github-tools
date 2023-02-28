<?php
namespace CxQ\Toolbox;
class Files{
    /******************************************
    *   File System Operations
    *******************************************/
    public static function getFileExtension($filename){
        return pathinfo($filename, PATHINFO_EXTENSION);
    }
    public static function getFileName($filename){
        return pathinfo($filename, PATHINFO_FILENAME);
    }
    public static function buildPath(){
        $path='';
        $args = func_get_args();

        if(count($args)>0){
            foreach ($args as $arg) {
                if(!empty($arg)){
                    if(is_array($arg)){
                        $paths[]=forward_static_call_array(array('self','buildPath'),$arg);
                    }elseif(is_string($arg)) {
                        $paths[] = $arg;
                    }
                }
            }
            //\Toolbox::backtrace();
            $path = preg_replace('#/+#','/',join('/', $paths));

            //If the last argument does not contain a period, assume it is not a file and append a trailing '/' if it isn't already there
            if(is_string(end($args)) && strpos(end($args),'.')===false && !\Toolbox::rightCompare(end($args),'/')){
                $path.='/';
            }

            //Preserve Network Paths
            if(\Toolbox::leftCompare($args[0],'//') && !\Toolbox::leftCompare($path,'//')){
                if(\Toolbox::leftCompare($path,'/')){
                    $path='/'.$path;
                }else{
                    $path='//'.$path;
                }
            }

            //Preserve URLs
            foreach(array('http','https','ftp') as $protocol){
                $path = str_replace($protocol.':///',$protocol.'://',str_replace($protocol.':/',$protocol.'://',$path));
            }

        }
        return $path;

    }/* Doesn't work.. and is slow
    public static function fileExistsInDir($fileName,$dir=''){
        if (file_exists($filename)) {
          return true;
        }

        $parts=explode('/',$fileName);

        $candidates = \Toolbox::listFiles($dir, true, '*', array(),count($parts));
        if($case_insentive) $candidates=array_map('strtolower', $candidates);
        $target_filename=$case_insentive?strtolower($fileName):$fileName;

        if(in_array(case_insentive?strtolower($part):$part,$candidates)){
            foreach($candidate as $candidates){

                if($candidate==$target_filename || ($case_insentive && strtolower($candidate)==strtolower($target_filename) )){
                    return $candidate;
                }
            }
          
        }
        return false;
        new dBug2($candidates);
        die();
        $this_dir=$dir;
        $lcaseFilename = strtolower($fileName);
        
        $subfolders=\Toolbox::listSubfolderNames($dir);
        if($case_insentive) $subfolders=array_map('strtolower', $subfolders);
        foreach($parts as $part){
            new dBug2(array($part,$this_dir));
            if(in_array(case_insentive?strtolower($part):$part,$subfolders)){
                $this_dir=\Toolbox::buildPath($this_dir,$part);
                $subfolders=\Toolbox::listSubfolderNames($this_dir);
                if($case_insentive) $subfolders=array_map('strtolower', $subfolders);
            }else{
              new dBug2($part);
                return false;

            }

        }

        new dBug2($this_dir);


        return false;
      
    }  */
    public static function listFiles($directory, $include_subdir=true, $filename_filter='*', $allowed_extensions=array(),$max_depth=40,$current_depth=0){
        $dirs=array($directory);
        if($include_subdir){
            $subdirs = array_filter(glob(\Toolbox::buildPath($directory,'*')), 'is_dir');
            $dirs=array_unique(array_merge($dirs,$subdirs));
        }
        $files=array();
        foreach($dirs as $dir){
            $these_files = array_diff(scandir($dir), array('.', '..'));
            foreach($these_files as $this_file){
                $current_depth++;
                $this_path=\Toolbox::buildPath($dir,$this_file);
                if(!is_dir($this_path)){
                    switch($filename_filter){
                        case '*':
                        

                    }
                    if($filename_filter=='*' || $filename_filter==$this_file){
                        $ext = pathinfo($this_path, PATHINFO_EXTENSION);
                        //new dBug2(array($ext,$allowed_extensions,empty($allowed_extensions)));
                        if(empty($allowed_extensions) || in_array($ext,$allowed_extensions, true)){
                            //new dBug2($this_path);
                            $files[]=$this_path;
                        }else{
                            //new dBug2(array($ext,$allowed_extensions,empty($allowed_extensions)));
                        }
                    }
                }elseif($include_subdir && ($max_depth==0 || $current_depth<$max_depth)){
                    $files=array_merge($files,\Toolbox::listFiles($this_path, $include_subdir, $filename_filter, $allowed_extensions,$max_depth,$current_depth));


                }
            }
        }
        return $files;
        

    }
    public static function listSubfolderNames($directory, $include_subdir=true,$max_depth=40,$current_depth=0){
        /*$dirs=array($directory);
        
        if($include_subdir){
            $subdirs = array_filter(glob(\Toolbox::buildPath($directory,'*')), 'is_dir');
            $dirs=array_unique(array_merge($dirs,$subdirs));
        } */
        if(!file_exists($directory)) return false;
        $dirs = array_diff(scandir($directory), array('.', '..'));

        return $dirs;


    }
    public static function locateFile($directories,$filename,$on_fail_return_first_dir = false,$return_dir_only=false){
        if(!is_array($directories)) $directories=array($directories);

        foreach($directories as $dir){
            $path = \Toolbox::buildPath($dir,$filename);
            if(file_exists($path)){
                return $return_dir_only?$dir:$path;
            }
            //new dBug2($path);new dBug2($path);
        }
        //new dBug2($directories);
        if($on_fail_return_first_dir){
            $dir=reset($directories);
            return $return_dir_only?$dir:\Toolbox::buildPath($dir,$filename);
        }
        

        return false;
    }

    /*not sure if this works...but opted not to use it
    static function validURL($url,$forceRefresh=false){
        $doRefresh=true;
        $retcode=0;
        if(!isset($_SESSION['url_validation_results'])){
            $_SESSION['url_validation_results']=array();

        }else{

            foreach($_SESSION['url_validation_results'] as $result=>$urls){
                if(in_array($url,$urls)){
                    $retcode=$result;
                    $doRefresh=false;
                    continue;
                }
            }
        }

        if($doRefresh){
            $ch = curl_init($url);
    
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // $retcode >= 400 -> not found, $retcode = 200, found.
            curl_close($ch);
            new dBug2($retcode);
            die();
            if(!isset($_SESSION['url_validation_results'][$retcode])){
               $_SESSION['url_validation_results'][$retcode]=array();
            }
            $_SESSION['url_validation_results'][$retcode][]=$url;
        }

        return $retcode==200;
    }
    */


}
