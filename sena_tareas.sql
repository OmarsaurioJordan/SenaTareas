
CREATE TABLE fichas (
  id int UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  nombre tinytext UNIQUE NOT NULL,
  password tinytext NOT NULL
);

CREATE TABLE materias (
  id int UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  nombre tinytext UNIQUE NOT NULL
);

CREATE TABLE fichamat (
  id int UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  ficha int UNSIGNED NOT NULL,
  materia int UNSIGNED NOT NULL,
  FOREIGN KEY (ficha)
        REFERENCES fichas(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
  FOREIGN KEY (materia)
        REFERENCES materias(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE tareas (
  id int UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  ficha int UNSIGNED NOT NULL,
  materia int UNSIGNED NOT NULL,
  fecha date NOT NULL,
  titulo tinytext NOT NULL,
  descripcion text NOT NULL,
  link tinytext,
  integrantes tinyint UNSIGNED NOT NULL,
  FOREIGN KEY (ficha)
        REFERENCES fichas(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
  FOREIGN KEY (materia)
        REFERENCES materias(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE master (
  password tinytext NOT NULL
);
