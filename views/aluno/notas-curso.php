<?php // views/aluno/notas-curso.php
$pageTitle = $curso['nome'] . ' — Notas';

// Calcular estatísticas
$comNota   = array_values(array_filter($notasRaw, fn($n) => $n['nota_final'] !== null));
$aprovadas = array_filter($comNota, fn($n) => $n['nota_final'] >= 10);
$reprov    = array_filter($comNota, fn($n) => $n['nota_final'] < 10);
$media     = count($comNota)
    ? array_sum(array_column($comNota, 'nota_final')) / count($comNota)
    : null;

ob_start(); ?>

<div class="page-header">
    <div>
        <p>Portal Académico do Aluno · Notas por UC</p>
        <h1><?= e($curso['nome']) ?></h1>
    </div>
    <div class="actions">
        <a href="<?= APP_URL ?>/aluno/notas.php" class="btn btn-secondary btn-sm">← Todas as Notas</a>
        <a href="<?= APP_URL ?>/aluno/dashboard.php" class="btn btn-secondary btn-sm">Dashboard</a>
    </div>
</div>

<?php if (empty($ucs)): ?>
    <div class="card" style="text-align:center;padding:3rem;">
        <div style="font-size:3rem;opacity:.3;margin-bottom:1rem;">📚</div>
        <p style="color:var(--text-muted);">Curso sem plano curricular definido.</p>
    </div>
<?php else: ?>

    <!-- Estatísticas do curso -->
    <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-num"><?= count($ucs) ?></div>
            <div class="stat-label">UCs do Curso</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= $media !== null ? number_format($media, 1) : '—' ?></div>
            <div class="stat-label">Média</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--success);"><?= count($aprovadas) ?></div>
            <div class="stat-label">Aprovações</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--danger);"><?= count($reprov) ?></div>
            <div class="stat-label">Reprovações</div>
        </div>
    </div>

    <!-- Uma card por UC -->
    <?php foreach ($ucs as $i => $uc):
        $notasUC = $notasPorUC[$i] ?? [];
        $melhorNota = null;
        foreach ($notasUC as $n) {
            if ($n['nota_final'] !== null && ($melhorNota === null || $n['nota_final'] > $melhorNota)) {
                $melhorNota = $n['nota_final'];
            }
        }
    ?>
    <div class="card" style="margin-bottom:1rem;">

        <!-- Cabeçalho UC -->
        <div style="display:flex;justify-content:space-between;align-items:center;
                    padding-bottom:.85rem;margin-bottom:1rem;border-bottom:1px solid var(--border);gap:1rem;">
            <div>
                <span style="font-size:.72rem;font-weight:700;text-transform:uppercase;
                             letter-spacing:.1em;color:var(--crimson);">
                    <?= e($uc['codigo']) ?>
                </span>
                <h3 style="font-family:'Playfair Display',Georgia,serif;font-size:1rem;
                           font-weight:700;margin-top:.2rem;">
                    <?= e($uc['nome']) ?>
                </h3>
                <div style="display:flex;gap:.75rem;margin-top:.3rem;font-size:.78rem;color:var(--text-muted);">
                    <span style="background:var(--crimson-bg);color:var(--crimson);
                                 padding:.15rem .5rem;border-radius:2px;font-weight:700;">
                        <?= $uc['creditos'] ?> ECTS
                    </span>
                    <span>Ano <?= $uc['primeiro_ano'] ?> · Sem <?= $uc['primeiro_semestre'] ?></span>
                </div>
            </div>
            <!-- Melhor nota desta UC -->
            <div style="text-align:center;flex-shrink:0;">
                <div style="font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:700;
                            line-height:1;color:<?= $melhorNota === null ? 'var(--text-muted)' : ($melhorNota >= 10 ? 'var(--success)' : 'var(--danger)') ?>;">
                    <?= $melhorNota !== null ? number_format($melhorNota, 1) : '—' ?>
                </div>
                <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;
                            letter-spacing:.07em;color:var(--text-muted);margin-top:.2rem;">
                    <?= $melhorNota === null ? 'Sem nota' : 'Melhor nota' ?>
                </div>
            </div>
        </div>

        <?php if (empty($notasUC)): ?>
            <p style="color:var(--text-muted);font-size:.9rem;font-style:italic;">
                Sem notas lançadas nesta UC.
            </p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Ano Letivo</th>
                            <th>Época</th>
                            <th>Nota</th>
                            <th>Situação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notasUC as $nota): ?>
                        <tr>
                            <td style="color:var(--text-muted);font-size:.85rem;"><?= e($nota['ano_letivo']) ?></td>
                            <td><span class="badge badge-rascunho"><?= e($nota['epoca']) ?></span></td>
                            <td>
                                <strong style="font-family:'Playfair Display',serif;font-size:1.05rem;">
                                    <?= $nota['nota_final'] !== null ? number_format($nota['nota_final'], 1) : '—' ?>
                                </strong>
                            </td>
                            <td>
                                <?php if ($nota['nota_final'] === null): ?>
                                    <span class="badge badge-rascunho">Por lançar</span>
                                <?php elseif ($nota['nota_final'] >= 10): ?>
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
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';