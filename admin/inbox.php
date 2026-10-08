<?php
/* Kotak masuk: pesan kontak, permohonan layanan, pelanggan newsletter */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/layout.php';

require_login();

$tab = $_GET['tab'] ?? 'pesan';
if (!in_array($tab, ['pesan', 'permohonan', 'langganan'], true)) {
    $tab = 'pesan';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $do = $_POST['do'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    switch ($do) {
        case 'baca':
            db_exec('UPDATE contact_messages SET is_read = 1 - is_read WHERE id = ?', [$id]);
            break;
        case 'hapus_pesan':
            db_delete('contact_messages', $id);
            flash('success', 'Pesan dihapus.');
            break;
        case 'status':
            $status = $_POST['status'] ?? 'baru';
            if (in_array($status, ['baru', 'diproses', 'selesai', 'ditolak'], true)) {
                db_exec('UPDATE service_applications SET status = ? WHERE id = ?', [$status, $id]);
                flash('success', 'Status permohonan diperbarui.');
            }
            break;
        case 'hapus_permohonan':
            $row = db_one('SELECT file FROM service_applications WHERE id = ?', [$id]);
            if ($row && $row['file']) {
                $full = realpath(UPLOAD_PATH . '/' . $row['file']);
                if ($full && str_starts_with($full, (string) realpath(UPLOAD_PATH))) {
                    @unlink($full);
                }
            }
            db_delete('service_applications', $id);
            flash('success', 'Permohonan dihapus.');
            break;
        case 'hapus_langganan':
            db_delete('newsletter_subscribers', $id);
            flash('success', 'Pelanggan dihapus.');
            break;
    }
    redirect(admin_url('inbox.php?tab=' . $tab));
}

$messages     = db_all('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 200');
$applications = db_all('SELECT * FROM service_applications ORDER BY created_at DESC LIMIT 200');
$subscribers  = db_all('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC LIMIT 500');

$statusPill = ['baru' => 'pill-on', 'diproses' => 'pill-warn', 'selesai' => 'pill-ok', 'ditolak' => 'pill-off'];

admin_head('Kotak Masuk', [['label' => 'Kotak Masuk']]);
?>

<div class="page-head">
  <div>
    <h1>Kotak Masuk</h1>
    <p>Kiriman formulir dari situs publik.</p>
  </div>
</div>

<div class="tabs">
  <a class="<?= $tab === 'pesan' ? 'active' : '' ?>" href="<?= e(admin_url('inbox.php?tab=pesan')) ?>">
    <i class="bi bi-envelope"></i> Pesan Kontak <span class="count"><?= count($messages) ?></span>
  </a>
  <a class="<?= $tab === 'permohonan' ? 'active' : '' ?>" href="<?= e(admin_url('inbox.php?tab=permohonan')) ?>">
    <i class="bi bi-file-earmark-text"></i> Permohonan Layanan <span class="count"><?= count($applications) ?></span>
  </a>
  <a class="<?= $tab === 'langganan' ? 'active' : '' ?>" href="<?= e(admin_url('inbox.php?tab=langganan')) ?>">
    <i class="bi bi-bell"></i> Newsletter <span class="count"><?= count($subscribers) ?></span>
  </a>
</div>

<?php if ($tab === 'pesan'): ?>
<div class="card">
  <?php if (!$messages): ?>
  <div class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada pesan masuk.</p></div>
  <?php else: ?>
  <div class="msg-list">
    <?php foreach ($messages as $m): ?>
    <article class="msg<?= (int) $m['is_read'] ? '' : ' unread' ?>" id="pesan-<?= (int) $m['id'] ?>">
      <div class="msg-head">
        <div>
          <b><?= e($m['name']) ?></b>
          <span><?= e($m['email']) ?><?= $m['phone'] ? ' · ' . e($m['phone']) : '' ?></span>
        </div>
        <div class="msg-meta">
          <?php if ($m['subject']): ?><span class="pill pill-neutral"><?= e($m['subject']) ?></span><?php endif; ?>
          <time><?= e(tanggal_id($m['created_at'], true)) ?>, <?= e(date('H:i', strtotime($m['created_at']))) ?></time>
        </div>
      </div>
      <p class="msg-body"><?= nl2br(e($m['message'])) ?></p>
      <div class="msg-actions">
        <a class="btn btn-ghost btn-sm" href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Balasan: ' . ($m['subject'] ?: 'Pesan Anda')) ?>"><i class="bi bi-reply"></i> Balas</a>
        <form method="post" style="display:inline">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
          <button class="btn btn-ghost btn-sm" name="do" value="baca">
            <i class="bi bi-<?= (int) $m['is_read'] ? 'envelope' : 'envelope-open' ?>"></i>
            <?= (int) $m['is_read'] ? 'Tandai belum dibaca' : 'Tandai sudah dibaca' ?>
          </button>
          <button class="btn btn-ghost btn-sm danger" name="do" value="hapus_pesan"
                  onclick="return confirm('Hapus pesan ini?')"><i class="bi bi-trash"></i> Hapus</button>
        </form>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php elseif ($tab === 'permohonan'): ?>
<div class="card">
  <?php if (!$applications): ?>
  <div class="empty-state"><i class="bi bi-file-earmark-text"></i><p>Belum ada permohonan layanan.</p></div>
  <?php else: ?>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>Tanggal</th><th>Pemohon</th><th>Layanan</th><th>Kontak</th><th>Berkas</th><th>Status</th><th class="col-actions">Aksi</th></tr>
      </thead>
      <tbody>
        <?php foreach ($applications as $a): ?>
        <tr>
          <td><?= e(tanggal_id($a['created_at'])) ?></td>
          <td><b><?= e($a['applicant_name']) ?></b><br><small><?= e($a['institution']) ?></small></td>
          <td><?= e(excerpt($a['service_name'], 70)) ?><?= $a['notes'] ? '<br><small>' . e(excerpt($a['notes'], 90)) . '</small>' : '' ?></td>
          <td><a href="mailto:<?= e($a['email']) ?>"><?= e($a['email']) ?></a><br><small><?= e($a['phone']) ?></small></td>
          <td><?php if ($a['file']): ?><a href="<?= e(asset_url($a['file'])) ?>" target="_blank" rel="noopener"><i class="bi bi-paperclip"></i> Unduh</a><?php else: ?>—<?php endif; ?></td>
          <td><span class="pill <?= $statusPill[$a['status']] ?? 'pill-neutral' ?>"><?= e(ucfirst($a['status'])) ?></span></td>
          <td class="col-actions">
            <form method="post" class="inline-form">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
              <select name="status" onchange="this.form.querySelector('[name=do]').click()">
                <?php foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $v => $l): ?>
                <option value="<?= $v ?>"<?= $a['status'] === $v ? ' selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" name="do" value="status" hidden></button>
              <button class="btn-icon danger" type="submit" name="do" value="hapus_permohonan"
                      onclick="return confirm('Hapus permohonan ini?')"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="card">
  <?php if (!$subscribers): ?>
  <div class="empty-state"><i class="bi bi-bell"></i><p>Belum ada pelanggan newsletter.</p></div>
  <?php else: ?>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Email</th><th>Tanggal Berlangganan</th><th class="col-actions">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($subscribers as $s): ?>
        <tr>
          <td><?= e($s['email']) ?></td>
          <td><?= e(tanggal_id($s['created_at'])) ?></td>
          <td class="col-actions">
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
              <button class="btn-icon danger" name="do" value="hapus_langganan"
                      onclick="return confirm('Hapus pelanggan ini?')"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php admin_foot(); ?>
