<?php
session_start();

// Handle Logout Request via POST submission from the sidebar
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>House of Wash — Laundry Management System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root{
  --sidebar:#0f172a;
  --sidebar-2:#111c33;
  --accent:#2563eb;
  --accent-soft:#dbeafe;
  --ink:#1e293b;
  --muted:#64748b;
  --bg:#f1f5f9;
  --line:#e2e8f0;
}
*{ -webkit-font-smoothing:antialiased; }
body{
  background:var(--bg); color:var(--ink);
  font-family:"Segoe UI",system-ui,-apple-system,"Helvetica Neue",sans-serif;
  margin:0;
}
.app{ display:flex; min-height:100vh; }

/* ---------- Sidebar ---------- */
.sidebar{
  width:250px; flex-shrink:0;
  background:linear-gradient(180deg,var(--sidebar) 0%,var(--sidebar-2) 100%);
  color:#cbd5e1; display:flex; flex-direction:column;
  position:sticky; top:0; height:100vh;
}
.sidebar .brand{
  padding:1.15rem 1.25rem; display:flex; align-items:center; gap:.7rem;
  border-bottom:1px solid rgba(255,255,255,.08);
}
.sidebar .brand .logo{
  width:38px;height:38px;border-radius:.6rem;
  object-fit:cover; display:grid;place-items:center;flex-shrink:0;
}
.sidebar .brand h1{ font-size:.95rem; font-weight:700; color:#fff; margin:0; line-height:1.15; }
.sidebar .brand small{ font-size:.68rem; color:#94a3b8; }
.nav-scroll{ overflow-y:auto; flex:1; padding:.75rem 0; }
.nav-scroll::-webkit-scrollbar{ width:6px; }
.nav-scroll::-webkit-scrollbar-thumb{ background:rgba(255,255,255,.12); border-radius:3px; }
.nav-label{
  font-size:.65rem; letter-spacing:.09em; text-transform:uppercase;
  color:#64748b; padding:.9rem 1.25rem .35rem; font-weight:700;
}
.nav-item{
  display:flex; align-items:center; gap:.75rem;
  padding:.6rem 1.25rem; color:#cbd5e1; cursor:pointer;
  font-size:.875rem; border-left:3px solid transparent; transition:.15s;
  text-decoration:none;
}
.nav-item:hover{ background:rgba(255,255,255,.06); color:#fff; }
.nav-item.active{ background:rgba(37,99,235,.18); border-left-color:var(--accent); color:#fff; font-weight:600; }
.nav-item i{ font-size:1.05rem; width:20px; text-align:center; }
.nav-item .badge{ margin-left:auto; font-size:.65rem; }
.sidebar .footer{ padding:1rem 1.25rem; border-top:1px solid rgba(255,255,255,.08); font-size:.72rem; color:#64748b; }

/* ---------- Main ---------- */
.main{ flex:1; min-width:0; display:flex; flex-direction:column; }
.topbar{
  background:#fff; border-bottom:1px solid var(--line);
  padding:.75rem 1.5rem; display:flex; align-items:center; gap:1rem;
  position:sticky; top:0; z-index:20;
}
.clock { font-size: .85rem; font-weight: 600; color: var(--ink); line-height: 1.2; }
.clock-date { font-size: .7rem; color: var(--muted); }
.avatar{
  width:36px;height:36px;border-radius:50%;
  background:linear-gradient(135deg,#2563eb,#7c3aed);
  color:#fff;display:grid;place-items:center;font-weight:700;font-size:.8rem;flex-shrink:0;
}
.page{ padding:1.5rem; flex:1; }
.page-head{ display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.25rem; }
.page-head h2{ font-size:1.35rem; font-weight:700; margin:0; }
.page-head p{ margin:0; font-size:.83rem; color:var(--muted); }

/* ---------- Cards ---------- */
.card{ border:1px solid var(--line); border-radius:.85rem; box-shadow:0 1px 2px rgba(15,23,42,.04); }
.card-header{ background:#fff; border-bottom:1px solid var(--line); font-weight:600; font-size:.9rem; padding:.9rem 1.1rem; }
.stat{ display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; }
.stat .ico{ width:44px;height:44px;border-radius:.7rem;display:grid;place-items:center;font-size:1.2rem;flex-shrink:0; }
.stat .val{ font-size:1.6rem; font-weight:700; line-height:1; margin:.2rem 0 .25rem; }
.stat .lbl{ font-size:.78rem; color:var(--muted); font-weight:600; letter-spacing:.02em; }
.trend{ font-size:.72rem; font-weight:600; }

/* ---------- Tables ---------- */
.table{ margin:0; font-size:.86rem; }
.table thead th{
  background:#f8fafc; color:#64748b; font-size:.7rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.06em; white-space:nowrap;
  padding:.7rem 1rem; border-bottom:1px solid var(--line);
}
.table tbody td{ padding:.7rem 1rem; vertical-align:middle; border-color:#f1f5f9; }
.table tbody tr:hover{ background:#f8fafc; }
.table-wrap{ overflow:auto; max-height:65vh; }
.table-wrap::-webkit-scrollbar{ width:8px;height:8px; }
.table-wrap::-webkit-scrollbar-thumb{ background:#cbd5e1; border-radius:4px; }

/* ---------- Pills ---------- */
.pill{ font-size:.72rem; font-weight:600; padding:.3rem .65rem; border-radius:999px; display:inline-flex; align-items:center; gap:.3rem; white-space:nowrap; }
.pill-pending   { background:#fef3c7; color:#92400e; }
.pill-washing   { background:#dbeafe; color:#1e40af; }
.pill-drying    { background:#e0e7ff; color:#3730a3; }
.pill-ready     { background:#dcfce7; color:#166534; }
.pill-delivered { background:#d1fae5; color:#065f46; }
.pill-cancelled { background:#fee2e2; color:#991b1b; }
.pill-unpaid    { background:#fee2e2; color:#991b1b; }
.pill-partial   { background:#fef3c7; color:#92400e; }
.pill-paid      { background:#dcfce7; color:#166534; }
.pill-available { background:#dcfce7; color:#166534; }
.pill-in_use    { background:#dbeafe; color:#1e40af; }
.pill-maintenance{ background:#fef3c7; color:#92400e; }
.pill-tub_clean { background:#ede9fe; color:#5b21b6; }
.pill-ok  { background:#dcfce7; color:#166534; }
.pill-low { background:#fef3c7; color:#92400e; }
.pill-out { background:#fee2e2; color:#991b1b; }

/* ---------- Machines ---------- */
.machine{ border:1px solid var(--line); border-radius:.75rem; padding:1rem; background:#fff; transition:.18s; height:100%; }
.machine:hover{ transform:translateY(-2px); box-shadow:0 .5rem 1rem rgba(15,23,42,.08); }
.machine .code{ font-family:ui-monospace,Menlo,monospace; font-weight:700; font-size:.8rem; color:var(--muted); }
.machine .name{ font-weight:700; font-size:.95rem; margin:.15rem 0 .5rem; }
.machine-icon{ width:52px;height:52px;border-radius:.75rem;display:grid;place-items:center;font-size:1.5rem; }

/* ---------- Misc ---------- */
.empty{ text-align:center; padding:3rem 1rem; color:var(--muted); }
.empty i{ font-size:2.5rem; opacity:.35; display:block; margin-bottom:.6rem; }
.quick-btn{
  display:flex; align-items:center; gap:.6rem; width:100%;
  padding:.7rem .9rem; border-radius:.6rem; border:1px solid var(--line);
  background:#fff; font-size:.85rem; font-weight:600; color:var(--ink);
  text-align:left; transition:.15s;
}
.quick-btn:hover{ border-color:var(--accent); background:var(--accent-soft); color:var(--accent); }
.bar-track{ height:8px; background:#f1f5f9; border-radius:999px; overflow:hidden; }
.bar-fill{ height:100%; border-radius:999px; background:linear-gradient(90deg,#2563eb,#38bdf8); }
.toast-container{ z-index:1090; }
.modal-content{ border:0; border-radius:1rem; }
.form-label{ font-size:.8rem; font-weight:600; color:#334155; margin-bottom:.3rem; }
.form-control,.form-select{ font-size:.875rem; }
.line-item{ background:#f8fafc; border:1px solid var(--line); border-radius:.6rem; padding:.6rem; }
.receipt{ font-family:ui-monospace,Menlo,monospace; font-size:.82rem; }
.receipt .hr{ border-top:1px dashed #94a3b8; margin:.5rem 0; }
.min-w-0{ min-width:0; }
@media print{
  .sidebar,.topbar,.no-print{ display:none !important; }
  .modal{ position:static !important; display:block !important; }
  .modal-dialog{ max-width:100%; }
}
@media (max-width:991.98px){
  .sidebar{ position:fixed; left:-260px; z-index:1045; transition:left .22s; }
  .sidebar.open{ left:0; }
  .sidebar-backdrop{ display:none; position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1040; }
  .sidebar-backdrop.show{ display:block; }
}
</style>
</head>
<body>
<div class="app">

  <!-- ================= SIDEBAR ================= -->
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <img class="logo" src="isa_2.jpg" alt="House of Wash Laundry">
      <div>
        <h1>House of Wash</h1>
        <small>Laundry Management</small>
      </div>
    </div>

    <nav class="nav-scroll">
      <div class="nav-label">Main</div>
      <a class="nav-item active" data-page="dashboard"><i class="bi bi-grid-1x2-fill"></i>Dashboard</a>
      <a class="nav-item" data-page="orders"><i class="bi bi-receipt"></i>Orders<span class="badge text-bg-primary" id="navOrderBadge">0</span></a>
      <a class="nav-item" data-page="queue"><i class="bi bi-hourglass-split"></i>Queue</a>

      <div class="nav-label">Management</div>
      <a class="nav-item" data-page="customers"><i class="bi bi-people-fill"></i>Customers</a>
      <a class="nav-item" data-page="services"><i class="bi bi-tags-fill"></i>Services</a>
      <a class="nav-item" data-page="machines"><i class="bi bi-cpu-fill"></i>Machines</a>
      <a class="nav-item" data-page="inventory"><i class="bi bi-box-seam-fill"></i>Inventory</a>
      <a class="nav-item" data-page="maintenance"><i class="bi bi-tools"></i>Maintenance</a>

      <div class="nav-label">Insights</div>
      <a class="nav-item" data-page="reports"><i class="bi bi-bar-chart-fill"></i>Reports</a>
    </nav>

    <div class="footer">
      <form action="?action=logout" method="POST" onsubmit="return confirm('Are you sure you want to log out?');">
        <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-semibold">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </button>
      </form>
    </div>
  </aside>
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- ================= MAIN ================= -->
  <div class="main">
    <header class="topbar">
      <button class="btn btn-light d-lg-none" id="menuToggle"><i class="bi bi-list"></i></button>
      
      <!-- Live Clock on Top Left -->
      <div class="clock">
        <div class="clock-time" id="clockTime"></div>
        <div class="clock-date" id="clockDate"></div>
      </div>

      <div class="ms-auto d-flex align-items-center gap-3">
        <button class="btn btn-light position-relative" id="alertBtn">
          <i class="bi bi-bell"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger" id="alertCount" style="display:none">0</span>
        </button>
        <div class="d-flex align-items-center gap-2">
          <div class="avatar">AD</div>
          <div class="d-none d-md-block lh-1">
            <div style="font-size:.83rem;font-weight:700">Admin Sarah</div>
            <div style="font-size:.7rem;color:var(--muted)">Administrator</div>
          </div>
        </div>
      </div>
    </header>

    <div class="page" id="pageContainer"></div>
  </div>
</div>

<!-- ================= MODALS ================= -->
<div class="modal fade" id="mdl" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mdlTitle">Modal</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="mdlBody"></div>
      <div class="modal-footer" id="mdlFooter"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="mdlSm" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mdlSmTitle">Modal</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="mdlSmBody"></div>
      <div class="modal-footer" id="mdlSmFooter"></div>
    </div>
  </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toasts"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* =========================================================
   HOUSE OF WASH — Laundry Management System
   ========================================================= */

const STORAGE_KEY = 'houseofwash_db_v1';

/* =========================================================
   SEED DATA
   ========================================================= */
function seedData() {
  const today = new Date();
  const iso = d => d.toISOString().slice(0, 10);
  const daysAgo = n => { const d = new Date(); d.setDate(d.getDate() - n); return iso(d); };
  const daysAhead = n => { const d = new Date(); d.setDate(d.getDate() + n); return iso(d); };

  return {
    customers: [
      { id: 1, name: 'Marc jonelle De villa',    phone: '0917 111 2233', email: 'maria@email.com',   address: 'Brgy. Poblacion, Nasugbu', notes: 'Prefers fabric softener' },
      { id: 2, name: 'Mark Jay Inumerable',  phone: '0917 222 3344', email: 'juan@email.com',    address: 'Brgy. Wawa, Nasugbu',       notes: '' },
      { id: 3, name: 'Ana Reyes',       phone: '0917 333 4455', email: 'ana@email.com',     address: 'Brgy. Bucana, Nasugbu',     notes: 'Has asthma - no strong scents' },
      { id: 4, name: 'Robert Lim',      phone: '0917 444 5566', email: 'robert@email.com',  address: 'Brgy. Balaytigue',          notes: '' },
      { id: 5, name: 'Josefina Cruz',   phone: '0917 555 6677', email: 'josefina@email.com',address: 'Brgy. Aga, Nasugbu',        notes: 'Weekly blanket wash' }
    ],
    services: [
      { id: 1, name: 'Wash & Fold',         price: 70,  unit: 'kg',    description: 'Machine wash, fold and pack',            active: 1 },
      { id: 2, name: 'Wash Only',           price: 50,  unit: 'kg',    description: 'Machine wash only',                     active: 1 },
      { id: 3, name: 'Dry Only',            price: 60,  unit: 'kg',    description: 'Machine dry only',                      active: 1 },
      { id: 4, name: 'Iron & Fold',         price: 40,  unit: 'kg',    description: 'Press and fold service',                active: 1 },
      { id: 5, name: 'Dry Clean',           price: 150, unit: 'pc',    description: 'Specialty dry cleaning',                active: 1 },
      { id: 6, name: 'Comforter / Blanket', price: 250, unit: 'pc',    description: 'Heavy fabric wash and dry',             active: 1 },
      { id: 7, name: 'Extra Drying (10 min)',price:30,  unit: 'cycle', description: 'Additional drying for damp items',      active: 1 },
      { id: 8, name: 'Stain Treatment',     price: 80,  unit: 'pc',    description: 'Manual pre-treatment for heavy stains', active: 1 }
    ],
    machines: [
      { id: 1, code: 'W-01', name: 'Washer 01', type: 'washer', status: 'available',   capacity: '8 kg',  notes: 'Front load' },
      { id: 2, code: 'W-02', name: 'Washer 02', type: 'washer', status: 'available',   capacity: '8 kg',  notes: 'Front load' },
      { id: 3, code: 'W-03', name: 'Washer 03', type: 'washer', status: 'in_use',      capacity: '10 kg', notes: 'Front load' },
      { id: 4, code: 'W-04', name: 'Washer 04', type: 'washer', status: 'available',   capacity: '10 kg', notes: 'Front load' },
      { id: 5, code: 'D-01', name: 'Dryer 01',  type: 'dryer',  status: 'available',   capacity: '8 kg',  notes: 'Vented' },
      { id: 6, code: 'D-02', name: 'Dryer 02',  type: 'dryer',  status: 'available',   capacity: '8 kg',  notes: 'Vented' },
      { id: 7, code: 'D-03', name: 'Dryer 03',  type: 'dryer',  status: 'tub_clean',   capacity: '10 kg', notes: 'Vented' }
    ],
    inventory: [
      { id: 1, name: 'Detergent A',   category: 'Consumables', stock: 500,  unit: 'L',     threshold: 100 },
      { id: 2, name: 'Softener A',    category: 'Consumables', stock: 200,  unit: 'L',     threshold: 50 },
      { id: 3, name: 'Stain Remover', category: 'Supplies',    stock: 8,    unit: 'L',     threshold: 10 },
      { id: 4, name: 'Laundry Bags',  category: 'Supplies',    stock: 1000, unit: 'units', threshold: 200 },
      { id: 5, name: 'Hangers',       category: 'Supplies',    stock: 150,  unit: 'units', threshold: 200 },
      { id: 6, name: 'Bleach',        category: 'Consumables', stock: 80,   unit: 'L',     threshold: 20 }
    ],
    maintenance: [
      { id: 1, machine_id: 1, scheduled_date: iso(today),   type: 'Tub Clean',    notes: 'Weekly Monday deep clean', done: 0 },
      { id: 2, machine_id: 2, scheduled_date: iso(today),   type: 'Tub Clean',    notes: 'Weekly Monday deep clean', done: 0 },
      { id: 3, machine_id: 5, scheduled_date: daysAhead(3), type: 'Filter Clean', notes: 'Lint filter servicing',   done: 0 }
    ],
    machineLogs: [
      { id: 1, machine_id: 3, customer_name: 'Walk-in Customer', cycle_minutes: 45, started_at: new Date(Date.now() - 20*60000).toISOString(), ended_at: null, status: 'running' }
    ],
    orders: [
      makeOrder(1, 1, 'HOW-' + codeDate(0) + '-001', 'delivered', 'paid',    [{ service_id: 1, qty: 8,  price: 70 }, { service_id: 7, qty: 1, price: 30 }], 590, 590, daysAgo(5), daysAgo(3), ''),
      makeOrder(2, 2, 'HOW-' + codeDate(0) + '-002', 'ready',     'unpaid',  [{ service_id: 6, qty: 2,  price: 250 }],                                             500, 0,   daysAgo(4), daysAgo(1), 'Comforter, quick turnaround please'),
      makeOrder(3, 3, 'HOW-' + codeDate(0) + '-003', 'washing',   'unpaid',  [{ service_id: 1, qty: 5,  price: 70 }],                                              350, 0,   daysAgo(1), daysAhead(1), 'No strong scents'),
      makeOrder(4, 4, 'HOW-' + codeDate(0) + '-004', 'pending',   'partial', [{ service_id: 2, qty: 6,  price: 50 }, { service_id: 4, qty: 6, price: 40 }],    540, 200, daysAgo(1), daysAhead(2), ''),
      makeOrder(5, 5, 'HOW-' + codeDate(0) + '-005', 'ready',     'unpaid',  [{ service_id: 6, qty: 1,  price: 250 }, { service_id: 8, qty: 1, price: 80 }],   330, 0,   daysAgo(22), daysAgo(20), 'Heavy stain on duvet cover'),
      makeOrder(6, 1, 'HOW-' + codeDate(0) + '-006', 'ready',     'paid',    [{ service_id: 5, qty: 3,  price: 150 }],                                             450, 450, daysAgo(3), daysAgo(1), 'Barong - handle with care')
    ],
    orderItems: [],
    payments: [],
    nextIds: { customer: 6, service: 9, machine: 8, inventory: 7, order: 7, orderItem: 1, payment: 1, machineLog: 2, maintenance: 4 }
  };

  function codeDate(offset) {
    const d = new Date(); d.setDate(d.getDate() - offset);
    return d.toISOString().slice(2, 10).replace(/-/g, '');
  }

  function makeOrder(id, custId, code, status, pay, items, total, paid, intake, pickup, notes) {
    return {
      id, order_code: code, customer_id: custId, status, payment_status: pay,
      total, amount_paid: paid, intake_date: intake, pickup_date: pickup,
      extra_dry: items.some(i => i.service_id === 7) ? 1 : 0,
      notes, created_at: intake + ' 09:30:00', updated_at: intake + ' 09:30:00',
      items
    };
  }
}

function normalizeSeeded(db) {
  db.orderItems = [];
  let itemId = 1;
  db.orders.forEach(o => {
    (o.items || []).forEach(it => {
      db.orderItems.push({
        id: itemId++,
        order_id: o.id,
        service_id: it.service_id,
        qty: it.qty,
        price: it.price,
        subtotal: it.qty * it.price
      });
    });
    delete o.items;
  });
  db.nextIds.orderItem = itemId;

  db.payments = [];
  let payId = 1;
  db.orders.forEach(o => {
    if (o.amount_paid > 0) {
      db.payments.push({
        id: payId++,
        order_id: o.id,
        amount: o.amount_paid,
        method: 'cash',
        created_at: o.created_at
      });
    }
  });
  db.nextIds.payment = payId;
  return db;
}

/* =========================================================
   STORAGE
   ========================================================= */
let DB;
function loadDB() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) { DB = JSON.parse(raw); return; }
  } catch (e) { console.warn('Corrupt storage, reseeding.', e); }
  DB = normalizeSeeded(seedData());
  saveDB();
}
function saveDB() {
  try { localStorage.setItem(STORAGE_KEY, JSON.stringify(DB)); } catch (e) {}
}

/* =========================================================
   HELPERS
   ========================================================= */
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c =>
  ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const peso = n => '₱' + Number(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const num  = n => Number(n || 0).toLocaleString('en-PH', { maximumFractionDigits: 2 });
const fmtDate = d => { if (!d) return '—'; const dt = new Date(String(d).replace(' ', 'T'));
  return isNaN(dt) ? '—' : dt.toLocaleDateString('en-PH', { day:'2-digit', month:'short', year:'numeric' }); };
const fmtDateTime = d => { if (!d) return '—'; const dt = new Date(String(d).replace(' ', 'T'));
  return isNaN(dt) ? '—' : dt.toLocaleString('en-PH', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' }); };
const today = () => new Date().toISOString().slice(0, 10);
const uid = k => { const n = DB.nextIds[k]; DB.nextIds[k] = n + 1; return n; };

const ORDER_STATUS = {
  pending:   { label:'Pending',   cls:'pending',   icon:'hourglass-split',  next:'washing',   nextLabel:'Start Washing' },
  washing:   { label:'Washing',   cls:'washing',   icon:'droplet-half',     next:'drying',    nextLabel:'Move to Drying' },
  drying:    { label:'Drying',    cls:'drying',    icon:'wind',             next:'ready',     nextLabel:'Mark Ready' },
  ready:     { label:'Ready',     cls:'ready',     icon:'bag-check',        next:'delivered', nextLabel:'Mark Delivered' },
  delivered: { label:'Delivered', cls:'delivered', icon:'check2-circle',    next:null },
  cancelled: { label:'Cancelled', cls:'cancelled', icon:'x-circle',         next:null }
};
const PAY_STATUS = {
  unpaid:  { label:'Unpaid',  cls:'unpaid'  },
  partial: { label:'Partial', cls:'partial' },
  paid:    { label:'Paid',    cls:'paid'    }
};
const MACHINE_STATUS = {
  available:   { label:'Available',   cls:'available',   icon:'check-circle-fill' },
  in_use:      { label:'In Use',      cls:'in_use',      icon:'play-circle-fill'  },
  maintenance: { label:'Maintenance', cls:'maintenance', icon:'wrench-adjustable' },
  tub_clean:   { label:'Tub Cleaning',cls:'tub_clean',   icon:'droplet-fill'      }
};

function toast(msg, type = 'success') {
  const icons = { success:'check-circle-fill', danger:'exclamation-triangle-fill', info:'info-circle-fill', warning:'exclamation-circle-fill' };
  const el = document.createElement('div');
  el.className = `toast align-items-center text-bg-${type} border-0`;
  el.innerHTML = `<div class="d-flex">
      <div class="toast-body"><i class="bi bi-${icons[type] || icons.info} me-2"></i>${esc(msg)}</div>
      <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>`;
  $('toasts').appendChild(el);
  const t = new bootstrap.Toast(el, { delay: 3200 });
  t.show();
  el.addEventListener('hidden.bs.toast', () => el.remove());
}

const findCustomer = id => DB.customers.find(c => c.id === Number(id));
const findService  = id => DB.services.find(s => s.id === Number(id));
const findMachine  = id => DB.machines.find(m => m.id === Number(id));
const findOrder    = id => DB.orders.find(o => o.id === Number(id));

function orderItems(orderId) {
  return DB.orderItems.filter(i => i.order_id === Number(orderId));
}
function orderServiceNames(orderId) {
  const items = orderItems(orderId);
  if (!items.length) return '—';
  return [...new Set(items.map(i => findService(i.service_id)?.name || 'Item'))].join(', ');
}

/* =========================================================
   ROUTER & CLOCK
   ========================================================= */
const PAGES = {
  dashboard:   renderDashboard,
  orders:      renderOrders,
  queue:       renderQueue,
  customers:   renderCustomers,
  services:    renderServices,
  machines:    renderMachines,
  inventory:   renderInventory,
  maintenance: renderMaintenance,
  reports:     renderReports
};
let currentPage = 'dashboard';

function navigate(page) {
  if (!PAGES[page]) page = 'dashboard';
  currentPage = page;
  document.querySelectorAll('.nav-item').forEach(a =>
    a.classList.toggle('active', a.dataset.page === page));
  $('sidebar').classList.remove('open');
  $('sidebarBackdrop').classList.remove('show');
  PAGES[page]();
}

document.querySelectorAll('.nav-item').forEach(a =>
  a.addEventListener('click', () => navigate(a.dataset.page)));

$('menuToggle').onclick = () => {
  $('sidebar').classList.toggle('open');
  $('sidebarBackdrop').classList.toggle('show');
};
$('sidebarBackdrop').onclick = () => {
  $('sidebar').classList.remove('open');
  $('sidebarBackdrop').classList.remove('show');
};

function updateClock() {
  const now = new Date();
  const timeEl = $('clockTime');
  const dateEl = $('clockDate');
  if (timeEl) timeEl.textContent = now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  if (dateEl) dateEl.textContent = now.toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
}
updateClock();
setInterval(updateClock, 1000);

/* =========================================================
   DASHBOARD
   ========================================================= */
function renderDashboard() {
  const c = $('pageContainer');
  const t = today();
  const revenueToday = DB.payments
    .filter(p => (p.created_at || '').slice(0, 10) === t)
    .reduce((s, p) => s + Number(p.amount), 0);
  const revenueMonth = DB.payments
    .filter(p => (p.created_at || '').slice(0, 7) === t.slice(0, 7))
    .reduce((s, p) => s + Number(p.amount), 0);

  const activeOrders = DB.orders.filter(o => ['pending','washing','drying'].includes(o.status)).length;
  const readyOrders  = DB.orders.filter(o => o.status === 'ready').length;
  const pendingPay   = DB.orders.filter(o => o.payment_status !== 'paid' && o.status !== 'cancelled').length;
  const unclaimed    = DB.orders.filter(o => o.status === 'ready');
  const recent       = [...DB.orders].sort((a, b) => b.id - a.id).slice(0, 6);

  const serviceStats = DB.services.map(s => {
    let orders = 0, revenue = 0;
    DB.orderItems.forEach(it => {
      if (it.service_id === s.id) { orders++; revenue += Number(it.subtotal); }
    });
    return { ...s, orders, revenue };
  }).filter(s => s.orders > 0).sort((a, b) => b.revenue - a.revenue).slice(0, 5);
  const topMax = Math.max(1, ...serviceStats.map(s => s.revenue));

  const machineCounts = Object.keys(MACHINE_STATUS).map(k => ({
    k, ...MACHINE_STATUS[k],
    count: DB.machines.filter(m => m.status === k).length
  }));

  const unclaimedList = [...unclaimed]
    .sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at))
    .slice(0, 6);

  c.innerHTML = `
    <div class="page-head">
      <div>
        <h2>Dashboard Overview</h2>
        <p>${new Date().toLocaleDateString('en-PH', { weekday:'long', day:'numeric', month:'long', year:'numeric' })}</p>
      </div>
      <button class="btn btn-primary fw-semibold" onclick="openOrderForm()">
        <i class="bi bi-plus-lg me-1"></i>New Order
      </button>
    </div>

    <div class="row g-3 mb-3">
      ${statCard('Total Orders', num(DB.orders.length), 'receipt', 'primary', `${num(DB.customers.length)} customers`)}
      ${statCard('Active Orders', num(activeOrders), 'arrow-repeat', 'info', `${num(readyOrders)} ready for pickup`)}
      ${statCard("Today's Revenue", peso(revenueToday), 'cash-stack', 'success', `${peso(revenueMonth)} this month`)}
      ${statCard('Pending Payments', num(pendingPay), 'exclamation-circle', 'warning', `${unclaimed.length} unclaimed order(s)`)}
    </div>

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock-history me-1"></i>Recent Orders</span>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control form-control-sm" id="dashboardSearch" placeholder="Search orders..." style="width: 180px;">
              <a class="small text-decoration-none" href="#" onclick="navigate('orders');return false;">View all →</a>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th class="text-end">Amount</th></tr></thead>
              <tbody id="dashboardOrdersBody">
                ${recent.length ? recent.map(o => renderRecentOrderRow(o)).join('') : `<tr><td colspan="5" class="empty"><i class="bi bi-inbox"></i>No orders yet</td></tr>`}
              </tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><i class="bi bi-graph-up-arrow me-1"></i>Top Services</div>
          <div class="card-body">
            ${serviceStats.length ? serviceStats.map(s => `
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-semibold small">${esc(s.name)}</span>
                  <span class="text-body-secondary small">${num(s.orders)} order(s) · ${peso(s.revenue)}</span>
                </div>
                <div class="bar-track"><div class="bar-fill" style="width:${(s.revenue/topMax*100).toFixed(1)}%"></div></div>
              </div>`).join('') : '<div class="text-body-secondary small">No service data yet.</div>'}
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card mb-3">
          <div class="card-header"><i class="bi bi-lightning-charge-fill me-1"></i>Quick Actions</div>
          <div class="card-body d-grid gap-2">
            <button class="quick-btn" onclick="openOrderForm()"><i class="bi bi-plus-circle text-primary"></i>Register New Order</button>
            <button class="quick-btn" onclick="openCustomerForm()"><i class="bi bi-person-plus text-primary"></i>Add Customer</button>
            <button class="quick-btn" onclick="navigate('queue')"><i class="bi bi-hourglass-split text-primary"></i>Monitor Machine Queue</button>
            <button class="quick-btn" onclick="navigate('inventory')"><i class="bi bi-box-seam text-primary"></i>Check Inventory</button>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-bag-check-fill me-1"></i>Unclaimed Laundry</span>
            <span class="badge text-bg-warning">${unclaimedList.length}</span>
          </div>
          <div class="card-body p-0">
            ${unclaimedList.length ? unclaimedList.map(u => {
              const cu = findCustomer(u.customer_id);
              const days = Math.max(0, Math.floor((Date.now() - new Date(u.updated_at).getTime()) / 86400000));
              return `<div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <div>
                  <div class="fw-semibold small">${esc(cu ? cu.name : '—')}</div>
                  <div class="text-body-secondary" style="font-size:.72rem">
                    <span class="font-monospace">${esc(u.order_code)}</span> · ${days} day(s)
                  </div>
                </div>
                <div class="text-end">
                  <div class="fw-bold small">${peso(u.total)}</div>
                  <button class="btn btn-sm btn-link p-0" style="font-size:.7rem" onclick="openOrderForm(${u.id})">Open</button>
                </div>
              </div>`;
            }).join('') : '<div class="p-3 text-body-secondary small">All caught up! 🎉</div>'}
          </div>
        </div>

        <div class="card">
          <div class="card-header"><i class="bi bi-cpu-fill me-1"></i>Machine Status</div>
          <div class="card-body">
            ${machineCounts.map(m => `
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small"><span class="pill pill-${m.cls}">${m.label}</span></span>
                <span class="fw-bold">${num(m.count)}</span>
              </div>`).join('')}
          </div>
        </div>
      </div>
    </div>
  `;

  $('dashboardSearch').addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    const filtered = recent.filter(o => {
      const cust = findCustomer(o.customer_id);
      return (o.order_code || '').toLowerCase().includes(q) || (cust && cust.name.toLowerCase().includes(q));
    });
    $('dashboardOrdersBody').innerHTML = filtered.length ? filtered.map(o => renderRecentOrderRow(o)).join('') : `<tr><td colspan="5" class="empty">No matching orders</td></tr>`;
  });

  refreshBadges();
}

function renderRecentOrderRow(o) {
  const cust = findCustomer(o.customer_id);
  const st = ORDER_STATUS[o.status] || ORDER_STATUS.pending;
  const ps = PAY_STATUS[o.payment_status] || PAY_STATUS.unpaid;
  return `<tr>
    <td><span class="fw-semibold font-monospace" style="font-size:.8rem">${esc(o.order_code)}</span></td>
    <td>${esc(cust ? cust.name : '—')}</td>
    <td class="text-body-secondary">${fmtDate(o.created_at)}</td>
    <td>
      <span class="pill pill-${st.cls}"><i class="bi bi-${st.icon}"></i>${st.label}</span>
      ${o.payment_status !== 'paid' ? `<span class="pill pill-${ps.cls} ms-1">${ps.label}</span>` : ''}
    </td>
    <td class="text-end fw-semibold">${peso(o.total)}</td>
  </tr>`;
}

function statCard(label, value, icon, color, sub) {
  return `
    <div class="col-6 col-lg-3">
      <div class="card h-100">
        <div class="card-body stat">
          <div class="min-w-0">
            <div class="lbl">${label}</div>
            <div class="val">${value}</div>
            <div class="trend text-${color}" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${sub}</div>
          </div>
          <div class="ico bg-${color}-subtle text-${color}"><i class="bi bi-${icon}"></i></div>
        </div>
      </div>
    </div>`;
}

/* =========================================================
   ORDERS
   ========================================================= */
function renderOrders() {
  const c = $('pageContainer');
  c.innerHTML = `
    <div class="page-head">
      <div>
        <h2>Orders</h2>
        <p>Create, monitor and track all laundry transactions.</p>
      </div>
      <button class="btn btn-primary fw-semibold" onclick="openOrderForm()">
        <i class="bi bi-plus-lg me-1"></i>New Order
      </button>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-7">
            <select class="form-select" id="orderStatusFilter">
              <option value="">All statuses</option>
              ${Object.entries(ORDER_STATUS).map(([k,v]) => `<option value="${k}">${v.label}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" id="orderPayFilter">
              <option value="">All payments</option>
              <option value="unpaid">Unpaid</option>
              <option value="partial">Partial</option>
              <option value="paid">Paid</option>
            </select>
          </div>
          <div class="col-md-2 text-md-end d-flex align-items-center justify-content-md-end">
            <span class="text-body-secondary small" id="orderCount">—</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Order</th><th>Customer</th><th>Services</th>
              <th class="text-end">Total</th><th class="text-end">Balance</th>
              <th>Status</th><th>Payment</th><th>Pickup</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="ordersBody"></tbody>
        </table>
      </div>
    </div>`;

  const runSearch = () => {
    const status  = $('orderStatusFilter').value;
    const payment = $('orderPayFilter').value;

    let list = [...DB.orders].sort((a, b) => b.id - a.id);

    if (status)  list = list.filter(o => o.status === status);
    if (payment) list = list.filter(o => o.payment_status === payment);

    $('orderCount').textContent = `${list.length} order(s)`;

    const tb = $('ordersBody');
    if (!list.length) {
      tb.innerHTML = `<tr><td colspan="9" class="empty"><i class="bi bi-inbox"></i>No orders match your filters</td></tr>`;
      return;
    }

    tb.innerHTML = list.map(o => {
      const cust = findCustomer(o.customer_id);
      const st = ORDER_STATUS[o.status] || ORDER_STATUS.pending;
      const ps = PAY_STATUS[o.payment_status] || PAY_STATUS.unpaid;
      const balance = Math.max(0, Number(o.total) - Number(o.amount_paid));
      const next = st.next ? `<button class="btn btn-sm btn-outline-primary" title="${st.nextLabel}"
                                onclick="advanceOrder(${o.id},'${st.next}')">
                                <i class="bi bi-arrow-right-circle"></i></button>` : '';

      return `<tr>
        <td>
          <div class="fw-semibold font-monospace" style="font-size:.8rem">${esc(o.order_code)}</div>
          <div class="text-body-secondary" style="font-size:.72rem">${fmtDate(o.created_at)}</div>
        </td>
        <td>
          <div class="fw-semibold">${esc(cust ? cust.name : '—')}</div>
          <div class="text-body-secondary" style="font-size:.72rem">${esc(cust ? (cust.phone || '—') : '—')}</div>
        </td>
        <td class="small">${esc(orderServiceNames(o.id))}</td>
        <td class="text-end fw-semibold">${peso(o.total)}</td>
        <td class="text-end ${balance > 0 ? 'text-danger fw-semibold' : 'text-success'}">${balance > 0 ? peso(balance) : 'Settled'}</td>
        <td><span class="pill pill-${st.cls}"><i class="bi bi-${st.icon}"></i>${st.label}</span></td>
        <td><span class="pill pill-${ps.cls}">${ps.label}</span></td>
        <td class="small text-body-secondary">${fmtDate(o.pickup_date)}</td>
        <td class="text-end text-nowrap">
          ${next}
          <button class="btn btn-sm btn-outline-success" title="Record Payment"
            onclick="openPaymentForm(${o.id})"><i class="bi bi-cash-coin"></i></button>
          <button class="btn btn-sm btn-outline-secondary" title="Receipt"
            onclick="showReceipt(${o.id})"><i class="bi bi-receipt-cutoff"></i></button>
          <button class="btn btn-sm btn-outline-secondary" title="Edit"
            onclick="openOrderForm(${o.id})"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-outline-danger" title="Delete"
            onclick="deleteOrder(${o.id})"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`;
    }).join('');
  };

  $('orderStatusFilter').addEventListener('change', runSearch);
  $('orderPayFilter').addEventListener('change', runSearch);
  runSearch();
}

function openOrderForm(id = 0) {
  const order = id > 0 ? findOrder(id) : null;
  if (id > 0 && !order) { toast('Order not found', 'danger'); return; }

  const activeServices = DB.services.filter(s => Number(s.active) === 1);

  $('mdlTitle').innerHTML = order
    ? `Edit Order <span class="font-monospace text-primary">${esc(order.order_code)}</span>`
    : 'New Laundry Order';

  $('mdlBody').innerHTML = `
    <input type="hidden" id="fOrderId" value="${order ? order.id : 0}">
    <div class="row g-3">
      <div class="col-md-7">
        <label class="form-label">Customer *</label>
        <div class="input-group">
          <select class="form-select" id="fCustomer">
            <option value="">— Select customer —</option>
            ${DB.customers.map(cu => `<option value="${cu.id}" ${order && cu.id === order.customer_id ? 'selected' : ''}>
              ${esc(cu.name)}${cu.phone ? ' · ' + esc(cu.phone) : ''}</option>`).join('')}
          </select>
          <button class="btn btn-outline-secondary" type="button" onclick="openCustomerForm(false, null, true)">
            <i class="bi bi-person-plus"></i>
          </button>
        </div>
      </div>
      <div class="col-md-5">
        <label class="form-label">Pickup Date</label>
        <input type="date" class="form-control" id="fPickup" value="${order && order.pickup_date ? order.pickup_date : ''}">
      </div>

      <div class="col-12">
        <label class="form-label d-flex justify-content-between align-items-center">
          <span>Service Items *</span>
          <button class="btn btn-sm btn-outline-primary" type="button" onclick="addLineItem()">
            <i class="bi bi-plus-lg"></i> Add item
          </button>
        </label>
        <div id="lineItems" class="d-grid gap-2"></div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" id="fStatus">
          ${Object.entries(ORDER_STATUS).map(([k,v]) =>
            `<option value="${k}" ${order && order.status === k ? 'selected' : ''}>${v.label}</option>`).join('')}
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Extra Drying Cycle (+₱30)</label>
        <div class="form-check form-switch mt-2">
          <input class="form-check-input" type="checkbox" id="fExtraDry" ${order && order.extra_dry ? 'checked' : ''}>
          <label class="form-check-label small" for="fExtraDry">Customer requested extra 10-min dry</label>
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label">Grand Total</label>
        <div class="form-control bg-light fw-bold text-primary" id="fTotal" style="font-size:1.1rem">₱0.00</div>
      </div>

      <div class="col-12">
        <label class="form-label">Notes / Special Instructions</label>
        <textarea class="form-control" id="fNotes" rows="2" maxlength="500"
          placeholder="Stain treatment, delicate fabric, contact before pickup…">${esc(order ? order.notes || '' : '')}</textarea>
      </div>
    </div>`;

  $('mdlFooter').innerHTML = `
    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button class="btn btn-primary px-4" id="saveOrderBtn"><i class="bi bi-check2 me-1"></i>Save Order</button>`;

  const container = $('lineItems');
  container.innerHTML = '';
  if (order) {
    const items = orderItems(order.id);
    items.forEach(it => addLineItem(it, activeServices));
    if (!items.length) addLineItem(null, activeServices);
  } else {
    addLineItem(null, activeServices);
  }

  $('fExtraDry').addEventListener('change', recalcTotal);
  $('saveOrderBtn').onclick = saveOrder;

  function addLineItem(item = null, services = activeServices) {
    const wrap = document.createElement('div');
    wrap.className = 'line-item row g-2 align-items-end';
    wrap.innerHTML = `
      <div class="col-md-6">
        <label class="form-label small mb-1">Service</label>
        <select class="form-select form-select-sm li-service">
          <option value="">— Select —</option>
          ${services.map(s => `<option value="${s.id}" data-price="${s.price}" data-unit="${esc(s.unit)}"
            ${item && item.service_id === s.id ? 'selected' : ''}>
            ${esc(s.name)} — ₱${Number(s.price).toFixed(2)}/${esc(s.unit)}</option>`).join('')}
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1">Qty <span class="li-unit text-muted"></span></label>
        <input type="number" class="form-control form-control-sm li-qty" min="0.01" step="0.01"
          value="${item ? item.qty : 1}">
      </div>
      <div class="col-md-2">
        <label class="form-label small mb-1">Subtotal</label>
        <input type="text" class="form-control form-control-sm li-sub bg-white" readonly value="₱0.00">
      </div>
      <div class="col-md-1 text-end">
        <button class="btn btn-sm btn-outline-danger w-100 li-remove" type="button"><i class="bi bi-x-lg"></i></button>
      </div>`;

    container.appendChild(wrap);
    const svcSel = wrap.querySelector('.li-service');
    const qtyEl  = wrap.querySelector('.li-qty');
    const subEl  = wrap.querySelector('.li-sub');
    const unitEl = wrap.querySelector('.li-unit');

    const updateRow = () => {
      const opt   = svcSel.selectedOptions[0];
      const price = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
      const unit  = opt ? opt.dataset.unit : '';
      const qty   = parseFloat(qtyEl.value) || 0;
      subEl.value = peso(price * qty);
      unitEl.textContent = unit && svcSel.value ? `(${unit})` : '';
      recalcTotal();
    };

    svcSel.addEventListener('change', updateRow);
    qtyEl.addEventListener('input', updateRow);
    wrap.querySelector('.li-remove').onclick = () => {
      if (container.children.length === 1) { toast('At least one item is required.', 'warning'); return; }
      wrap.remove(); recalcTotal();
    };

    if (item) {
      const opt = svcSel.selectedOptions[0];
      if (opt && opt.dataset.unit) unitEl.textContent = `(${opt.dataset.unit})`;
    }
    updateRow();
  }

  function recalcTotal() {
    let total = 0;
    container.querySelectorAll('.line-item').forEach(row => {
      const opt   = row.querySelector('.li-service').selectedOptions[0];
      const price = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
      const qty   = parseFloat(row.querySelector('.li-qty').value) || 0;
      total += price * qty;
    });
    if ($('fExtraDry').checked) total += 30;
    $('fTotal').textContent = peso(total);
  }

  window.addLineItem = () => addLineItem(null, activeServices);
  new bootstrap.Modal('#mdl').show();
}

function saveOrder() {
  const btn = $('saveOrderBtn');
  const items = [];
  document.querySelectorAll('#lineItems .line-item').forEach(row => {
    const svcId = row.querySelector('.li-service').value;
    const qty   = parseFloat(row.querySelector('.li-qty').value) || 0;
    const opt   = row.querySelector('.li-service').selectedOptions[0];
    const price = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
    if (svcId && qty > 0) items.push({ service_id: Number(svcId), qty, price });
  });

  const customerId = $('fCustomer').value;
  if (!customerId) { toast('Please select a customer.', 'danger'); return; }
  if (!items.length) { toast('Add at least one service item.', 'danger'); return; }

  let total = items.reduce((s, i) => s + i.qty * i.price, 0);
  const extraDry = $('fExtraDry').checked ? 1 : 0;
  if (extraDry) total += 30;

  const id = Number($('fOrderId').value);
  const orderData = {
    customer_id:  Number(customerId),
    status:       $('fStatus').value,
    pickup_date:  $('fPickup').value || null,
    extra_dry:    extraDry,
    notes:        $('fNotes').value.trim(),
    total,
    updated_at:   new Date().toISOString()
  };

  btn.disabled = true;
  try {
    let orderId, message;
    if (id > 0) {
      const o = findOrder(id);
      Object.assign(o, orderData);
      DB.orderItems = DB.orderItems.filter(i => i.order_id !== id);
      items.forEach(i => DB.orderItems.push({ id: uid('orderItem'), order_id: id, service_id: i.service_id, qty: i.qty, price: i.price, subtotal: i.qty * i.price }));
      orderId = id;
      message = `Order ${o.order_code} updated.`;
    } else {
      const code = nextOrderCode();
      const newOrder = {
        id: uid('order'), order_code: code, customer_id: orderData.customer_id,
        status: orderData.status, payment_status: 'unpaid', total: orderData.total,
        amount_paid: 0, intake_date: today(), pickup_date: orderData.pickup_date,
        extra_dry: orderData.extra_dry, notes: orderData.notes,
        created_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
        updated_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
      };
      DB.orders.push(newOrder);
      orderId = newOrder.id;
      items.forEach(i => DB.orderItems.push({ id: uid('orderItem'), order_id: orderId, service_id: i.service_id, qty: i.qty, price: i.price, subtotal: i.qty * i.price }));
      message = `Order ${code} created.`;
    }

    saveDB();
    bootstrap.Modal.getInstance($('mdl')).hide();
    toast(message);
    navigate(currentPage);
    refreshBadges();
  } catch (err) {
    toast(err.message, 'danger');
  } finally {
    btn.disabled = false;
  }
}

function nextOrderCode() {
  const prefix = 'HOW-' + new Date().toISOString().slice(2, 10).replace(/-/g, '') + '-';
  const same = DB.orders.filter(o => (o.order_code || '').startsWith(prefix));
  return prefix + String(same.length + 1).padStart(3, '0');
}

function advanceOrder(id, status) {
  const o = findOrder(id);
  if (!o) return;
  o.status = status;
  o.updated_at = new Date().toISOString().slice(0, 19).replace('T', ' ');
  saveDB();
  toast(`Order status updated.`);
  navigate(currentPage);
  refreshBadges();
}

function deleteOrder(id) {
  const o = findOrder(id);
  if (!o || !confirm(`Delete order ${o.order_code}?`)) return;
  DB.orders = DB.orders.filter(x => x.id !== id);
  DB.orderItems = DB.orderItems.filter(i => i.order_id !== id);
  DB.payments = DB.payments.filter(p => p.order_id !== id);
  saveDB();
  toast('Order deleted.');
  navigate(currentPage);
  refreshBadges();
}

function openPaymentForm(id) {
  const o = findOrder(id);
  const cust = findCustomer(o.customer_id);
  const balance = Math.max(0, Number(o.total) - Number(o.amount_paid));

  $('mdlSmTitle').textContent = 'Record Payment';
  $('mdlSmBody').innerHTML = `
    <div class="mb-3 p-3 bg-light rounded">
      <div class="d-flex justify-content-between small"><span>Order</span><strong class="font-monospace">${esc(o.order_code)}</strong></div>
      <div class="d-flex justify-content-between small"><span>Customer</span><strong>${esc(cust ? cust.name : '—')}</strong></div>
      <div class="d-flex justify-content-between border-top mt-1 pt-1"><span>Balance</span><strong class="text-danger">${peso(balance)}</strong></div>
    </div>
    <label class="form-label">Amount Received *</label>
    <input type="number" class="form-control mb-3" id="payAmount" min="0.01" step="0.01" value="${balance.toFixed(2)}">
    <label class="form-label">Payment Method</label>
    <select class="form-select" id="payMethod">
      <option value="cash">Cash</option>
      <option value="gcash">GCash</option>
      <option value="maya">Maya</option>
      <option value="bank">Bank Transfer</option>
    </select>`;

  $('mdlSmFooter').innerHTML = `
    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button class="btn btn-success" id="payBtn">Record Payment</button>`;

  new bootstrap.Modal('#mdlSm').show();

  $('payBtn').onclick = () => {
    const amount = parseFloat($('payAmount').value);
    if (!amount || amount <= 0) { toast('Enter a valid amount.', 'danger'); return; }

    DB.payments.push({
      id: uid('payment'), order_id: id, amount,
      method: $('payMethod').value,
      created_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
    });

    o.amount_paid = Number(o.amount_paid) + amount;
    o.payment_status = o.amount_paid >= Number(o.total) ? 'paid' : (o.amount_paid > 0 ? 'partial' : 'unpaid');
    o.updated_at = new Date().toISOString().slice(0, 19).replace('T', ' ');

    saveDB();
    bootstrap.Modal.getInstance($('mdlSm')).hide();
    toast('Payment recorded.');
    navigate(currentPage);
    refreshBadges();
  };
}

function showReceipt(id) {
  const o = findOrder(id);
  const cust = findCustomer(o.customer_id);
  const items = orderItems(o.id);
  const balance = Math.max(0, Number(o.total) - Number(o.amount_paid));

  $('mdlTitle').innerHTML = 'Digital Receipt &amp; Claim Slip';
  $('mdlBody').innerHTML = `
    <div class="receipt p-3 border rounded">
      <div class="text-center mb-2">
        <div style="font-size:1.1rem;font-weight:700">HOUSE OF WASH</div>
        <div>Laundry Management System</div>
      </div>
      <div class="hr"></div>
      <div class="d-flex justify-content-between"><span>Order Code</span><strong>${esc(o.order_code)}</strong></div>
      <div class="d-flex justify-content-between"><span>Customer</span><span>${esc(cust ? cust.name : '—')}</span></div>
      <div class="hr"></div>
      <table class="w-100" style="font-size:.8rem">
        <thead><tr><th class="text-start">Item</th><th class="text-end">Qty</th><th class="text-end">Price</th><th class="text-end">Sub</th></tr></thead>
        <tbody>
          ${items.map(i => {
            const s = findService(i.service_id);
            return `<tr><td>${esc(s ? s.name : 'Item')}</td><td class="text-end">${num(i.qty)}</td><td class="text-end">${peso(i.price)}</td><td class="text-end">${peso(i.subtotal)}</td></tr>`;
          }).join('')}
        </tbody>
      </table>
      <div class="hr"></div>
      <div class="d-flex justify-content-between"><span>TOTAL</span><strong>${peso(o.total)}</strong></div>
      <div class="d-flex justify-content-between"><span>PAID</span><span>${peso(o.amount_paid)}</span></div>
      <div class="d-flex justify-content-between"><span>BALANCE</span><strong>${peso(balance)}</strong></div>
    </div>`;

  $('mdlFooter').innerHTML = `
    <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-primary" onclick="window.print()">Print</button>`;

  new bootstrap.Modal('#mdl').show();
}

/* =========================================================
   QUEUE
   ========================================================= */
function renderQueue() {
  const c = $('pageContainer');
  const running = DB.machineLogs.filter(l => l.status === 'running');
  const completed = DB.machineLogs.filter(l => l.status !== 'running').slice(-12).reverse();
  const activeOrders = DB.orders.filter(o => ['pending','washing','drying'].includes(o.status));

  c.innerHTML = `
    <div class="page-head">
      <div><h2>Queue &amp; Machine Monitoring</h2><p>Track self-service sessions.</p></div>
      <button class="btn btn-primary fw-semibold" onclick="openStartSession()">Start Machine Session</button>
    </div>
    <div class="row g-3">
      <div class="col-lg-7">
        <div class="card mb-3">
          <div class="card-header">Currently Running (${running.length})</div>
          <div class="card-body p-0" id="runningList"></div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card mb-3">
          <div class="card-header">Processing Queue</div>
          <div class="card-body p-0">
            ${activeOrders.length ? activeOrders.map(o => {
              const cust = findCustomer(o.customer_id);
              return `<div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <div>
                  <div class="fw-semibold small">${esc(cust ? cust.name : '—')}</div>
                  <div class="text-body-secondary" style="font-size:.72rem"><span class="font-monospace">${esc(o.order_code)}</span></div>
                </div>
                <span class="pill pill-${o.status}">${o.status}</span>
              </div>`;
            }).join('') : '<div class="empty">Queue is empty</div>'}
          </div>
        </div>
      </div>
    </div>`;

  renderRunningList();
}

function renderRunningList() {
  const el = $('runningList');
  if (!el) return;
  const running = DB.machineLogs.filter(l => l.status === 'running');
  if (!running.length) { el.innerHTML = '<div class="empty">No machines currently running</div>'; return; }

  el.innerHTML = running.map(l => {
    const m = findMachine(l.machine_id);
    const elapsed = Math.floor((Date.now() - new Date(l.started_at).getTime()) / 60000);
    return `<div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
      <div>
        <div class="fw-semibold">${esc(m ? m.code : '')} · ${esc(m ? m.name : '')}</div>
        <div class="small text-body-secondary">${esc(l.customer_name)}</div>
      </div>
      <div class="text-end">
        <div class="fw-bold">${elapsed} / ${l.cycle_minutes} min</div>
        <button class="btn btn-sm btn-outline-success mt-1" onclick="endSession(${l.id})">Free Machine</button>
      </div>
    </div>`;
  }).join('');
}

function openStartSession() {
  const free = DB.machines.filter(m => m.status === 'available' && m.type === 'washer');
  if (!free.length) { toast('No available washers.', 'warning'); return; }

  $('mdlSmTitle').textContent = 'Start Machine Session';
  $('mdlSmBody').innerHTML = `
    <label class="form-label">Machine *</label>
    <select class="form-select mb-3" id="ssMachine">
      ${free.map(m => `<option value="${m.id}">${esc(m.code)} · ${esc(m.name)}</option>`).join('')}
    </select>
    <label class="form-label">Customer Name *</label>
    <input type="text" class="form-control mb-3" id="ssName" placeholder="Customer name">
    <label class="form-label">Duration (minutes)</label>
    <input type="number" class="form-control" id="ssMinutes" value="45">`;

  $('mdlSmFooter').innerHTML = `<button class="btn btn-primary" id="ssBtn">Start</button>`;
  new bootstrap.Modal('#mdlSm').show();

  $('ssBtn').onclick = () => {
    DB.machineLogs.push({
      id: uid('machineLog'), machine_id: Number($('ssMachine').value),
      customer_name: $('ssName').value.trim() || 'Walk-in',
      cycle_minutes: Number($('ssMinutes').value) || 45,
      started_at: new Date().toISOString(), ended_at: null, status: 'running'
    });
    const m = findMachine($('ssMachine').value);
    if (m) m.status = 'in_use';
    saveDB();
    bootstrap.Modal.getInstance($('mdlSm')).hide();
    renderQueue();
  };
}

function endSession(id) {
  const l = DB.machineLogs.find(x => x.id === id);
  if (!l) return;
  l.ended_at = new Date().toISOString();
  l.status = 'completed';
  const m = findMachine(l.machine_id);
  if (m) m.status = 'available';
  saveDB();
  renderQueue();
}

/* =========================================================
   CUSTOMERS
   ========================================================= */
function renderCustomers() {
  const c = $('pageContainer');
  c.innerHTML = `
    <div class="page-head">
      <div><h2>Customers</h2><p>Manage customer records and contact details.</p></div>
      <button class="btn btn-primary fw-semibold" onclick="openCustomerForm()">
        <i class="bi bi-person-plus me-1"></i>Add Customer
      </button>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <div class="input-group" style="max-width:420px">
          <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" id="custSearch" placeholder="Search name, phone or email…">
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th>Customer</th><th>Contact</th><th>Address</th>
                <th class="text-end">Orders</th><th class="text-end">Total Spent</th>
                <th>Last Order</th><th class="text-end">Actions</th></tr>
          </thead>
          <tbody id="custBody"></tbody>
        </table>
      </div>
    </div>`;

  const load = () => {
    const q = $('custSearch').value.trim().toLowerCase();
    let list = DB.customers;
    if (q) list = list.filter(cu =>
      (cu.name || '').toLowerCase().includes(q) ||
      (cu.phone || '').toLowerCase().includes(q) ||
      (cu.email || '').toLowerCase().includes(q));

    const tb = $('custBody');
    if (!list.length) { tb.innerHTML = `<tr><td colspan="7" class="empty"><i class="bi bi-inbox"></i>No customers found</td></tr>`; return; }

    tb.innerHTML = list.map(cu => {
      const orders = DB.orders.filter(o => o.customer_id === cu.id);
      const totalSpent = DB.payments
        .filter(p => orders.some(o => o.id === p.order_id))
        .reduce((s, p) => s + Number(p.amount), 0);
      const lastOrder = orders.length ? orders.reduce((a, b) =>
        new Date(a.created_at) > new Date(b.created_at) ? a : b).created_at : null;

      return `<tr>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="avatar" style="width:32px;height:32px;font-size:.7rem">
              ${esc((cu.name || '?').split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase())}
            </div>
            <div>
              <div class="fw-semibold">${esc(cu.name)}</div>
              ${cu.notes ? `<div class="text-body-secondary" style="font-size:.7rem">${esc(cu.notes)}</div>` : ''}
            </div>
          </div>
        </td>
        <td class="small">
          ${cu.phone ? `<div><i class="bi bi-telephone"></i> ${esc(cu.phone)}</div>` : ''}
          ${cu.email ? `<div class="text-body-secondary"><i class="bi bi-envelope"></i> ${esc(cu.email)}</div>` : ''}
        </td>
        <td class="small text-body-secondary">${esc(cu.address || '—')}</td>
        <td class="text-end">${num(orders.length)}</td>
        <td class="text-end fw-semibold">${peso(totalSpent)}</td>
        <td class="small text-body-secondary">${lastOrder ? fmtDate(lastOrder) : '—'}</td>
        <td class="text-end text-nowrap">
          <button class="btn btn-sm btn-outline-secondary" onclick='openCustomerForm(false, ${JSON.stringify(cu).replace(/'/g,"&#39;")})'>
            <i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-outline-danger" onclick="deleteCustomer(${cu.id})">
            <i class="bi bi-trash"></i></button>
        </td>
      </tr>`;
    }).join('');
  };

  let t;
  $('custSearch').addEventListener('input', () => { clearTimeout(t); t = setTimeout(load, 250); });
  load();
}

function openCustomerForm(fromOrder = false, data = null) {
  $('mdlSmTitle').textContent = data ? 'Edit Customer' : 'Add Customer';
  $('mdlSmBody').innerHTML = `
    <input type="hidden" id="cfId" value="${data ? data.id : ''}">
    <label class="form-label">Full Name *</label>
    <input type="text" class="form-control mb-3" id="cfName" value="${data ? esc(data.name) : ''}" maxlength="120">
    <label class="form-label">Phone</label>
    <input type="text" class="form-control mb-3" id="cfPhone" value="${data ? esc(data.phone || '') : ''}" maxlength="40" placeholder="0917 123 4567">
    <label class="form-label">Email</label>
    <input type="email" class="form-control mb-3" id="cfEmail" value="${data ? esc(data.email || '') : ''}" maxlength="120">
    <label class="form-label">Address</label>
    <input type="text" class="form-control mb-3" id="cfAddress" value="${data ? esc(data.address || '') : ''}" maxlength="255">
    <label class="form-label">Notes</label>
    <textarea class="form-control" id="cfNotes" rows="2" maxlength="255">${data ? esc(data.notes || '') : ''}</textarea>`;

  $('mdlSmFooter').innerHTML = `
    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button class="btn btn-primary" id="cfBtn"><i class="bi bi-check2 me-1"></i>Save</button>`;

  new bootstrap.Modal('#mdlSm').show();

  $('cfBtn').onclick = () => {
    const name = $('cfName').value.trim();
    if (!name) { toast('Customer name is required.', 'danger'); return; }

    const id = Number($('cfId').value);
    const payload = {
      name,
      phone:   $('cfPhone').value.trim(),
      email:   $('cfEmail').value.trim(),
      address: $('cfAddress').value.trim(),
      notes:   $('cfNotes').value.trim()
    };

    if (id > 0) {
      const cu = DB.customers.find(x => x.id === id);
      if (!cu) return;
      Object.assign(cu, payload);
      toast('Customer updated.');
    } else {
      DB.customers.push({ id: uid('customer'), ...payload });
      toast('Customer added.');
    }
    saveDB();

    bootstrap.Modal.getInstance($('mdlSm')).hide();
    if (fromOrder) {
      setTimeout(() => openOrderForm(), 200);
    } else if (currentPage === 'customers') {
      renderCustomers();
    }
  };
}

function deleteCustomer(id) {
  const used = DB.orders.filter(o => o.customer_id === id).length;
  if (used > 0) { toast(`Cannot delete: customer has ${used} order(s).`, 'danger'); return; }
  if (!confirm('Delete this customer?')) return;
  DB.customers = DB.customers.filter(c => c.id !== id);
  saveDB();
  toast('Customer deleted.');
  renderCustomers();
}

/* =========================================================
   SERVICES
   ========================================================= */
function renderServices() {
  const c = $('pageContainer');
  c.innerHTML = `
    <div class="page-head">
      <div><h2>Laundry Services</h2><p>Manage service types, pricing and units.</p></div>
      <button class="btn btn-primary fw-semibold" onclick="openServiceForm()">
        <i class="bi bi-plus-lg me-1"></i>Add Service
      </button>
    </div>
    <div class="row g-3" id="serviceGrid"></div>`;

  const grid = $('serviceGrid');
  if (!DB.services.length) {
    grid.innerHTML = `<div class="col-12"><div class="empty"><i class="bi bi-tags"></i>No services yet</div></div>`;
    return;
  }

  grid.innerHTML = DB.services.map(s => `
    <div class="col-md-6 col-xl-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <div class="fw-bold" style="font-size:1rem">${esc(s.name)}</div>
              <div class="text-body-secondary" style="font-size:.75rem">${esc(s.description || 'No description')}</div>
            </div>
            <span class="pill ${s.active == 1 ? 'pill-ok' : 'pill-cancelled'}">${s.active == 1 ? 'Active' : 'Inactive'}</span>
          </div>
          <div class="d-flex align-items-baseline gap-1 mb-3">
            <span class="fw-bold text-primary" style="font-size:1.35rem">${peso(s.price)}</span>
            <span class="text-body-secondary small">/ ${esc(s.unit)}</span>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary flex-fill" onclick='openServiceForm(${JSON.stringify(s).replace(/'/g,"&#39;")})'>
              <i class="bi bi-pencil me-1"></i>Edit</button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteService(${s.id})">
              <i class="bi bi-trash"></i></button>
          </div>
        </div>
      </div>
    </div>`).join('');
}

function openServiceForm(data = null) {
  $('mdlSmTitle').textContent = data ? 'Edit Service' : 'Add Service';
  $('mdlSmBody').innerHTML = `
    <input type="hidden" id="sfId" value="${data ? data.id : ''}">
    <label class="form-label">Service Name *</label>
    <input type="text" class="form-control mb-3" id="sfName" value="${data ? esc(data.name) : ''}" maxlength="100">
    <div class="row g-3 mb-3">
      <div class="col-6">
        <label class="form-label">Price (₱) *</label>
        <input type="number" class="form-control" id="sfPrice" value="${data ? data.price : ''}" min="0" step="0.01">
      </div>
      <div class="col-6">
        <label class="form-label">Unit *</label>
        <select class="form-select" id="sfUnit">
          ${['kg','pc','cycle','load','set'].map(u =>
            `<option value="${u}" ${data && data.unit === u ? 'selected' : ''}>${u}</option>`).join('')}
        </select>
      </div>
    </div>
    <label class="form-label">Description</label>
    <input type="text" class="form-control mb-3" id="sfDesc" value="${data ? esc(data.description || '') : ''}" maxlength="255">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="sfActive" ${!data || data.active == 1 ? 'checked' : ''}>
      <label class="form-check-label" for="sfActive">Active (visible when creating orders)</label>
    </div>`;

  $('mdlSmFooter').innerHTML = `
    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button class="btn btn-primary" id="sfBtn"><i class="bi bi-check2 me-1"></i>Save</button>`;

  new bootstrap.Modal('#mdlSm').show();

  $('sfBtn').onclick = () => {
    const name = $('sfName').value.trim();
    const price = parseFloat($('sfPrice').value);
    if (!name) { toast('Service name is required.', 'danger'); return; }
    if (isNaN(price) || price < 0) { toast('Enter a valid price.', 'danger'); return; }

    const id = Number($('sfId').value);
    const payload = {
      name,
      price,
      unit:        $('sfUnit').value,
      description: $('sfDesc').value.trim(),
      active:      $('sfActive').checked ? 1 : 0
    };

    if (id > 0) {
      const s = findService(id);
      if (!s) return;
      Object.assign(s, payload);
      toast('Service updated.');
    } else {
      DB.services.push({ id: uid('service'), ...payload });
      toast('Service added.');
    }
    saveDB();
    bootstrap.Modal.getInstance($('mdlSm')).hide();
    renderServices();
  };
}

function deleteService(id) {
  const used = DB.orderItems.filter(i => i.service_id === id).length;
  if (used > 0) { toast(`Cannot delete: used in ${used} order item(s). Deactivate instead.`, 'danger'); return; }
  if (!confirm('Delete this service?')) return;
  DB.services = DB.services.filter(s => s.id !== id);
  saveDB();
  toast('Service deleted.');
  renderServices();
}

/* =========================================================
   MACHINES
   ========================================================= */
function renderMachines() {
  const c = $('pageContainer');
  c.innerHTML = `
    <div class="page-head">
      <div><h2>Machines &amp; Equipment</h2><p>Monitor washer and dryer availability, usage and maintenance.</p></div>
      <button class="btn btn-primary fw-semibold" onclick="openMachineForm()">
        <i class="bi bi-plus-lg me-1"></i>Add Machine
      </button>
    </div>
    <div class="row g-3" id="machineGrid"></div>`;

  const grid = $('machineGrid');
  if (!DB.machines.length) {
    grid.innerHTML = `<div class="col-12"><div class="empty"><i class="bi bi-cpu"></i>No machines registered</div></div>`;
    return;
  }

  grid.innerHTML = DB.machines.map(m => {
    const st = MACHINE_STATUS[m.status] || MACHINE_STATUS.available;
    const isWasher = m.type === 'washer';
    return `
      <div class="col-md-6 col-xl-4 col-xxl-3">
        <div class="machine">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="machine-icon ${isWasher ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info'}">
              <i class="bi bi-${isWasher ? 'water' : 'wind'}"></i>
            </div>
            <span class="pill pill-${st.cls}"><i class="bi bi-${st.icon}"></i>${st.label}</span>
          </div>
          <div class="code">${esc(m.code)}</div>
          <div class="name">${esc(m.name)}</div>
          <div class="small text-body-secondary mb-2">
            <i class="bi bi-arrows-angle-expand"></i> ${esc(m.capacity || '—')}
          </div>
          <div class="d-flex gap-1 flex-wrap">
            ${m.status === 'available'
              ? `<button class="btn btn-sm btn-outline-primary" onclick="setMachineStatus(${m.id},'maintenance')">
                   <i class="bi bi-wrench"></i> Maintenance</button>`
              : m.status === 'in_use'
                ? `<button class="btn btn-sm btn-outline-primary" onclick="navigate('queue')">
                     <i class="bi bi-hourglass"></i> View Queue</button>`
                : `<button class="btn btn-sm btn-outline-success" onclick="setMachineStatus(${m.id},'available')">
                     <i class="bi bi-check2"></i> Available</button>`}
            <button class="btn btn-sm btn-outline-secondary" onclick='openMachineForm(${JSON.stringify(m).replace(/'/g,"&#39;")})'>
              <i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteMachine(${m.id})">
              <i class="bi bi-trash"></i></button>
          </div>
        </div>
      </div>`;
  }).join('');
}

function openMachineForm(data = null) {
  $('mdlSmTitle').textContent = data ? 'Edit Machine' : 'Add Machine';
  $('mdlSmBody').innerHTML = `
    <input type="hidden" id="mfId" value="${data ? data.id : ''}">
    <div class="row g-3 mb-3">
      <div class="col-6">
        <label class="form-label">Machine Code *</label>
        <input type="text" class="form-control" id="mfCode" value="${data ? esc(data.code) : ''}" placeholder="W-05" maxlength="20">
      </div>
      <div class="col-6">
        <label class="form-label">Type *</label>
        <select class="form-select" id="mfType">
          <option value="washer" ${data && data.type === 'washer' ? 'selected' : ''}>Washer</option>
          <option value="dryer"  ${data && data.type === 'dryer'  ? 'selected' : ''}>Dryer</option>
        </select>
      </div>
    </div>
    <label class="form-label">Machine Name *</label>
    <input type="text" class="form-control mb-3" id="mfName" value="${data ? esc(data.name) : ''}" maxlength="80" placeholder="Washer 05">
    <div class="row g-3 mb-3">
      <div class="col-6">
        <label class="form-label">Capacity</label>
        <input type="text" class="form-control" id="mfCap" value="${data ? esc(data.capacity || '') : ''}" placeholder="8 kg" maxlength="40">
      </div>
      <div class="col-6">
        <label class="form-label">Status</label>
        <select class="form-select" id="mfStatus">
          ${Object.entries(MACHINE_STATUS).map(([k,v]) =>
            `<option value="${k}" ${data && data.status === k ? 'selected' : ''}>${v.label}</option>`).join('')}
        </select>
      </div>
    </div>
    <label class="form-label">Notes</label>
    <input type="text" class="form-control" id="mfNotes" value="${data ? esc(data.notes || '') : ''}" maxlength="255">`;

  $('mdlSmFooter').innerHTML = `
    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button class="btn btn-primary" id="mfBtn"><i class="bi bi-check2 me-1"></i>Save</button>`;

  new bootstrap.Modal('#mdlSm').show();

  $('mfBtn').onclick = () => {
    const code = $('mfCode').value.trim();
    const name = $('mfName').value.trim();
    if (!code || !name) { toast('Machine code and name are required.', 'danger'); return; }

    const id = Number($('mfId').value);
    const payload = {
      code, name,
      type:     $('mfType').value,
      status:   $('mfStatus').value,
      capacity: $('mfCap').value.trim(),
      notes:    $('mfNotes').value.trim()
    };

    if (id > 0) {
      const m = findMachine(id);
      if (!m) return;
      Object.assign(m, payload);
      toast('Machine updated.');
    } else {
      DB.machines.push({ id: uid('machine'), ...payload });
      toast('Machine added.');
    }
    saveDB();
    bootstrap.Modal.getInstance($('mdlSm')).hide();
    renderMachines();
  };
}

function setMachineStatus(id, status) {
  const m = findMachine(id);
  if (!m) return;
  m.status = status;
  saveDB();
  toast(`Machine set to ${MACHINE_STATUS[status].label}.`);
  renderMachines();
}

function deleteMachine(id) {
  if (!confirm('Delete this machine?')) return;
  DB.machines = DB.machines.filter(m => m.id !== id);
  DB.machineLogs = DB.machineLogs.filter(l => l.machine_id !== id);
  DB.maintenance = DB.maintenance.filter(m => m.machine_id !== id);
  saveDB();
  toast('Machine deleted.');
  renderMachines();
}

/* =========================================================
   INVENTORY
   ========================================================= */
function renderInventory() {
  const c = $('pageContainer');
  c.innerHTML = `
    <div class="page-head">
      <div><h2>Inventory</h2><p>Monitor consumables and supplies with low-stock alerts.</p></div>
      <button class="btn btn-primary fw-semibold" onclick="openInventoryForm()">
        <i class="bi bi-plus-lg me-1"></i>Add Item
      </button>
    </div>
    <div class="card">
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th>Item</th><th>Category</th><th class="text-end">Stock</th>
                <th class="text-end">Threshold</th><th>Status</th>
                <th class="text-end">Quick Adjust</th><th class="text-end">Actions</th></tr>
          </thead>
          <tbody id="invBody"></tbody>
        </table>
      </div>
    </div>`;

  const tb = $('invBody');
  if (!DB.inventory.length) {
    tb.innerHTML = `<tr><td colspan="7" class="empty"><i class="bi bi-box"></i>No inventory items</td></tr>`;
    return;
  }

  tb.innerHTML = DB.inventory.map(i => {
    const status = Number(i.stock) <= 0 ? 'out' : (Number(i.stock) <= Number(i.threshold) ? 'low' : 'ok');
    const label  = { ok:'In Stock', low:'Low Stock', out:'Out of Stock' }[status];
    return `<tr>
      <td class="fw-semibold">${esc(i.name)}</td>
      <td class="small text-body-secondary">${esc(i.category)}</td>
      <td class="text-end fw-semibold">${num(i.stock)} <span class="text-body-secondary small">${esc(i.unit)}</span></td>
      <td class="text-end text-body-secondary">${num(i.threshold)} ${esc(i.unit)}</td>
      <td><span class="pill pill-${status}">${label}</span></td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-secondary" onclick="adjustStock(${i.id},-1)">−1</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="adjustStock(${i.id},1)">+1</button>
      </td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-secondary" onclick='openInventoryForm(${JSON.stringify(i).replace(/'/g,"&#39;")})'>
          <i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteInventory(${i.id})">
          <i class="bi bi-trash"></i></button>
      </td>
    </tr>`;
  }).join('');
}

function openInventoryForm(data = null) {
  $('mdlSmTitle').textContent = data ? 'Edit Inventory Item' : 'Add Inventory Item';
  $('mdlSmBody').innerHTML = `
    <input type="hidden" id="ifId" value="${data ? data.id : ''}">
    <label class="form-label">Item Name *</label>
    <input type="text" class="form-control mb-3" id="ifName" value="${data ? esc(data.name) : ''}" maxlength="120">
    <div class="row g-3 mb-3">
      <div class="col-6">
        <label class="form-label">Category</label>
        <select class="form-select" id="ifCategory">
          ${['Consumables','Supplies','Equipment'].map(cat =>
            `<option value="${cat}" ${data && data.category === cat ? 'selected' : ''}>${cat}</option>`).join('')}
        </select>
      </div>
      <div class="col-6">
        <label class="form-label">Unit</label>
        <input type="text" class="form-control" id="ifUnit" value="${data ? esc(data.unit) : 'units'}" maxlength="20">
      </div>
    </div>
    <div class="row g-3">
      <div class="col-6">
        <label class="form-label">Current Stock</label>
        <input type="number" class="form-control" id="ifStock" value="${data ? data.stock : 0}" step="0.01" min="0">
      </div>
      <div class="col-6">
        <label class="form-label">Low Stock Threshold</label>
        <input type="number" class="form-control" id="ifThreshold" value="${data ? data.threshold : 0}" step="0.01" min="0">
      </div>
    </div>`;

  $('mdlSmFooter').innerHTML = `
    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button class="btn btn-primary" id="ifBtn"><i class="bi bi-check2 me-1"></i>Save</button>`;

  new bootstrap.Modal('#mdlSm').show();

  $('ifBtn').onclick = () => {
    const name = $('ifName').value.trim();
    if (!name) { toast('Item name is required.', 'danger'); return; }

    const id = Number($('ifId').value);
    const payload = {
      name,
      category:  $('ifCategory').value,
      stock:     parseFloat($('ifStock').value) || 0,
      unit:      $('ifUnit').value.trim() || 'units',
      threshold: parseFloat($('ifThreshold').value) || 0
    };

    if (id > 0) {
      const it = DB.inventory.find(x => x.id === id);
      if (!it) return;
      Object.assign(it, payload);
      toast('Item updated.');
    } else {
      DB.inventory.push({ id: uid('inventory'), ...payload });
      toast('Item added.');
    }
    saveDB();
    bootstrap.Modal.getInstance($('mdlSm')).hide();
    renderInventory();
  };
}

function adjustStock(id, delta) {
  const it = DB.inventory.find(x => x.id === id);
  if (!it) return;
  it.stock = Math.max(0, Number(it.stock) + delta);
  saveDB();
  renderInventory();
}

function deleteInventory(id) {
  if (!confirm('Delete this inventory item?')) return;
  DB.inventory = DB.inventory.filter(i => i.id !== id);
  saveDB();
  toast('Item deleted.');
  renderInventory();
}

/* =========================================================
   MAINTENANCE
   ========================================================= */
function renderMaintenance() {
  const c = $('pageContainer');
  c.innerHTML = `
    <div class="page-head">
      <div><h2>Maintenance Schedule</h2><p>Plan tub cleaning (Mondays &amp; Thursdays) and machine servicing.</p></div>
      <button class="btn btn-primary fw-semibold" onclick="openMaintenanceForm()">
        <i class="bi bi-plus-lg me-1"></i>Schedule Maintenance
      </button>
    </div>
    <div class="card">
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th>Machine</th><th>Type</th><th>Scheduled</th><th>Notes</th>
                <th>Status</th><th class="text-end">Actions</th></tr>
          </thead>
          <tbody id="mtBody"></tbody>
        </table>
      </div>
    </div>`;

  const tb = $('mtBody');
  if (!DB.maintenance.length) {
    tb.innerHTML = `<tr><td colspan="6" class="empty"><i class="bi bi-tools"></i>No scheduled maintenance</td></tr>`;
    return;
  }

  tb.innerHTML = DB.maintenance.map(m => {
    const mac = findMachine(m.machine_id);
    return `<tr>
      <td><span class="font-monospace fw-bold">${esc(mac ? mac.code : '—')}</span> · ${esc(mac ? mac.name : '—')}</td>
      <td><span class="pill pill-washing">${esc(m.type)}</span></td>
      <td class="small">${fmtDate(m.scheduled_date)}</td>
      <td class="small text-body-secondary">${esc(m.notes || '—')}</td>
      <td>
        ${m.done
          ? '<span class="pill pill-ok"><i class="bi bi-check2"></i>Done</span>'
          : '<span class="pill pill-pending"><i class="bi bi-clock"></i>Pending</span>'}
      </td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm ${m.done ? 'btn-outline-secondary' : 'btn-outline-success'}"
          onclick="toggleMaintenance(${m.id})">
          <i class="bi bi-${m.done ? 'arrow-counterclockwise' : 'check2'}"></i>
          ${m.done ? 'Reopen' : 'Complete'}
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteMaintenance(${m.id})">
          <i class="bi bi-trash"></i></button>
      </td>
    </tr>`;
  }).join('');
}

function openMaintenanceForm() {
  if (!DB.machines.length) { toast('Add a machine first.', 'danger'); return; }

  $('mdlSmTitle').textContent = 'Schedule Maintenance';
  $('mdlSmBody').innerHTML = `
    <label class="form-label">Machine *</label>
    <select class="form-select mb-3" id="mtMachine">
      <option value="">— Select machine —</option>
      ${DB.machines.map(x => `<option value="${x.id}">${esc(x.code)} · ${esc(x.name)}</option>`).join('')}
    </select>
    <label class="form-label">Maintenance Type</label>
    <select class="form-select mb-3" id="mtType">
      <option>Tub Clean</option>
      <option>Filter Clean</option>
      <option>Servicing</option>
      <option>Repair</option>
    </select>
    <label class="form-label">Scheduled Date *</label>
    <input type="date" class="form-control mb-3" id="mtDate" value="${today()}">
    <label class="form-label">Notes</label>
    <input type="text" class="form-control" id="mtNotes" maxlength="255">`;

  $('mdlSmFooter').innerHTML = `
    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button class="btn btn-primary" id="mtBtn"><i class="bi bi-check2 me-1"></i>Schedule</button>`;

  new bootstrap.Modal('#mdlSm').show();

  $('mtBtn').onclick = () => {
    const mid = Number($('mtMachine').value);
    const date = $('mtDate').value;
    if (!mid || !date) { toast('Select a machine and date.', 'danger'); return; }

    DB.maintenance.push({
      id: uid('maintenance'),
      machine_id: mid,
      scheduled_date: date,
      type: $('mtType').value,
      notes: $('mtNotes').value.trim(),
      done: 0
    });
    saveDB();
    bootstrap.Modal.getInstance($('mdlSm')).hide();
    toast('Maintenance scheduled.');
    renderMaintenance();
  };
}

function toggleMaintenance(id) {
  const m = DB.maintenance.find(x => x.id === id);
  if (!m) return;
  m.done = m.done ? 0 : 1;
  const mac = findMachine(m.machine_id);
  if (mac) mac.status = m.done ? 'available' : 'tub_clean';
  saveDB();
  toast(m.done ? 'Maintenance completed.' : 'Machine set to maintenance.');
  renderMaintenance();
}

function deleteMaintenance(id) {
  if (!confirm('Remove this maintenance schedule?')) return;
  DB.maintenance = DB.maintenance.filter(m => m.id !== id);
  saveDB();
  toast('Schedule removed.');
  renderMaintenance();
}

/* =========================================================
   REPORTS
   ========================================================= */
function renderReports() {
  const c = $('pageContainer');
  const days = 14;
  const cutoff = Date.now() - days * 86400000;

  const dailyMap = {};
  for (let i = days - 1; i >= 0; i--) {
    const d = new Date(); d.setDate(d.getDate() - i);
    dailyMap[d.toISOString().slice(0, 10)] = 0;
  }
  DB.payments.forEach(p => {
    const day = (p.created_at || '').slice(0, 10);
    if (day in dailyMap && new Date(p.created_at).getTime() >= cutoff) {
      dailyMap[day] += Number(p.amount);
    }
  });
  const daily = Object.entries(dailyMap).map(([d, revenue]) => ({ d, revenue }));
  const maxRev = Math.max(1, ...daily.map(x => x.revenue));

  const statusCounts = {};
  DB.orders.forEach(o => { statusCounts[o.status] = (statusCounts[o.status] || 0) + 1; });

  const serviceStats = DB.services.map(s => {
    let cnt = 0, revenue = 0;
    DB.orderItems.forEach(it => {
      if (it.service_id === s.id) { cnt++; revenue += Number(it.subtotal); }
    });
    return { name: s.name, cnt, revenue };
  }).filter(x => x.cnt > 0).sort((a, b) => b.revenue - a.revenue);

  const totalRevenue = DB.payments.reduce((s, p) => s + Number(p.amount), 0);
  const outstanding = DB.orders.reduce((s, o) => s + Math.max(0, Number(o.total) - Number(o.amount_paid)), 0);

  c.innerHTML = `
    <div class="page-head">
      <div><h2>Reports &amp; Analytics</h2><p>Overview of orders, revenue and service performance.</p></div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="card"><div class="card-body">
          <div class="text-body-secondary small">Total Orders</div>
          <div class="h3 fw-bold mb-0">${num(DB.orders.length)}</div>
        </div></div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="card"><div class="card-body">
          <div class="text-body-secondary small">Total Revenue</div>
          <div class="h3 fw-bold mb-0 text-success">${peso(totalRevenue)}</div>
        </div></div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="card"><div class="card-body">
          <div class="text-body-secondary small">Outstanding Balance</div>
          <div class="h3 fw-bold mb-0 text-danger">${peso(outstanding)}</div>
        </div></div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="card"><div class="card-body">
          <div class="text-body-secondary small">Customers</div>
          <div class="h3 fw-bold mb-0">${num(DB.customers.length)}</div>
        </div></div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-7">
        <div class="card mb-3">
          <div class="card-header"><i class="bi bi-graph-up me-1"></i>Daily Revenue (last ${days} days)</div>
          <div class="card-body">
            <div class="d-flex align-items-end gap-1" style="height:180px">
              ${daily.map(x => {
                const h = (x.revenue / maxRev) * 100;
                return `<div class="flex-fill d-flex flex-column align-items-center justify-content-end" style="min-width:0">
                  <div title="${fmtDate(x.d)} · ${peso(x.revenue)}"
                    style="width:100%;height:${h}%;min-height:3px;background:linear-gradient(180deg,#3b82f6,#1d4ed8);border-radius:.35rem .35rem 0 0"></div>
                </div>`;
              }).join('')}
            </div>
            <div class="d-flex justify-content-between mt-2 small text-body-secondary">
              <span>${fmtDate(daily[0].d)}</span>
              <span>${fmtDate(daily[daily.length-1].d)}</span>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><i class="bi bi-list-check me-1"></i>Orders by Status</div>
          <div class="card-body">
            ${Object.entries(ORDER_STATUS).map(([k,v]) => {
              const count = statusCounts[k] || 0;
              return `<div class="d-flex justify-content-between align-items-center mb-2">
                <span class="pill pill-${v.cls}"><i class="bi bi-${v.icon}"></i>${v.label}</span>
                <span class="fw-bold">${num(count)}</span>
              </div>`;
            }).join('')}
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card">
          <div class="card-header"><i class="bi bi-trophy me-1"></i>Service Performance</div>
          <div class="card-body p-0">
            <table class="table mb-0">
              <thead><tr><th>Service</th><th class="text-end">Orders</th><th class="text-end">Revenue</th></tr></thead>
              <tbody>
                ${serviceStats.length ? serviceStats.map(s => `<tr>
                  <td class="fw-semibold">${esc(s.name)}</td>
                  <td class="text-end">${num(s.cnt)}</td>
                  <td class="text-end fw-semibold">${peso(s.revenue)}</td>
                </tr>`).join('') : `<tr><td colspan="3" class="empty"><i class="bi bi-inbox"></i>No data</td></tr>`}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>`;
}

/* =========================================================
   BADGES & BOOT
   ========================================================= */
function refreshBadges() {
  const activeOrders = DB.orders.filter(o => ['pending','washing','drying'].includes(o.status)).length;
  const pendingPay   = DB.orders.filter(o => o.payment_status !== 'paid' && o.status !== 'cancelled').length;
  const unclaimed    = DB.orders.filter(o => o.status === 'ready').length;

  const badge = $('navOrderBadge');
  badge.textContent = activeOrders;
  badge.style.display = activeOrders > 0 ? '' : 'none';

  const alerts = $('alertCount');
  const total = pendingPay + unclaimed;
  alerts.textContent = total;
  alerts.style.display = total > 0 ? '' : 'none';
}

$('alertBtn').onclick = () => navigate('orders');

loadDB();
navigate('dashboard');
refreshBadges();
setInterval(refreshBadges, 30000);
</script>
</body>
</html>