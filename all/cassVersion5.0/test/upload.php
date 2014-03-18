<?php

if($_FILES['myfile']['tmp_name'] != null &&  $_FILES['myfile']['name'] != null){


 echo "<pre>";
 print_r($_FILES);
 echo "<pre>";
 $file_size=$_FILES['myfile']['size'];//获取文件的大小
 echo "当前文件大小：".$file_size."B=".(int)$file_size/(1024)."KB<br>";
 $file_type=$_FILES['myfile']['type'];
 //如何控制用户上传的文件类型
 if($file_type=='image/jpeg' || $file_type=='image/pjpeg' ){//只允许上传jpg格式的图片
 //如何控制用户上传的文件大小    
     if($file_size>2*1024*1024){
         echo "文件大小限制在2M以内...";
 //    echo "<meta content=\"3,http://www.baidu.com\" http-equiv=\"refresh\" />";//页面跳转
         exit();
 }
     if (is_uploaded_file($_FILES['myfile']['tmp_name'])){//通过if语句判断文件是否上传成功
         $move_from=$_FILES['myfile']['tmp_name'];
 //如何防止用户覆盖图片问题===>可以根据用户输入的用户名来创建文件夹
         $user_path=$_SERVER['DOCUMENT_ROOT']."/upload/".$_POST['name'];//将上传的文件移到你所希望的目录下
         if (!file_exists($user_path)) {//如果此文件夹不存在，则创建之，接下来的就应该把图片移到这个文件夹中来
             mkdir($user_path);
         }
//如何防止同一个用户上传的文件名相冲突问题
         $suffix=substr($_FILES['myfile']['name'],strrpos($_FILES['myfile']['name'],'.'));//取得文件文件后缀
         $move_to=$user_path."/".time().rand().$suffix;//通过time(),rand()，就可以解决文件名冲突问题
         if(move_uploaded_file($move_from,iconv("UTF-8","GBK",$move_to))){
         echo "文件上传成功!";
         echo "路径为:".$move_to;
 //        echo "<meta content=\"3,http://www.baidu.com\" http-equiv=\"refresh\" />";
         }
     }else{
         echo "文件上传失败...";
     //    echo "<meta content=\"3,http://www.baidu.com\" http-equiv=\"refresh\" />";//页面跳转
         die();
     }    
 }
 else{
     echo "<script>alert('只支持图片上传，并且只支持jpg格式的图片');</script>";
 //    echo "<meta content=\"3,http://www.baidu.com\" http-equiv=\"refresh\" />";//页面跳转
     die();
 }
 
 }
 ?>