<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 26.06.13
 * Time: 16:20
 * To change this template use File | Settings | File Templates.
 */

class HelperFile {

    /*
     * получаем размер указанной папки
     * в байтах
     */
    static function getDirSize($dir){

        if(is_dir($dir)){
            $ite=new RecursiveDirectoryIterator(dirname(__FILE__));

            $bytestotal=0;
            $nbfiles=0;
            foreach (new RecursiveIteratorIterator($ite) as $filename=>$cur) {
                $filesize=$cur->getSize();
                $bytestotal+=$filesize;
                $nbfiles++;
                //echo "$filename => $filesize\n";
            }

            $bytestotal=number_format($bytestotal);
            //echo "Total: $nbfiles files, $bytestotal bytes\n";
            return $bytestotal;
        }else{
            return 0;
        }
    }
}