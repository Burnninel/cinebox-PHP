-- # TABELAS 

CREATE TABLE filmes (
  
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(100) NOT NULL,          
  diretor VARCHAR(100) NOT NULL,
  ano_de_lancamento YEAR NOT NULL,           
  sinopse TEXT NOT NULL,                     
  categoria VARCHAR(55) NOT NULL             

);


CREATE TABLE usuarios (
  
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,          
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(100) NOT NULL

);

CREATE TABLE avaliacoes (
  
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  filme_id INT NOT NULL,
  nota INT NOT NULL,
  comentario TEXT,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (filme_id) REFERENCES filmes(id)

);

CREATE TABLE usuarios_filmes (
  
  usuario_id INT NOT NULL,
  filme_id INT NOT NULL,
  PRIMARY KEY (usuario_id, filme_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (filme_id) REFERENCES filmes(id)
  
);



-- # INSERTS 

INSERT INTO usuarios 
	(nome, email, senha) 
VALUES 
	('Bruno', 'brunoabc11@gmail.com', 'Pokopika1.');

INSERT INTO filmes 
	(titulo, diretor, ano_de_lancamento, sinopse, categoria)
VALUES 
 (
   'Projeto X', 
   'Nilma Nourizadeh', 
   2012,
   'Três amigos de colégio planejam uma festa inesquecível para entrar para a história na tentativa de ficarem famosos. A notícia se espalha rapidamente e tudo foge ao controle quando os imprevistos começam a acontecer.',
   'Comédia/Ficção policial'
 )
 
INSERT INTO avaliacoes
	(usuario_id, filme_id, nota, comentario)
VALUES
	(
    1,
    1, 
    5, 
    'Excelnte filme!'
	)
    
INSERT INTO usuarios_filmes 
	(usuario_id, filme_id) 
VALUES 
	(2, 1);
  
-- # SELECTS    
SELECT nome, email, senha FROM usuarios;

SELECT titulo, diretor, ano_de_lancamento, sinopse, categoria FROM filmes;

SELECT 
  u.nome AS usuario, 
  f.titulo AS filme,
  a.comentario
FROM avaliacoes AS a
LEFT JOIN usuarios AS u ON u.id = a.usuario_id
LEFT JOIN filmes AS f ON f.id = a.filme_id;

SELECT 
  u.nome AS usuario, 
  f.titulo AS filme
FROM usuarios_filmes AS u_f
LEFT JOIN usuarios AS u ON u.id = u_f.usuario_id
LEFT JOIN filmes AS f ON f.id = u_f.filme_id
WHERE u_f.usuario_id = 1;
