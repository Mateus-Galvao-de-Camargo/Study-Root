CREATE DATABASE IF NOT EXISTS study_root;

USE study_root;

CREATE TABLE IF NOT EXISTS estudante(
  id_estudante INT NOT NULL AUTO_INCREMENT,
  usuario VARCHAR(30) NOT NULL,
  email VARCHAR(50) NOT NULL,
  senha VARCHAR(37) NOT NULL,
);

CREATE TABLE IF NOT EXISTS assunto(
  id_assunto INT NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(52) NOT NULL,
  resumo VARCHAR(300),
  id_estudante_fk VARCHAR NOT NULL,
  PRIMARY KEY (id_assunto),
  FOREIGN KEY (id_estudante_fk) REFERENCES estudante(id_estudante)
);

CREATE TABLE IF NOT EXISTS anotacao(
  id_anotacao INT NOT NULL AUTO_INCREMENT,
  conteudo TEXT COLLATE utf8_unicode_ci NOT NULL,
  id_assunto_fk VARCHAR NOT NULL,
  PRIMARY KEY (id_anotacao),
  FOREIGN KEY (id_assunto_fk) REFERENCES assunto(id_assunto)
);
