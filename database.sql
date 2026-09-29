-- =========================================================
-- Hardware Materials Database
-- Import this file sa phpMyAdmin o via command line:
--   mysql -u root -p < database.sql
-- =========================================================

CREATE DATABASE IF NOT EXISTS hardware_materials
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE hardware_materials;

DROP TABLE IF EXISTS items;

CREATE TABLE items (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    item_number   INT NOT NULL,                 -- original per-category ID (1-5 per category)
    category      VARCHAR(50)  NOT NULL,         -- slug: hand-tools, power-tools, etc.
    category_name VARCHAR(100) NOT NULL,         -- display name: "Hand Tools"
    name          VARCHAR(150) NOT NULL,
    description   TEXT,
    price         DECIMAL(10,2) NOT NULL,
    icon          VARCHAR(100),                  -- font-awesome icon class fallback
    color         VARCHAR(150),                  -- tailwind gradient classes fallback
    image         VARCHAR(255),                  -- path to photo, e.g. images/1.webp
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO items (item_number, category, category_name, name, description, price, icon, color, image) VALUES
-- Hand Tools
(1, 'hand-tools', 'Hand Tools', 'Claw Hammer', 'Hand tool used for driving and removing nails.', 350.00, 'fa-hammer', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/1.webp'),
(2, 'hand-tools', 'Hand Tools', 'Flat Screwdriver', 'Screwdriver designed for slotted or flat-head screws.', 120.00, 'fa-screwdriver', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/2.jpg'),
(3, 'hand-tools', 'Hand Tools', 'Phillips Screwdriver', 'Screwdriver designed for cross-head screws.', 150.00, 'fa-screwdriver', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/3.jpg'),
(4, 'hand-tools', 'Hand Tools', 'Combination Pliers', 'Hand tool used for gripping, bending, and cutting wires.', 280.00, 'fa-wrench', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/4.jpg'),
(5, 'hand-tools', 'Hand Tools', 'Adjustable Wrench', 'Wrench with an adjustable jaw for different nut sizes.', 320.00, 'fa-wrench', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/5.jpg'),

-- Power Tools
(1, 'power-tools', 'Power Tools', 'Electric Drill', 'Power tool used for drilling holes in wood, metal, and other materials.', 1800.00, 'fa-bolt-lightning', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/6.webp'),
(2, 'power-tools', 'Power Tools', 'Angle Grinder', 'Power tool used for cutting, grinding, and polishing materials.', 2200.00, 'fa-compact-disc', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/7.avif'),
(3, 'power-tools', 'Power Tools', 'Circular Saw', 'Power saw used for making straight cuts in wood and boards.', 2800.00, 'fa-gear', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/8.webp'),
(4, 'power-tools', 'Power Tools', 'Jigsaw', 'Power saw used for curved and detailed cuts.', 2000.00, 'fa-saw-blade', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/9.webp'),
(5, 'power-tools', 'Power Tools', 'Heat Gun', 'Tool that produces hot air for heating, stripping, and other applications.', 1500.00, 'fa-fire-flame-curved', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/10.webp'),

-- Plumbing Materials
(1, 'plumbing', 'Plumbing Materials', 'PVC Pipe', 'Plastic pipe commonly used for water and drainage systems.', 180.00, 'fa-faucet', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/11.webp'),
(2, 'plumbing', 'Plumbing Materials', 'PVC Elbow', 'Fitting used to change the direction of a PVC pipe.', 45.00, 'fa-turn-up', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/12.jpg'),
(3, 'plumbing', 'Plumbing Materials', 'PVC Tee', 'Pipe fitting used to connect three pipe sections.', 55.00, 'fa-diagram-project', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/13.jpg'),
(4, 'plumbing', 'Plumbing Materials', 'Pipe Wrench', 'Wrench designed for gripping and turning pipes and fittings.', 450.00, 'fa-wrench', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/14.webp'),
(5, 'plumbing', 'Plumbing Materials', 'Teflon Tape', 'Thread-sealing tape used to help prevent leaks in pipe connections.', 35.00, 'fa-tape', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/15.jpg'),

-- Electrical Materials
(1, 'electrical', 'Electrical Materials', 'Electrical Wire', 'Conductive wire used for electrical connections and installations.', 850.00, 'fa-plug', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/16.jpg'),
(2, 'electrical', 'Electrical Materials', 'Circuit Breaker', 'Safety device that interrupts electrical current during overloads or faults.', 450.00, 'fa-shield-halved', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/17.png'),
(3, 'electrical', 'Electrical Materials', 'Electrical Outlet', 'Device that provides a connection point for electrical appliances.', 120.00, 'fa-plug-circle-bolt', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/18.webp'),
(4, 'electrical', 'Electrical Materials', 'Light Switch', 'Electrical device used to turn a light or circuit on and off.', 90.00, 'fa-toggle-on', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/19.jpg'),
(5, 'electrical', 'Electrical Materials', 'Junction Box', 'Enclosure used to protect and organize electrical wire connections.', 75.00, 'fa-box', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/20.jpg'),

-- Construction Materials
(1, 'construction', 'Construction Materials', 'Common Nail', 'Metal fastener used to join wood and other construction materials.', 90.00, 'fa-hashtag', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/21.jpg'),
(2, 'construction', 'Construction Materials', 'Wood Screw', 'Threaded fastener commonly used for securing wood materials.', 140.00, 'fa-screwdriver', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/22.jpg'),
(3, 'construction', 'Construction Materials', 'Cement', 'Binding material used in concrete, mortar, and other construction work.', 280.00, 'fa-cubes', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/23.jpg'),
(4, 'construction', 'Construction Materials', 'GI Sheet', 'Galvanized metal sheet used for roofing and various construction applications.', 650.00, 'fa-sheet-plastic', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/24.jpg'),
(5, 'construction', 'Construction Materials', 'Paint Roller', 'Tool used to apply paint evenly on walls and other large surfaces.', 180.00, 'fa-paint-roller', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/25.webp');
