-- Importación: Veteranos Tres de Mayo
-- Fuente: VeteranosTresdeMayo.xlsx
-- club_id=4 (tres de mayo), categoria_id=9 (Veteranos)
-- fecha_nacimiento = 1 de enero del año de nacimiento (columna "Clase" del Excel)

SET @now = UNIX_TIMESTAMP();

INSERT INTO jugador (nombre, dni, fecha_nacimiento, numero_carnet, categoria_id, club_id, cant_fechas_suspension, created_at, updated_at)
VALUES
  ('Diaz Gabriel',          '34194996', '1989-01-01', '328', 9, 4, 0, @now, @now),
  ('Baldes Mauricio',       '34677457', '1989-01-01', '330', 9, 4, 0, @now, @now),
  ('Villegas Hernan',       '34463145', '1988-01-01', '331', 9, 4, 0, @now, @now),
  ('Gauna Ricardo',         '34194898', '1990-01-01', '332', 9, 4, 0, @now, @now),
  ('Zapata Rodolfo',        '35547028', '1986-01-01', '333', 9, 4, 0, @now, @now),
  ('Palomino Juan',         '31948978', '1982-01-01', '334', 9, 4, 0, @now, @now),
  ('Gonzalez Hernan',       '29648308', '1984-01-01', '335', 9, 4, 0, @now, @now),
  ('Rua Hector',            '30995418', '1990-01-01', '336', 9, 4, 0, @now, @now),
  ("D'Ambrosio Emiliano",   '34786116', '1990-01-01', '337', 9, 4, 0, @now, @now);
