-- Tabel Relasi Role <-> Menu
CREATE TABLE IF NOT EXISTS role_menus (
    role_id INT NOT NULL,
    menu_id INT NOT NULL,
    PRIMARY KEY (role_id, menu_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);
