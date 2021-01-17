<?php

	$pdo = new PDO('mysql:host=localhost;dbname=productes_general', "root", "Marzo20!");

try
{
	$pdo->beginTransaction();
	$sql_productes = "SELECT id, referencia, nom, preu, iva, categoria, imatge FROM productes_general.productes";
	$smt_productes = $pdo->prepare($sql_productes);
	$smt_productes->execute();
	$ar_productes = $smt_productes->fetchAll(PDO::FETCH_ASSOC);
	
	$pdo->commit();
}
catch(Exception $exception)
{
	echo $exception->getMessage();
	$pdo->rollBack();
}

echo json_encode($ar_productes);
