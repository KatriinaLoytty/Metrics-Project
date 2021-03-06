<?php
/*Shahzad Choudhary 99707
Metrics Monitoring Tool
Project Work 2014/2015
Updated: 7.2.2015
This file serves to update a particular Facebook group's list of members in the DB.*/
set_include_path ("../Composer/files/facebook/php-sdk-v4/facebook-facebook-php-sdk-v4-e2dc662");
include "autoload.php";
include('../Login/db_connection.php');
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\GraphObject;
error_reporting(E_ALL);
session_start();

if ((!isset($_GET["group"])) || (!isset($_SESSION["token"])))
  header("Location:login.php");
if (!is_numeric($_GET["group"]))
  exit("Make sure that the group ID contains digits only.");
FacebookSession::setDefaultApplication('777065655684035', '3648579cf4a413d1dfe490304456cd4c');
$session = new FacebookSession($_SESSION["token"]);
$request = new FacebookRequest($session, 'GET', "/".$_GET["group"]."/members");
try{$response = $request->execute();
$graphObject = $response->getGraphObject(GraphUser::className());
$outcome=$graphObject->getProperty('data')->asArray();

$id=$_GET["group"];
$query="SELECT group_id FROM facebook_group WHERE fgroup_id=".$id." LIMIT 1";
$result=mysqli_query($con,$query);
if (($result) && (mysqli_num_rows($result)>0)) //a successful query might return a valid result, yet with no rows
{
  $tmp=mysqli_fetch_array($result);
  $id=$tmp[0];
  mysqli_query($con,"DELETE FROM link_table WHERE group_id=".$id); //clear all existing group relations

  $query="SELECT member_id FROM facebook_member WHERE member_name IN ("; //questionable - fetch all members again
  foreach ($outcome as $i)
    $query=$query."'".mysqli_real_escape_string($con,$i->name)."',";
  $query=substr($query,0,count($query)-2).")";
  $result=mysqli_query($con,$query);

  $query="INSERT INTO link_table (group_id, member_id) VALUES "; //and write the new relations, using the above query's results
  while ($r=mysqli_fetch_array($result))
    $query=$query."(".$id.",".$r[0]."), ";
  $query=substr($query,0,count($query)-3);
  $result=mysqli_query($con,$query);
}
else $result=FALSE;	
mysqli_close($con);
if ($result==TRUE) echo "Updated successfully"; //doesn't really report on any errors above
else echo "Did not update (is the group added to DB?)";
}
catch (Exception $e)
{echo "Caught exception: ".$e->getMessage()."\n";}
?>