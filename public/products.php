<?php
require_once __DIR__.'/../config/config.php'; require_once __DIR__.'/../app/helpers.php'; require_login();
$pdo=db();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); require_role('admin','staff');
 $action=$_POST['action']??'';
 if($action==='save'){
  $id=(int)($_POST['id']??0); $sku=trim((string)($_POST['sku']??'')); $name=trim((string)($_POST['name']??''));
  $category=(int)($_POST['category_id']??0); $cost=(float)($_POST['cost_price']??0); $price=(float)($_POST['sale_price']??0); $min=(int)($_POST['min_stock']??0);
  if($sku===''||$name===''||$category<1||$cost<0||$price<0||$min<0){flash('danger','Vui lòng nhập dữ liệu hợp lệ.');redirect('/products.php');}
  try {
   if($id>0){$st=$pdo->prepare('UPDATE products SET sku=?,name=?,category_id=?,cost_price=?,sale_price=?,min_stock=? WHERE id=?');$st->execute([$sku,$name,$category,$cost,$price,$min,$id]);}
   else{$st=$pdo->prepare('INSERT INTO products(sku,name,category_id,cost_price,sale_price,min_stock,stock_qty) VALUES(?,?,?,?,?,?,0)');$st->execute([$sku,$name,$category,$cost,$price,$min]);}
   flash('success','Đã lưu sản phẩm.'); 
  }catch(PDOException $e){flash('danger',$e->getCode()==='23000'?'Mã SKU đã tồn tại hoặc danh mục không hợp lệ.':'Không thể lưu sản phẩm.');}
  redirect('/products.php');
 }
 if($action==='delete'){
  require_role('admin'); $id=(int)($_POST['id']??0);
  $st=$pdo->prepare('UPDATE products SET is_active=0 WHERE id=?');$st->execute([$id]);flash('success','Đã ngừng kinh doanh sản phẩm.');redirect('/products.php');
 }
}
$q=trim((string)($_GET['q']??''));$sql="SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.is_active=1";$params=[];
if($q!==''){$sql.=" AND (p.name LIKE ? OR p.sku LIKE ?)";$params=["%$q%","%$q%"];}
$sql.=" ORDER BY p.id DESC";$st=$pdo->prepare($sql);$st->execute($params);$products=$st->fetchAll();
$categories=$pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();
$edit=null;if(isset($_GET['edit'])){$st=$pdo->prepare('SELECT * FROM products WHERE id=? AND is_active=1');$st->execute([(int)$_GET['edit']]);$edit=$st->fetch()?:null;}
$pageTitle='Sản phẩm';require __DIR__.'/../app/layout/header.php';
?>
<div class="page-heading mb-3"><div><h1 class="h3 mb-1">Sản phẩm</h1><p class="text-secondary mb-0">Quản lý danh mục hàng hóa, giá bán và ngưỡng tồn kho.</p></div></div>
<div class="row g-4"><div class="col-12 col-xl-4"><div class="card"><div class="card-body"><h2 class="h5 mb-3"><?= $edit?'Sửa sản phẩm':'Thêm sản phẩm' ?></h2>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<div class="mb-3"><label class="form-label">Mã SKU</label><input name="sku" class="form-control" required maxlength="50" value="<?= e($edit['sku']??'') ?>"></div>
<div class="mb-3"><label class="form-label">Tên sản phẩm</label><input name="name" class="form-control" required value="<?= e($edit['name']??'') ?>"></div>
<div class="mb-3"><label class="form-label">Danh mục</label><select name="category_id" class="form-select" required><option value="">Chọn danh mục</option><?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)($edit['category_id']??0)===(int)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
<div class="row"><div class="col-6 mb-3"><label class="form-label">Giá vốn</label><input type="number" min="0" step="1000" name="cost_price" class="form-control" required value="<?= e($edit['cost_price']??0) ?>"></div><div class="col-6 mb-3"><label class="form-label">Giá bán</label><input type="number" min="0" step="1000" name="sale_price" class="form-control" required value="<?= e($edit['sale_price']??0) ?>"></div></div>
<div class="mb-3"><label class="form-label">Ngưỡng tồn kho thấp</label><input type="number" min="0" name="min_stock" class="form-control" value="<?= e($edit['min_stock']??5) ?>" required></div>
<?php if($edit): ?><div class="alert alert-info small">Tồn kho hiện tại: <?= (int)$edit['stock_qty'] ?>. Thay đổi tồn kho qua chức năng nhập kho để lưu lịch sử.</div><?php endif; ?>
<button class="btn btn-primary"><?= $edit?'Cập nhật':'Thêm sản phẩm' ?></button><?php if($edit): ?> <a class="btn btn-light" href="<?= BASE_URL ?>/products.php">Hủy</a><?php endif; ?>
</form></div></div></div>
<div class="col-12 col-xl-8"><div class="card"><div class="card-body"><form class="row g-2 mb-3"><div class="col"><input name="q" class="form-control" placeholder="Tìm theo SKU hoặc tên..." value="<?= e($q) ?>"></div><div class="col-auto"><button class="btn btn-outline-primary">Tìm kiếm</button></div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>SKU / Tên</th><th>Danh mục</th><th>Giá bán</th><th>Tồn kho</th><th></th></tr></thead><tbody>
<?php foreach($products as $p): ?><tr><td><small class="text-secondary"><?= e($p['sku']) ?></small><br><b><?= e($p['name']) ?></b></td><td><?= e($p['category_name']) ?></td><td><?= money($p['sale_price']) ?></td><td><span class="<?= $p['stock_qty']<=$p['min_stock']?'low-stock':'' ?>"><?= (int)$p['stock_qty'] ?></span></td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-primary" href="?edit=<?= (int)$p['id'] ?>">Sửa</a> <?php if(current_user()['role']==='admin'): ?><form method="post" class="d-inline"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="btn btn-sm btn-outline-danger" data-confirm="Ngừng kinh doanh sản phẩm này?">Ngừng</button></form><?php endif; ?></td></tr><?php endforeach; ?>
<?php if(!$products): ?><tr><td colspan="5" class="text-center text-secondary py-4">Không tìm thấy sản phẩm.</td></tr><?php endif; ?>
</tbody></table></div></div></div></div></div>
<?php require __DIR__.'/../app/layout/footer.php'; ?>
