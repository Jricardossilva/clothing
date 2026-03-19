<?php
// index.php
$page = $_GET['page'] ?? 'dashboard';
$subpage = $_GET['subpage'] ?? 'index';

// Monta o caminho do arquivo de conteúdo
$contentFile = "pages/$page/$subpage.php";

// Inclui o template base
include 'base.php';