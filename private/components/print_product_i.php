<?php
$_SESSION['id']=$products[$i]['id'];
$_SESSION['name']=$products[$i]['name'];
$_SESSION['prices']=$products[$i]['prices'];
$_SESSION['image']=$products[$i]['image'];
$_SESSION['customizations']=$products[$i]['image'];
include("/home/bitnami/bakehouse/private/components/product.php");
?>