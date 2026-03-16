<?php
// public/aluno/notas.php
require_once __DIR__ . '/../../config/bootstrap.php';
requireRole(ROLE_ALUNO);

$userId = currentUser()['id'];
$pdo    = getDB();

// Buscar todas as notas do aluno, com info da pauta e UC
$stmt = $pdo->prepare("
    SELECT
        n.nota_final,
        uc.nome       AS uc_nome,
        uc.codigo     AS uc_codigo,
        c.id          AS curso_id,
        c.nome        AS curso_nome,
        p.ano_letivo,
        p.epoca
    FROM notas n
    JOIN pautas p                  ON p.id    = n.pauta_id
    JOIN unidades_curriculares uc  ON uc.id   = p.uc_id
    JOIN cursos c                  ON c.id    = p.curso_id
    WHERE n.aluno_id = ?
    ORDER BY p.ano_letivo DESC, c.nome, uc.nome, p.epoca
");
$stmt->execute([$userId]);
$notas = $stmt->fetchAll();

include __DIR__ . '/../../views/aluno/notas.php';