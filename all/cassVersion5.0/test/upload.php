<?php

if($_FILES['myfile']['tmp_name'] != null &&  $_FILES['myfile']['name'] != null){


 echo "<pre>";
 print_r($_FILES);
 echo "<pre>";
 $file_size=$_FILES['myfile']['size'];//��ȡ�ļ��Ĵ�С
 echo "��ǰ�ļ���С��".$file_size."B=".(int)$file_size/(1024)."KB<br>";
 $file_type=$_FILES['myfile']['type'];
 //��ο����û��ϴ����ļ�����
 if($file_type=='image/jpeg' || $file_type=='image/pjpeg' ){//ֻ�����ϴ�jpg��ʽ��ͼƬ
 //��ο����û��ϴ����ļ���С    
     if($file_size>2*1024*1024){
         echo "�ļ���С������2M����...";
 //    echo "<meta content=\"3,http://www.baidu.com\" http-equiv=\"refresh\" />";//ҳ����ת
         exit();
 }
     if (is_uploaded_file($_FILES['myfile']['tmp_name'])){//ͨ��if����ж��ļ��Ƿ��ϴ��ɹ�
         $move_from=$_FILES['myfile']['tmp_name'];
 //��η�ֹ�û�����ͼƬ����===>���Ը����û�������û����������ļ���
         $user_path=$_SERVER['DOCUMENT_ROOT']."/upload/".$_POST['name'];//���ϴ����ļ��Ƶ�����ϣ����Ŀ¼��
         if (!file_exists($user_path)) {//������ļ��в����ڣ��򴴽�֮���������ľ�Ӧ�ð�ͼƬ�Ƶ�����ļ�������
             mkdir($user_path);
         }
//��η�ֹͬһ���û��ϴ����ļ������ͻ����
         $suffix=substr($_FILES['myfile']['name'],strrpos($_FILES['myfile']['name'],'.'));//ȡ���ļ��ļ���׺
         $move_to=$user_path."/".time().rand().$suffix;//ͨ��time(),rand()���Ϳ��Խ���ļ�����ͻ����
         if(move_uploaded_file($move_from,iconv("UTF-8","GBK",$move_to))){
         echo "�ļ��ϴ��ɹ�!";
         echo "·��Ϊ:".$move_to;
 //        echo "<meta content=\"3,http://www.baidu.com\" http-equiv=\"refresh\" />";
         }
     }else{
         echo "�ļ��ϴ�ʧ��...";
     //    echo "<meta content=\"3,http://www.baidu.com\" http-equiv=\"refresh\" />";//ҳ����ת
         die();
     }    
 }
 else{
     echo "<script>alert('ֻ֧��ͼƬ�ϴ�������ֻ֧��jpg��ʽ��ͼƬ');</script>";
 //    echo "<meta content=\"3,http://www.baidu.com\" http-equiv=\"refresh\" />";//ҳ����ת
     die();
 }
 
 }
 ?>