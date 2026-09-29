<?php
require_once __DIR__ . '/config.php';

// Kunin ang lahat ng items mula sa database
$stmt = $pdo->query(
    "SELECT id, item_number, category, category_name, name, description, price, icon, color, image
     FROM items
     ORDER BY category, item_number"
);
$rows = $stmt->fetchAll();

$items = array_map(function ($row) {
    return [
        'dbId'         => (int) $row['id'],
        'id'           => (int) $row['item_number'],
        'category'     => $row['category'],
        'categoryName' => $row['category_name'],
        'name'         => $row['name'],
        'desc'         => $row['description'],
        'price'        => (float) $row['price'],
        'icon'         => $row['icon'],
        'color'        => $row['color'],
        'img'          => $row['image'],
    ];
}, $rows);

// Bilangin ang items per category (para sa sidebar badges)
$counts = ['all' => count($items)];
foreach ($items as $item) {
    $counts[$item['category']] = ($counts[$item['category']] ?? 0) + 1;
}

// I-convert ang PHP array papuntang JSON, para magamit ng JavaScript sa client side
$itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hardware Materials - Jhon Philip Cangrijelia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#fff7ed', 100: '#ffedd5', 500: '#f97316', 600: '#ea580c', 700: '#c2410c', 900: '#7c2d12' }
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800 dark:text-slate-100 dark:bg-slate-900 transition-colors duration-200">

    <!-- Top Header -->
    <header class="bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-30 shadow-md">
        <div class="px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button id="mobile-menu-btn" class="lg:hidden text-slate-300 hover:text-white p-1 rounded-md focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <div class="bg-brand-500 p-2 rounded-lg text-white shadow-sm flex items-center justify-center">
                    <i class="fa-solid fa-toolbox text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold tracking-tight text-white leading-tight">HARDWARE MATERIALS</h1>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button id="add-item-btn" class="hidden sm:flex items-center gap-2 bg-brand-500 hover:bg-brand-600 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add Item</span>
                </button>
                <button id="theme-toggle" class="p-2 text-slate-400 hover:text-slate-200 hover:bg-slate-800 rounded-lg transition-colors" title="Toggle Dark/Light Mode">
                    <i id="theme-toggle-dark-icon" class="fa-solid fa-moon text-lg hidden"></i>
                    <i id="theme-toggle-light-icon" class="fa-solid fa-sun text-lg"></i>
                </button>
                <div class="hidden sm:flex items-center space-x-2 bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-700 text-xs text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>MySQL Connected</span>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <div id="mobile-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700/60 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col justify-between">
            <div class="p-4 overflow-y-auto">
                <div class="mb-4 px-2 pt-2">
                    <h2 class="text-xs font-semibold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Categories</h2>
                </div>

                <nav class="space-y-1" id="category-nav">
                    <button data-category="all" class="nav-btn w-full flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all group bg-brand-500 text-white shadow-sm shadow-brand-500/30">
                        <i class="fa-solid fa-border-all w-6 text-center text-lg mr-2"></i>
                        <span class="flex-1 text-left">All Categories</span>
                        <span class="bg-white/20 text-white px-2 py-0.5 rounded-full text-xs font-semibold" id="count-all"><?= $counts['all'] ?? 0 ?></span>
                    </button>

                    <button data-category="hand-tools" class="nav-btn w-full flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all group">
                        <i class="fa-solid fa-hammer w-6 text-center text-lg mr-2 text-slate-400 group-hover:text-brand-500 transition-colors"></i>
                        <span class="flex-1 text-left">Hand Tools</span>
                        <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-full text-xs" id="count-hand-tools"><?= $counts['hand-tools'] ?? 0 ?></span>
                    </button>

                    <button data-category="power-tools" class="nav-btn w-full flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all group">
                        <i class="fa-solid fa-bolt w-6 text-center text-lg mr-2 text-slate-400 group-hover:text-brand-500 transition-colors"></i>
                        <span class="flex-1 text-left">Power Tools</span>
                        <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-full text-xs" id="count-power-tools"><?= $counts['power-tools'] ?? 0 ?></span>
                    </button>

                    <button data-category="plumbing" class="nav-btn w-full flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all group">
                        <i class="fa-solid fa-faucet-drip w-6 text-center text-lg mr-2 text-slate-400 group-hover:text-brand-500 transition-colors"></i>
                        <span class="flex-1 text-left">Plumbing Materials</span>
                        <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-full text-xs" id="count-plumbing"><?= $counts['plumbing'] ?? 0 ?></span>
                    </button>

                    <button data-category="electrical" class="nav-btn w-full flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all group">
                        <i class="fa-solid fa-plug w-6 text-center text-lg mr-2 text-slate-400 group-hover:text-brand-500 transition-colors"></i>
                        <span class="flex-1 text-left">Electrical Materials</span>
                        <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-full text-xs" id="count-electrical"><?= $counts['electrical'] ?? 0 ?></span>
                    </button>

                    <button data-category="construction" class="nav-btn w-full flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all group">
                        <i class="fa-solid fa-trowel-bricks w-6 text-center text-lg mr-2 text-slate-400 group-hover:text-brand-500 transition-colors"></i>
                        <span class="flex-1 text-left">Construction Materials</span>
                        <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-full text-xs" id="count-construction"><?= $counts['construction'] ?? 0 ?></span>
                    </button>
                </nav>

                <button id="add-item-btn-mobile" class="sm:hidden mt-4 w-full flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add Item</span>
                </button>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-slate-900 text-brand-500 font-bold flex items-center justify-center border border-slate-700 text-sm shadow">JPC</div>
                    <div class="overflow-hidden">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Hardware Store Catalog (MySQL)</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 id="selected-category-title" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                        <span>All Hardware Materials</span>
                    </h2>
                    <p id="selected-category-desc" class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Browse and filter through hardware items, prices, and specifications.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 sm:w-64">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="search-input" placeholder="Search item or ID..." class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 dark:text-white shadow-sm transition-all">
                    </div>

                    <div class="flex bg-white dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <button id="view-grid-btn" class="px-3 py-1.5 rounded-lg text-sm font-medium bg-brand-500 text-white shadow-sm transition-all flex items-center gap-1.5" title="Grid View">
                            <i class="fa-solid fa-border-all"></i>
                            <span class="hidden sm:inline">Grid</span>
                        </button>
                        <button id="view-table-btn" class="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all flex items-center gap-1.5" title="Table View">
                            <i class="fa-solid fa-list"></i>
                            <span class="hidden sm:inline">Table</span>
                        </button>
                    </div>
                </div>
            </div>

            <div id="grid-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5"></div>

            <div id="table-container" class="hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th class="py-3.5 px-4 w-20 text-center">ID</th>
                                <th class="py-3.5 px-4 w-24">Item</th>
                                <th class="py-3.5 px-4">Name & Category</th>
                                <th class="py-3.5 px-4">Description</th>
                                <th class="py-3.5 px-4 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="divide-y divide-slate-100 dark:divide-slate-700/60 text-sm"></tbody>
                    </table>
                </div>
            </div>

            <div id="empty-state" class="hidden py-16 text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">No items found</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Try adjusting your search query or selecting a different category.</p>
                <button id="reset-search-btn" class="mt-4 px-4 py-2 bg-brand-500 text-white rounded-xl text-xs font-semibold shadow hover:bg-brand-600 transition">Reset Search</button>
            </div>
        </main>
    </div>

    <!-- Add Item Modal -->
    <div id="add-item-modal" class="hidden fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6 relative">
            <button id="close-modal-btn" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add New Item</h3>

            <form id="add-item-form" class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Category</label>
                    <select name="category" id="form-category" required class="w-full mt-1 px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 dark:text-white">
                        <option value="hand-tools" data-name="Hand Tools">Hand Tools</option>
                        <option value="power-tools" data-name="Power Tools">Power Tools</option>
                        <option value="plumbing" data-name="Plumbing Materials">Plumbing Materials</option>
                        <option value="electrical" data-name="Electrical Materials">Electrical Materials</option>
                        <option value="construction" data-name="Construction Materials">Construction Materials</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Item Name</label>
                    <input type="text" name="name" required class="w-full mt-1 px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 dark:text-white" placeholder="e.g. Steel Tape Measure">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Description</label>
                    <textarea name="desc" rows="2" class="w-full mt-1 px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 dark:text-white" placeholder="Short description"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Price (₱)</label>
                        <input type="number" step="0.01" name="price" required class="w-full mt-1 px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 dark:text-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Image path</label>
                        <input type="text" name="img" class="w-full mt-1 px-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 dark:text-white" placeholder="images/26.jpg">
                    </div>
                </div>

                <p id="form-error" class="hidden text-xs text-red-500 font-medium"></p>

                <button type="submit" class="w-full mt-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                    Save Item
                </button>
            </form>
        </div>
    </div>

    <script>
        // =========================================================
        // Ang data na ito ay galing na sa MySQL database (via PHP),
        // hindi na ito hardcoded na array. I-check ang index.php
        // para makita kung paano kinukuha ang datos gamit ang PDO.
        // =========================================================
        let hardwareData = <?= $itemsJson ?>;

        // Application State
        let currentCategory = 'all';
        let viewMode = 'grid';
        let searchQuery = '';

        const gridContainer = document.getElementById('grid-container');
        const tableContainer = document.getElementById('table-container');
        const tableBody = document.getElementById('table-body');
        const emptyState = document.getElementById('empty-state');
        const searchInput = document.getElementById('search-input');
        const resetSearchBtn = document.getElementById('reset-search-btn');
        const titleElement = document.getElementById('selected-category-title');
        const descElement = document.getElementById('selected-category-desc');
        const viewGridBtn = document.getElementById('view-grid-btn');
        const viewTableBtn = document.getElementById('view-table-btn');

        const sidebar = document.getElementById('sidebar');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileOverlay = document.getElementById('mobile-overlay');

        const categoryDescriptions = {
            'all': 'Browse and filter through hardware items, prices, and specifications.',
            'hand-tools': 'Manual tools used for fastening, shaping, assembly, and general hardware work.',
            'power-tools': 'High-performance motor-driven equipment for heavy woodworking and metal tasks.',
            'plumbing': 'Pipes, fittings, and tools designed for water distribution and drainage systems.',
            'electrical': 'Wiring, circuit protection, outlets, and junction accessories for standard wiring.',
            'construction': 'Essential building materials, fasteners, cement, and finishing tools.'
        };

        const categoryTitles = {
            'all': 'All Hardware Materials',
            'hand-tools': 'Hand Tools',
            'power-tools': 'Power Tools',
            'plumbing': 'Plumbing Materials',
            'electrical': 'Electrical Materials',
            'construction': 'Construction Materials'
        };

        function renderItems() {
            const filteredData = hardwareData.filter(item => {
                const matchesCategory = currentCategory === 'all' || item.category === currentCategory;
                const matchesSearch = item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                      (item.desc || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
                                      `id ${item.id}`.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                      `id:${item.id}`.toLowerCase().includes(searchQuery.toLowerCase());
                return matchesCategory && matchesSearch;
            });

            if (filteredData.length === 0) {
                gridContainer.classList.add('hidden');
                tableContainer.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            } else {
                emptyState.classList.add('hidden');
            }

            if (viewMode === 'grid') {
                gridContainer.classList.remove('hidden');
                tableContainer.classList.add('hidden');
                renderGrid(filteredData);
            } else {
                gridContainer.classList.add('hidden');
                tableContainer.classList.remove('hidden');
                renderTable(filteredData);
            }
        }

        function renderGrid(items) {
            gridContainer.innerHTML = items.map(item => `
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700/80 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                    <div>
                        <div class="relative h-36 w-full rounded-xl bg-gradient-to-br ${item.color} flex items-center justify-center mb-4 border border-slate-100 dark:border-slate-700/50 overflow-hidden group-hover:scale-[1.02] transition-transform duration-200">
                            ${item.img ? `
                            <img src="${item.img}" alt="${item.name}" loading="lazy" class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            ` : ''}
                            <div class="${item.img ? 'hidden' : 'flex'} absolute inset-0 items-center justify-center">
                                <i class="fa-solid ${item.icon} text-4xl opacity-80"></i>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase">ID: ${item.id}</span>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                ${item.categoryName}
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-brand-500 transition-colors">
                            ${item.name}
                        </h3>

                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2">
                            ${item.desc || ''}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-medium">Retail Price</span>
                        <span class="text-lg font-extrabold text-brand-600 dark:text-brand-500">₱${Number(item.price).toLocaleString()}</span>
                    </div>
                </div>
            `).join('');
        }

        function renderTable(items) {
            tableBody.innerHTML = items.map(item => `
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="py-3.5 px-4 text-center font-mono text-xs font-bold text-slate-400 dark:text-slate-500">
                        #${item.id}
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="relative w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 overflow-hidden">
                            ${item.img ? `
                            <img src="${item.img}" alt="${item.name}" loading="lazy" class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            ` : ''}
                            <div class="${item.img ? 'hidden' : 'flex'} absolute inset-0 items-center justify-center">
                                <i class="fa-solid ${item.icon}"></i>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="font-semibold text-slate-900 dark:text-white">${item.name}</div>
                        <div class="text-xs text-slate-400">${item.categoryName}</div>
                    </td>
                    <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300 max-w-xs">
                        ${item.desc || ''}
                    </td>
                    <td class="py-3.5 px-4 text-right font-bold text-brand-600 dark:text-brand-500 text-base">
                        ₱${Number(item.price).toLocaleString()}
                    </td>
                </tr>
            `).join('');
        }

        const categoryButtons = document.querySelectorAll('.nav-btn');
        categoryButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                categoryButtons.forEach(b => {
                    b.classList.remove('bg-brand-500', 'text-white', 'shadow-sm', 'shadow-brand-500/30');
                    b.classList.add('text-slate-600', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
                    const badge = b.querySelector('span:last-child');
                    if (badge) {
                        badge.classList.remove('bg-white/20', 'text-white');
                        badge.classList.add('bg-slate-100', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
                    }
                });

                btn.classList.add('bg-brand-500', 'text-white', 'shadow-sm', 'shadow-brand-500/30');
                btn.classList.remove('text-slate-600', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700/50');
                const activeBadge = btn.querySelector('span:last-child');
                if (activeBadge) {
                    activeBadge.classList.remove('bg-slate-100', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
                    activeBadge.classList.add('bg-white/20', 'text-white');
                }

                currentCategory = btn.getAttribute('data-category');
                titleElement.textContent = categoryTitles[currentCategory];
                descElement.textContent = categoryDescriptions[currentCategory];
                renderItems();
                closeMobileSidebar();
            });
        });

        viewGridBtn.addEventListener('click', () => {
            viewMode = 'grid';
            viewGridBtn.className = 'px-3 py-1.5 rounded-lg text-sm font-medium bg-brand-500 text-white shadow-sm transition-all flex items-center gap-1.5';
            viewTableBtn.className = 'px-3 py-1.5 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all flex items-center gap-1.5';
            renderItems();
        });

        viewTableBtn.addEventListener('click', () => {
            viewMode = 'table';
            viewTableBtn.className = 'px-3 py-1.5 rounded-lg text-sm font-medium bg-brand-500 text-white shadow-sm transition-all flex items-center gap-1.5';
            viewGridBtn.className = 'px-3 py-1.5 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all flex items-center gap-1.5';
            renderItems();
        });

        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value;
            renderItems();
        });

        resetSearchBtn.addEventListener('click', () => {
            searchQuery = '';
            searchInput.value = '';
            renderItems();
        });

        function openMobileSidebar() {
            sidebar.classList.remove('-translate-x-full');
            mobileOverlay.classList.remove('hidden');
        }

        function closeMobileSidebar() {
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        }

        mobileMenuBtn.addEventListener('click', openMobileSidebar);
        mobileOverlay.addEventListener('click', closeMobileSidebar);

        // Dark/Light Mode Switcher
        const themeToggleBtn = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            lightIcon.classList.remove('hidden');
            darkIcon.classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            darkIcon.classList.remove('hidden');
            lightIcon.classList.add('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            darkIcon.classList.toggle('hidden');
            lightIcon.classList.toggle('hidden');
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        });

        // =========================================================
        // Add Item Modal Logic — nagpapadala ng bagong item sa
        // api/add_item.php (INSERT sa MySQL), pagkatapos kinukuha
        // muli ang buong listahan mula sa api/get_items.php
        // =========================================================
        const addItemModal = document.getElementById('add-item-modal');
        const addItemForm = document.getElementById('add-item-form');
        const formError = document.getElementById('form-error');
        const categorySelect = document.getElementById('form-category');

        function openAddItemModal() {
            addItemModal.classList.remove('hidden');
        }
        function closeAddItemModal() {
            addItemModal.classList.add('hidden');
            addItemForm.reset();
            formError.classList.add('hidden');
        }

        document.getElementById('add-item-btn').addEventListener('click', openAddItemModal);
        document.getElementById('add-item-btn-mobile').addEventListener('click', () => {
            openAddItemModal();
            closeMobileSidebar();
        });
        document.getElementById('close-modal-btn').addEventListener('click', closeAddItemModal);
        addItemModal.addEventListener('click', (e) => {
            if (e.target === addItemModal) closeAddItemModal();
        });

        addItemForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            formError.classList.add('hidden');

            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const payload = {
                category: categorySelect.value,
                categoryName: selectedOption.getAttribute('data-name'),
                name: addItemForm.name.value.trim(),
                desc: addItemForm.desc.value.trim(),
                price: addItemForm.price.value,
                img: addItemForm.img.value.trim(),
                icon: 'fa-box',
                color: 'from-slate-500/20 to-slate-400/20 text-slate-600'
            };

            try {
                const res = await fetch('api/add_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (!result.success) {
                    formError.textContent = result.message || 'Something went wrong.';
                    formError.classList.remove('hidden');
                    return;
                }

                // I-refresh ang listahan mula sa database
                const itemsRes = await fetch('api/get_items.php');
                hardwareData = await itemsRes.json();
                renderItems();
                closeAddItemModal();
            } catch (err) {
                formError.textContent = 'Network error: ' + err.message;
                formError.classList.remove('hidden');
            }
        });

        // Initial render (gamit ang data na galing sa PHP/MySQL)
        renderItems();
    </script>
</body>
</html>
