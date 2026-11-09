<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product = [
    'id'    => $_POST['id'],
    'name'  => $_POST['name'],
    'price' => $_POST['price'],
    'image' => $_POST['image'],
    'qty'   => 1
  ];

  $_SESSION['cart'][] = $product;
  header('Location: ' . $_SERVER['HTTP_REFERER']);
  exit();
}
