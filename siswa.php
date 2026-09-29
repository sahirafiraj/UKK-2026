<?php
session_start();
include "config/koneksi.php";

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {

}