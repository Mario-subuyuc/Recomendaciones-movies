-- MySQL / MariaDB, esquema equivalente a 2026_10_06_000001_create_chat_tables.
-- Requiere users.id BIGINT UNSIGNED (tabla existente de Laravel).
-- Referencia para entrega académica. Usar migrations O este script, no ambos.
-- No contiene DROP, TRUNCATE, credenciales ni modificaciones a los catálogos.
CREATE TABLE conversaciones (
    id_conversacion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario BIGINT UNSIGNED NOT NULL,
    categoria VARCHAR(20) NOT NULL,
    consulta_uuid CHAR(36) NOT NULL,
    fecha_creacion TIMESTAMP NOT NULL,
    UNIQUE KEY conversaciones_consulta_uuid_unique (consulta_uuid),
    KEY conversaciones_id_usuario_fecha_creacion_index (id_usuario, fecha_creacion),
    CONSTRAINT conversaciones_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mensajes (
    id_mensaje BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_conversacion BIGINT UNSIGNED NOT NULL,
    rol VARCHAR(20) NOT NULL,
    contenido TEXT NOT NULL,
    fecha TIMESTAMP NOT NULL,
    UNIQUE KEY mensajes_id_conversacion_rol_unique (id_conversacion, rol),
    CONSTRAINT mensajes_id_conversacion_foreign FOREIGN KEY (id_conversacion) REFERENCES conversaciones(id_conversacion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE consumo_tokens (
    id_consumo BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario BIGINT UNSIGNED NOT NULL,
    id_conversacion BIGINT UNSIGNED NULL,
    consulta_uuid CHAR(36) NOT NULL,
    categoria VARCHAR(20) NOT NULL,
    etapa VARCHAR(20) NOT NULL,
    tokens_entrada INT UNSIGNED NOT NULL,
    tokens_salida INT UNSIGNED NOT NULL,
    tokens INT UNSIGNED NOT NULL,
    fecha TIMESTAMP NOT NULL,
    UNIQUE KEY consumo_tokens_consulta_uuid_etapa_unique (consulta_uuid, etapa),
    KEY consumo_tokens_id_usuario_categoria_fecha_index (id_usuario, categoria, fecha),
    KEY consumo_tokens_id_conversacion_index (id_conversacion),
    CONSTRAINT consumo_tokens_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT consumo_tokens_id_conversacion_foreign FOREIGN KEY (id_conversacion) REFERENCES conversaciones(id_conversacion) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
