<?php // views/aluno/notas.php
$pageTitle = 'As Minhas Notas';
ob_start(); ?>

<div class="page-header">
    <div>
        <p>Portal Académico do Aluno</p>
        <h1>As Minhas Notas</h1>
    </div>
    <a href="<?= APP_URL ?>/aluno/dashboard.php" class="btn btn-secondary btn-sm">← Voltar</a>
</div>

<?php if (empty($notas)): ?>
    <div class="card" style="text-align:center;padding:3rem;">
        <div style="font-size:3rem;margin-bottom:1rem;opacity:.3;">📊</div>
        <p style="color:var(--text-muted);">Ainda não existem notas lançadas para si.</p>
    </div>
<?php else: ?>

<?php
// Agrupar por curso → ano letivo → notas
// Guardar também o curso_id para o link "Ver notas por UC"
$agrupado    = [];
$cursosIds   = [];
foreach ($notas as $n) {
    $agrupado[$n['curso_nome']][$n['ano_letivo']][] = $n;
    $cursosIds[$n['curso_nome']] = $n['curso_id'] ?? null;
}

// Calcular estatísticas GLOBAIS (todos os cursos)
$comNotaGlobal   = array_values(array_filter($notas, fn($n) => $n['nota_final'] !== null));
$aprovadasGlobal = array_filter($comNotaGlobal, fn($n) => $n['nota_final'] >= 10);
$reprovGlobal    = array_filter($comNotaGlobal, fn($n) => $n['nota_final'] < 10);

// Calcular estatísticas POR CURSO
$statsPorCurso = [];
foreach ($agrupado as $cursoNome => $anos) {
    $todasNotasCurso = [];
    foreach ($anos as $anoNotas) {
        foreach ($anoNotas as $n) {
            $todasNotasCurso[] = $n;
        }
    }
    $comNota   = array_values(array_filter($todasNotasCurso, fn($n) => $n['nota_final'] !== null));
    $aprovadas = array_filter($comNota, fn($n) => $n['nota_final'] >= 10);
    $reprov    = array_filter($comNota, fn($n) => $n['nota_final'] < 10);
    $media     = count($comNota)
        ? array_sum(array_column($comNota, 'nota_final')) / count($comNota)
        : null;

    $statsPorCurso[$cursoNome] = [
        'total'     => count($comNota),
        'aprovadas' => count($aprovadas),
        'reprov'    => count($reprov),
        'media'     => $media,
    ];
}
?>

<!-- Resumo global (só aparece se houver mais de um curso) -->
<?php if (count($agrupado) > 1): ?>
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1rem;">Resumo Geral</div>
    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:0;">
        <div class="stat-card">
            <div class="stat-num"><?= count($comNotaGlobal) ?></div>
            <div class="stat-label">Notas lançadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--success);"><?= count($aprovadasGlobal) ?></div>
            <div class="stat-label">Aprovações</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--danger);"><?= count($reprovGlobal) ?></div>
            <div class="stat-label">Reprovações</div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Notas agrupadas por curso -->
<?php foreach ($agrupado as $cursoNome => $anos):
    $s = $statsPorCurso[$cursoNome];
?>
    <div class="card">

        <!-- Cabeçalho do curso com estatísticas do curso -->
        <div style="display:flex;justify-content:space-between;align-items:flex-start;
                    padding-bottom:1rem;margin-bottom:1.25rem;border-bottom:1px solid var(--border);
                    flex-wrap:wrap;gap:1rem;">
            <div class="card-title" style="margin:0;padding:0;border:none;">
                <?= e($cursoNome) ?>
            </div>
            <?php if (!empty($cursosIds[$cursoNome])): ?>
            <a href="<?= APP_URL ?>/aluno/notas-curso.php?id=<?= $cursosIds[$cursoNome] ?>"
               class="btn btn-secondary btn-sm" style="flex-shrink:0;">
               Ver notas por UC →
            </a>
            <?php endif; ?>
            <!-- Stats do curso -->
            <div style="display:flex;gap:1.25rem;flex-wrap:wrap;">
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;
                                color:var(--crimson);line-height:1;">
                        <?= $s['media'] !== null ? number_format($s['media'], 1) : '—' ?>
                    </div>
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;
                                letter-spacing:.07em;color:var(--text-muted);margin-top:.2rem;">
                        Média do Curso
                    </div>
                </div>
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;
                                color:var(--success);line-height:1;">
                        <?= $s['aprovadas'] ?>
                    </div>
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;
                                letter-spacing:.07em;color:var(--text-muted);margin-top:.2rem;">
                        Aprovações
                    </div>
                </div>
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;
                                color:var(--danger);line-height:1;">
                        <?= $s['reprov'] ?>
                    </div>
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;
                                letter-spacing:.07em;color:var(--text-muted);margin-top:.2rem;">
                        Reprovações
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabelas por ano letivo -->
        <?php foreach ($anos as $anoLetivo => $ucs): ?>
            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
                        color:var(--text-muted);margin-bottom:.6rem;">
                Ano Letivo <?= e($anoLetivo) ?>
            </div>
            <div class="table-wrap" style="margin-bottom:1.5rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Unidade Curricular</th>
                            <th>Época</th>
                            <th>Nota</th>
                            <th>Situação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ucs as $n): ?>
                        <tr>
                            <td>
                                <strong style="color:var(--crimson);font-size:.75rem;
                                               text-transform:uppercase;letter-spacing:.05em;">
                                    <?= e($n['uc_codigo']) ?>
                                </strong>
                                <span style="color:var(--text-muted);margin:0 .3rem;">·</span>
                                <?= e($n['uc_nome']) ?>
                            </td>
                            <td><span class="badge badge-rascunho"><?= e($n['epoca']) ?></span></td>
                            <td>
                                <strong style="font-size:1.1rem;font-family:'Playfair Display',serif;">
                                    <?= $n['nota_final'] !== null ? number_format($n['nota_final'], 1) : '—' ?>
                                </strong>
                            </td>
                            <td>
                                <?php if ($n['nota_final'] === null): ?>
                                    <span class="badge badge-rascunho">Por lançar</span>
                                <?php elseif ($n['nota_final'] >= 10): ?>
                                    <span class="badge badge-aprovada">Aprovado</span>
                                <?php else: ?>
                                    <span class="badge badge-rejeitada">Reprovado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

    </div>
<?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';