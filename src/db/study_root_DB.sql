CREATE DATABASE IF NOT EXISTS study_root;

USE study_root;

CREATE TABLE estudante(
  id_estudante INT NOT NULL AUTO_INCREMENT,
  email VARCHAR NOT NULL,
  senha VARCHAR NOT NULL,
);



CREATE TABLE assunto(
  id_assunto INT NOT NULL AUTO_INCREMENT,
  titulo VARCHAR NOT NULL,
  PRIMARY KEY (id_assunto)
);

CREATE TABLE anotacao(
  id_anotacao INT NOT NULL AUTO_INCREMENT,
  titulo_fk VARCHAR NOT NULL,
  PRIMARY KEY (id_anotacao),
  FOREIGN KEY (titulo_fk) REFERENCES assunto(titulo)
);
