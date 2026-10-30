<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers.php';
require_login();
$pdo = db();
$stats = [
 'products' => (int)$pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn(),
 'stock_value' => (float)$pdo->query("SELECT COALESCE(SUM(stock_qty*cost_price),0) FROM products WHERE is_active=1")->fetchColumn(),
 'orders_today' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()")->fetchColumn(),
 'revenue_month' => (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status='completed' AND YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())")->fetchColumn()
];
$low = $pdo->query("SELECT sku,name,stock_qty,min_stock FROM products WHERE is_active=1 AND stock_qty<=min_stock ORDER BY stock_qty ASC LIMIT 8")->fetchAll();
$recent = $pdo->query("SELECT o.order_code,o.customer_name,o.total_amount,o.status,o.created_at,u.full_name FROM orders o LEFT JOIN users u ON u.id=o.created_by ORDER BY o.id DESC LIMIT 8")->fetchAll();
$pageTitle='Tổng quan'; require __DIR__.'/../app/layout/header.php';
?>
<div class="page-heading mb-4"><div><h1 class="h3 mb-1">Tổng quan</h1><p class="text-secondary mb-0">Theo dõi hoạt động kho và bán hàng.</p></div><span class="badge text-bg-primary p-2"><?= e(date('d/m/Y')) ?></span></div>
<div class="row g-3 mb-4">
<?php foreach ([['Sản phẩm đang bán',$stats['products'],'bi-box-seam'],['Giá trị tồn kho',money($stats['stock_value']),'bi-archive'],['Đơn hàng hôm nay',$stats['orders_today'],'bi-receipt'],['Doanh thu tháng',money($stats['revenue_month']),'bi-graph-up']] as $s): ?>
<div class="col-12 col-sm-6 col-xl-3"><div class="card stat-card h-100"><div class="card-body"><div class="muted-label"><?= e($s[0]) ?></div><div class="stat-value mt-2"><?= e($s[1]) ?></div></div></div></div>
<?php endforeach; ?>
</div>
<div class="row g-4">
<div class="col-12 col-xl-5"><div class="card h-100"><div class="card-body"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Cảnh báo tồn kho</h2><a href="<?= BASE_URL ?>/products.php" class="small">Xem sản phẩm</a></div>
<div class="table-responsive"><table class="table table-sm"><thead><tr><th>SKU / Sản phẩm</th><th>Tồn</th><th>Tối thiểu</th></tr></thead><tbody>
<?php if (!$low): ?><tr><td colspan="3" class="text-secondary">Không có sản phẩm dưới ngưỡng.</td></tr><?php endif; ?>
<?php foreach($low as $p): ?><tr><td><small class="text-secondary"><?= e($p['sku']) ?></small><br><?= e($p['name']) ?></td><td class="low-stock"><?= (int)$p['stock_qty'] ?></td><td><?= (int)$p['min_stock'] ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></div></div>
<div class="col-12 col-xl-7"><div class="card h-100"><div class="card-body"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Đơn hàng gần đây</h2><a href="<?= BASE_URL ?>/orders.php" class="small">Tất cả đơn</a></div>
<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Tổng</th><th>Trạng thái</th></tr></thead><tbody>
<?php foreach($recent as $o): ?><tr><td><?= e($o['order_code']) ?><div class="small text-secondary"><?= e(date('d/m H:i',strtotime($o['created_at']))) ?></div></td><td><?= e($o['customer_name']) ?></td><td><?= money($o['total_amount']) ?></td><td><span class="badge text-bg-<?= $o['status']==='completed'?'success':($o['status']==='cancelled'?'danger':'warning') ?>"><?= e(['pending'=>'Chờ xử lý','completed'=>'Hoàn tất','cancelled'=>'Đã hủy'][$o['status']] ?? $o['status']) ?></span></td></tr><?php endforeach; ?>
<?php if (!$recent): ?><tr><td colspan="4" class="text-secondary">Chưa có đơn hàng.</td></tr><?php endif; ?>
</tbody></table></div></div></div></div></div>
<?php require __DIR__.'/../app/layout/footer.php'; ?>
